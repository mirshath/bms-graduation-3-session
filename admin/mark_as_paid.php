<?php
session_start();
include("../database/connection.php");

header('Content-Type: application/json');

$student_id = $_POST['student_id'] ?? '';

if (empty($student_id)) {
    echo json_encode(['success' => false, 'message' => 'Student ID is missing']);
    exit;
}

try {
    // Update old_student_db
    $stmt1 = $conn->prepare("UPDATE old_student_db SET payment_status = 'paid' WHERE student_id = ?");
    $stmt1->bind_param("s", $student_id);
    $stmt1->execute();

    // Update registered_students
    $stmt2 = $conn->prepare("UPDATE registered_students SET crsfee_payment_status = 'paid' WHERE student_id = ?");
    $stmt2->bind_param("s", $student_id);
    $stmt2->execute();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
