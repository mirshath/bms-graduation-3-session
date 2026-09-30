<?php
session_start();
include("../database/connection.php");

try {
    $student_id = $_POST['student_id'] ?? '';
    if (empty($student_id)) {
        echo json_encode(['status' => 'error', 'message' => 'No student ID provided']);
        exit;
    }

    // First, check if the student has attended
    $checkSql = "SELECT attend FROM registered_students WHERE student_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    if (!$checkStmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }
    $checkStmt->bind_param("s", $student_id);
    if (!$checkStmt->execute()) {
        throw new Exception("Failed to execute check attend statement: " . $checkStmt->error);
    }
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Student not found in registered students']);
        exit;
    }

    $studentData = $checkResult->fetch_assoc();
    $attendStatus = $studentData['attend'] ?? '';

    // Check if student has attended
    if ($attendStatus !== 'attended') {
        echo json_encode(['status' => 'error', 'message' => 'Student must attend first before marking as graduated']);
        exit;
    }

    // If attended, proceed with marking as graduated
    $sql = "UPDATE bulk_data_table SET graduated_status='Yes' WHERE student_id=?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare graduate statement: " . $conn->error);
    }
    $stmt->bind_param("s", $student_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Student marked as graduated']);
    } else {
        throw new Exception("Database error: " . $stmt->error);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
