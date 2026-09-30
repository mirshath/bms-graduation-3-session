<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include("../database/connection.php");

$studentID = trim($_POST['studentID'] ?? $_GET['studentID'] ?? '');
if ($studentID === '') {
    echo json_encode(['status' => 'error', 'message' => 'Student ID required']);
    exit();
}

// 1) Student's program
$stmt = $conn->prepare("SELECT program_name FROM registered_students WHERE student_id = ? LIMIT 1");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    echo json_encode(['status' => 'error', 'message' => 'Student not found']);
    exit();
}

// 2) Items required by the program (only value 1 counts)
$stmt = $conn->prepare("SELECT cloak, slashes, hats FROM data_tables WHERE programName = ? LIMIT 1");
$stmt->bind_param("s", $student['program_name']);
$stmt->execute();
$req = $stmt->get_result()->fetch_assoc() ?: ['cloak' => 0, 'slashes' => 0, 'hats' => 0];
$stmt->close();

// 3) Items the student has collected (works even if there are several rows)
$stmt = $conn->prepare("
    SELECT MAX(collect_cloak   = 'collected') AS cloak,
           MAX(collect_slashes = 'collected') AS slashes,
           MAX(collect_hats    = 'collected') AS hats
    FROM clothing_collections
    WHERE student_id = ?
");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$got = $stmt->get_result()->fetch_assoc() ?: [];
$stmt->close();

$labels    = ['cloak' => 'Cloak', 'slashes' => 'Slashes', 'hats' => 'Hat'];
$required  = [];
$collected = [];
$missing   = [];

foreach ($labels as $key => $label) {
    if ((int)($req[$key] ?? 0) === 1) {
        $required[] = $label;
        if ((int)($got[$key] ?? 0) === 1) {
            $collected[] = $label;
        } else {
            $missing[] = $label;
        }
    }
}

echo json_encode([
    'status'    => 'success',
    'required'  => $required,
    'collected' => $collected,
    'missing'   => $missing,
]);
