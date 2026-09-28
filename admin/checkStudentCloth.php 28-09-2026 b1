<?php
session_start();
include("../database/connection.php");

if (!isset($_POST['studentID'])) {
    echo json_encode(['status' => 'error', 'message' => 'No student ID provided']);
    exit;
}

$studentID = trim($_POST['studentID']);
if ($studentID === '') {
    echo json_encode(['status' => 'error', 'message' => 'No student ID provided']);
    exit;
}

// Step 1: Check if student is registered
$regStmt = $conn->prepare("SELECT calling_name, program_name FROM registered_students WHERE student_id = ?");
$regStmt->bind_param("s", $studentID);
$regStmt->execute();
$regResult = $regStmt->get_result();
$regStudent = $regResult->fetch_assoc();

if ($regResult->num_rows == 0) {
    echo json_encode(['status' => 'not_registered']);
    exit;
}

// Step 2: Check if student has completed payment (must exist in payment_records)
$payStmt = $conn->prepare("SELECT 1 FROM payment_records WHERE student_id = ? LIMIT 1");
$payStmt->bind_param("s", $studentID);
$payStmt->execute();
$payResult = $payStmt->get_result();

if ($payResult->num_rows == 0) {
    echo json_encode(['status' => 'not_paid']);
    exit;
}

// Step 3: Registered + paid — get items by program, check if already issued
$programName = $regStudent['program_name'];

$itemStmt = $conn->prepare("SELECT cloak, slashes, hats FROM data_tables WHERE programName = ?");
$itemStmt->bind_param("s", $programName);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();

$items_str = 'None';
if ($itemResult->num_rows > 0) {
    $items = $itemResult->fetch_assoc();
    $assigned = [];
    if (!empty($items['cloak']) && $items['cloak'] == 1) $assigned[] = "Cloak";
    if (!empty($items['slashes']) && $items['slashes'] == 1) $assigned[] = "Slashes";
    if (!empty($items['hats']) && $items['hats'] == 1) $assigned[] = "Hats";
    $items_str = count($assigned) > 0 ? implode(", ", $assigned) : "None";
}

$check = $conn->prepare("SELECT * FROM clothing_collections WHERE student_id = ?");
$check->bind_param("s", $studentID);
$check->execute();
$checkResult = $check->get_result();

$response = [
    'student_name' => $regStudent['calling_name'],
    'student_id'   => $studentID,
    'program'      => $regStudent['program_name'],
    'items'        => $items_str
];

if ($checkResult->num_rows > 0) {
    $response['status'] = 'collected';
} else {
    $response['status'] = 'found';
}

echo json_encode($response);
exit;
