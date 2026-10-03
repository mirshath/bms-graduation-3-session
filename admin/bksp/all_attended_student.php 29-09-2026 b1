<?php
session_start();
// Check if the admin is logged in by checking session variable
if (!isset($_SESSION['admin_id'])) {
    // Redirect to the login page if not logged in
    header("Location: login");
    exit();
}

include("../database/connection.php");
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
                    <h4 class="h4 mb-0 text-gray-800">All Attended Students</h4>
                </div>
            </div>

            <div class="container-fluid">
                <div class="card shadow mb-4" style="font-size: 13px;">
                    <div class="card-header d-flex align-items-center" style="height: 60px;">
                        <span class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                            <i class="fas fa-check-circle"></i>
                        </span> &nbsp;&nbsp;&nbsp;&nbsp;
                        <h6 class="mb-0">All Attended Students</h6>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="sessionFilter" class="font-weight-bold">
                                    <i class="fas fa-filter"></i> Filter by Session:
                                </label>
                                <select id="sessionFilter" class="form-control form-control-sm select2">
                                    <option value="">All Sessions</option>
                                    <?php
                                    // Get unique sessions for filter
                                    try {
                                        $session_query = "SELECT DISTINCT dt.session 
                                                          FROM data_tables dt 
                                                          WHERE dt.session IS NOT NULL AND dt.session != '' 
                                                          ORDER BY dt.session ASC";
                                        $session_result = mysqli_query($conn, $session_query);

                                        if ($session_result && mysqli_num_rows($session_result) > 0) {
                                            while ($session_row = mysqli_fetch_assoc($session_result)) {
                                                $session = htmlspecialchars($session_row['session'], ENT_QUOTES, 'UTF-8');
                                                $session_display = ucfirst(strtolower($session));
                                                echo "<option value='" . $session . "'>" . $session_display . "</option>";
                                            }
                                        }
                                    } catch (Exception $e) {
                                        error_log("Session filter error: " . $e->getMessage());
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="programFilter" class="font-weight-bold">
                                    <i class="fas fa-filter"></i> Filter by Program:
                                </label>
                                <select id="programFilter" class="form-control form-control-sm select2">
                                    <option value="">All Programs</option>
                                    <?php
                                    // Get unique programs for filter (include full name, with batch, as before)
                                    try {
                                        $program_query = "SELECT DISTINCT rs.program_name 
                                                          FROM registered_students rs 
                                                          WHERE rs.program_name IS NOT NULL AND rs.program_name != '' 
                                                          ORDER BY rs.program_name ASC";
                                        $program_result = mysqli_query($conn, $program_query);

                                        if ($program_result && mysqli_num_rows($program_result) > 0) {
                                            while ($program_row = mysqli_fetch_assoc($program_result)) {
                                                $program = htmlspecialchars($program_row['program_name'], ENT_QUOTES, 'UTF-8');
                                                echo "<option value='" . $program . "'>" . $program . "</option>";
                                            }
                                        }
                                    } catch (Exception $e) {
                                        error_log("Program filter error: " . $e->getMessage());
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="programMainFilter" class="font-weight-bold">
                                    <i class="fas fa-filter"></i> Program (Before "-"):
                                </label>
                                <select id="programMainFilter" class="form-control form-control-sm select2">
                                    <option value="">All Programs (Before "-")</option>
                                    <?php
                                    // Collect all possible program_name
                                    $main_program_names = [];
                                    $base_query = "SELECT DISTINCT rs.program_name 
                                                   FROM registered_students rs
                                                   WHERE rs.program_name IS NOT NULL AND rs.program_name != ''
                                                   ORDER BY rs.program_name ASC";
                                    $base_result = mysqli_query($conn, $base_query);
                                    if ($base_result && mysqli_num_rows($base_result) > 0) {
                                        while ($r = mysqli_fetch_assoc($base_result)) {
                                            $full = trim($r['program_name']);
                                            // Want the substring before first "-" (with spaces trimmed)
                                            $main = $full;
                                            if (strpos($full, '-') !== false) {
                                                $main = trim(substr($full, 0, strpos($full, '-')));
                                            }
                                            if (!empty($main)) {
                                                $main_program_names[$main] = true;
                                            }
                                        }
                                        foreach (array_keys($main_program_names) as $main_prog) {
                                            $main_prog_h = htmlspecialchars($main_prog, ENT_QUOTES, 'UTF-8');
                                            echo "<option value=\"{$main_prog_h}\">{$main_prog_h}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="attendanceFilter" class="font-weight-bold">
                                    <i class="fas fa-filter"></i> Filter by Attendance Status:
                                </label>
                                <select id="attendanceFilter" class="form-control form-control-sm select2">
                                    <option value="">All Status</option>
                                    <option value="attended">Attended</option>
                                    <option value="N/A">N/A</option>
                                </select>
                                <div class="d-flex align-items-end mt-2">
                                    <button id="resetFilter" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-redo"></i> Reset Filter
                                    </button>
                                    <span id="filterInfo" class="ml-3 text-muted" style="line-height: 31px; font-size: 12px;"></span>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID</th>
                                        <th>Full Name</th>
                                        <th>IN Number</th>
                                        <th>Seat No</th>
                                        <th>Phone</th>
                                        <th>Programs</th>
                                        <th>Session</th>
                                        <th>Attendance Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $counter = 1; // Initialize counter
                                    // Query to fetch all students with seat number and session from bulk_data_table and data_tables
                                    $query = "SELECT rs.*, bdt.seat_no, COALESCE(dt.session, 'N/A') as session_name,
                                              CASE 
                                                  WHEN rs.attend IS NULL OR rs.attend = '' THEN 'N/A'
                                                  ELSE rs.attend
                                              END as attendance_status
                                              FROM registered_students rs 
                                              LEFT JOIN bulk_data_table bdt ON rs.student_id = bdt.student_id 
                                              LEFT JOIN data_tables dt ON rs.program_name = dt.programName
                                              ORDER BY rs.id DESC";
                                    $result = mysqli_query($conn, $query);

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $counter++ . "</td>";
                                            echo "<td>" . htmlspecialchars($row['student_id'], ENT_QUOTES, 'UTF-8') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['name_in_full'], ENT_QUOTES, 'UTF-8') . "</td>";
                                            echo "<td>" . (!empty($row['in_no']) ? htmlspecialchars($row['in_no'], ENT_QUOTES, 'UTF-8') : 'N/A') . "</td>";
                                            echo "<td>" . (!empty($row['seat_no']) ? htmlspecialchars($row['seat_no'], ENT_QUOTES, 'UTF-8') : 'N/A') . "</td>";
                                            echo "<td>" . (!empty($row['phone_no']) ? htmlspecialchars($row['phone_no'], ENT_QUOTES, 'UTF-8') : 'N/A') . "</td>";
                                            echo "<td>" . (!empty($row['program_name']) ? htmlspecialchars($row['program_name'], ENT_QUOTES, 'UTF-8') : 'N/A') . "</td>";
                                            $session_display = !empty($row['session_name']) && $row['session_name'] != 'N/A' ? ucfirst(strtolower($row['session_name'])) : 'N/A';
                                            echo "<td>" . htmlspecialchars($session_display, ENT_QUOTES, 'UTF-8') . "</td>";
                                            $attendance_status = $row['attendance_status'];
                                            $badge_class = ($attendance_status == 'attended' || $attendance_status == 'Registered') ? 'bg-success' : 'bg-secondary';
                                            echo "<td><span class='badge " . $badge_class . "'>" . htmlspecialchars($attendance_status, ENT_QUOTES, 'UTF-8') . "</span></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='9' class='text-center text-muted'>No students found.</td></tr>";
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

<!-- Page level plugins -->
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">

<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

<!-- DataTables Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- JSZip for Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- PDFMake for PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<style>
    .dt-buttons {
        margin-bottom: 15px;
    }

    .dt-button {
        margin-right: 5px;
        border-radius: 5px;
        padding: 8px 15px;
        font-size: 13px;
    }

    /* Make Select2 match .form-control-sm height */
    .select2-container .select2-selection--single {
        height: 31px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 31px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 31px;
    }
</style>

<script>
    $(document).ready(function() {
        try {
            // Initialize Select2 on filter dropdowns
            $('#sessionFilter').select2({
                placeholder: 'All Sessions',
                allowClear: true,
                width: '100%'
            });

            $('#programFilter').select2({
                placeholder: 'All Programs',
                allowClear: true,
                width: '100%'
            });

            $('#programMainFilter').select2({
                placeholder: 'All Programs (Before "-")',
                allowClear: true,
                width: '100%'
            });

            $('#attendanceFilter').select2({
                placeholder: 'All Status',
                allowClear: true,
                width: '100%'
            });

            var table = $('#dataTable').DataTable({
                "pageLength": 200,
                "order": [
                    [1, "asc"]
                ],
                "dom": 'Bfrtip',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'All Attended Students',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        title: 'All Attended Students',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function(doc) {
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;
                            doc.styles.tableHeader.fillColor = '#28a745';
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-info btn-sm',
                        title: 'All Attended Students',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-secondary btn-sm',
                        title: 'All Attended Students',
                        exportOptions: {
                            columns: ':visible'
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
                            columns: ':visible'
                        }
                    }
                ]
            });

            // Helper to extract program name before first "-"
            function getBeforeHyphen(program) {
                if(typeof program !== 'string') return program;
                var idx = program.indexOf('-');
                if(idx === -1) return program.trim();
                return program.slice(0, idx).trim();
            }

            // Function to update filter info
            function updateFilterInfo() {
                var selectedSession = $('#sessionFilter').val();
                var selectedAttendance = $('#attendanceFilter').val();
                var selectedProgram = $('#programFilter').val();
                var selectedMainProgram = $('#programMainFilter').val();
                var info = table.page.info();
                var filterText = [];

                if (selectedSession !== '') {
                    var sessionDisplay = selectedSession.charAt(0).toUpperCase() + selectedSession.slice(1).toLowerCase();
                    filterText.push('Session: <strong>' + sessionDisplay + '</strong>');
                }
                if (selectedAttendance !== '') {
                    var attendanceText = selectedAttendance === 'attended' ? 'Attended' : 'N/A';
                    filterText.push('Status: <strong>' + attendanceText + '</strong>');
                }
                if (selectedProgram !== '') {
                    filterText.push('Program: <strong>' + selectedProgram + '</strong>');
                }
                if (selectedMainProgram !== '') {
                    filterText.push('Program (Before "-"): <strong>' + selectedMainProgram + '</strong>');
                }

                if (filterText.length > 0) {
                    $('#filterInfo').html('<i class="fas fa-info-circle"></i> Showing <strong>' + info.recordsDisplay + '</strong> student(s) - ' + filterText.join(', '));
                } else {
                    $('#filterInfo').html('');
                }
            }

            // Session Filter Functionality
            $('#sessionFilter').on('change', function() {
                var selectedSession = $(this).val();

                if (selectedSession === '') {
                    table.column(7).search('').draw();
                } else {
                    var sessionDisplay = selectedSession.charAt(0).toUpperCase() + selectedSession.slice(1).toLowerCase();
                    table.column(7).search('^' + $.fn.dataTable.util.escapeRegex(sessionDisplay) + '$', true, false).draw();
                }

                updateFilterInfo();
            });

            // Attendance Status Filter Functionality
            $('#attendanceFilter').on('change', function() {
                var selectedAttendance = $(this).val();

                if (selectedAttendance === '') {
                    table.column(8).search('').draw();
                } else if (selectedAttendance === 'attended') {
                    table.column(8).search('^(attended|Registered)$', true, false).draw();
                } else if (selectedAttendance === 'N/A') {
                    table.column(8).search('^N/A$', true, false).draw();
                }

                updateFilterInfo();
            });

            // Program Filter Functionality
            $('#programFilter').on('change', function() {
                var selectedProgram = $(this).val();

                // Remove main filter selection when "program with batch" filter is used
                $('#programMainFilter').val('').trigger('change.select2');

                if (selectedProgram === '') {
                    table.column(6).search('').draw();
                } else {
                    table.column(6).search('^' + $.fn.dataTable.util.escapeRegex(selectedProgram) + '$', true, false).draw();
                }

                updateFilterInfo();
            });

            // Program Main Filter Functionality (Before Hyphen)
            $('#programMainFilter').on('change', function() {
                var selectedMainProgram = $(this).val();

                // Remove batch filter selection when "main" program filter is used
                $('#programFilter').val('').trigger('change.select2');

                // Remove any previous filter
                $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function (f) {
                    return !f._isMainProgFilter;
                });

                if (selectedMainProgram === '') {
                    table.column(6).search('').draw();
                } else {
                    // Custom search by program name before hyphen
                    var mainProgFilter = function(settings, data, dataIndex){
                        var program = data[6] || '';
                        // If no filter, show all
                        if (selectedMainProgram === '') return true;
                        return getBeforeHyphen(program) === selectedMainProgram;
                    }
                    mainProgFilter._isMainProgFilter = true;
                    $.fn.dataTable.ext.search.push(mainProgFilter);
                    table.draw();
                }

                updateFilterInfo();
            });

            // Reset Filter Button
            $('#resetFilter').on('click', function() {
                $('#sessionFilter').val('').trigger('change');
                $('#attendanceFilter').val('').trigger('change');
                $('#programFilter').val('').trigger('change');
                $('#programMainFilter').val('').trigger('change');
                // Remove ext.search
                $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function (f) {
                    return !f._isMainProgFilter;
                });
                table.columns().search('').draw();
                updateFilterInfo();
            });

            // If user changes the filter in another one, clear the other
            $('#programFilter').on('change', function() {
                if ($(this).val() !== '') {
                    $('#programMainFilter').val('').trigger('change.select2');
                }
            });

            $('#programMainFilter').on('change', function() {
                if ($(this).val() !== '') {
                    $('#programFilter').val('').trigger('change.select2');
                }
            });

            // Update filter info on page change
            table.on('draw', function() {
                updateFilterInfo();
            });

            // Initial filter info update
            updateFilterInfo();

        } catch (e) {
            console.error("DataTable initialization error:", e);
        }
    });
</script>

</body>
</html>