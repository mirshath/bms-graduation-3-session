<?php
session_start();

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");

// Fetch all collected invitations
$collectedList = $conn->query("
    SELECT student_id, in_no, name_in_full, invitation_collected, updated_at_invitation
    FROM registered_students
    WHERE invitation_collected = 'collected'
    ORDER BY updated_at_invitation DESC
");



// Fetch total collected invitations
$totalCollectedResult = $conn->query("SELECT COUNT(*) AS total_collected FROM registered_students WHERE invitation_collected = 'collected'");
$totalCollected = $totalCollectedResult->fetch_assoc()['total_collected'] ?? 0;

// Count only those collected today
$today = date('Y-m-d');
$totalCollectedTodayResult = $conn->query("
    SELECT COUNT(*) AS total_collected_today 
    FROM registered_students 
    WHERE invitation_collected = 'collected'
      AND DATE(updated_at_invitation) = '$today'
");
$totalCollectedToday = $totalCollectedTodayResult->fetch_assoc()['total_collected_today'] ?? 0;



?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    body,
    .archivo {
        font-family: "Archivo", sans-serif;
    }
</style>

<div id="wrapper">
    <?php include("nav.php"); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <div class="container-fluid mt-4">
                <!-- Scanner Section -->
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="border-0 mb-5">
                            <div class="card-body text-center">
                                <h1 class="archivo fw-bold mb-4" style="color: darkblue;">SCAN QR <br><span style="font-size: 25px;">FOR</span> <br> INVITATION COLLECTION</h1>

                                <form id="studentIDForm" autocomplete="off">
                                    <div class="mb-4">
                                        <input type="text" class="form-control text-center fw-bold"
                                            style="font-size: 15px; padding:25px; letter-spacing: 1px;"
                                            id="studentID" name="studentID"
                                            placeholder="Scan or Enter StudentID|InvitationNumber" required>
                                    </div>

                                    <button type="button" class="btn btn-primary w-50 p-2 fw-bold" onclick="checkStudentID()">Check</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collected Invitations Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold text-primary">Collected Invitations</h5>
                        <span class="badge bg-success fs-6">Total Collected: <?= $totalCollected ?></span>
                        <span class="badge bg-danger fs-6">Today Collected: <?= $totalCollectedToday ?></span>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table  table-striped" id="collectedTable" width="100%" cellspacing="0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Invitation Number</th>
                                        <th>Student Name</th>
                                        <th>Status</th>
                                        <th>Collected Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $collectedList->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['student_id']) ?></td>
                                            <td><?= htmlspecialchars($row['in_no']) ?></td>
                                            <td><?= htmlspecialchars($row['name_in_full']) ?></td>
                                            <td><span class="badge bg-success">Collected</span></td>
                                            <td><?= htmlspecialchars($row['updated_at_invitation'] ?? '-') ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div> <!-- /.container-fluid -->
        </div>
    </div>
</div>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center" id="modalMessage"></div>
        </div>
    </div>
</div>


<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="vendor/datatables/dataTables.bootstrap4.min.css">

<script>
    $(document).ready(function() {
        $('#collectedTable').DataTable({
            order: [
                [4, 'desc']
            ],
            pageLength: 200,
        });

        $('#studentID').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                checkStudentID();
            }
        });
    });

   
    function checkStudentID() {
        let student_id = $('#studentID').val().trim();

        if (student_id === "") {
            showModal(`<div class="alert alert-danger">Please enter or scan Student ID.</div>`);
            return;
        }

        $.ajax({
            url: "check_invitation.php",
            type: "POST",
            data: {
                student_id: student_id
            }, // ✅ only student_id is passed
            success: function(response) {
                showModal(response);
                $('#studentID').val('');
            },
            error: function() {
                showModal(`<div class="alert alert-danger">Server error. Try again.</div>`);
            }
        });
    }

   
    function collectInvitation(student_id) {
        $.ajax({
            url: "update_invitation_status.php",
            type: "POST",
            data: {
                student_id: student_id
            },
            success: function(response) {
                showModal(response);
                setTimeout(() => location.reload(), 1500);
            },
            error: function() {
                showModal(`<div class="alert alert-danger">Failed to update. Try again.</div>`);
            }
        });
    }

    function showModal(message) {
        $('#modalMessage').html(message);
        $('#infoModal').modal('show');
    }
</script>