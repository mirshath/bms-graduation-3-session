<?php
include("../database/connection.php");
session_start();

$addTickets = intval($_POST['addTickets']);
$ticketPrice = floatval($_POST['ticketPrice']);
$added_by = $_SESSION['admin_id'] ?? 0;
$totalAdded = $addTickets * $ticketPrice;

if ($addTickets > 0 && $ticketPrice > 0) {
    @$conn->query("
        INSERT INTO extra_ticket_log (student_id, added_tickets, ticket_price, total_added, added_by)
        VALUES (NULL, '$addTickets', '$ticketPrice', '$totalAdded', '$added_by')
    ");

    echo "Extra tickets added successfully!";
}
else {
    echo "Invalid ticket amount or price!";
}
