<?php
include("../database/connection.php");
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$ids = $data['ids'] ?? [];

if (!empty($ids)) {
    $cleanIds = implode(',', array_map('intval', $ids));
    $sql = "UPDATE notifications SET status = 'read' WHERE id IN ($cleanIds)";
    if ($conn->query($sql)) {
        echo json_encode(["success" => true]);
        exit;
    }
}

echo json_encode(["success" => false]);
