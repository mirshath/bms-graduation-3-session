<?php
// include("../database/connection.php");

// $student_id = $_POST['student_id'];
// $query = "SELECT program_name, graduation_fee, extra_ticket_count, extra_ticket_fee, total_amount
//           FROM payment_records WHERE student_id=?";
// $stmt = $conn->prepare($query);
// $stmt->bind_param("s", $student_id);
// $stmt->execute();
// $result = $stmt->get_result();
// $data = $result->fetch_assoc();
// echo json_encode($data);




include("../database/connection.php");

$student_id = $_POST['student_id'];

// Get student payment info + program ticket price
$query = "
    SELECT 
        p.program_name,
        p.graduation_fee,
        p.extra_ticket_count,
        p.extra_ticket_fee,
        p.total_amount,
        d.extraTicketFee AS default_ticket_price
    FROM payment_records p
    LEFT JOIN data_tables d ON p.program_name = d.programName
    WHERE p.student_id = ?
    ORDER BY p.payment_date DESC
    LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Calculate ticket price
// if ($data['extra_ticket_count'] > 0) {
//     $data['ticket_price'] = $data['extra_ticket_fee'] / $data['extra_ticket_count'];
// } else {
//     $data['ticket_price'] = $data['default_ticket_price']; // Use default from data_tables
// }

echo json_encode($data);
