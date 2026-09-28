<?php
session_start();
include("../database/connection.php");

if (!isset($_POST['studentID'])) {
    echo json_encode(['status' => 'error', 'message' => 'No student ID provided']);
    exit;
}

$studentID = $_POST['studentID'];

// Get student info
$stmt = $conn->prepare("SELECT id, calling_name, program_name FROM bulk_data_table WHERE student_id = ?");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();

    // Check if already collected
    $check = $conn->prepare("SELECT * FROM clothing_collections WHERE student_id = ?");
    $check->bind_param("s", $studentID);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode([
            'status' => 'collected',
            'student_name' => $student['calling_name'],
            'student_id' => $studentID,
            'program' => $student['program_name']
        ]);
    } else {
        echo json_encode([
            'status' => 'found',
            'student_name' => $student['calling_name'],
            'student_id' => $studentID,
            'program' => $student['program_name']
        ]);
    }
} else {
    echo json_encode(['status' => 'not_found']);
}
exit;
