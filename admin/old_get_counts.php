<?php
include("../database/connection.php");

// Query 1: Count of all records in old_student_db
$result1 = $conn->query("SELECT COUNT(*) AS total FROM old_student_db");
$count1 = $result1->fetch_assoc()['total'];

// Query 2: Count of registered students in old_student_db
$result2 = $conn->query("SELECT COUNT(*) AS total FROM old_student_db WHERE status = 'registered'");
$count2 = $result2->fetch_assoc()['total'];

// Query 3: Count of all records in registered_students
$result3 = $conn->query("SELECT COUNT(*) AS total FROM registered_students WHERE attend='attended'");
$count3 = $result3->fetch_assoc()['total'];
// Query 3: Count of all records in registered_students science
$result4 = $conn->query("SELECT COUNT(*) AS total FROM registered_students WHERE field_of_study='science'");
$count4 = $result4->fetch_assoc()['total'];
// Query 3: Count of all records in registered_students science
$result5 = $conn->query("SELECT COUNT(*) AS total FROM registered_students WHERE field_of_study='business'");
$count5 = $result5->fetch_assoc()['total'];

// Return counts as JSON
echo json_encode([
    'old_students_total' => $count1,
    'old_students_registered' => $count2,
    'registered_students_total' => $count3,
    'registered_science_total' => $count4,
    'registered_bussiness_total' => $count5,
]);

$conn->close();
?>
