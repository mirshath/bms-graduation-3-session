<?php
session_start();
include("../database/connection.php");

// Get the student_id from the POST request
$student_id = $_POST['student_id'];

// Check if the student_id is empty
if (empty($student_id)) {
    echo "<script>
            document.getElementById('modalErrorMessage').innerHTML = `
                <div class='alert alert-danger text-center' role='alert' style='margin-bottom: 0px;'>
                    <i class='fas fa-exclamation-circle fa-8x mb-2' style='color:red;'></i>
                    <h5 class='alert-heading'>Error!</h5>
                    <h3 class='fw-bolder'>Student ID cannot be empty!</h3>
                    <hr>
                    <p class='mb-0'>Please enter a valid Student ID to proceed.</p>
                </div>
            `;
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
            setTimeout(function() {
                window.location.reload();
            }, 3000); // Refresh after 3 seconds
        </script>";
    exit; // Stop further execution if student_id is empty
}

// Prepare the SQL query to check if the student_id exists
$sql = "SELECT * FROM registered_students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Student is registered, check if already marked as 'attended'
    $row = $result->fetch_assoc();
    if ($row['attend'] === 'attended') {
        echo "<script>
            document.getElementById('modalErrorMessage').innerHTML = `
                <div class='alert alert-danger text-center' role='alert' style='margin-bottom: 0px;'>
                    <i class='fas fa-exclamation-circle fa-8x mb-2' style='color:red;'></i>
                    <h5 class='alert-heading'>Error!</h5>
                    <h3 class='fw-bolder'>Student has already attended.</h3>
                </div>
            `;
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
            setTimeout(function() {
                window.location.reload();
            }, 3000); // Refresh after 3 seconds
        </script>";
    } else {
        // Update the attend column to 'attended'
        $updateSql = "UPDATE registered_students SET attend = 'attended' WHERE student_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("s", $student_id);
        $updateStmt->execute();

        // Check if the update was successful
        if ($updateStmt->affected_rows > 0) {
            echo "<script>
                document.getElementById('modalErrorMessage').innerHTML = `
                    <div class='alert alert-success text-center' role='alert' style='margin-bottom: 0px;'>
                        <i class='fas fa-check-circle fa-8x mb-2' style='color:green;'></i>
                        <h5 class='alert-heading'>Success!</h5>
                        <h3>Student attendance updated successfully!</h3>
                    </div>
                `;
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
                setTimeout(function() {
                    window.location.reload();
                }, 3000); // Refresh after 3 seconds
            </script>";
        } else {
            echo "<script>
                document.getElementById('modalErrorMessage').innerHTML = `
                    <div class='alert alert-danger text-center' role='alert' style='margin-bottom: 0px;'>
                        <i class='fas fa-exclamation-circle fa-8x mb-2' style='color:red;'></i>
                        <h5 class='alert-heading'>Error!</h5>
                        <h3>Failed to update attendance.</h3>
                    </div>
                `;
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
                setTimeout(function() {
                    window.location.reload();
                }, 3000); // Refresh after 3 seconds
            </script>";
        }

        // Close the update statement
        $updateStmt->close();
    }
} else {
    // Student ID not found
    echo "<script>
        document.getElementById('modalErrorMessage').innerHTML = `
            <div class='alert alert-danger text-center' role='alert' style='margin-bottom: 0px;'>
                <i class='fas fa-exclamation-circle fa-8x mb-2' style='color:red;'></i>
                <h5 class='alert-heading'>Error!</h5>
                <h3 class='fw-bolder'>Student ID not registered in the database!</h3>
            </div>
        `;
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
        setTimeout(function() {
            window.location.reload();
        }, 3000); // Refresh after 3 seconds
    </script>";
}

// Close the statements and the database connection
$stmt->close();
$conn->close();
