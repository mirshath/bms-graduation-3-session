<?php
include("../database/connection.php");

$student_id = $_POST['student_id'] ?? '';

if (empty($student_id)) {
    echo "Student ID missing!";
    exit;
}

// ✅ Update course fee payment status in both tables
try {
    // Update in registered_students
    $update1 = $conn->prepare("UPDATE registered_students 
                               SET crsfee_payment_status = 'paid' 
                               WHERE student_id = ?");
    $update1->bind_param("s", $student_id);
    $update1->execute();

    // Update in old_student_db
    $update2 = $conn->prepare("UPDATE old_student_db 
                               SET payment_status = 'paid' 
                               WHERE student_id = ?");
    $update2->bind_param("s", $student_id);
    $update2->execute();

    // ✅ Check if at least one table was updated
    if ($update1->affected_rows > 0 || $update2->affected_rows > 0) {
        echo "success";
    } else {
        echo "no change";
    }

    $update1->close();
    $update2->close();
    $conn->close();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
