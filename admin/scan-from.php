<?php
session_start();

// Check if the admin is logged in by checking session variable
if (!isset($_SESSION['admin_id'])) {
    // Redirect to the login page if not logged in
    header("Location: login"); // Change 'login.php' to your actual login page URL
    exit();
}

include("../database/connection.php");
include("includes/header.php");

// Fetch summary data
// Fetch summary data including extra tickets sold
$summaryQuery = $conn->query("
    SELECT 
        COUNT(*) AS total_payments,
        SUM(graduation_fee) AS total_graduation_fee,
        SUM(extra_ticket_fee) AS total_extra_ticket_fee,
        SUM(total_amount) AS total_collected,
        SUM(extra_ticket_count) AS total_extra_tickets_sold
    FROM payment_records
");
$summary = $summaryQuery->fetch_assoc();

?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

<style>
    .archivo {
        font-family: "Archivo", sans-serif;

    }
</style>
<!-- Page Wrapper -->
<div id="wrapper">
    <!-- Sidebar -->
    <?php include("nav.php"); ?>
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
            <!-- Topbar -->
            <?php include("includes/topnav.php"); ?>

            <!-- Begin Page Content -->
            <div class="">
                <!-- Page Heading -->
                <!-- Add form // create forms -->

                <div class="container shadow p-5 my-4">
                    <div class="row g-4">

                        <!-- Total Payments -->
                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm gradient-card" style="background: linear-gradient(135deg, #e0f7fa, #ffffff); border-radius: 12px;">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Total Payments</h6>
                                    <h3 class="fw-bold mb-0"><?php echo $summary['total_payments'] ?? 0; ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Graduation Fees Collected -->
                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm gradient-card" style="background: linear-gradient(135deg, #e8f5e9, #ffffff); border-radius: 12px;">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Graduation Fees Collected</h6>
                                    <h3 class="fw-bold mb-0">LKR <?php echo number_format($summary['total_graduation_fee'] ?? 0, 2); ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Extra Tickets Sold -->
                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm gradient-card" style="background: linear-gradient(135deg, #fff3e0, #ffffff); border-radius: 12px;">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Extra Tickets Sold</h6>
                                    <h3 class="fw-bold mb-0"><?php echo $summary['total_extra_tickets_sold'] ?? 0; ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Extra Ticket Fees Collected -->
                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm gradient-card" style="background: linear-gradient(135deg, #f3e5f5, #ffffff); border-radius: 12px;">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Extra Ticket Fees Collected</h6>
                                    <h3 class="fw-bold mb-0">LKR <?php echo number_format($summary['total_extra_ticket_fee'] ?? 0, 2); ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Total Amount Collected -->
                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm gradient-card" style="background: linear-gradient(135deg, #ffebee, #ffffff); border-radius: 12px;">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Total Amount Collected</h6>
                                    <h3 class="fw-bold mb-0">LKR <?php echo number_format($summary['total_collected'] ?? 0, 2); ?></h3>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row mb-5" style="margin-top: -30px;">
                    <div class="col-md-12">
                        <div class="car">
                            <div class="card-header d-flex align-items-center" style="height: 60px;">
                            </div>
                            <div class="card-body">


                                <form class="" id="studentIDForm" autocomplete="off">
                                    <div class="mb-4" ">
                                        <h1 class=" mb-0 me-2 text-center text-gray-900 archivo" style="font-size: 45px;">SCAN QR FOR PAYMENT</h1>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-center"> <!-- Centering div -->
                                            <input type="text" class="form-control w-75 p-4 fw-bolder text-center" style="font-size: 25px;" id="studentID" name="studentID" required>
                                        </div>
                                    </div>

                                    <!-- <button type="button" class="btn btn-primary w-50 p-3" onclick="checkStudentID()">Check Student ID</button> -->
                                    <div class="d-flex justify-content-center"> <!-- Centering div -->
                                        <button type="button" class="btn btn-primary w-50 p-2" onclick="checkStudentID()">Scan for payment</button>
                                    </div>
                                </form>

                                <!-- Add this script below your existing JavaScript -->
                                <script>
                                    // Trigger checkStudentID function when Enter key is pressed in the input field
                                    $('#studentID').on('keypress', function(event) {
                                        if (event.which === 13) { // 13 is the Enter key
                                            event.preventDefault(); // Prevent the default form submission
                                            checkStudentID(); // Call the function
                                        }
                                    });
                                </script>

                                <!-- Result Div -->
                                <div id="result"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

<script>
    // Function to check student ID
    function checkStudentID() {
        var student_id = $('#studentID').val();

        $.ajax({
            url: 'scan-from1.php',
            method: 'POST',
            data: {
                student_id: student_id
            },
            success: function(response) {
                $('#result').html(response);
                // After updating attendance, fetch attended users in real-time

            }
        });
    }
    // Function to fetch attended users


    // Fetch attended users on page load
    $(document).ready(function() {
        $('#studentID').focus();
        fetchAttendedUsers();
    });
</script>


<!-- -------------------------------------------------------------------------  -->
<!-- modal section here  -->
<!-- -------------------------------------------------------------------------  -->

<style>
    .margin_btm {
        margin-bottom: 0px;
    }

    .gradient-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .gradient-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
</style>

<!-- Bootstrap Modal for Error Messages -->
<div class="modal fade margin_btm" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel">
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-heade">
                <!-- <h5 class="modal-title" id="errorModalLabel">Error</h5> -->
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-bod" id="modalErrorMessage">
                <!-- The error message will be inserted here dynamically -->
            </div>
            <div class="modal-foote">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
            </div>
        </div>
    </div>
</div>


<!-- -------------------------------------------------------------------------  -->
<!-- -------------------------------------------------------------------------  -->


<!-- Page level plugins -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">

<!-- Page level custom scripts -->
<script src="js/demo/datatables-demo.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

</body>

</html>