<?php
include("../database/connection.php");

$student_id = $_POST['student_id'] ?? '';

if (empty($student_id)) {
    echo "<div class='alert alert-danger'>Student ID missing.</div>";
    exit;
}

// 1️⃣ Get the correct in_no from old_student_db
$stmt = $conn->prepare("SELECT in_no FROM old_student_db WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Student not found in old student records.</div>";
    exit;
}

$row = $result->fetch_assoc();
$correct_in_no = $row['in_no'];

// 2️⃣ Update registered_students: invitation_collected, updated_at_invitation, in_no
$update = $conn->prepare("
    UPDATE registered_students 
    SET invitation_collected = 'collected', 
        updated_at_invitation = NOW(), 
        in_no = ? 
    WHERE student_id = ?
");
$update->bind_param("is", $correct_in_no, $student_id);

if ($update->execute()) {
    echo "
    <div class='alert alert-success text-center'>
        <i class='fas fa-check-circle fa-3x mb-3 text-success'></i>
        <h4>Invitation marked as <strong>Collected</strong></h4>
        <h5>Student ID: <strong>$student_id</strong></h5>
        <h5>Invitation Number: <strong>$correct_in_no</strong></h5>
    </div>";
} else {
    echo "<div class='alert alert-danger'>Database update failed. Try again.</div>";
}
?>
