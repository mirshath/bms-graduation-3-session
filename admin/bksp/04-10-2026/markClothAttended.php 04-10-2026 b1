<?php
session_start();
include("../database/connection.php");

header('Content-Type: application/json');

if (!isset($_POST['studentID']) || !isset($_POST['item'])) {
    echo json_encode(['status' => 'error', 'message' => 'Student ID and item are required']);
    exit;
}

$studentID = trim($_POST['studentID']);
$item = strtolower(trim($_POST['item']));

$columnMap = [
    'cloak'   => 'collect_cloak',
    'slashes' => 'collect_slashes',
    'hats'    => 'collect_hats',
];

if ($studentID === '' || !isset($columnMap[$item])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid student ID or item']);
    exit;
}

$column = $columnMap[$item];

$stmt = $conn->prepare("SELECT calling_name, program_name FROM registered_students WHERE student_id = ?");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'not_found', 'message' => 'Student not found.']);
    exit;
}

$student = $result->fetch_assoc();
$programName = $student['program_name'];

$itemStmt = $conn->prepare("SELECT cloak, slashes, hats FROM data_tables WHERE programName = ?");
$itemStmt->bind_param("s", $programName);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();

if ($itemResult->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No clothing items assigned to this program.']);
    exit;
}

$assigned = $itemResult->fetch_assoc();
$isAssigned = !empty($assigned[$item]) && (int)$assigned[$item] === 1;

if (!$isAssigned) {
    echo json_encode(['status' => 'error', 'message' => 'This item is not assigned to the student program.']);
    exit;
}

$check = $conn->prepare("SELECT id FROM clothing_collections WHERE student_id = ?");
$check->bind_param("s", $studentID);
$check->execute();
$existing = $check->get_result();

if ($existing->num_rows === 0) {
    $collect_cloak = $item === 'cloak' ? 'collected' : '';
    $collect_slashes = $item === 'slashes' ? 'collected' : '';
    $collect_hats = $item === 'hats' ? 'collected' : '';

    $insert = $conn->prepare("
        INSERT INTO clothing_collections
        (student_id, student_name, program_name, collect_cloak, collect_slashes, collect_hats, collected_at)
        VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
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
    exit;
}

$update = $conn->prepare("UPDATE clothing_collections SET `$column` = 'collected' WHERE student_id = ?");
$update->bind_param("s", $studentID);

if ($update->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    $err = $conn->error ? $conn->error : 'Unknown database error';
    echo json_encode(['status' => 'error', 'message' => 'Failed to mark as collected.', 'debug' => $err]);
}

exit;
