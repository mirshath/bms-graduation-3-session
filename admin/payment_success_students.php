<?php
session_start();

// ✅ Check admin login
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
                    <h4 class="h4 mb-0 text-gray-800">Paid Students - Graduation Payments</h4>
                </div>
            </div>

            <div class="container-fluid">
                <div class="card shadow mb-4" style="font-size: 13px;">
                    <div class="card-header d-flex align-items-center" style="height: 60px;">
                        <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center"
                            style="width: 30px; height: 30px;">
                            <i class="fas fa-receipt"></i>
                        </span>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <h6 class="mb-0">Payment Records of Students</h6>
                    </div>
                    <div class="card-body">
                        <!-- ✅ Program Filter Dropdown -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="programFilter" class="font-weight-bold">
                                    <i class="fas fa-filter"></i> Filter by Program:
                                </label>
                                <select id="programFilter" class="form-control form-control-sm">
                                    <option value="">All Programs</option>
                                    <?php
                                    // ✅ Get unique programs for filter
                                    try {
                                        $program_query = "SELECT DISTINCT program_name FROM payment_records WHERE program_name IS NOT NULL AND program_name != '' ORDER BY program_name ASC";
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
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="resetFilter" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-redo"></i> Reset Filter
                                </button>
                                <span id="filterInfo" class="ml-3 text-muted" style="line-height: 31px;"></span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Program Name</th>
                                        <th>Extra Tickets</th>
                                        <th>Total Amount (Rs.)</th>
                                        <th>Receipt (Click to View)</th>
                                        <th>Payment Date</th>
                                        <th>Payment By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $counter = 1;

                                    // ✅ Query for payment records
                                    $query = "SELECT p.student_id, r.name_in_full, p.program_name, p.extra_ticket_count, 
                                        p.total_amount, p.receipt_number, p.payment_date, p.created_by
                                        FROM payment_records p
                                        LEFT JOIN registered_students r ON p.student_id = r.student_id
                                        ORDER BY p.payment_date DESC";

                                    try {
                                        $result = mysqli_query($conn, $query);

                                        if (!$result) {
                                            throw new Exception(mysqli_error($conn));
                                        }

                                        if (mysqli_num_rows($result) > 0) {
                                            // ✅ Define receipts directory once
                                            $receipts_dir = __DIR__ . "/saved_receipts/";
                                            $receipts_dir_real = realpath($receipts_dir);

                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $student_id = $row['student_id'];
                                                $student_name = $row['name_in_full'] ?? 'N/A';
                                                $receipt_number = $row['receipt_number'];
                                                $program_name = $row['program_name'];
                                                $extra_tickets = $row['extra_ticket_count'];
                                                $total_amount = $row['total_amount'];
                                                $payment_date_db = $row['payment_date'];
                                                $created_by = $row['created_by'] ?? '-';

                                                // ✅ Sanitize values for file operations
                                                $safe_receipt = preg_replace('/[^a-zA-Z0-9\-_]/', '', $receipt_number);
                                                $safe_student_id = preg_replace('/[^a-zA-Z0-9\-_]/', '', $student_id);

                                                echo "<tr>";
                                                echo "<td>" . $counter++ . "</td>";
                                                echo "<td>" . htmlspecialchars($student_id, ENT_QUOTES, 'UTF-8') . "</td>";
                                                echo "<td>" . htmlspecialchars($student_name, ENT_QUOTES, 'UTF-8') . "</td>";
                                                echo "<td>" . htmlspecialchars($program_name, ENT_QUOTES, 'UTF-8') . "</td>";
                                                echo "<td>" . htmlspecialchars($extra_tickets, ENT_QUOTES, 'UTF-8') . "</td>";
                                                echo "<td>" . number_format($total_amount, 2) . "</td>";

                                                // ✅ Search for PDF with proper validation
                                                $pdf_found = false;
                                                $pdf_relative_path = '';

                                                if (!empty($safe_receipt) && !empty($safe_student_id)) {
                                                    $search_pattern = $receipts_dir . "Receipt-" . $safe_receipt . "-" . $safe_student_id . "-*.pdf";
                                                    $matching_files = glob($search_pattern);

                                                    if (!empty($matching_files) && is_array($matching_files)) {
                                                        $pdf_full_path = realpath($matching_files[0]);

                                                        // ✅ Validate file is in correct directory (prevent directory traversal)
                                                        if ($pdf_full_path && $receipts_dir_real && strpos($pdf_full_path, $receipts_dir_real) === 0) {
                                                            // ✅ Verify it's actually a PDF
                                                            if (pathinfo($pdf_full_path, PATHINFO_EXTENSION) === 'pdf' && file_exists($pdf_full_path)) {
                                                                $pdf_filename = basename($pdf_full_path);
                                                                $pdf_relative_path = "saved_receipts/" . $pdf_filename;
                                                                $pdf_found = true;
                                                            }
                                                        }
                                                    }
                                                }

                                                if ($pdf_found) {
                                                    echo "<td>
                                                            <a href='" . htmlspecialchars($pdf_relative_path, ENT_QUOTES, 'UTF-8') . "' 
                                                               target='_blank' 
                                                               style='color:#007bff;text-decoration:underline;font-weight:500;'>
                                                                <i class='fas fa-file-pdf'></i> Receipt-" . htmlspecialchars($safe_receipt, ENT_QUOTES, 'UTF-8') . "
                                                            </a>
                                                          </td>";
                                                } else {
                                                    echo "<td>
                                                            <span class='text-danger'>
                                                                <i class='fas fa-exclamation-triangle'></i> Missing PDF
                                                            </span>
                                                          </td>";
                                                }

                                                // ✅ Format date properly with error handling
                                                $formatted_date = 'Invalid Date';
                                                if (!empty($payment_date_db)) {
                                                    $timestamp = strtotime($payment_date_db);
                                                    if ($timestamp !== false) {
                                                        $formatted_date = date("Y-m-d H:i:s", $timestamp);
                                                    }
                                                }

                                                echo "<td>" . htmlspecialchars($formatted_date, ENT_QUOTES, 'UTF-8') . "</td>";
                                                echo "<td>" . htmlspecialchars($created_by, ENT_QUOTES, 'UTF-8') . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            // ✅ Fixed colspan to match 9 columns
                                            echo "<tr><td colspan='9' class='text-center text-muted'>No payment records found.</td></tr>";
                                        }
                                    } catch (Exception $e) {
                                        // ✅ Error handling for query failures
                                        echo "<tr><td colspan='9' class='text-center text-danger'>";
                                        echo "<i class='fas fa-exclamation-circle'></i> Error loading payment records. Please try again later.";
                                        echo "</td></tr>";

                                        // Log error for admin (optional - add to error log file)
                                        error_log("Payment Records Error: " . $e->getMessage());
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

    /* ✅ Make Select2 match .form-control-sm height */
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
        // ✅ Error handling for DataTable initialization with Export Buttons
        try {
            // ✅ Initialize Select2 on Program Filter
            $('#programFilter').select2({
                placeholder: 'All Programs',
                allowClear: true,
                width: '100%'
            });

            var table = $('#dataTable').DataTable({
                "pageLength": 200,
                "order": [
                    [7, "desc"]
                ], // ✅ Sort by Payment Date (column 7)
                "language": {
                    "search": "Search Payment:",
                    "emptyTable": "No payment records available",
                    "zeroRecords": "No matching records found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ payments",
                    "infoEmpty": "Showing 0 to 0 of 0 payments",
                    "infoFiltered": "(filtered from _MAX_ total payments)"
                },
                "columnDefs": [{
                        "orderable": false,
                        "targets": 6
                    } // Disable sorting on Receipt column
                ],
                // ✅ Export Buttons Configuration
                "dom": 'Bfrtip',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Graduation Payment Records',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 7, 8] // Exclude Receipt column (6)
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        title: 'Graduation Payment Records',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 7, 8] // Exclude Receipt column (6)
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
                        title: 'Graduation Payment Records',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 7, 8] // Exclude Receipt column (6)
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-secondary btn-sm',
                        title: 'Graduation Payment Records',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 7, 8] // Exclude Receipt column (6)
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
                            columns: [0, 1, 2, 3, 4, 5, 7, 8] // Exclude Receipt column (6)
                        }
                    }
                ]
            });

            // ✅ Function to update filter info
            function updateFilterInfo() {
                var selectedProgram = $('#programFilter').val();
                var info = table.page.info();

                if (selectedProgram !== '') {
                    $('#filterInfo').html('<i class="fas fa-info-circle"></i> Showing <strong>' + info.recordsDisplay + '</strong> payment(s) for <strong>' + selectedProgram + '</strong>');
                } else {
                    $('#filterInfo').html('');
                }
            }

            // ✅ Program Filter Functionality
            $('#programFilter').on('change', function() {
                var selectedProgram = $(this).val();

                if (selectedProgram === '') {
                    // Show all records
                    table.column(3).search('').draw();
                } else {
                    // Filter by selected program (column 3 = Program Name)
                    // Using exact match with regex
                    table.column(3).search('^' + $.fn.dataTable.util.escapeRegex(selectedProgram) + '$', true, false).draw();
                }

                updateFilterInfo();
            });

            // ✅ Reset Filter Button
            $('#resetFilter').on('click', function() {
                $('#programFilter').val('');
                $('#programFilter').trigger('change'); // keep Select2 UI in sync
                table.column(3).search('').draw();
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