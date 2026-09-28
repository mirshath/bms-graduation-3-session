<?php
include("../database/connection.php");

$admin_id = intval($_POST['admin_id'] ?? 0);
$stmt = $conn->prepare("SELECT id, admin_name, email, role FROM admin WHERE id=?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
echo json_encode($result->fetch_assoc());
?>
