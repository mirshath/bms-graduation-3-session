<?php
include("../database/connection.php");

$student_id = $_POST['student_id'] ?? '';

if (empty($student_id)) {
    echo "<div class='alert alert-danger text-center'>Student ID is required.</div>";
    exit;
}

// 1️⃣ Check in registered_students
$stmt = $conn->prepare("SELECT name_in_full, invitation_collected FROM registered_students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<div class='alert alert-danger text-center'>Student ID <strong>$student_id</strong> not found in registered students.</div>";
    exit;
}

$row = $res->fetch_assoc();
$status = $row['invitation_collected'] ?? '';
$name_registered = htmlspecialchars($row['name_in_full']);

// Already collected?
if ($status === 'collected') {
    echo "<div class='alert alert-warning text-center'>
            Invitation already collected for <strong>$name_registered</strong> (ID: $student_id)
          </div>";
    exit;
}

// 2️⃣ Get details from old_student_db
$stmt2 = $conn->prepare("SELECT * FROM old_student_db WHERE student_id = ?");
$stmt2->bind_param("s", $student_id);
$stmt2->execute();
$res2 = $stmt2->get_result();

if ($res2->num_rows === 0) {
    echo "<div class='alert alert-danger text-center'>Student ID <strong>$student_id</strong> not found in old student records.</div>";
    exit;
}

$row2 = $res2->fetch_assoc();
$old_name = htmlspecialchars($row2['name']);
$old_in_no = $row2['in_no'] ?? 'N/A';
$program = htmlspecialchars($row2['program'] ?? 'N/A');

// ✅ Modal HTML
echo "
<div class='text-center'>
    <h5 class='text-success mb-2'>Student: <strong>$old_name</strong></h5>
    <p>ID: <strong>$student_id</strong></p>
    <p>Program: <strong>$program</strong></p>
    <p>Invitation Number: <strong>$old_in_no</strong></p>
    <hr>
    <button class='btn btn-success btn-lg me-3' onclick='collectInvitation(\"$student_id\")'>Mark as Collected</button>
    <button class='btn btn-secondary btn-lg' data-bs-dismiss='modal'>Cancel</button>
</div>
";
