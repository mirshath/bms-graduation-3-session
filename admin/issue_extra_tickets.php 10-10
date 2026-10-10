<?php

/**
 * Marks a student's extra tickets as issued:
 *   payment_records.issued_ex_ticket = 'issued'
 *   payment_records.issued_by        = logged-in admin name
 * Called by the "Issue extra tickets" button on live_scan.php.
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

if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_name'] ?? '') === 'Unknown Admin') {
    out(401, ['ok' => false, 'error' => 'unauthorized']);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    out(405, ['ok' => false, 'error' => 'POST only']);
}
if (!hash_equals((string)($_SESSION['csrf'] ?? ''), (string)($_POST['csrf'] ?? ''))) {
    out(403, ['ok' => false, 'error' => 'Session expired. Reload the page and try again.']);
}
// who is pressing the button: always taken from the login session, never from the browser
$issued_by = trim((string)($_SESSION['admin_name'] ?? ''));
if ($issued_by === '') {
    $issued_by = 'Admin #' . (int)($_SESSION['admin_id'] ?? 0);
}
session_write_close();

include("../database/connection.php");

$student_id = trim((string)($_POST['student_id'] ?? ''));
if ($student_id === '') {
    out(400, ['ok' => false, 'error' => 'Student ID is missing.']);
}

try {
    foreach (['issued_ex_ticket' => 'add_issued_ex_ticket.sql', 'issued_by' => 'add_issued_by.sql'] as $colName => $sqlFile) {
        $col = $conn->query("SHOW COLUMNS FROM `payment_records` LIKE '$colName'");
        if (!$col || $col->num_rows === 0) {
            out(500, ['ok' => false, 'error' => "Column $colName is missing. Run $sqlFile first."]);
        }
    }

    // no payment at all -> the payment has to be made first
    $st = $conn->prepare("SELECT COUNT(*) FROM payment_records WHERE student_id = ?");
    $st->bind_param("s", $student_id);
    $st->execute();
    $any = (int)$st->get_result()->fetch_row()[0];
    $st->close();
    if ($any === 0) {
        out(422, ['ok' => false, 'error' => 'Make the payment first. No payment record exists for this student.']);
    }

    // the student must actually have bought extra tickets
    $st = $conn->prepare("SELECT COUNT(*) FROM payment_records WHERE student_id = ? AND extra_ticket_count > 0");
    $st->bind_param("s", $student_id);
    $st->execute();
    $has = (int)$st->get_result()->fetch_row()[0];
    $st->close();
    if ($has === 0) {
        out(422, ['ok' => false, 'error' => 'This student has no extra tickets to issue.']);
    }

    // only rows that are not issued yet are touched, so a double click changes nothing
    $st = $conn->prepare(
        "UPDATE payment_records
         SET issued_ex_ticket = 'issued', issued_by = ?
         WHERE student_id = ? AND extra_ticket_count > 0
           AND (issued_ex_ticket IS NULL OR issued_ex_ticket <> 'issued')"
    );
    $st->bind_param("ss", $issued_by, $student_id);
    $st->execute();
    $changed = $st->affected_rows;
    $st->close();

    out(200, ['ok' => true, 'issued' => $changed, 'already' => ($changed === 0), 'by' => $issued_by]);
} catch (Throwable $e) {
    out(500, ['ok' => false, 'error' => 'Could not save. Please try again.']);
}
