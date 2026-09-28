<?php
// Disable error display and only return JSON
error_reporting(0);
ini_set('display_errors', 0);

session_start();
include("../database/connection.php");

// Set JSON header at the very beginning
header('Content-Type: application/json');

// Check connection
if (!$conn) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed'
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentID = isset($_POST['studentID']) ? trim($_POST['studentID']) : '';
    
    if (empty($studentID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Student ID is required'
        ]);
        exit();
    }
    
    // First check if student exists
    $checkQuery = "SELECT student_id, attend FROM registered_students WHERE student_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);

    if (!$checkStmt) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database query error'
        ]);
        exit();
    }
    
    mysqli_stmt_bind_param($checkStmt, "s", $studentID);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Student not found'
        ]);
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        exit();
    }

    mysqli_stmt_bind_result($checkStmt, $res_student_id, $res_attend);
    mysqli_stmt_fetch($checkStmt);

    // Check if already attended
    if ($res_attend === 'attended') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Student has already marked attendance'
        ]);
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        exit();
    }

    mysqli_stmt_close($checkStmt);
    
    // Update attendance status
    $updateQuery = "UPDATE registered_students SET attend = 'attended' WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    
    if (!$stmt) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database update error'
        ]);
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "s", $studentID);
    
    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Attendance marked successfully!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to mark attendance'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database execution error'
        ]);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
exit();
?>