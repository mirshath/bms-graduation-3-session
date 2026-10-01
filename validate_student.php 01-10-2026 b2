<?php

// --------- new - -------

// validate_student.php
include('./database/connection.php'); // Include your database connection file

header('Content-Type: application/json'); // Return JSON

if (isset($_POST['student_id']) && isset($_POST['dob'])) {
    $student_id = $_POST['student_id'];
    $dob = $_POST['dob'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM old_student_db WHERE student_id = ? AND DOB = ?");
    $stmt->bind_param("ss", $student_id, $dob);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        echo json_encode([
            'status' => 'valid',
            'student_id' => $student['student_id'],
            'name' => $student['name'],
            'program_name' => $student['program'],
            'mobile_number' => $student['mobile_no'],
            'payment_status' => $student['payment_status'],
            'given_email' => $student['given_email'],
            'crsfee_payment_status' => $student['payment_status'],
            'dob' => $student['DOB']
        ]);
    } else {
        echo json_encode(['status' => 'invalid']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'invalid']);
}

$conn->close();
