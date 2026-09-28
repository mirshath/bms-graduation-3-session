<?php
include("../database/connection.php");

$studentID = trim($_POST['studentID'] ?? '');
if ($studentID === '') {
    echo json_encode(["status" => "error", "message" => "No ID provided"]);
    exit;
}

// Fetch student info and seat number from bulk_data_table
$sql = "
    SELECT rs.student_id, rs.name_in_full, rs.*, rs.attend, bd.seat_no, bd.*
    FROM registered_students rs
    LEFT JOIN bulk_data_table bd ON rs.student_id = bd.student_id
    WHERE rs.student_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Check if student already attended
    if (!empty($row['attend'])) {
        echo json_encode([
            "status" => "attended",
            "data" => [
                "student_id" => $row['student_id'],
                "full_name" => $row['name_in_full'],
                "program_name" => $row['program_name'],
                "attend" => $row['attend'],
                "seat_no" => $row['seat_no'] ?? 'N/A',
                "session" => $row['session_time'] ?? 'N/A',
                "graduation_payment_status" => $row['graduation_payment_status'] ?? 'N/A'
            ]
        ]);
    } else {
        echo json_encode([
            "status" => "success",
            "data" => [
                "student_id" => $row['student_id'],
                "full_name" => $row['name_in_full'],
                "program_name" => $row['program_name'],
                "seat_no" => $row['seat_no'] ?? 'N/A',
                "session" => $row['session_time'] ?? 'N/A',
                "graduation_payment_status" => $row['graduation_payment_status'] ?? 'N/A'
            ]
        ]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Student not found"]);
}

$stmt->close();
$conn->close();
