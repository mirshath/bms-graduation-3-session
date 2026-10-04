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

function at_h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function at_initials(string $name): string
{
    $w = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);
    if (!$w) return '?';
    $first = mb_substr($w[0], 0, 1);
    $last  = count($w) > 1 ? mb_substr($w[count($w) - 1], 0, 1) : '';
    return mb_strtoupper($first . $last);
}

// All students with seat number and session (bulk_data_table + data_tables)
$query = "SELECT rs.*, bdt.seat_no, COALESCE(dt.session, 'N/A') AS session_name,
          CASE
              WHEN rs.attend IS NULL OR rs.attend = '' THEN 'N/A'
              ELSE rs.attend
          END AS attendance_status
          FROM registered_students rs
          LEFT JOIN bulk_data_table bdt ON rs.student_id = bdt.student_id
          LEFT JOIN data_tables dt ON rs.program_name = dt.programName
          ORDER BY rs.id DESC";
$result = mysqli_query($conn, $query);

$rows = [];
$totalAttended = 0;
if ($result) {
    while ($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
        if ($r['attendance_status'] === 'attended' || $r['attendance_status'] === 'Registered') {
            $totalAttended++;
        }
    }
}
$totalStudents = count($rows);
$totalPending  = $totalStudents - $totalAttended;
$ratePct       = $totalStudents > 0 ? (int)round($totalAttended / $totalStudents * 100) : 0;
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Page level plugins -->
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

<!---------------- Custom styles for this template---------------------------->
<link href="css/new_style_css/all_attended_student.css" rel="stylesheet">
<!-- Page Wrapper -->
<div id="wrapper">
    <!-- Sidebar -->
    <?php include("nav.php"); ?>
    <div id="sidebarOverlay"></div>
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
            <!-- Topbar -->
            <?php include("includes/topnav.php"); ?>

            <div class="container-fluid mt-4">
                <button type="button" id="sidebarToggleMobile" class="at-menu" aria-label="Open menu">
                    <i class="fas fa-bars" aria-hidden="true"></i> <span>Menu</span>
                </button>

                <div class="at">

                    <header class="at-head">
                        <div>
                            <h1>All attended students</h1>
                            <p>Every registered student with seat, session and attendance status.</p>
                        </div>
                        <a href="scan" class="at-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back to scan</a>
                    </header>

                    <!-- summary -->
                    <section class="at-kpis">
                        <div class="at-card at-kpi">
                            <span class="at-kpi-ic"><i class="fas fa-users" aria-hidden="true"></i></span>
                            <div><span class="k">Total students</span><strong><?php echo $totalStudents; ?></strong></div>
                        </div>
                        <div class="at-card at-kpi ok">
                            <span class="at-kpi-ic"><i class="fas fa-user-check" aria-hidden="true"></i></span>
                            <div><span class="k">Attended</span><strong><?php echo $totalAttended; ?></strong></div>
                        </div>
                        <div class="at-card at-kpi warn">
                            <span class="at-kpi-ic"><i class="fas fa-user-clock" aria-hidden="true"></i></span>
                            <div><span class="k">Not attended</span><strong><?php echo $totalPending; ?></strong></div>
                        </div>

                    </section>

                    <!-- filters -->
                    <section class="at-card at-filters">
                        <div class="at-grid">
                            <div class="at-field">
                                <label for="sessionFilter"><i class="fas fa-filter"></i> Session</label>
                                <select id="sessionFilter" class="select2">
                                    <option value="">All Sessions</option>
                                    <?php
                                    // Sessions = unique values of data_tables.session (SESSION_01, SESSION_02, SESSION_03 ...)
                                    try {
                                        $session_query = "SELECT DISTINCT dt.session
                                                          FROM data_tables dt
                                                          WHERE dt.session IS NOT NULL AND dt.session != ''
                                                          ORDER BY dt.session ASC";
                                        $session_result = mysqli_query($conn, $session_query);

                                        if ($session_result && mysqli_num_rows($session_result) > 0) {
                                            while ($session_row = mysqli_fetch_assoc($session_result)) {
                                                $session = at_h($session_row['session']);
                                                echo "<option value='" . $session . "'>" . $session . "</option>";
                                            }
                                        }
                                    } catch (Exception $e) {
                                        error_log("Session filter error: " . $e->getMessage());
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="at-field">
                                <label for="programFilter"><i class="fas fa-filter"></i> Program</label>
                                <select id="programFilter" class="select2">
                                    <option value="">All Programs</option>
                                    <?php
                                    // Programs actually registered, each tagged with its session (data_tables),
                                    // so the Program list can be narrowed by Session in JS.
                                    try {
                                        $program_query = "SELECT DISTINCT rs.program_name, dt.session
                                                          FROM registered_students rs
                                                          LEFT JOIN data_tables dt ON rs.program_name = dt.programName
                                                          WHERE rs.program_name IS NOT NULL AND rs.program_name != ''
                                                          ORDER BY rs.program_name ASC";
                                        $program_result = mysqli_query($conn, $program_query);

                                        if ($program_result && mysqli_num_rows($program_result) > 0) {
                                            while ($program_row = mysqli_fetch_assoc($program_result)) {
                                                $program = at_h($program_row['program_name']);
                                                $prog_session = at_h($program_row['session'] ?? '');
                                                echo "<option value='" . $program . "' data-session='" . $prog_session . "'>" . $program . "</option>";
                                            }
                                        }
                                    } catch (Exception $e) {
                                        error_log("Program filter error: " . $e->getMessage());
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="at-field">
                                <label for="programMainFilter"><i class="fas fa-filter"></i> Program (before "-")</label>
                                <select id="programMainFilter" class="select2">
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
                                            // Substring before first "-" (with spaces trimmed)
                                            $main = $full;
                                            if (strpos($full, '-') !== false) {
                                                $main = trim(substr($full, 0, strpos($full, '-')));
                                            }
                                            if (!empty($main)) {
                                                $main_program_names[$main] = true;
                                            }
                                        }
                                        foreach (array_keys($main_program_names) as $main_prog) {
                                            $main_prog_h = at_h($main_prog);
                                            echo "<option value=\"{$main_prog_h}\">{$main_prog_h}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="at-field">
                                <label for="attendanceFilter"><i class="fas fa-filter"></i> Attendance status</label>
                                <select id="attendanceFilter" class="select2">
                                    <option value="">All Status</option>
                                    <option value="attended">Attended</option>
                                    <option value="N/A">Not attended (N/A)</option>
                                </select>
                            </div>
                        </div>

                        <div class="at-filter-foot">
                            <button id="resetFilter" type="button" class="at-reset">
                                <i class="fas fa-redo" aria-hidden="true"></i> Reset filters
                            </button>
                            <span id="filterInfo"></span>
                        </div>
                    </section>

                    <!-- table -->
                    <section class="at-card at-table-card">
                        <table class="table" id="dataTable" width="100%" cellspacing="0">
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
                                $counter = 1;
                                foreach ($rows as $row):
                                    $inNo   = !empty($row['in_no'])    ? at_h($row['in_no'])    : '';
                                    $seatNo = !empty($row['seat_no'])  ? at_h($row['seat_no'])  : '';
                                    $phone  = !empty($row['phone_no']) ? at_h($row['phone_no']) : '';
                                    $prog   = !empty($row['program_name']) ? at_h($row['program_name']) : '';
                                    $sessionDisplay = !empty($row['session_name']) ? $row['session_name'] : 'N/A';
                                    $sesCls = preg_match('/(\d+)$/', $sessionDisplay, $m) && in_array((int)$m[1], [1, 2, 3], true) ? 's' . (int)$m[1] : '';
                                    $status = $row['attendance_status'];
                                    $isOk   = ($status === 'attended' || $status === 'Registered');
                                ?>
                                    <tr>
                                        <td data-label="#"><span class="at-n"><?php echo $counter++; ?></span></td>
                                        <td data-label="Student ID"><span class="at-id"><?php echo at_h($row['student_id']); ?></span></td>
                                        <td data-label="Name">
                                            <div class="at-person">
                                                <span class="at-ini" data-i="<?php echo at_h(at_initials((string)$row['name_in_full'])); ?>" aria-hidden="true"></span>
                                                <span class="nm"><?php echo at_h($row['name_in_full']); ?></span>
                                            </div>
                                        </td>
                                        <td data-label="IN number"><?php echo $inNo !== '' ? '<span class="at-mono">' . $inNo . '</span>' : '<span class="at-na">N/A</span>'; ?></td>
                                        <td data-label="Seat no"><?php echo $seatNo !== '' ? '<span class="at-seat">' . $seatNo . '</span>' : '<span class="at-na">N/A</span>'; ?></td>
                                        <td data-label="Phone"><?php echo $phone !== '' ? '<span class="at-mono">' . $phone . '</span>' : '<span class="at-na">N/A</span>'; ?></td>
                                        <td data-label="Program"><?php echo $prog !== '' ? '<div class="at-prog">' . $prog . '</div>' : '<span class="at-na">N/A</span>'; ?></td>
                                        <td data-label="Session"><span class="at-ses <?php echo $sesCls; ?>"><?php echo at_h($sessionDisplay); ?></span></td>
                                        <td data-label="Status"><span class="at-st <?php echo $isOk ? 'ok' : 'off'; ?>"><?php echo at_h($status); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </section>

                </div> <!-- /at -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

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

<script>
    $(document).ready(function() {
        // Mobile sidebar: open with the Menu button, close by tapping the overlay / Esc / widening the screen
        $('body').addClass('at-page');

        $('#sidebarToggleMobile').on('click', function() {
            $('body').toggleClass('sidebar-open');
        });

        $('#sidebarOverlay').on('click', function() {
            $('body').removeClass('sidebar-open');
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') $('body').removeClass('sidebar-open');
        });

        $(window).on('resize', function() {
            if (window.innerWidth >= 992) $('body').removeClass('sidebar-open');
        });

        try {
            // Initialize Select2 on filter dropdowns
            $('#sessionFilter').select2({
                placeholder: 'All Sessions',
                allowClear: true,
                width: '100%'
            });

            // Full Program list (value + session), captured once, so the Session filter can rebuild
            // #programFilter's options without a page reload or extra query.
            var allProgramOptions = [];
            $('#programFilter option[value!=""]').each(function() {
                allProgramOptions.push({
                    value: $(this).val(),
                    session: $(this).data('session') || ''
                });
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
                "dom": "<'at-bar'<'at-btns'B><'at-search'f>><'at-scroll'rt><'at-foot'<'at-info'i><'at-pg'p>>",
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search students…",
                    "emptyTable": "No students found.",
                    "zeroRecords": "No matching students found."
                },
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-sm at-b at-xl',
                        title: 'All Attended Students',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-sm at-b at-pdf',
                        title: 'All Attended Students',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function(doc) {
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;
                            doc.styles.tableHeader.fillColor = '#1f4bb6';
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-sm at-b at-csv',
                        title: 'All Attended Students',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-sm at-b at-prt',
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
                        className: 'btn btn-sm at-b at-cp',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            });

            // Helper to extract program name before first "-"
            function getBeforeHyphen(program) {
                if (typeof program !== 'string') return program;
                var idx = program.indexOf('-');
                if (idx === -1) return program.trim();
                return program.slice(0, idx).trim();
            }

            // One persistent custom filter for "Program (before '-')"
            var selectedMainProgram = '';
            $.fn.dataTable.ext.search.push(function(settings, data) {
                if (settings.nTable.id !== 'dataTable' || selectedMainProgram === '') return true;
                return getBeforeHyphen(data[6] || '') === selectedMainProgram;
            });

            // Function to update filter info
            function updateFilterInfo() {
                var selectedSession = $('#sessionFilter').val();
                var selectedAttendance = $('#attendanceFilter').val();
                var selectedProgram = $('#programFilter').val();
                var info = table.page.info();
                var filterText = [];

                if (selectedSession) {
                    filterText.push('Session: <strong>' + selectedSession + '</strong>');
                }
                if (selectedAttendance) {
                    filterText.push('Status: <strong>' + (selectedAttendance === 'attended' ? 'Attended' : 'Not attended') + '</strong>');
                }
                if (selectedProgram) {
                    filterText.push('Program: <strong>' + $('<div>').text(selectedProgram).html() + '</strong>');
                }
                if (selectedMainProgram) {
                    filterText.push('Program (before "-"): <strong>' + $('<div>').text(selectedMainProgram).html() + '</strong>');
                }

                if (filterText.length > 0) {
                    $('#filterInfo').html('<i class="fas fa-info-circle"></i> Showing <strong>' + info.recordsDisplay + '</strong> student(s) · ' + filterText.join(' · '));
                } else {
                    $('#filterInfo').html('');
                }
            }

            // Session Filter Functionality
            $('#sessionFilter').on('change', function() {
                var selectedSession = $(this).val() || '';

                // Narrow the Program dropdown to this session's programs (data-session, set from data_tables)
                var $programFilter = $('#programFilter');
                var currentProgram = $programFilter.val();
                var stillValid = false;

                $programFilter.empty().append('<option value="">All Programs</option>');
                allProgramOptions.forEach(function(opt) {
                    if (selectedSession === '' || opt.session === selectedSession) {
                        $programFilter.append(
                            $('<option>', {
                                value: opt.value,
                                text: opt.value,
                                'data-session': opt.session
                            })
                        );
                        if (opt.value === currentProgram) stillValid = true;
                    }
                });

                if (stillValid) {
                    $programFilter.val(currentProgram);
                } else if (currentProgram) {
                    // The program that was selected doesn't belong to the new session
                    table.column(6).search('');
                }
                $programFilter.trigger('change.select2');

                if (selectedSession === '') {
                    table.column(7).search('').draw();
                } else {
                    table.column(7).search('^' + $.fn.dataTable.util.escapeRegex(selectedSession) + '$', true, false).draw();
                }
            });

            // Attendance Status Filter Functionality
            $('#attendanceFilter').on('change', function() {
                var selectedAttendance = $(this).val() || '';

                if (selectedAttendance === '') {
                    table.column(8).search('').draw();
                } else if (selectedAttendance === 'attended') {
                    table.column(8).search('^(attended|Registered)$', true, false).draw();
                } else if (selectedAttendance === 'N/A') {
                    table.column(8).search('^N/A$', true, false).draw();
                }
            });

            // Program Filter (full name with batch) - clears the "before hyphen" filter
            $('#programFilter').on('change', function() {
                var selectedProgram = $(this).val() || '';

                if (selectedProgram !== '') {
                    selectedMainProgram = '';
                    $('#programMainFilter').val('').trigger('change.select2');
                }

                if (selectedProgram === '') {
                    table.column(6).search('').draw();
                } else {
                    table.column(6).search('^' + $.fn.dataTable.util.escapeRegex(selectedProgram) + '$', true, false).draw();
                }
            });

            // Program Main Filter (before hyphen) - clears the "full name" filter
            $('#programMainFilter').on('change', function() {
                selectedMainProgram = $(this).val() || '';

                if (selectedMainProgram !== '') {
                    $('#programFilter').val('').trigger('change.select2');
                    table.column(6).search('');
                }

                table.draw();
            });

            // Reset Filter Button
            $('#resetFilter').on('click', function() {
                selectedMainProgram = '';
                $('#sessionFilter').val('').trigger('change');
                $('#attendanceFilter').val('').trigger('change');
                $('#programFilter').val('').trigger('change');
                $('#programMainFilter').val('').trigger('change.select2');
                table.columns().search('').draw();
            });

            // Update filter info on every redraw
            table.on('draw', updateFilterInfo);

            // Initial filter info update
            updateFilterInfo();

        } catch (e) {
            console.error("DataTable initialization error:", e);
        }
    });
</script>

</body>

</html>