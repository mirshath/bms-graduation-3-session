<?php
// session_start();
// include("../database/connection.php");

// if (!isset($_SESSION['admin_id'])) {
//     echo "Unauthorized";
//     exit();
// }

// if (isset($_POST['id']) && isset($_POST['dob'])) {
//     $id = intval($_POST['id']);
//     $dob = $_POST['dob'];

//     $stmt = $conn->prepare("UPDATE old_student_db SET DOB=? WHERE id=?");
//     $stmt->bind_param("si", $dob, $id);

//     if ($stmt->execute()) {
//         echo "DOB updated successfully";
//     } else {
//         echo "Failed to update DOB";
//     }
//     $stmt->close();
// } else {
//     echo "Invalid request";
// }



session_start();
include("../database/connection.php");

if (!isset($_SESSION['admin_id'])) {
    echo "Unauthorized";
    exit();
}

if (isset($_POST['id']) && isset($_POST['dob']) && isset($_POST['student_id'])) {
    $id = intval($_POST['id']);
    $dob = $_POST['dob'];
    $student_id = $_POST['student_id'];

    $stmt = $conn->prepare("UPDATE old_student_db SET DOB=?, student_id=? WHERE id=?");
    $stmt->bind_param("ssi", $dob, $student_id, $id);

    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Failed to update record";
    }

    $stmt->close();
} else {
    echo "Invalid request";
}
