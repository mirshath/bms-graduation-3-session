<?php
session_start();

include("../database/connection.php");

// Fetch attended users (where attend = 'attended')
$attendedSql = "SELECT * FROM registered_students WHERE attend = 'attended'";
$attendedResult = $conn->query($attendedSql);

// Prepare the response
$response = "";

if ($attendedResult->num_rows > 0) {
    $response .= "<table class='table table-bordered table-striped' id='attendedStudentsTable'>";
    $response .= "<thead>
                    <tr>
                        <th>#</th> <!-- Added column for row count -->
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Date of Birth</th>
                        <th>Programme</th>
                        <th>Status</th>
                    </tr>
                  </thead>";
    $response .= "<tbody>";

    // Initialize a counter for row numbers
    $rowNumber = 1;

    while ($attendedRow = $attendedResult->fetch_assoc()) {
        $attendStatus = "<span class='badge bg-success'>Attended</span>";
        $response .= "<tr>
                        <td>" . $rowNumber . "</td> <!-- Display row number -->
                        <td>" . $attendedRow['student_id'] . "</td>
                         <td>" . $attendedRow['name_in_full'] . "</td>
                        <td>" . $attendedRow['dob'] . "</td>
                        <td>" . $attendedRow['program_name'] . "</td>
                        <td>$attendStatus</td>
                      </tr>";
        $rowNumber++; // Increment the row number
    }
    $response .= "</tbody></table>";
} else {
    $response = "
    <div class='alert alert-info d-flex align-items-center' role='alert'>
        <i class='fas fa-info-circle' style='font-size: 24px; margin-right: 10px;'></i>
        <div>No attended users found.</div>
    </div>
    ";
}

// Close the database connection
$conn->close();

// Return the response
echo $response;
