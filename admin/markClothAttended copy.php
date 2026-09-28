<?php
session_start();
include("../database/connection.php");

if (!isset($_POST['studentID'])) {
    echo json_encode(['status' => 'error', 'message' => 'No student ID provided']);
    exit;
}

$studentID = $_POST['studentID'];

// Get student info
$stmt = $conn->prepare("SELECT calling_name, program_name FROM bulk_data_table WHERE student_id = ?");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();

    // Insert into clothing_collections, prevent duplicates
    $insert = $conn->prepare("INSERT INTO clothing_collections (student_id, student_name, program_name) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE collected_at = CURRENT_TIMESTAMP");
    $insert->bind_param("sss", $studentID, $student['calling_name'], $student['program_name']);

    if ($insert->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to mark as collected.']);
    }
} else {
    echo json_encode(['status' => 'not_found']);
}
exit;
