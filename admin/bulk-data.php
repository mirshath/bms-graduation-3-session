<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");
?>

<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>
            <div class="container-fluid mt-4">

                <h4 class="mb-4 text-danger text-center">Bulk Data Upload for Seat Allocations</h4>

                <!-- Upload Form Card -->
                <div class="card w-75 mb-4 container shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <!-- Left side: Upload Form -->
                            <div class="col-md-8 border-end">
                                <form id="bulkUploadForm" action="upload_bulk_data.php" method="POST" enctype="multipart/form-data">

                                    <div class="form-group">
                                        <label><strong>Select Program:</strong> <span class="text-danger">*</span></label>
                                        <select name="program_name" class="form-control" id="programSelect" required>
                                            <option value="">-- Select Program --</option>
                                            <?php
                                            // Fetch programName and session from data_tables
                                            $query = "SELECT programName, session FROM data_tables ORDER BY programName ASC";
                                            $result = mysqli_query($conn, $query);

                                            $programSessions = []; // For JS mapping

                                            if (mysqli_num_rows($result) > 0) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    $program = htmlspecialchars($row['programName']);
                                                    $session = htmlspecialchars($row['session']);
                                                    echo '<option value="' . $program . '">' . $program . '</option>';

                                                    // Save mapping for JS
                                                    $programSessions[$program] = $session;
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group mt-2">
                                        <label><strong>Session:</strong></label>
                                        <input type="text" name="session_time" id="programSession" class="form-control" placeholder="Session will appear here" readonly>
                                    </div>


                                    <div class="form-group mt-3">
                                        <label><strong>Upload CSV File:</strong> <span class="text-danger">*</span></label>
                                        <input type="file" name="bulk_file" class="form-control" accept=".csv" required>
                                        <small class="text-muted">Only .csv files allowed (columns: student_id, seat_no, student_result, calling_name)</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary mt-3">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                </form>
                            </div>

                            <!-- Right side: Instructions + Download -->
                            <div class="col-md-4">
                                <h6><i class="fas fa-info-circle text-primary"></i> Instructions</h6>
                                <ol class="mt-2 small">
                                    <li>Download the sample CSV file.</li>
                                    <li>Fill or update student data (<code>student_id</code>, <code>seat_no</code>, <code>student_result</code>, <code>calling_name</code>).</li>
                                    <li>Upload the completed CSV file using the form.</li>
                                </ol>

                                <hr>

                                <p class="small mb-2">Sample CSV file:</p>
                                <a href="./bulk-data-excel-final.csv" class="btn btn-success btn-sm mt-2" download>
                                    <i class="fas fa-download"></i> Download Sample CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- /container-fluid -->
        </div> <!-- /content -->
    </div> <!-- /content-wrapper -->
</div> <!-- /wrapper -->


<!-- ===================== -->
<!-- REQUIRED JS & CSS -->
<!-- ===================== -->

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery -->
<script src="vendor/jquery/jquery.min.js"></script>

<!-- Bootstrap Bundle -->
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#programSelect').select2({
            placeholder: "Select a program",
            allowClear: true,
            width: '100%'
        });

        // Mapping of programs to sessions
        var programSessions = <?php echo json_encode($programSessions); ?>;

        // Show session when program changes
        $('#programSelect').on('change', function() {
            var selectedProgram = $(this).val();
            if (selectedProgram && programSessions[selectedProgram]) {
                $('#programSession').val(programSessions[selectedProgram]);
            } else {
                $('#programSession').val('');
            }
        });
    });
</script>