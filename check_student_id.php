<?php
// check_student_id.php
include('./database/connection.php'); // Include your database connection file

// Check if student_id is passed in the request
if (isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // Query to check if the student ID exists
    $checkQuery = "SELECT * FROM registered_students WHERE student_id = '$student_id'";
    $result = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        echo 'exists'; // Student ID already exists
    } else {
        echo 'not_exists'; // Student ID is not found
    }
}
