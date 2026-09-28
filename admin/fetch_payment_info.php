<?php
// include("../database/connection.php");

// $student_id = $_POST['student_id'];

// $query = $conn->prepare("SELECT * FROM payment_records WHERE student_id = ?");
// $query->bind_param("s", $student_id);
// $query->execute();
// $result = $query->get_result();

// if ($row = $result->fetch_assoc()) {
//     echo json_encode([
//         "success" => true,
//         "data" => $row
//     ]);
// } else {
//     echo json_encode(["success" => false]);
// }





include("../database/connection.php");

$student_id = $_POST['student_id'];

// Fetch payment info
$paymentQuery = $conn->prepare("SELECT * FROM payment_records WHERE student_id = ?");
$paymentQuery->bind_param("s", $student_id);
$paymentQuery->execute();
$paymentResult = $paymentQuery->get_result();

if ($paymentRow = $paymentResult->fetch_assoc()) {

    // Fetch student name from separate table
    $studentQuery = $conn->prepare("SELECT * FROM registered_students WHERE student_id = ?");
    $studentQuery->bind_param("s", $student_id);
    $studentQuery->execute();
    $studentResult = $studentQuery->get_result();
    $studentRow = $studentResult->fetch_assoc();

    // Add student_name to payment info
    $paymentRow['student_name'] = $studentRow ? $studentRow['name_in_full'] : 'Unknown';

    echo json_encode([
        "success" => true,
        "data" => $paymentRow
    ]);
} else {
    echo json_encode(["success" => false]);
}
