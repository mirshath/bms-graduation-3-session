<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    exit("Unauthorized");
}

include("../database/connection.php");

$session = trim($_POST['session'] ?? '');

if ($session == '') {
    exit("No session selected");
}

// Students are matched to a session through their program:
// bulk_data_table.program_name -> data_tables.programName -> data_tables.session
$sql = "SELECT b.student_id, b.seat_no, b.program_name, b.calling_name, r.name_in_full
        FROM bulk_data_table b
        INNER JOIN data_tables d ON TRIM(b.program_name) = TRIM(d.programName)
        LEFT JOIN registered_students r ON b.student_id = r.student_id
        WHERE d.session = ?
        ORDER BY LEFT(TRIM(b.seat_no), 1) ASC,
                 CAST(SUBSTRING_INDEX(TRIM(b.seat_no), ' ', -1) AS UNSIGNED) ASC";

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
        // Fall back to full name if calling name is empty
        $name = !empty($row['calling_name']) ? $row['calling_name'] : $row['name_in_full'];

        echo "<tr>
                <td>{$sl}</td>
                <td>" . htmlspecialchars($row['student_id']) . "</td>
                <td>" . htmlspecialchars($name ?? '') . "</td>
                <td>" . htmlspecialchars($row['seat_no']) . "</td>
                <td>" . htmlspecialchars($row['program_name']) . "</td>
              </tr>";
        $sl++;
    }

    echo "</tbody></table>";
} else {
    echo "<p class='text-warning'>No records found for session: " . htmlspecialchars($session) . "</p>";
}
