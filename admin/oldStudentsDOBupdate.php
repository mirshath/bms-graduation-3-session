<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
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

$rows = [];
$loadError = false;
$result = mysqli_query($conn, "SELECT o.*,
        (SELECT dt.session FROM data_tables dt WHERE dt.programName = o.program LIMIT 1) AS session_name
    FROM old_student_db o
    ORDER BY o.id DESC");
if ($result) {
    while ($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
    }
} else {
    $loadError = true;
    error_log("DOB update load error: " . mysqli_error($conn));
}

// Filter lists
$sessionList = [];
$res2 = mysqli_query($conn, "SELECT DISTINCT session FROM data_tables WHERE session IS NOT NULL AND session != '' ORDER BY session ASC");
if ($res2) {
    while ($sr = mysqli_fetch_assoc($res2)) {
        $sessionList[] = $sr['session'];
    }
}
$programList = [];
foreach ($rows as $r) {
    $pn = trim((string)($r['program'] ?? ''));
    if ($pn !== '' && !isset($programList[$pn])) {
        $programList[$pn] = (string)($r['session_name'] ?? '');
    }
}
ksort($programList);

$totalStudents = count($rows);
$totalCompleted = 0;
$totalNotCompleted = 0;
foreach ($rows as $r) {
    $a = strtolower(trim((string)($r['active'] ?? '')));
    if ($a === 'completed') $totalCompleted++;
    elseif ($a === 'not-completed' || $a === 'not completed') $totalNotCompleted++;
}
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Page level plugins -->
<link rel="stylesheet" href="vendor/datatables/dataTables.bootstrap4.min.css">


<!---------------- Custom styles for this template---------------------------->
<link href="css/new_style_css/update_dob.css" rel="stylesheet">

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
                            <h1>DOB Updates</h1>
                            <p>Correct a student's ID, date of birth or active status, then save that row.</p>
                        </div>
                    </header>

                    <?php if ($loadError): ?>
                        <div class="at-alert" role="alert">
                            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                            Error loading student records. Please try again later.
                        </div>
                    <?php endif; ?>

                    <!-- summary -->
                    <section class="at-kpis">
                        <div class="at-card at-kpi">
                            <span class="at-kpi-ic"><i class="fas fa-users" aria-hidden="true"></i></span>
                            <div><span class="k">Total students</span><strong><?php echo $totalStudents; ?></strong></div>
                        </div>
                        <div class="at-card at-kpi ok">
                            <span class="at-kpi-ic"><i class="fas fa-user-check" aria-hidden="true"></i></span>
                            <div><span class="k">Completed</span><strong id="kpiCompleted"><?php echo $totalCompleted; ?></strong></div>
                        </div>
                        <div class="at-card at-kpi warn">
                            <span class="at-kpi-ic"><i class="fas fa-user-clock" aria-hidden="true"></i></span>
                            <div><span class="k">Not completed</span><strong id="kpiNot"><?php echo $totalNotCompleted; ?></strong></div>
                        </div>
                        <div class="at-card at-kpi">
                            <span class="at-kpi-ic"><i class="fas fa-save" aria-hidden="true"></i></span>
                            <div><span class="k">Saved this session</span><strong id="kpiSaved">0</strong></div>
                        </div>
                    </section>

                    <!-- filters -->
                    <section class="at-card at-filters">
                        <div class="at-grid">
                            <div class="at-field">
                                <label for="sessionFilter"><i class="fas fa-filter"></i> Session</label>
                                <select id="sessionFilter" class="at-sel">
                                    <option value="">All Sessions</option>
                                    <?php foreach ($sessionList as $sv): ?>
                                        <option value="<?php echo at_h($sv); ?>"><?php echo at_h($sv); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="at-field">
                                <label for="programFilter"><i class="fas fa-filter"></i> Program</label>
                                <select id="programFilter" class="at-sel">
                                    <option value="">All Programs</option>
                                    <?php foreach ($programList as $pv => $ps): ?>
                                        <option value="<?php echo at_h($pv); ?>" data-session="<?php echo at_h($ps); ?>"><?php echo at_h($pv); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="at-field">
                                <label for="statusFilter"><i class="fas fa-filter"></i> Registration status</label>
                                <select id="statusFilter" class="at-sel">
                                    <option value="">All Status</option>
                                    <option value="registered">Registered</option>
                                    <option value="N/A">Not Registered</option>
                                </select>
                            </div>

                            <div class="at-field">
                                <label for="activeFilter"><i class="fas fa-filter"></i> Active status</label>
                                <select id="activeFilter" class="at-sel">
                                    <option value="">All Active Status</option>
                                    <option value="completed">Completed</option>
                                    <option value="not-completed">Not completed</option>

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
                                    <th>Name</th>
                                    <th>DOB<br>(yyyy-mm-dd)</th>
                                    <th>Given Email</th>
                                    <th>Program</th>
                                    <th>Course Fee</th>
                                    <th>Registered / Not</th>
                                    <th>Active</th>
                                    <th>Action</th>
                                    <th>Session</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $counter = 1;
                                $activeOptions = ['completed' => 'completed', 'not-completed' => 'not-completed'];
                                foreach ($rows as $row):
                                    $rid        = (int)$row['id'];
                                    $student_id = trim((string)($row['student_id'] ?? ''));
                                    $name       = trim((string)($row['name'] ?? ''));
                                    $email      = trim((string)($row['given_email'] ?? ''));
                                    $program    = trim((string)($row['program'] ?? ''));
                                    $payment    = trim((string)($row['payment_status'] ?? ''));
                                    $status     = trim((string)($row['status'] ?? ''));
                                    $active     = trim((string)($row['active'] ?? ''));
                                    if ($payment === '') $payment = 'N/A';
                                    if ($status === '')  $status  = 'N/A';

                                    $dobVal = '';
                                    $dobRaw = (string)($row['DOB'] ?? '');
                                    if ($dobRaw !== '' && $dobRaw !== '0000-00-00') {
                                        $ts = strtotime($dobRaw);
                                        if ($ts) $dobVal = date('Y-m-d', $ts);
                                    }

                                    $payCls = 'off';
                                    if (strcasecmp($payment, 'paid') === 0) $payCls = 'ok';
                                    elseif (strcasecmp($payment, 'pending') === 0) $payCls = 'warn';

                                    $stCls = 'off';
                                    if (strcasecmp($status, 'registered') === 0) $stCls = 'ok';
                                    elseif (strcasecmp($status, 'not registered') === 0) $stCls = 'bad';

                                    $activeLower = strtolower($active);
                                    if ($activeLower === 'not completed') $active = 'not-completed';
                                ?>
                                    <tr data-id="<?php echo $rid; ?>">
                                        <td data-label="#"><span class="at-n"><?php echo $counter++; ?></span></td>
                                        <td data-label="Student ID" data-order="<?php echo at_h($student_id); ?>" data-search="<?php echo at_h($student_id); ?>">
                                            <input type="text" class="at-in mono student-id-input" value="<?php echo at_h($student_id); ?>" data-orig="<?php echo at_h($student_id); ?>" aria-label="Student ID">
                                        </td>
                                        <td data-label="Name">
                                            <?php if ($name !== ''): ?>
                                                <div class="at-person">
                                                    <span class="at-ini" data-i="<?php echo at_h(at_initials($name)); ?>" aria-hidden="true"></span>
                                                    <span class="nm"><?php echo at_h($name); ?></span>
                                                </div>
                                            <?php else: ?>
                                                <span class="at-na">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="DOB" data-order="<?php echo at_h($dobVal); ?>" data-search="<?php echo at_h($dobVal); ?>">
                                            <input type="text" class="at-in mono dob-input" value="<?php echo at_h($dobVal); ?>" data-orig="<?php echo at_h($dobVal); ?>" placeholder="yyyy-mm-dd" maxlength="10" inputmode="numeric" autocomplete="off" aria-label="Date of birth (yyyy-mm-dd)">
                                        </td>
                                        <td data-label="Email"><?php echo $email !== '' ? '<span class="at-em"><a class="at-mail" href="mailto:' . at_h($email) . '">' . at_h($email) . '</a></span>' : '<span class="at-na">N/A</span>'; ?></td>
                                        <td data-label="Program"><?php echo $program !== '' ? '<div class="at-prog">' . at_h($program) . '</div>' : '<span class="at-na">N/A</span>'; ?></td>
                                        <td data-label="Course fee"><span class="at-st <?php echo $payCls; ?>"><?php echo at_h($payment); ?></span></td>
                                        <td data-label="Registration"><span class="at-st <?php echo $stCls; ?>"><?php echo at_h($status); ?></span></td>
                                        <td data-label="Active" data-order="<?php echo at_h($active); ?>" data-search="<?php echo at_h($active !== '' ? $active : 'unset'); ?>">
                                            <select class="at-in active-select" data-orig="<?php echo at_h($active); ?>" aria-label="Active status">

                                                <?php
                                                foreach ($activeOptions as $val => $label) {
                                                    echo '<option value="' . at_h($val) . '"' . ($active === $val ? ' selected' : '') . '>' . at_h($label) . '</option>';
                                                }
                                                // keep any unexpected existing value selectable so it is not lost
                                                if ($active !== '' && !isset($activeOptions[$active])) {
                                                    echo '<option value="' . at_h($active) . '" selected>' . at_h($active) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td data-label="Action">
                                            <button type="button" class="at-save update-btn" data-id="<?php echo $rid; ?>" disabled title="Save changes">
                                                <i class="fas fa-check" aria-hidden="true"></i><span class="t">Save</span>
                                            </button>
                                        </td>
                                        <td data-label="Session"><?php echo at_h(($row['session_name'] ?? '') !== '' ? $row['session_name'] : 'N/A'); ?></td>
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

<div id="atToast" role="status" aria-live="polite"><i class="fas fa-check-circle" aria-hidden="true"></i><span></span></div>

<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        // Mobile sidebar
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

        var table = $('#dataTable').DataTable({
            "autoWidth": false,
            "pageLength": 200,
            "order": [
                [1, "asc"]
            ],
            "columnDefs": [{
                "orderable": false,
                "targets": 9
            }, {
                // Session is only used for filtering (kept in the data, not shown as a column)
                "visible": false,
                "targets": 10
            }],
            "dom": "<'at-bar'<'at-hint'>f><'at-scroll'rt><'at-foot'<'at-info'i><'at-pg'p>>",
            "language": {
                "search": "",
                "searchPlaceholder": "Search students…",
                "emptyTable": "No records found",
                "zeroRecords": "No matching students found",
                "info": "Showing _START_ to _END_ of _TOTAL_ students",
                "infoEmpty": "Showing 0 to 0 of 0 students",
                "infoFiltered": "(filtered from _MAX_ total students)"
            },
            "initComplete": function() {
                $('.at-hint').html('<i class="fas fa-info-circle" aria-hidden="true"></i> Edit a row, then press Save on that row.');
            }
        });

        // ---------- Filters ----------
        // Column positions: 5 = Program, 7 = Registered / Not, 8 = Active, 10 = Session (hidden)
        if ($.fn.select2) {
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
            $('#statusFilter').select2({
                placeholder: 'All Status',
                allowClear: true,
                width: '100%'
            });
            $('#activeFilter').select2({
                placeholder: 'All Active Status',
                allowClear: true,
                width: '100%'
            });
        }

        // Keep the full program list so the Session filter can narrow it
        var allPrograms = [];
        $('#programFilter option[value!=""]').each(function() {
            allPrograms.push({
                value: $(this).val(),
                session: $(this).data('session') || ''
            });
        });

        function esc(v) {
            return $('<div>').text(v).html();
        }

        function exact(v) {
            return '^' + $.fn.dataTable.util.escapeRegex(v) + '$';
        }

        function updateFilterInfo() {
            var parts = [];
            var ses = $('#sessionFilter').val();
            var prog = $('#programFilter').val();
            var st = $('#statusFilter').val();
            var act = $('#activeFilter').val();
            var info = table.page.info();

            if (ses) parts.push('Session: <strong>' + esc(ses) + '</strong>');
            if (prog) parts.push('Program: <strong>' + esc(prog) + '</strong>');
            if (st) parts.push('Status: <strong>' + (st === 'N/A' ? 'Not Registered' : 'Registered') + '</strong>');
            if (act) parts.push('Active: <strong>' + (act === 'completed' ? 'Completed' : (act === 'none' ? 'Not set' : 'Not completed')) + '</strong>');

            if (parts.length) {
                $('#filterInfo').html('<i class="fas fa-info-circle"></i> Showing <strong>' + info.recordsDisplay + '</strong> student(s) · ' + parts.join(' · '));
            } else {
                $('#filterInfo').html('');
            }
        }

        $('#sessionFilter').on('change', function() {
            var ses = $(this).val() || '';
            var $pf = $('#programFilter');
            var current = $pf.val();
            var stillValid = false;

            // narrow the Program list to this session
            $pf.empty().append('<option value="">All Programs</option>');
            allPrograms.forEach(function(o) {
                if (ses === '' || o.session === ses) {
                    $pf.append($('<option>', {
                        value: o.value,
                        text: o.value,
                        'data-session': o.session
                    }));
                    if (o.value === current) stillValid = true;
                }
            });
            if (stillValid) {
                $pf.val(current);
            } else {
                table.column(5).search('');
            }
            $pf.trigger('change.select2');

            table.column(10).search(ses === '' ? '' : exact(ses), true, false).draw();
        });

        $('#programFilter').on('change', function() {
            var v = $(this).val() || '';
            table.column(5).search(v === '' ? '' : exact(v), true, false).draw();
        });

        $('#statusFilter').on('change', function() {
            var v = $(this).val() || '';
            if (v === '') {
                table.column(7).search('').draw();
            } else if (v === 'N/A') {
                table.column(7).search('^(N/A|not registered)$', true, false).draw();
            } else {
                table.column(7).search(exact(v), true, false).draw();
            }
        });

        $('#activeFilter').on('change', function() {
            var v = $(this).val() || '';
            if (v === '') {
                table.column(8).search('').draw();
            } else if (v === 'completed') {
                table.column(8).search('^completed$', true, false).draw();
            } else if (v === 'none') {
                table.column(8).search('^unset$', true, false).draw();
            } else {
                table.column(8).search('^not[- ]completed$', true, false).draw();
            }
        });

        $('#resetFilter').on('click', function() {
            $('#sessionFilter, #programFilter, #statusFilter, #activeFilter').val('');
            $('#sessionFilter').trigger('change'); // rebuilds the full program list
            $('#statusFilter, #activeFilter').trigger('change.select2');
            table.columns().search('').draw();
        });

        table.on('draw', updateFilterInfo);

        var savedCount = 0;
        var toastTimer = null;

        function toast(msg, ok) {
            var $t = $('#atToast');
            $t.removeClass('ok err').addClass(ok ? 'ok' : 'err');
            $t.find('i').attr('class', ok ? 'fas fa-check-circle' : 'fas fa-exclamation-circle');
            $t.find('span').text(msg);
            $t.addClass('show');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(function() {
                $t.removeClass('show');
            }, 3200);
        }

        function recount() {
            var done = 0,
                not = 0;
            table.rows().nodes().to$().find('.active-select').each(function() {
                var v = ($(this).val() || '').toLowerCase();
                if (v === 'completed') done++;
                else if (v === 'not-completed') not++;
            });
            $('#kpiCompleted').text(done);
            $('#kpiNot').text(not);
        }

        function isDirty($tr) {
            return $tr.find('.student-id-input').val() !== String($tr.find('.student-id-input').attr('data-orig')) ||
                $tr.find('.dob-input').val() !== String($tr.find('.dob-input').attr('data-orig')) ||
                $tr.find('.active-select').val() !== String($tr.find('.active-select').attr('data-orig'));
        }

        // DOB is plain text in yyyy-mm-dd: keep digits only and add the hyphens automatically (also works for paste)
        function formatDob(v) {
            var d = String(v).replace(/\D/g, '').slice(0, 8);
            if (d.length > 6) return d.slice(0, 4) + '-' + d.slice(4, 6) + '-' + d.slice(6);
            if (d.length > 4) return d.slice(0, 4) + '-' + d.slice(4);
            return d;
        }

        function validDob(v) {
            var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(v);
            if (!m) return false;
            var dt = new Date(Date.UTC(+m[1], +m[2] - 1, +m[3]));
            return dt.getUTCFullYear() === +m[1] && dt.getUTCMonth() === +m[2] - 1 && dt.getUTCDate() === +m[3];
        }

        $(document).on('input', '.dob-input', function() {
            var f = formatDob(this.value);
            if (this.value !== f) this.value = f;
        });

        // Enable Save only when something changed
        $(document).on('input change', '.student-id-input, .dob-input, .active-select', function() {
            var $tr = $(this).closest('tr');
            var dirty = isDirty($tr);
            $tr.toggleClass('is-dirty', dirty).removeClass('is-saved');
            $tr.find('.update-btn').prop('disabled', !dirty);
        });

        // Enter key saves the row
        $(document).on('keydown', '.student-id-input, .dob-input', function(e) {
            if (e.key === 'Enter') {
                $(this).closest('tr').find('.update-btn:not(:disabled)').trigger('click');
            }
        });

        $(document).on('click', '.update-btn', function() {
            var $btn = $(this);
            var $tr = $btn.closest('tr');
            var rowId = $btn.data('id');
            var studentId = $.trim($tr.find('.student-id-input').val());
            var dob = $.trim($tr.find('.dob-input').val());
            var active = $tr.find('.active-select').val();

            if (studentId === '' || dob === '') {
                toast('Student ID and DOB cannot be empty', false);
                return;
            }

            if (!validDob(dob)) {
                toast('DOB must be a valid date like 1999-01-19', false);
                $tr.find('.dob-input').trigger('focus');
                return;
            }

            var label = $btn.html();
            $btn.prop('disabled', true).addClass('busy').html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i>');

            $.ajax({
                url: 'update_dob.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: rowId,
                    student_id: studentId,
                    dob: dob,
                    active: active
                }
            }).done(function(res) {
                if (res && res.success) {
                    // new values become the baseline
                    $tr.find('.student-id-input').attr('data-orig', studentId).closest('td').attr({
                        'data-order': studentId,
                        'data-search': studentId
                    });
                    $tr.find('.dob-input').attr('data-orig', dob).closest('td').attr({
                        'data-order': dob,
                        'data-search': dob
                    });
                    $tr.find('.active-select').attr('data-orig', active).closest('td').attr({
                        'data-order': active,
                        'data-search': active !== '' ? active : 'unset'
                    });
                    table.row($tr).invalidate();

                    $tr.removeClass('is-dirty').addClass('is-saved');
                    setTimeout(function() {
                        $tr.removeClass('is-saved');
                    }, 1800);

                    savedCount++;
                    $('#kpiSaved').text(savedCount);
                    recount();
                    toast(res.message || 'Record updated successfully', true);
                    $btn.html(label).removeClass('busy').prop('disabled', true);
                } else {
                    toast((res && res.message) || 'Failed to update record', false);
                    $btn.html(label).removeClass('busy').prop('disabled', false);
                }
            }).fail(function() {
                toast('Error updating record', false);
                $btn.html(label).removeClass('busy').prop('disabled', false);
            });
        });
    });
</script>

</body>

</html>