<?php
session_start();
include("../database/connection.php");

header('Content-Type: application/json');

if (!isset($_POST['studentID'])) {
    echo json_encode(['status' => 'error', 'message' => 'No student ID provided']);
    exit;
}

$studentID = trim($_POST['studentID']);
if ($studentID === '') {
    echo json_encode(['status' => 'error', 'message' => 'No student ID provided']);
    exit;
}

$regStmt = $conn->prepare("SELECT calling_name, program_name FROM registered_students WHERE student_id = ?");
$regStmt->bind_param("s", $studentID);
$regStmt->execute();
$regResult = $regStmt->get_result();
$regStudent = $regResult->fetch_assoc();

if ($regResult->num_rows == 0) {
    echo json_encode(['status' => 'not_registered']);
    exit;
}

$payStmt = $conn->prepare("SELECT 1 FROM payment_records WHERE student_id = ? LIMIT 1");
$payStmt->bind_param("s", $studentID);
$payStmt->execute();
$payResult = $payStmt->get_result();

if ($payResult->num_rows == 0) {
    echo json_encode(['status' => 'not_paid']);
    exit;
}

$programName = $regStudent['program_name'];
$session_str = 'None';
$items = [
    'cloak'   => ['assigned' => false, 'collected' => false],
    'slashes' => ['assigned' => false, 'collected' => false],
    'hats'    => ['assigned' => false, 'collected' => false],
];

$itemStmt = $conn->prepare("SELECT cloak, slashes, hats, session FROM data_tables WHERE programName = ?");
$itemStmt->bind_param("s", $programName);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();

if ($itemResult->num_rows > 0) {
    $row = $itemResult->fetch_assoc();
    $items['cloak']['assigned']   = !empty($row['cloak']) && (int)$row['cloak'] === 1;
    $items['slashes']['assigned'] = !empty($row['slashes']) && (int)$row['slashes'] === 1;
    $items['hats']['assigned']    = !empty($row['hats']) && (int)$row['hats'] === 1;
    $session_str = $row['session'] ?? 'None';
}

$check = $conn->prepare("SELECT collect_cloak, collect_slashes, collect_hats FROM clothing_collections WHERE student_id = ?");
$check->bind_param("s", $studentID);
$check->execute();
$checkResult = $check->get_result();

if ($checkResult->num_rows > 0) {
    $collected = $checkResult->fetch_assoc();
    $items['cloak']['collected']   = !empty($collected['collect_cloak']);
    $items['slashes']['collected'] = !empty($collected['collect_slashes']);
    $items['hats']['collected']    = !empty($collected['collect_hats']);
}

echo json_encode([
    'status'       => 'found',
    'student_name' => $regStudent['calling_name'],
    'student_id'   => $studentID,
    'program'      => $regStudent['program_name'],
    'session'      => $session_str,
    'items'        => $items,
]);
exit;
