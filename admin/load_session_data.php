<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    exit("Unauthorized");
}

include("../database/connection.php");

$session = $_POST['session'] ?? '';

if ($session == '') {
    exit("No session selected");
}

// Fetch data from bulk_data_table joined with registered_students
// $sql = "SELECT b.id,b.*, b.student_id, b.seat_no, b.program_name, r.name_in_full, b.calling_name
//         FROM bulk_data_table b
//         LEFT JOIN registered_students r ON b.student_id = r.student_id
//         WHERE b.session_time = ?
//         ORDER BY CAST(SUBSTRING(b.seat_no, 2) AS UNSIGNED) ASC"; // Order by numeric part of seat_no
// $sql = "SELECT b.id, b.*, b.student_id, b.seat_no, b.program_name, r.name_in_full, b.calling_name
//         FROM bulk_data_table b
//         LEFT JOIN registered_students r ON b.student_id = r.student_id
//         WHERE b.session_time = ?
//         ORDER BY b.seat_no ASC";


// Order by integer value only - extract numeric part from seat_no and order by that integer
// This extracts all digits from seat_no and converts to integer for sorting

$sql = "SELECT b.id, b.*, b.student_id, b.seat_no, b.program_name, r.name_in_full, b.calling_name
        FROM bulk_data_table b
        LEFT JOIN registered_students r ON b.student_id = r.student_id
        WHERE b.session_time = ?
        ORDER BY CAST(SUBSTRING_INDEX(TRIM(b.seat_no), ' ', -1) AS UNSIGNED) ASC";



$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $session);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table class='table table-bordered table-striped' id='sessionTable'>
            <thead class='table-dark'>
                <tr>
                    <th>#</th>
                    <th>Student ID</th>
                   
                    <th>Student Calling Name</th>
                    <th>Seat No</th>
                  
                    <th>Program Name</th>
                </tr>
            </thead>
            <tbody>";

    $sl = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$sl}</td>
                <td>{$row['student_id']}</td>
              
                <td>{$row['calling_name']}</td>
                <td>{$row['seat_no']}</td>
              
                <td>{$row['program_name']}</td>
              </tr>";
        $sl++;
    }

    echo "</tbody></table>";
} else {
    echo "<p class='text-warning'>No records found for session: {$session}</p>";
}
