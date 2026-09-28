<?php
session_start();
include("../database/connection.php");

if (!isset($_POST['studentID'])) {
    echo json_encode(['status' => 'error', 'message' => 'No student ID provided']);
    exit;
}

$studentID = $_POST['studentID'];

// Get student info from bulk_data_table
// $stmt = $conn->prepare("SELECT calling_name, program_name FROM bulk_data_table WHERE student_id = ?");
// $stmt->bind_param("s", $studentID);
// $stmt->execute();
// $result = $stmt->get_result();
$stmt = $conn->prepare("SELECT calling_name, program_name FROM registered_students WHERE student_id = ?");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    $programName = $student['program_name'];

    // Get items info from data_tables (use empty string when no row or not assigned)
    $collect_cloak = $collect_slashes = $collect_hats = '';
    $itemStmt = $conn->prepare("SELECT cloak, slashes, hats FROM data_tables WHERE programName = ?");
    $itemStmt->bind_param("s", $programName);
    $itemStmt->execute();
    $itemResult = $itemStmt->get_result();
    if ($itemResult->num_rows > 0) {
        $items = $itemResult->fetch_assoc();
        $collect_cloak   = (!empty($items['cloak']) && $items['cloak'] == 1) ? "collected" : '';
        $collect_slashes = (!empty($items['slashes']) && $items['slashes'] == 1) ? "collected" : '';
        $collect_hats    = (!empty($items['hats']) && $items['hats'] == 1) ? "collected" : '';
    }

    // Insert or update record (use empty string for optional collect_* to avoid bind_param null issues)
    $insert = $conn->prepare("
        INSERT INTO clothing_collections 
        (student_id, student_name, program_name, collect_cloak, collect_slashes, collect_hats, collected_at)
        VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ON DUPLICATE KEY UPDATE 
            student_name = VALUES(student_name),
            program_name = VALUES(program_name),
            collect_cloak = VALUES(collect_cloak),
            collect_slashes = VALUES(collect_slashes),
            collect_hats = VALUES(collect_hats),
            collected_at = CURRENT_TIMESTAMP
    ");

    $insert->bind_param(
        "ssssss",
        $studentID,
        $student['calling_name'],
        $student['program_name'],
        $collect_cloak,
        $collect_slashes,
        $collect_hats
    );

    if ($insert->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        $err = $conn->error ? $conn->error : 'Unknown database error';
        echo json_encode(['status' => 'error', 'message' => 'Failed to mark as collected.', 'debug' => $err]);
    }
} else {
    echo json_encode(['status' => 'not_found']);
}

exit;
