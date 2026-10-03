<?php
session_start();
if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_name'] ?? '') === 'Unknown Admin') {
    header("Location: login");
    exit();
}

include("../database/connection.php");

/** True when extra_ticket_log has this column (older databases may not). */
function ed_has_col($conn, $col)
{
    static $cache = [];
    if (!isset($cache[$col])) {
        $r = $conn->query("SHOW COLUMNS FROM `extra_ticket_log` LIKE '" . $conn->real_escape_string($col) . "'");
        $cache[$col] = ($r && $r->num_rows > 0);
    }
    return $cache[$col];
}

function ed_session_label($v)
{
    $v = trim((string)$v);
    return $v === '' ? '-' : ucwords(strtolower(str_replace('_', ' ', $v)));
}

// student / programme / session come from the log row, or from registered_students for older rows
$nameSel = ed_has_col($conn, 'student_name') ? "COALESCE(NULLIF(e.`student_name`, ''), r.`name_in_full`)" : "r.`name_in_full`";
$progSel = ed_has_col($conn, 'program_name') ? "COALESCE(NULLIF(e.`program_name`, ''), r.`program_name`)" : "r.`program_name`";
$sessSel = ed_has_col($conn, 'session')      ? "COALESCE(NULLIF(e.`session`, ''), r.`session`)"           : "r.`session`";

$canIssue = ed_has_col($conn, 'issued_adExtra_ticket') && ed_has_col($conn, 'issued_adExtra_ticket_by');
$issSel   = $canIssue
    ? "e.`issued_adExtra_ticket` AS iss, e.`issued_adExtra_ticket_by` AS iss_by"
    : "NULL AS iss, NULL AS iss_by";

$rows = [];
$sessions = [];
$sumTickets = 0;
$sumAmount = 0.0;
$pendingTickets = 0;
$issuedTickets = 0;

$res = $conn->query("
    SELECT e.id, e.student_id, $nameSel AS student_name, $progSel AS program_name, $sessSel AS session_name,
           e.added_tickets, e.ticket_price, e.total_added, e.added_on,
           COALESCE(a.admin_name, e.added_by) AS added_by_name,
           $issSel
    FROM extra_ticket_log e
    LEFT JOIN registered_students r ON r.student_id = e.student_id
    LEFT JOIN admin a ON e.added_by REGEXP '^[0-9]+$' AND a.id = CAST(e.added_by AS UNSIGNED)
    ORDER BY e.id DESC
");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $r['issued'] = trim((string)$r['iss']) !== '';
        $rows[] = $r;
        $n = (int)$r['added_tickets'];
        $sumTickets += $n;
        $sumAmount  += (float)$r['total_added'];
        if ($r['issued']) {
            $issuedTickets += $n;
        } else {
            $pendingTickets += $n;
        }
        $s = trim((string)$r['session_name']);
        if ($s !== '') {
            $sessions[$s] = true;
        }
    }
}
ksort($sessions);

include("includes/header.php");
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,600&family=Public+Sans:wght@400;500;600;700;800&family=Geist+Mono:wght@500;600;700&display=swap" rel="stylesheet">


<!---------------- Custom styles for this template---------------------------->
<link href="css/new_style_css/extra_ticket_data.css" rel="stylesheet">
<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="sidebarOverlay" class="d-md-none"></div>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <div class="container-fluid mt-4">
                <button id="sidebarToggleMobile" class="btn btn-light border d-inline-flex align-items-center gap-2 d-lg-none mb-3 shadow-sm">
                    <i class="fas fa-bars"></i><span class="font-weight-bold">Menu</span>
                </button>

                <div class="ed">
                    <header class="ed-head">
                        <h1 style="text-align: center;">Extra Ticket log</h1> <br>

                        <!-- <p>Every extra ticket added at the desk, with the student, programme and session, who added it, and whether the tickets have been issued.</p> -->
                    </header>

                    <?php if (!$canIssue): ?>
                        <div class="ed-warn" role="alert">Issue status is not available yet. Run <b>add_issued_adextra_ticket.sql</b> in phpMyAdmin to turn it on.</div>
                    <?php endif; ?>

                    <div class="ed-card ed-facts">
                        <div class="ed-fact"><span>Purchases</span><strong><?= count($rows) ?></strong></div>
                        <div class="ed-fact"><span>Tickets added</span><strong><?= (int)$sumTickets ?></strong></div>
                        <div class="ed-fact"><span>Amount collected (Rs.)</span><strong><?= number_format($sumAmount, 2) ?></strong></div>
                        <?php if ($canIssue): ?>
                            <div class="ed-fact pending"><span>Waiting to be issued</span><strong><?= (int)$pendingTickets ?></strong><small><?= (int)$issuedTickets ?> already issued</small></div>
                        <?php else: ?>
                            <div class="ed-fact"><span>Students</span><strong><?= count(array_unique(array_column($rows, 'student_id'))) ?></strong></div>
                        <?php endif; ?>
                    </div>

                    <section class="ed-card">
                        <div class="ed-tools">
                            <h2>All purchases</h2>
                            <?php if ($canIssue): ?>
                                <div class="ed-seg" role="radiogroup" aria-label="Issue status">
                                    <label><input type="radio" name="st" value="" checked><span>All</span></label>
                                    <label><input type="radio" name="st" value="pending"><span>Pending</span></label>
                                    <label><input type="radio" name="st" value="issued"><span>Issued</span></label>
                                </div>
                            <?php endif; ?>
                            <?php if (count($sessions)): ?>
                                <select id="sessFilter" class="ed-sel" aria-label="Filter by session">
                                    <option value="">All sessions</option>
                                    <?php foreach (array_keys($sessions) as $s): ?>
                                        <option value="<?= htmlspecialchars($s, ENT_QUOTES) ?>"><?= htmlspecialchars(ed_session_label($s)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div class="ed-wrap">
                            <div class="table-responsive">
                                <table class="table ed-table" id="extraTicketTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Added on</th>
                                            <th>Student</th>
                                            <th>Programme</th>
                                            <th>Session</th>
                                            <th class="c">Tickets</th>
                                            <th class="r">Price (Rs.)</th>
                                            <th class="r">Total (Rs.)</th>
                                            <th>Added by</th>
                                            <th class="c">Issued</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($rows)): ?>
                                            <tr>
                                                <td colspan="10" class="ed-empty">No extra tickets have been added yet.</td>
                                            </tr>
                                            <?php else: foreach ($rows as $r):
                                                $ts   = strtotime((string)$r['added_on']);
                                                $sess = trim((string)$r['session_name']);
                                                $stat = !$canIssue ? 'na' : ($r['issued'] ? 'issued' : 'pending');
                                            ?>
                                                <tr>
                                                    <td class="ed-mono"><?= (int)$r['id'] ?></td>
                                                    <td class="ed-time" data-order="<?= (int)$ts ?>"><?= $ts ? date('d M Y', $ts) : '-' ?><small><?= $ts ? date('h:i A', $ts) : '' ?></small></td>
                                                    <td>
                                                        <div class="ed-stu">
                                                            <strong><?= htmlspecialchars((string)($r['student_name'] ?? '') !== '' ? (string)$r['student_name'] : '-') ?></strong>
                                                            <span class="ed-mono"><?= htmlspecialchars((string)($r['student_id'] ?? '-')) ?></span>
                                                        </div>
                                                    </td>
                                                    <td class="ed-prog"><?= htmlspecialchars(trim((string)$r['program_name']) !== '' ? (string)$r['program_name'] : '-') ?></td>
                                                    <td data-search="<?= htmlspecialchars($sess === '' ? 'none' : $sess, ENT_QUOTES) ?>"><?php if ($sess !== ''): ?><span class="ed-sess"><?= htmlspecialchars(ed_session_label($sess)) ?></span><?php else: ?>-<?php endif; ?></td>
                                                    <td class="c" data-order="<?= (int)$r['added_tickets'] ?>"><span class="ed-tk">+<?= (int)$r['added_tickets'] ?></span></td>
                                                    <td class="r ed-mono" data-order="<?= (float)$r['ticket_price'] ?>"><?= number_format((float)$r['ticket_price'], 2) ?></td>
                                                    <td class="r ed-mono" data-order="<?= (float)$r['total_added'] ?>"><strong><?= number_format((float)$r['total_added'], 2) ?></strong></td>
                                                    <td><?= htmlspecialchars(trim((string)$r['added_by_name']) !== '' ? (string)$r['added_by_name'] : '-') ?></td>
                                                    <td class="c" data-search="<?= $stat ?>">
                                                        <?php if (!$canIssue): ?>-
                                                    <?php elseif ($r['issued']): ?><span class="ed-iss done">Issued</span><?php if (trim((string)$r['iss_by']) !== ''): ?><div class="ed-by">by <?= htmlspecialchars(trim((string)$r['iss_by'])) ?></div><?php endif; ?>
                                                    <?php else: ?><span class="ed-iss wait">Pending</span><?php endif; ?>
                                                    </td>
                                                </tr>
                                        <?php endforeach;
                                        endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>

            </div> <!-- /container-fluid -->
        </div> <!-- /content -->
    </div> <!-- /content-wrapper -->
</div> <!-- /wrapper -->

<!-- ========== JS & CSS ========== -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
    document.body.classList.add('dashboard-page');
    document.getElementById('sidebarToggleMobile').addEventListener('click', function() {
        document.body.classList.toggle('sidebar-open');
    });
    document.getElementById('sidebarOverlay').addEventListener('click', function() {
        document.body.classList.remove('sidebar-open');
    });

    $(document).ready(function() {
        if ($('#extraTicketTable tbody tr td[colspan]').length) return; // nothing to turn into a DataTable

        var table = $('#extraTicketTable').DataTable({
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            order: [
                [1, 'desc']
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search student, ID or programme',
                lengthMenu: 'Show _MENU_'
            }
        });

        // issue status (column 9) and session (column 4)
        $('input[name="st"]').on('change', function() {
            var v = $('input[name="st"]:checked').val();
            table.column(9).search(v ? '^' + v + '$' : '', true, false).draw();
        });
        $('#sessFilter').on('change', function() {
            var v = this.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            table.column(4).search(v ? '^' + v + '$' : '', true, false).draw();
        });
    });
</script>

</body>

</html>