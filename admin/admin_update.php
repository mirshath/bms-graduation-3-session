<?php
include("../database/connection.php");

$admin_id = intval($_POST['admin_id']);
$admin_name = trim($_POST['admin_name']);
$email = trim($_POST['email']);
$role = $_POST['role'] ?? 'admin';
$password = $_POST['password'] ?? '';

if ($admin_id && $admin_name && $email && $role) {
    if (!empty($password)) {
        if (strlen($password) < 6) {
            echo "Password must be at least 6 characters!";
            exit;
        }
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE admin SET admin_name=?, email=?, role=?, password=? WHERE id=?");
        $stmt->bind_param("ssssi", $admin_name, $email, $role, $hashedPassword, $admin_id);
    } else {
        $stmt = $conn->prepare("UPDATE admin SET admin_name=?, email=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $admin_name, $email, $role, $admin_id);
    }

    if ($stmt->execute()) {
        echo "Admin updated successfully!";
    } else {
        echo "Failed to update admin!";
    }
} else {
    echo "Invalid data!";
}
?>