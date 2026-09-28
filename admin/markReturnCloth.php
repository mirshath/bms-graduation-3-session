<?php
session_start();
include("../database/connection.php");

if (!isset($_POST['studentID']) || !isset($_POST['item'])) {
    echo json_encode(['status' => 'error']);
    exit;
}

$studentID = $_POST['studentID'];
$item = $_POST['item'];

// Map item to column
$column = '';
if ($item == 'cloak') $column = 'return_cloak';
if ($item == 'slashes') $column = 'return_slashes';
if ($item == 'hats') $column = 'return_hats';

if ($column) {
    $stmt = $conn->prepare("UPDATE clothing_collections SET $column='returned' WHERE student_id=?");
    $stmt->bind_param("s", $studentID);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
} else {
    echo json_encode(['status' => 'error']);
}
