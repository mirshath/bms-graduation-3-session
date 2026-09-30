<?php
session_start();

// ✅ Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

// ✅ Database connection with error handling
try {
    include("../database/connection.php");

    if (!isset($conn) || !$conn) {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    die("System Error: Unable to connect to database. Please contact administrator.");
}

include("includes/header.php");
?>

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

            <div class="p-3">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h4 class="h4 mb-0 text-gray-800">Old Students Database</h4>
                </div>
            </div>

            <div class="container-fluid">
                <div class="card shadow mb-4" style="font-size: 13px;">
                    <div class="card-header d-flex align-items-center" style="height: 60px;">
                        <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center"
                            style="width: 30px; height: 30px;">
                            <i class="fas fa-list"></i>
                        </span>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <h6 class="mb-0">Old Students Details</h6>
                    </div>
                    <div class="card-body">
                        <!-- ✅ Program Filter Dropdown -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="programFilter" class="font-weight-bold">
                                    <i class="fas fa-filter"></i> Filter by Program:
                                </label>
                                <select id="programFilter" class="form-control form-control-sm">
                                    <option value="">All Programs</option>
                                    <?php
                                    // ✅ Get unique programs for filter
                                    try {
                                        $program_query = "SELECT DISTINCT program FROM old_student_db WHERE program IS NOT NULL AND program != '' ORDER BY program ASC";
                                        $program_result = mysqli_query($conn, $program_query);

                                        if ($program_result && mysqli_num_rows($program_result) > 0) {
                                            while ($program_row = mysqli_fetch_assoc($program_result)) {
                                                $program = htmlspecialchars($program_row['program'], ENT_QUOTES, 'UTF-8');
                                                echo "<option value='" . $program . "'>" . $program . "</option>";
                                            }
                                        }
                                    } catch (Exception $e) {
                                        error_log("Program filter error: " . $e->getMessage());
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="statusFilter" class="font-weight-bold">
                                    <i class="fas fa-filter"></i> Filter by Registration Status:
                                </label>
                                <select id="statusFilter" class="form-control form-control-sm">
                                    <option value="">All Status</option>
                                    <option value="Registered">Registered</option>
                                    <option value="N/A">Not Registered</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="resetFilter" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-redo"></i> Reset Filters
                                </button>
                                <span id="filterInfo" class="ml-3 text-muted" style="line-height: 31px;"></span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Invitation Number</th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Date of Birth</th>
                                        <th>Phone No</th>
                                        <th>Given Email</th>
                                        <th>Program</th>
                                        <th>Course Fee Status</th>
                                        <th>Registered / Not</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $counter = 1;

                                    // ✅ Fetch data from the old_student_db table
                                    $query = "SELECT * FROM old_student_db ORDER BY id DESC";

                                    try {
                                        $result = mysqli_query($conn, $query);

                                        if (!$result) {
                                            throw new Exception(mysqli_error($conn));
                                        }

                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                // ✅ Sanitize all outputs
                                                $student_id = htmlspecialchars($row['student_id'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                                $name = htmlspecialchars($row['name'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                                $dob = htmlspecialchars($row['DOB'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                                $mobile = htmlspecialchars($row['mobile_no'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                                $email = htmlspecialchars($row['given_email'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                                $program = htmlspecialchars($row['program'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                                $in_no = htmlspecialchars($row['in_no'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                                $payment_status = htmlspecialchars($row['payment_status'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                                // $status = htmlspecialchars($row['status'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                                
                                                
                                                 $status = trim($row['status'] ?? '');
                                                $status = ($status === '') ? 'N/A' : htmlspecialchars($status, ENT_QUOTES, 'UTF-8');


                                                echo "<tr>";
                                                echo "<td>" . $counter++ . "</td>";
                                                echo "<td>" . $in_no . "</td>";
                                                echo "<td>" . $student_id . "</td>";
                                                echo "<td>" . $name . "</td>";
                                                echo "<td>" . $dob . "</td>";
                                                echo "<td>" . $mobile . "</td>";
                                                echo "<td>" . $email . "</td>";
                                                echo "<td>" . $program . "</td>";

                                                // ✅ Color-coded payment status
                                                if ($payment_status == 'Paid' || $payment_status == 'paid') {
                                                    echo "<td><span class='badge badge-success'>" . $payment_status . "</span></td>";
                                                } elseif ($payment_status == 'Pending' || $payment_status == 'pending') {
                                                    echo "<td><span class='badge badge-warning'>" . $payment_status . "</span></td>";
                                                } else {
                                                    echo "<td>" . $payment_status . "</td>";
                                                }

                                                // ✅ Color-coded registration status
                                                if ($status == 'Registered' || $status == 'registered') {
                                                    echo "<td><span class='badge badge-success'>" . $status . "</span></td>";
                                                } elseif ($status == 'Not Registered' || $status == 'not registered') {
                                                    echo "<td><span class='badge badge-danger'>" . $status . "</span></td>";
                                                } else {
                                                    echo "<td>" . $status . "</td>";
                                                }

                                                echo "</tr>";
                                            }
                                        } else {
                                            // ✅ Fixed colspan to match 9 columns
                                            echo "<tr><td colspan='9' class='text-center text-muted'>No records found</td></tr>";
                                        }
                                    } catch (Exception $e) {
                                        // ✅ Error handling
                                        echo "<tr><td colspan='9' class='text-center text-danger'>";
                                        echo "<i class='fas fa-exclamation-circle'></i> Error loading student records. Please try again later.";
                                        echo "</td></tr>";

                                        error_log("Old Students DB Error: " . $e->getMessage());
                                    }
                                    ?>
                                </tbody>
                            </table>
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

<!-- ✅ DataTables with Buttons Extension -->
<link rel="stylesheet" href="vendor/datatables/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- ✅ DataTables Buttons Extension -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- ✅ Required for Excel Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- ✅ Required for PDF Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<style>
    /* ✅ Style for export buttons */
    .dt-buttons {
        margin-bottom: 15px;
    }

    .dt-button {
        margin-right: 5px;
        border-radius: 5px;
        padding: 8px 15px;
        font-size: 13px;
    }

    #filterInfo {
        font-size: 13px;
        font-weight: 500;
    }

    .badge {
        font-size: 12px;
        padding: 5px 10px;
    }
</style>

<script>
    $(document).ready(function() {
        // ✅ Error handling for DataTable initialization with Export Buttons
        try {
            var table = $('#dataTable').DataTable({
                "pageLength": 200,
                "order": [
                    [1, "asc"]
                ], // ✅ Sort by counter column
                "language": {
                    "search": "Search Student:",
                    "emptyTable": "No student records available",
                    "zeroRecords": "No matching student records found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ students",
                    "infoEmpty": "Showing 0 to 0 of 0 students",
                    "infoFiltered": "(filtered from _MAX_ total students)"
                },
                // ✅ Export Buttons Configuration
                "dom": 'Bfrtip',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Old Students Database',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] // All columns
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        title: 'Old Students Database',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] // All columns
                        },
                        customize: function(doc) {
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;
                            doc.styles.tableHeader.fillColor = '#343a40';
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-info btn-sm',
                        title: 'Old Students Database',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] // All columns
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-secondary btn-sm',
                        title: 'Old Students Database',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] // All columns
                        },
                        customize: function(win) {
                            $(win.document.body).find('table').addClass('display').css('font-size', '12px');
                            $(win.document.body).find('h1').css('text-align', 'center');
                        }
                    },
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn btn-warning btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] // All columns
                        }
                    }
                ]
            });

            // ✅ Function to update filter info
            function updateFilterInfo() {
                var selectedProgram = $('#programFilter').val();
                var selectedStatus = $('#statusFilter').val();
                var info = table.page.info();
                var filterText = '';

                if (selectedProgram !== '' || selectedStatus !== '') {
                    filterText = '<i class="fas fa-info-circle"></i> Showing <strong>' + info.recordsDisplay + '</strong> student(s)';

                    if (selectedProgram !== '') {
                        filterText += ' for <strong>' + selectedProgram + '</strong>';
                    }

                    if (selectedStatus !== '') {
                        filterText += ' with status <strong>' + selectedStatus + '</strong>';
                    }

                    $('#filterInfo').html(filterText);
                } else {
                    $('#filterInfo').html('');
                }
            }

            // ✅ Program Filter Functionality
            $('#programFilter').on('change', function() {
                var selectedProgram = $(this).val();

                if (selectedProgram === '') {
                    table.column(7).search('').draw(); // Column 6 = Program
                } else {
                    table.column(7).search('^' + $.fn.dataTable.util.escapeRegex(selectedProgram) + '$', true, false).draw();
                }

                updateFilterInfo();
            });

            // ✅ Status Filter Functionality
            $('#statusFilter').on('change', function() {
                var selectedStatus = $(this).val();

                if (selectedStatus === '') {
                    table.column(9).search('').draw(); // Column 8 = Status
                } else {
                    table.column(9).search('^' + $.fn.dataTable.util.escapeRegex(selectedStatus) + '$', true, false).draw();
                }

                updateFilterInfo();
            });

            // ✅ Reset Filter Button
            $('#resetFilter').on('click', function() {
                $('#programFilter').val('');
                $('#statusFilter').val('');
                table.column(7).search('').draw();
                table.column(9).search('').draw();
                $('#filterInfo').html('');
            });

            // ✅ Update info on table draw
            table.on('draw', function() {
                updateFilterInfo();
            });

        } catch (e) {
            console.error("DataTable initialization error:", e);
            alert("Error loading table features. Please refresh the page.");
        }
    });
</script>

</body>

</html>