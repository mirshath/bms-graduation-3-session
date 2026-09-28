<?php
// session_start();

// $response = [
//     'valid' => true
// ];

// if (!isset($_SESSION['admin_id']) || $_SESSION['admin_name'] === 'Unknown Admin') {
//     $response['valid'] = false;
// }

// echo json_encode($response);
// exit();






session_start();
include("../database/connection.php");

$response = ['valid' => true];

// Step 1: Check session variables
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_name'])) {
    $response['valid'] = false;
    echo json_encode($response);
    exit;
}

// Step 2: Verify session data with database
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];

$stmt = $conn->prepare("SELECT id FROM admin WHERE id = ? AND admin_name = ?");
$stmt->bind_param("is", $admin_id, $admin_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // No matching admin found — possibly tampered session or deleted admin
    $response['valid'] = false;
}

echo json_encode($response);
exit;
