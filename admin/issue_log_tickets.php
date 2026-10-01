<?php

/**
 * Marks the extra tickets bought later (extra_ticket_log) as issued:
 *   extra_ticket_log.issued_adExtra_ticket    = 'issued'
 *   extra_ticket_log.issued_adExtra_ticket_by = logged-in admin name
 * Called by the "Issue extra tickets" button on the "Extra tickets added later" card of live_scan.php.
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
    foreach (['issued_adExtra_ticket', 'issued_adExtra_ticket_by'] as $colName) {
        $col = $conn->query("SHOW COLUMNS FROM `extra_ticket_log` LIKE '$colName'");
        if (!$col || $col->num_rows === 0) {
            out(500, ['ok' => false, 'error' => "Column $colName is missing. Run add_issued_adextra_ticket.sql first."]);
        }
    }

    // the payment has to be made first
    $st = $conn->prepare("SELECT COUNT(*) FROM payment_records WHERE student_id = ?");
    $st->bind_param("s", $student_id);
    $st->execute();
    $paid = (int)$st->get_result()->fetch_row()[0];
    $st->close();
    if ($paid === 0) {
        out(422, ['ok' => false, 'error' => 'Make the payment first. No payment record exists for this student.']);
    }

    // the student must have extra tickets added in extra_ticket_log
    $st = $conn->prepare("SELECT COUNT(*) FROM extra_ticket_log WHERE student_id = ? AND added_tickets > 0");
    $st->bind_param("s", $student_id);
    $st->execute();
    $has = (int)$st->get_result()->fetch_row()[0];
    $st->close();
    if ($has === 0) {
        out(422, ['ok' => false, 'error' => 'This student has no added extra tickets to issue.']);
    }

    // only rows that are not issued yet are touched, so a double click changes nothing
    $st = $conn->prepare(
        "UPDATE extra_ticket_log
         SET issued_adExtra_ticket = 'issued', issued_adExtra_ticket_by = ?
         WHERE student_id = ? AND added_tickets > 0
           AND (issued_adExtra_ticket IS NULL OR TRIM(issued_adExtra_ticket) = '')"
    );
    $st->bind_param("ss", $issued_by, $student_id);
    $st->execute();
    $changed = $st->affected_rows;
    $st->close();

    out(200, ['ok' => true, 'issued' => $changed, 'already' => ($changed === 0), 'by' => $issued_by]);
} catch (Throwable $e) {
    out(500, ['ok' => false, 'error' => 'Could not save. Please try again.']);
}
