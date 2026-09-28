<?php
session_start();
include("../database/connection.php");

// Only allow if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$studentID = trim($_POST['studentID'] ?? '');
if ($studentID === '') {
    echo json_encode(["status" => "error", "message" => "No student ID provided"]);
    exit;
}

// Update the 'attend' column to current datetime or 'Yes'
$sql = "UPDATE registered_students SET attend = 'attended' WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentID);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Attendance marked"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database update failed"]);
}

$stmt->close();
$conn->close();
