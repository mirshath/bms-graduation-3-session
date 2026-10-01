<?php

/**
 * Backend for extra-ticket-buying.php
 *   Extra tickets can only be added once the student has a row in payment_records.
 *   action=lookup : student + programme + session + extra ticket fee for a scanned student ID
 *   action=add    : writes one row to extra_ticket_log (price always comes from data_tables.extraTicketFee)
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function out($code, $arr)
{
    http_response_code($code);
    echo json_encode($arr);
    exit;
}

$admin_name = trim((string)($_SESSION['admin_name'] ?? '')); // stored in extra_ticket_log.added_by
if (!isset($_SESSION['admin_id']) || $admin_name === '' || $admin_name === 'Unknown Admin') {
    out(401, ['ok' => false, 'error' => 'unauthorized']);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    out(405, ['ok' => false, 'error' => 'POST only']);
}

$action   = (string)($_POST['action'] ?? '');
$csrf_ok  = hash_equals((string)($_SESSION['csrf'] ?? ''), (string)($_POST['csrf'] ?? ''));
session_write_close(); // lookups are polled/clicked fast, do not hold the session lock

include("../database/connection.php");

$student_id = trim((string)($_POST['student_id'] ?? ''));
if ($student_id === '') {
    out(400, ['ok' => false, 'error' => 'Student ID is missing.']);
}

/** Everything the page needs about one student, or null when not registered. */
function load_student($conn, $student_id)
{
    $st = $conn->prepare(
        "SELECT student_id, in_no, name_in_full, program_name, `session`, graduation_payment_status
         FROM registered_students WHERE student_id = ?"
    );
    $st->bind_param("s", $student_id);
    $st->execute();
    $reg = $st->get_result()->fetch_assoc();
    $st->close();
    if (!$reg) {
        return null;
    }

    $program = (string)$reg['program_name'];
    $session = '';
    $fee     = null;
    $st = $conn->prepare(
        "SELECT `session`, `extraTicketFee` FROM `data_tables`
         WHERE TRIM(`programName`) = TRIM(?) ORDER BY `active` DESC, `id` DESC LIMIT 1"
    );
    $st->bind_param("s", $program);
    $st->execute();
    if ($row = $st->get_result()->fetch_assoc()) {
        $session = trim((string)$row['session']);
        $fee     = $row['extraTicketFee'] !== null ? (float)$row['extraTicketFee'] : null;
    }
    $st->close();
    if ($session === '') {
        $session = trim((string)($reg['session'] ?? '')); // fall back to the session saved at registration
    }

    // has this student made the graduation payment? (latest row in payment_records)
    $pay = null;
    $st = $conn->prepare(
        "SELECT receipt_number, payment_date, free_ticket_count, extra_ticket_count
         FROM payment_records WHERE student_id = ? ORDER BY payment_date DESC, id DESC LIMIT 1"
    );
    $st->bind_param("s", $student_id);
    $st->execute();
    $pay = $st->get_result()->fetch_assoc();
    $st->close();

    $bought = 0;
    $st = $conn->prepare("SELECT COALESCE(SUM(added_tickets), 0) FROM extra_ticket_log WHERE student_id = ?");
    $st->bind_param("s", $student_id);
    $st->execute();
    $bought = (int)$st->get_result()->fetch_row()[0];
    $st->close();

    return [
        'student_id' => (string)$reg['student_id'],
        'name'       => (string)$reg['name_in_full'],
        'in_no'      => $reg['in_no'] !== null ? (string)$reg['in_no'] : '',
        'program'    => $program,
        'session'    => $session,
        'fee'        => $fee,
        'paid'         => $pay !== null,
        'receipt'      => $pay ? (string)$pay['receipt_number'] : '',
        'paid_on'      => $pay ? date('j M Y', strtotime((string)$pay['payment_date'])) : '',
        'free_tickets' => $pay ? (int)$pay['free_ticket_count'] : 0,
        'paid_extra'   => $pay ? (int)$pay['extra_ticket_count'] : 0,
        'bought'       => $bought,
    ];
}

try {
    $s = load_student($conn, $student_id);

    if ($action === 'lookup') {
        if (!$s) {
            out(200, ['ok' => true, 'found' => false]);
        }
        out(200, ['ok' => true, 'found' => true, 'student' => $s]);
    }

    if ($action === 'add') {
        if (!$csrf_ok) {
            out(403, ['ok' => false, 'error' => 'Session expired. Reload the page and try again.']);
        }
        $count = (int)($_POST['count'] ?? 0);
        if ($count < 1 || $count > 5) {
            out(422, ['ok' => false, 'error' => 'Choose between 1 and 5 tickets.']);
        }
        if (!$s) {
            out(404, ['ok' => false, 'error' => 'No registered student found for this ID.']);
        }
        if (!$s['paid']) {
            out(402, ['ok' => false, 'error' => 'Payment not found. Make the graduation payment first.']);
        }
        if ($s['fee'] === null || $s['fee'] <= 0) {
            out(422, ['ok' => false, 'error' => 'The extra ticket fee is not set for this programme.']);
        }

        foreach (['student_name', 'program_name', 'session'] as $col) {
            $r = $conn->query("SHOW COLUMNS FROM `extra_ticket_log` LIKE '$col'");
            if (!$r || $r->num_rows === 0) {
                out(500, ['ok' => false, 'error' => "Column $col is missing. Run add_extra_ticket_log_columns.sql first."]);
            }
        }

        $price = (float)$s['fee'];          // never taken from the browser
        $total = $price * $count;
        $st = $conn->prepare(
            "INSERT INTO extra_ticket_log
                (student_id, student_name, program_name, `session`, added_tickets, ticket_price, total_added, added_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $st->bind_param("ssssidds", $s['student_id'], $s['name'], $s['program'], $s['session'], $count, $price, $total, $admin_name);
        $st->execute();
        $st->close();

        out(200, ['ok' => true, 'count' => $count, 'price' => $price, 'total' => $total, 'name' => $s['name']]);
    }

    out(400, ['ok' => false, 'error' => 'Unknown action.']);
} catch (Throwable $e) {
    out(500, ['ok' => false, 'error' => 'Could not complete the request. Please try again.']);
}
