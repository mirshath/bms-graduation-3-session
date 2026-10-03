<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include("../database/connection.php");

function respond(bool $ok, string $msg): void
{
    echo json_encode(['success' => $ok, 'message' => $msg]);
    exit();
}

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    respond(false, 'Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'], $_POST['dob'], $_POST['student_id'])) {
    respond(false, 'Invalid request');
}

$id         = intval($_POST['id']);
$dob        = trim($_POST['dob']);
$student_id = trim($_POST['student_id']);

if ($id <= 0 || $student_id === '') {
    respond(false, 'Student ID cannot be empty');
}

// DOB must be a real date in yyyy-mm-dd
$d = DateTime::createFromFormat('Y-m-d', $dob);
if (!$d || $d->format('Y-m-d') !== $dob) {
    respond(false, 'Invalid date. Use yyyy-mm-dd');
}

try {
    if (isset($_POST['active'])) {
        // Active status: completed / not-completed (empty = not set)
        $active = trim($_POST['active']);
        if ($active === '') {
            $active = null;
        } elseif (mb_strlen($active) > 255) {
            respond(false, 'Active status is too long');
        }

        $stmt = $conn->prepare("UPDATE old_student_db SET DOB=?, student_id=?, active=? WHERE id=?");
        $stmt->bind_param("sssi", $dob, $student_id, $active, $id);
    } else {
        $stmt = $conn->prepare("UPDATE old_student_db SET DOB=?, student_id=? WHERE id=?");
        $stmt->bind_param("ssi", $dob, $student_id, $id);
    }

    if ($stmt->execute()) {
        respond(true, 'Record updated successfully');
    }
    error_log("update_dob.php: " . $stmt->error);
    respond(false, 'Failed to update record');
} catch (Throwable $e) {
    error_log("update_dob.php: " . $e->getMessage());
    respond(false, 'Failed to update record');
}
