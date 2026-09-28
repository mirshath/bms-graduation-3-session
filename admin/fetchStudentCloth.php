<?php
session_start();
include("../database/connection.php");

if (!isset($_POST['studentID'])) {
    echo json_encode(['status' => 'error']);
    exit;
}

$studentID = $_POST['studentID'];

$stmt = $conn->prepare("SELECT * FROM clothing_collections WHERE student_id=?");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['status' => 'success', 'data' => $row]);
} else {
    echo json_encode(['status' => 'not_registered']);
}
