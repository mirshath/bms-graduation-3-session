<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");
?>

<!-- Page Wrapper -->
<div id="wrapper">
    <?php include("nav.php"); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <div class="p-3">
                <div class="d-sm-flex align-items-center justify-content-between mb-4text-center">
                    <h4 class="h4 mb-0 text-gray-800 ">DOB Updates</h4>
                </div>
            </div>

            <div class="container-fluid">
                <div class="card shadow mb-4" style="font-size: 13px;">
                    <div class="card-header d-flex align-items-center" style="height: 60px;">
                        <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                            <i class="fas fa-list"></i>
                        </span> &nbsp;&nbsp;&nbsp;&nbsp;
                        <h6 class="mb-0">DOB Update Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="display:none;">Student ID</th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>DOB <br> (yyyy-mm-dd)</th>
                                        <th>Given Email</th>
                                        <th>Program</th>
                                        <th>Coursefee Status</th>
                                        <th>Registered / Not</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $counter = 1;
                                    $query = "SELECT * FROM old_student_db ORDER BY id DESC";
                                    $result = mysqli_query($conn, $query);

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $dobFormatted = date('Y-m-d', strtotime($row['DOB']));
                                            echo "<tr>";
                                            echo "<td>" . $counter++ . "</td>";
                                            echo "<td style='display:none;'>" . $row['student_id'] . "</td>";
                                            echo "<td><input type='text' class='student-id-input form-control' style='width:120px;' value='" . $row['student_id'] . "' data-id='" . $row['id'] . "'></td>";

                                            echo "<td>" . $row['name'] . "</td>";
                                            echo "<td><input type='text' class='dob-input form-control' style='width:120px;' value='" . $dobFormatted . "' data-id='" . $row['id'] . "' placeholder='mm/dd/yyyy'></td>";
                                            echo "<td>" . $row['given_email'] . "</td>";
                                            echo "<td>" . $row['program'] . "</td>";
                                            echo "<td>" . $row['payment_status'] . "</td>";
                                            echo "<td>" . $row['status'] . "</td>";
                                            echo "<td><button class='btn btn-primary update-btn' data-id='" . $row['id'] . "'>Update</button></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='9'>No records found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Page level plugins -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">

<!-- Custom Scripts -->
<script>
    $(document).ready(function() {
        // Initialize DataTable once
        $('#dataTable').DataTable({
            "pageLength": 200,
            "order": [
                [1, "asc"]
            ],
            "language": {
                "search": "Search:"
            }
        });

        // AJAX update button
        // $(document).on('click', '.update-btn', function() {
        //     let studentId = $(this).data('id');
        //     let newDOB = $(this).closest('tr').find('.dob-input').val();

        //     if (newDOB === '') {
        //         alert('DOB cannot be empty');
        //         return;
        //     }

        //     $.ajax({
        //         url: 'update_dob.php',
        //         type: 'POST',
        //         data: {
        //             id: studentId,
        //             dob: newDOB
        //         },
        //         success: function(response) {
        //             alert(response);
        //         },
        //         error: function() {
        //             alert('Error updating DOB');
        //         }
        //     });
        // });


        $(document).on('click', '.update-btn', function() {
            let rowId = $(this).data('id');
            let studentId = $(this).closest('tr').find('.student-id-input').val();
            let dob = $(this).closest('tr').find('.dob-input').val();

            if (studentId === '' || dob === '') {
                alert('Student ID and DOB cannot be empty');
                return;
            }

            $.ajax({
                url: 'update_dob.php',
                type: 'POST',
                data: {
                    id: rowId,
                    student_id: studentId,
                    dob: dob
                },
                success: function(response) {
                    alert(response);
                },
                error: function() {
                    alert('Error updating record');
                }
            });
        });

    });
</script>

</body>

</html>