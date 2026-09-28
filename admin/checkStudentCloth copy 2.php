<?php
session_start();
include("../database/connection.php");

if (!isset($_POST['studentID'])) {
    echo json_encode(['status' => 'error', 'message' => 'No student ID provided']);
    exit;
}

$studentID = $_POST['studentID'];

// Get student info from bulk_data_table
$stmt = $conn->prepare("SELECT calling_name, program_name FROM bulk_data_table WHERE student_id = ?");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();

    // Get items info from data_tables based on program_name
    $programName = $student['program_name'];
    $itemStmt = $conn->prepare("SELECT cloak, slashes, hats FROM data_tables WHERE programName = ?");
    $itemStmt->bind_param("s", $programName);
    $itemStmt->execute();
    $itemResult = $itemStmt->get_result();

    $items_str = '';
    if ($itemResult->num_rows > 0) {
        $items = $itemResult->fetch_assoc();
        $assigned = [];
        if (!empty($items['cloak']) && $items['cloak'] == 1) $assigned[] = "cloak";
        if (!empty($items['slashes']) && $items['slashes'] == 1) $assigned[] = "Slashes";
        if (!empty($items['hats']) && $items['hats'] == 1) $assigned[] = "Hats";
        $items_str = implode(", ", $assigned);
    } else {
        $items_str = "None";
    }

    // Check if student already collected
    $check = $conn->prepare("SELECT * FROM clothing_collections WHERE student_id = ?");
    $check->bind_param("s", $studentID);
    $check->execute();
    $checkResult = $check->get_result();

    $response = [
        'student_name' => $student['calling_name'],
        'student_id'   => $studentID,
        'program'      => $student['program_name'],
        'items'        => $items_str
    ];

    if ($checkResult->num_rows > 0) {
        $response['status'] = 'collected';
    } else {
        $response['status'] = 'found';
    }

    echo json_encode($response);
} else {
    echo json_encode(['status' => 'not_found']);
}

exit;
