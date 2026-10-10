<?php

/**
 * Invitation issue (used when the student has 0 extra tickets).
 *
 *   UPDATE registered_students
 *      SET in_no = ?, invitation_collected = 'collected'
 *    WHERE student_id = ?
 *
 * in_no = the same invitation number the panel shows
 *         (old_student_db.in_no first, otherwise registered_students.in_no).
 * Only that one student is touched. POST: student_id + csrf, JSON back.
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_name'] ?? '') === 'Unknown Admin') {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'unauthorized']);
    exit;
}

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !hash_equals((string)($_SESSION['csrf'] ?? ''), (string)($_POST['csrf'] ?? ''))
) {
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

// the student (and the current in_no / invitation state)
$st = $conn->prepare("SELECT in_no, invitation_collected FROM registered_students WHERE student_id = ?");
$st->bind_param("s", $student_id);
$st->execute();
$reg = $st->get_result()->fetch_assoc();
$st->close();
if (!$reg) {
    echo json_encode(['ok' => false, 'error' => 'Student not found.']);
    exit;
}

// already collected? nothing to do
if (strtolower(trim((string)$reg['invitation_collected'])) === 'collected') {
    echo json_encode(['ok' => true, 'already' => true, 'by' => $admin]);
    exit;
}

// payment must exist first (same rule as the panel)
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

// invitation number: old_student_db first, then registered_students (same rule as the panel)
$st = $conn->prepare("SELECT in_no FROM old_student_db WHERE student_id = ?");
$st->bind_param("s", $student_id);
$st->execute();
$old = $st->get_result()->fetch_assoc();
$st->close();
$in_no = !empty($old['in_no']) ? (int)$old['in_no'] : (int)($reg['in_no'] ?? 0);

if ($in_no <= 0) {
    echo json_encode(['ok' => false, 'error' => 'No invitation number found for this student.']);
    exit;
}

// only this student; the extra condition stops a double click / second desk from running twice
$st = $conn->prepare(
    "UPDATE registered_students
        SET in_no = ?, invitation_collected = 'collected'
      WHERE student_id = ?
        AND LOWER(TRIM(COALESCE(invitation_collected, ''))) <> 'collected'"
);
$st->bind_param("is", $in_no, $student_id);
$ok = $st->execute();
$changed = $st->affected_rows;
$st->close();

if (!$ok) {
    echo json_encode(['ok' => false, 'error' => 'Could not save. Try again.']);
    exit;
}

echo json_encode(['ok' => true, 'already' => ($changed === 0), 'by' => $admin]);
