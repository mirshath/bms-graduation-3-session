<?php
/**
 * Marks a student's invitation as issued (used when the student has 0 extra tickets).
 * Writes registered_students.invitation_collected + updated_at_invitation.
 * Same pattern as issue_extra_tickets.php: POST student_id + csrf, JSON back.
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_name'] ?? '') === 'Unknown Admin') {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || !hash_equals((string)($_SESSION['csrf'] ?? ''), (string)($_POST['csrf'] ?? ''))) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Session expired. Reload the page and try again.']);
    exit;
}

$admin      = trim((string)($_SESSION['admin_name'] ?? ''));
$student_id = trim((string)($_POST['student_id'] ?? ''));
session_write_close();

if ($student_id === '') {
    echo json_encode(['ok' => false, 'error' => 'Student ID is missing.']);
    exit;
}

include("../database/connection.php");

// must have a payment record first (same rule as the panel)
$st = $conn->prepare("SELECT COUNT(*) FROM payment_records WHERE student_id = ?");
$st->bind_param("s", $student_id);
$st->execute();
$st->bind_result($payments);
$st->fetch();
$st->close();
if ((int)$payments === 0) {
    echo json_encode(['ok' => false, 'error' => 'Make the payment first.']);
    exit;
}

// only update when it is still empty, so a double click or two desks cannot overwrite each other
$st = $conn->prepare(
    "UPDATE registered_students
        SET invitation_collected = 'Issued', updated_at_invitation = NOW()
      WHERE student_id = ?
        AND (invitation_collected IS NULL OR TRIM(invitation_collected) = '')"
);
$st->bind_param("s", $student_id);
$st->execute();
$changed = $st->affected_rows;
$st->close();

if ($changed === 1) {
    echo json_encode(['ok' => true, 'already' => false, 'by' => $admin]);
    exit;
}

// nothing updated: either already issued, or the student does not exist
$st = $conn->prepare("SELECT COUNT(*) FROM registered_students WHERE student_id = ?");
$st->bind_param("s", $student_id);
$st->execute();
$st->bind_result($exists);
$st->fetch();
$st->close();

echo $exists
    ? json_encode(['ok' => true, 'already' => true, 'by' => $admin])
    : json_encode(['ok' => false, 'error' => 'Student not found.']);
