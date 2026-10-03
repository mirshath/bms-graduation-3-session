<?php
session_start();


if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_name'] ?? '') === 'Unknown Admin') {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");

$role = $_SESSION['role'] ?? '';
$today = date('Y-m-d');

// -------------------------
// 1️⃣ Today’s Registered Students
// -------------------------
$todayRegistered = 0;
$sql = "SELECT COUNT(*) AS total FROM registered_students WHERE DATE(created_at) = '$today'";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $todayRegistered = $row['total'];
}

// -------------------------
// 2️⃣ Today’s Payments
// -------------------------
$todayPayments = 0;
$sql = "SELECT COUNT(DISTINCT student_id) AS total FROM payment_records WHERE DATE(payment_date) = '$today'";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $todayPayments = $row['total'];
}

// -------------------------
// 3️⃣ Total Registered Students
// -------------------------
$totalRegistered = 0;
$sql = "SELECT COUNT(*) AS total FROM registered_students";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $totalRegistered = $row['total'];
}

// -------------------------
// 4️⃣ Total Students with Payments
// -------------------------
$totalPayments = 0;
$sql = "SELECT COUNT(DISTINCT student_id) AS total FROM payment_records";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $totalPayments = $row['total'];
}

// -------------------------
// 5️⃣ Total Notifications
// -------------------------
$totalNotifications = 0;
$sql = "SELECT COUNT(*) AS total FROM notifications WHERE status = 'unread'";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $totalNotifications = $row['total'];
}

// -------------------------
// 6️⃣ Recent Notifications
// -------------------------
$notifications = [];
$result = $conn->query("SELECT * FROM notifications WHERE status = 'unread' ORDER BY created_at DESC LIMIT 20");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

// -------------------------
// 7️⃣ Old Student Count
// -------------------------
$oldStudentCount = 0;
$sql = "SELECT COUNT(*) AS total FROM old_student_db";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $oldStudentCount = $row['total'];
}

// -------------------------
// 8️⃣ Total Extra Tickets Sold
// -------------------------
$totalExtraTickets = 0;
$sql1 = "SELECT SUM(extra_ticket_count) AS total FROM payment_records";
$result1 = $conn->query($sql1);
$paymentExtraTickets = ($result1 && $row = $result1->fetch_assoc()) ? ($row['total'] ?? 0) : 0;

$sql2 = "SELECT SUM(added_tickets) AS total FROM extra_ticket_log";
$result2 = $conn->query($sql2);
$logExtraTickets = ($result2 && $row = $result2->fetch_assoc()) ? ($row['total'] ?? 0) : 0;

$totalExtraTickets = $paymentExtraTickets + $logExtraTickets;

// -------------------------
// 9️⃣ Total Free Tickets
// -------------------------
$totalFreeTickets = 0;
$sql = "SELECT SUM(free_ticket_count) AS total FROM payment_records";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $totalFreeTickets = $row['total'] ?? 0;
}


// -------------------------
// 🔟 Sessions = the unique values of data_tables.session (no hard-coded list)
// -------------------------
function session_label(string $code): string
{
    return ucwords(strtolower(str_replace('_', ' ', trim($code))));
}

$sessionCodes = [];
$res = $conn->query("SELECT DISTINCT TRIM(`session`) AS code FROM data_tables WHERE `session` IS NOT NULL AND TRIM(`session`) <> ''");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $sessionCodes[] = $r['code'];
    }
}
natcasesort($sessionCodes);
$sessionCodes = array_values($sessionCodes);

$sessionData = [];
foreach ($sessionCodes as $code) {
    $sessionData[$code] = [
        'label' => session_label($code),
        'registered' => 0,
        'paid' => 0,
        'attended' => 0,
        'remaining' => 0,
        'free' => 0,
        'base_extra' => 0,
        'log_extra' => 0,
        'log_legacy' => 0,
    ];
}

// programme -> session map, one row per programme so a programme listed twice is never counted twice
$programMap = "(SELECT DISTINCT TRIM(programName) AS pname, TRIM(`session`) AS sess
                FROM data_tables WHERE `session` IS NOT NULL AND TRIM(`session`) <> '') m";

// registered / paid / attended / not attended / free + extra tickets from payment receipts
$res = $conn->query("
    SELECT m.sess AS code,
           COUNT(DISTINCT rs.student_id) AS registered,
           COUNT(DISTINCT pr.student_id) AS paid,
           COUNT(DISTINCT CASE WHEN rs.attend = 'attended' THEN rs.student_id END) AS attended,
           COUNT(DISTINCT CASE WHEN pr.student_id IS NOT NULL
                                AND (rs.attend IS NULL OR rs.attend != 'attended') THEN rs.student_id END) AS remaining,
           COALESCE(SUM(pr.free_ticket_count), 0)  AS free_t,
           COALESCE(SUM(pr.extra_ticket_count), 0) AS extra_t
    FROM registered_students rs
    JOIN $programMap ON TRIM(rs.program_name) = m.pname
    LEFT JOIN payment_records pr ON pr.student_id = rs.student_id
    GROUP BY m.sess
");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        if (!isset($sessionData[$r['code']])) {
            continue;
        }
        $sessionData[$r['code']]['registered'] = (int)$r['registered'];
        $sessionData[$r['code']]['paid']       = (int)$r['paid'];
        $sessionData[$r['code']]['attended']   = (int)$r['attended'];
        $sessionData[$r['code']]['remaining']  = (int)$r['remaining'];
        $sessionData[$r['code']]['free']       = (int)$r['free_t'];
        $sessionData[$r['code']]['base_extra'] = (int)$r['extra_t'];
    }
}

// extra tickets added at the desk: extra_ticket_log.session decides the session.
// Rows with no session (older rows) or an unknown session are NOT dropped: they go to the first
// session, exactly like the old rule, and are shown as "older tickets" so the totals stay complete.
$sessionByUpper = [];
foreach ($sessionCodes as $code) {
    $sessionByUpper[strtoupper($code)] = $code;
}
$firstSession = $sessionCodes[0] ?? null;
$res = $conn->query("
    SELECT UPPER(TRIM(COALESCE(`session`, ''))) AS code, COALESCE(SUM(added_tickets), 0) AS t
    FROM extra_ticket_log
    GROUP BY code
");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $t = (int)$r['t'];
        if (isset($sessionByUpper[$r['code']])) {
            $sessionData[$sessionByUpper[$r['code']]]['log_extra'] += $t;
        } elseif ($firstSession !== null) {
            $sessionData[$firstSession]['log_extra']  += $t;
            $sessionData[$firstSession]['log_legacy'] += $t;
        }
    }
}

// receipts' extra tickets that belong to a student whose programme has no session
$unlinkedExtra = max(0, (int)$paymentExtraTickets - array_sum(array_column($sessionData, 'base_extra')));

?>
<meta http-equiv="refresh" content="10">

<link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,600&family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@500;600;700&display=swap" rel="stylesheet">

<!---------------- Custom styles for this template---------------------------->
<link href="css/new_style_css/admin_index.css" rel="stylesheet">

<!-- Page Wrapper -->
<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="sidebarOverlay" class="d-md-none"></div>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <div class="container-fluid mt-4">
                <button id="sidebarToggleMobile"
                    class="btn btn-light border d-inline-flex align-items-center gap-2 d-lg-none mb-3 shadow-sm">
                    <i class="fas fa-bars"></i>
                    <span class="fw-semibold">Menu</span>
                </button>
                <div class="dash">
                    <header class="dash-head">
                        <h1>Graduation dashboard</h1>
                        <!-- <p><?php echo date('l, j F Y'); ?> &middot; updates every 10 seconds</p> -->
                    </header>

                    <?php
                    $tones = ['pink', 'purple', 'green', 'blue', 'amber'];
                    $toneOf = function ($code) use ($sessionCodes, $tones) {
                        $i = array_search($code, $sessionCodes, true);
                        return $tones[($i === false ? 0 : $i) % count($tones)];
                    };
                    ?>

                    <?php if (in_array($role, ['admin', 'registrationDesk'])): ?>
                        <section class="dash-sec">
                            <h2>Attendance by session</h2>
                            <?php if (!$sessionData): ?>
                                <p class="dash-empty">No sessions are set in the programme list yet.</p>
                            <?php else: ?>
                                <div class="dash-grid">
                                    <?php foreach ($sessionData as $code => $d):
                                        $pct = $d['paid'] ? min(100, (int)round($d['attended'] / $d['paid'] * 100)) : 0; ?>
                                        <article class="dash-card tone tone-<?php echo $toneOf($code); ?>">
                                            <div class="dash-card-head">
                                                <span class="dash-chip"><?php echo htmlspecialchars($d['label']); ?></span>
                                                <span class="dash-pct"><?php echo $pct; ?>% attended</span>
                                            </div>
                                            <div class="dash-nums">
                                                <div><span>Paid students</span><strong><?php echo $d['paid']; ?></strong></div>
                                                <div><span>Attended</span><strong class="ok"><?php echo $d['attended']; ?></strong></div>
                                                <div><span>Not attended</span><strong class="bad"><?php echo $d['remaining']; ?></strong></div>
                                            </div>
                                            <div class="dash-bar" role="img" aria-label="<?php echo $pct; ?> percent attended"><i style="width:<?php echo $pct; ?>%"></i></div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </section>
                    <?php endif; ?>

                    <section class="dash-sec">
                        <h2>Today and overall</h2>
                        <div class="dash-kpis">
                            <div class="dash-kpi"><span>Today's registered</span><strong><?php echo number_format((int)$todayRegistered); ?></strong></div>
                            <div class="dash-kpi"><span>Today's payments</span><strong><?php echo number_format((int)$todayPayments); ?></strong></div>
                            <div class="dash-kpi"><span>Total registered</span><strong><?php echo number_format((int)$totalRegistered); ?></strong></div>
                            <div class="dash-kpi"><span>Total payments</span><strong><?php echo number_format((int)$totalPayments); ?></strong></div>
                            <?php if ($role == 'admin'): ?>
                                <div class="dash-kpi"><span>Database students</span><strong><?php echo number_format((int)$oldStudentCount); ?></strong></div>
                                <div class="dash-kpi"><span>Free tickets</span><strong><?php echo number_format((int)$totalFreeTickets); ?></strong></div>
                                <div class="dash-kpi"><span>Extra tickets sold</span><strong><?php echo number_format((int)$totalExtraTickets); ?></strong></div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <?php if ($role == 'admin'): ?>
                        <section class="dash-sec">
                            <h2>Tickets by session</h2>
                            <?php if (!$sessionData): ?>
                                <p class="dash-empty">No sessions are set in the programme list yet.</p>
                            <?php else: ?>
                                <div class="dash-grid">
                                    <?php foreach ($sessionData as $code => $d):
                                        $extraTotal = $d['base_extra'] + $d['log_extra'];
                                        $total = $d['paid'] + $d['free'] + $extraTotal; ?>
                                        <article class="dash-card tone tone-<?php echo $toneOf($code); ?>">
                                            <div class="dash-card-head">
                                                <span class="dash-chip"><?php echo htmlspecialchars($d['label']); ?></span>
                                            </div>
                                            <dl class="dash-rows">
                                                <div>
                                                    <dt>Registered students</dt>
                                                    <dd><?php echo $d['registered']; ?></dd>
                                                </div>
                                                <div>
                                                    <dt>Paid students</dt>
                                                    <dd><?php echo $d['paid']; ?></dd>
                                                </div>
                                                <div>
                                                    <dt>Free tickets</dt>
                                                    <dd><?php echo $d['free']; ?></dd>
                                                </div>
                                                <div>
                                                    <dt>Extra tickets <small>receipts + added at desk</small></dt>
                                                    <dd><?php echo $d['base_extra']; ?> + <?php echo $d['log_extra']; ?> = <b><?php echo $extraTotal; ?></b></dd>
                                                </div>
                                            </dl>
                                            <?php if ($d['log_legacy'] > 0): ?>
                                                <p class="dash-note"><?php echo (int)$d['log_legacy']; ?> older desk ticket(s) saved without a session are counted here.</p>
                                            <?php endif; ?>
                                            <div class="dash-total"><span>Total (paid + free + extra)</span><strong><?php echo $total; ?></strong></div>
                                            <form action="export_session_data.php" method="POST">
                                                <input type="hidden" name="session" value="<?php echo htmlspecialchars($code); ?>">
                                                <button type="submit" class="dash-btn"><i class="fas fa-file-csv" aria-hidden="true"></i> Export <?php echo htmlspecialchars($d['label']); ?> (CSV)</button>
                                            </form>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($unlinkedExtra > 0): ?>
                                    <p class="dash-note mt">Not in any session: <?php echo $unlinkedExtra; ?> extra ticket(s) from receipts of students whose programme has no session.</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </section>

                        <section class="dash-sec">
                            <div class="dash-sec-head">
                                <h2>Recent notifications</h2>
                                <span class="dash-pill">Unread: <?php echo (int)$totalNotifications; ?></span>
                            </div>
                            <div class="dash-card flush">
                                <?php if (count($notifications) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table dash-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Table</th>
                                                    <th>Student ID</th>
                                                    <th>Action</th>
                                                    <th>Description</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($notifications as $index => $note):
                                                    $action = htmlspecialchars($note['action_type']);
                                                    $cls = ($action == 'INSERT') ? 'ins' : (($action == 'UPDATE') ? 'upd' : 'oth'); ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td><span class="dash-tag"><?php echo htmlspecialchars($note['table_name']); ?></span></td>
                                                        <td><?php echo htmlspecialchars($note['student_id']); ?></td>
                                                        <td><span class="dash-tag <?php echo $cls; ?>"><?php echo $action; ?></span></td>
                                                        <td><?php echo htmlspecialchars($note['description']); ?></td>
                                                        <td class="nowrap"><?php echo htmlspecialchars($note['created_at']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="dash-foot"><a href="notifications.php">View all notifications</a></div>
                                <?php else: ?>
                                    <p class="dash-empty m">No notifications available.</p>
                                <?php endif; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('body').addClass('dashboard-page');

        const sidebarOverlay = $('#sidebarOverlay');

        $('#sidebarToggleMobile').on('click', function() {
            $('body').toggleClass('sidebar-open');
        });

        sidebarOverlay.on('click', function() {
            $('body').removeClass('sidebar-open');
        });
    });
</script>

</body>

</html>