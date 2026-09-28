<?php
include("../database/connection.php");

$program_name = $_POST['program_name'];

$stmt = $conn->prepare("SELECT extraTicketFee FROM data_tables WHERE programName = ?");
$stmt->bind_param("s", $program_name);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
