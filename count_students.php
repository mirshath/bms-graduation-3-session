<?php

include('./database/connection.php'); // Include your database connection file

// Query to count rows in registered_students table
$sql = "SELECT COUNT(*) as count FROM registered_students";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo json_encode($row); // Return the count as JSON

$conn->close();
?>