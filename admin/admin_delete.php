<?php
include("../database/connection.php");

$admin_id = intval($_POST['admin_id'] ?? 0);
if ($admin_id) {
    // Temporarily drop the trigger that prevents deletion
    $conn->query("DROP TRIGGER IF EXISTS prevent_admin_delete");

    $stmt = $conn->prepare("DELETE FROM admin WHERE id=?");
    $stmt->bind_param("i", $admin_id);

    if ($stmt->execute()) {
        $msg = "Admin deleted successfully!";
    } else {
        $msg = "Failed to delete admin!";
    }


    // Re-create the trigger immediately after deletion (success or failure)
    $triggerSQL = "CREATE TRIGGER `prevent_admin_delete` BEFORE DELETE ON `admin` FOR EACH ROW BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Deletion from admin table is not allowed for security reasons'; END";
    $conn->query($triggerSQL);

    echo $msg;
} else {
    echo "Invalid admin ID!";
}

?>