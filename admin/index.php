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
<style>
    body.dashboard-page {
        overflow-x: hidden;
    }

    #sidebarToggleMobile {
        border-radius: 12px;
        font-weight: 600;
    }

    #sidebarOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease;
        z-index: 1039;
    }

    body.sidebar-open #sidebarOverlay {
        opacity: 1;
        visibility: visible;
    }

    @media (max-width: 991.98px) {
        body.dashboard-page #accordionSidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1040;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        body.dashboard-page.sidebar-open #accordionSidebar {
            transform: translateX(0);
        }

        body.dashboard-page #content-wrapper {
            margin-left: 0 !important;
        }
    }

    @media (max-width: 576px) {
        .card {
            border-radius: 12px;
        }

        .table-responsive {
            overflow-x: auto;
        }
    }
</style>
<link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,600&family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@500;600;700&display=swap" rel="stylesheet">
<style>
    .dash {
        --ink: #17233d;
        --muted: #5f6b7e;
        --line: #e2e6ee;
        --soft: #f4f6fb;
        --brand: #1f4bb6;
        --brand-bg: #e8eefb;
        --ok: #1b7f4b;
        --ok-bg: #e6f4ec;
        --bad: #c0372f;
        font-family: 'Public Sans', system-ui, sans-serif;
        color: var(--ink);
        max-width: 1180px;
        margin: 0 auto;
        padding-bottom: 56px;
        -webkit-font-smoothing: antialiased;
    }

    .dash * {
        box-sizing: border-box;
    }

    .dash-head {
        margin-bottom: 26px;
    }

    .dash-head h1 {
        font-family: 'Newsreader', Georgia, serif;
        font-weight: 600;
        font-size: 2.1rem;
        letter-spacing: -.02em;
        margin: 0 0 4px;
    }

    .dash-head p {
        margin: 0;
        color: var(--muted);
    }

    .dash-sec {
        margin-bottom: 30px;
    }

    .dash-sec h2,
    .dash-sec-head h2 {
        font-size: 1.05rem;
        font-weight: 700;
        letter-spacing: -.01em;
        margin: 0 0 14px;
    }

    .dash-sec-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
    }

    .dash-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
        gap: 16px;
    }

    .dash-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 20px 22px;
        min-width: 0;
    }

    .dash-card.flush {
        padding: 0;
        overflow: hidden;
    }

    .dash-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .dash-chip {
        display: inline-block;
        padding: 4px 13px;
        border-radius: 999px;
        background: var(--brand-bg);
        color: var(--brand);
        font-weight: 700;
        font-size: .9rem;
    }

    .dash-pct {
        font-size: .8rem;
        font-weight: 600;
        color: var(--muted);
    }

    .dash-nums {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 16px;
    }

    .dash-nums span {
        display: block;
        font-size: .78rem;
        color: var(--muted);
        margin-bottom: 4px;
    }

    .dash-nums strong,
    .dash-kpi strong,
    .dash-rows dd,
    .dash-total strong {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-variant-numeric: tabular-nums;
    }

    .dash-nums strong {
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: -.04em;
        line-height: 1.1;
    }

    .dash-nums .ok {
        color: var(--ok);
    }

    .dash-nums .bad {
        color: var(--bad);
    }

    .dash-bar {
        height: 8px;
        border-radius: 4px;
        background: #eceff5;
        overflow: hidden;
    }

    .dash-bar i {
        display: block;
        height: 100%;
        background: var(--ok);
        border-radius: 4px;
    }

    .dash-kpis {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 14px;
    }

    .dash-kpi {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 16px 20px;
    }

    .dash-kpi span {
        display: block;
        font-size: .84rem;
        color: var(--muted);
        margin-bottom: 6px;
    }

    .dash-kpi strong {
        font-size: 1.9rem;
        font-weight: 700;
        letter-spacing: -.04em;
    }

    .dash-rows {
        margin: 0 0 6px;
    }

    .dash-rows>div {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        padding: 9px 0;
        border-bottom: 1px solid var(--line);
    }

    .dash-rows dt {
        font-weight: 500;
        font-size: .92rem;
    }

    .dash-rows dt small {
        display: block;
        font-weight: 400;
        font-size: .74rem;
        color: var(--muted);
    }

    .dash-rows dd {
        margin: 0;
        font-weight: 600;
        white-space: nowrap;
    }

    .dash-rows dd b {
        font-weight: 700;
        color: var(--brand);
    }

    .dash-note {
        margin: 10px 0 0;
        padding: 8px 12px;
        border-radius: 8px;
        background: #fff3c4;
        color: #6b4a00;
        font-size: .78rem;
    }

    .dash-note.mt {
        margin-top: 14px;
    }

    .dash-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 14px 0 16px;
        padding: 14px 16px;
        border-radius: 12px;
        background: var(--brand);
        color: #fff;
    }

    .dash-total span {
        font-size: .84rem;
        font-weight: 600;
    }

    .dash-total strong {
        font-size: 1.9rem;
        font-weight: 700;
        letter-spacing: -.04em;
    }

    .dash-btn {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 42px;
        font: inherit;
        font-weight: 600;
        font-size: .9rem;
        color: var(--ink);
        background: #fff;
        border: 1px solid #c4ccd9;
        border-radius: 8px;
        cursor: pointer;
        transition: background .15s, transform .1s;
    }

    .dash-btn:hover {
        background: #f1f4f9;
    }

    .dash-btn:active {
        transform: scale(.98);
    }

    .dash-btn:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    .dash-pill {
        padding: 3px 12px;
        border-radius: 999px;
        background: var(--soft);
        font-size: .8rem;
        font-weight: 600;
        color: var(--muted);
    }

    .dash-table {
        margin: 0 !important;
        font-size: .9rem;
    }

    .dash-table thead th {
        font-size: .78rem;
        font-weight: 600;
        color: var(--muted);
        background: var(--soft);
        border-bottom: 1px solid var(--line) !important;
        padding: 12px 16px;
        white-space: nowrap;
    }

    .dash-table td {
        padding: 12px 16px;
        border-color: var(--line);
        vertical-align: middle;
    }

    .dash-table .nowrap {
        white-space: nowrap;
        color: var(--muted);
    }

    .dash-tag {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 999px;
        background: var(--soft);
        font-size: .76rem;
        font-weight: 600;
    }

    .dash-tag.ins {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .dash-tag.upd {
        background: #fff3c4;
        color: #6b4a00;
    }

    .dash-foot {
        padding: 12px 16px;
        text-align: center;
        border-top: 1px solid var(--line);
    }

    .dash-foot a {
        font-weight: 600;
        color: var(--brand);
        text-decoration: none;
    }

    .dash-empty {
        color: var(--muted);
        margin: 0;
    }

    .dash-empty.m {
        padding: 26px;
        text-align: center;
    }

    /* attendance cards: soft shadow + a light tint per session */
    .dash-card.tone {
        --t: 31, 75, 182;
        --tc: #1f4bb6;
        background: rgba(var(--t), .11);
        border-color: rgba(var(--t), .30);
        box-shadow: 0 10px 26px -12px rgba(23, 35, 61, .22), 0 2px 6px rgba(23, 35, 61, .05);
        transition: box-shadow .2s, transform .2s;
    }

    .dash-card.tone:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 32px -12px rgba(23, 35, 61, .28), 0 3px 8px rgba(23, 35, 61, .06);
    }

    .dash-card.tone .dash-chip {
        background: rgba(var(--t), .16);
        color: var(--tc);
    }

    .dash-card.tone .dash-bar {
        background: rgba(255, 255, 255, .75);
    }

    .dash-card.tone .dash-bar i {
        background: var(--tc);
    }

    .dash-card.tone .dash-rows>div {
        border-bottom-color: rgba(var(--t), .24);
    }

    .dash-card.tone .dash-rows dd b {
        color: var(--tc);
    }

    .dash-card.tone .dash-total {
        background: var(--tc);
    }

    .dash-card.tone .dash-btn {
        background: rgba(255, 255, 255, .85);
    }

    .dash-card.tone .dash-btn:hover {
        background: #fff;
    }

    .tone-pink {
        --t: 236, 72, 153;
        --tc: #c02a74;
    }

    .tone-purple {
        --t: 124, 58, 237;
        --tc: #6a35c9;
    }

    .tone-green {
        --t: 22, 163, 74;
        --tc: #1b8a4c;
    }

    .tone-blue {
        --t: 31, 75, 182;
        --tc: #1f4bb6;
    }

    .tone-amber {
        --t: 217, 119, 6;
        --tc: #b25f05;
    }

    @media (prefers-reduced-motion: reduce) {
        .dash-card.tone {
            transition: none;
        }

        .dash-card.tone:hover {
            transform: none;
        }
    }

    @media (max-width: 575px) {
        .dash-head h1 {
            font-size: 1.7rem;
        }

        .dash-grid {
            grid-template-columns: 1fr;
        }

        .dash-nums strong {
            font-size: 1.7rem;
        }

        .dash-kpis {
            grid-template-columns: 1fr 1fr;
        }

        .dash-kpi {
            padding: 14px 16px;
        }

        .dash-kpi strong {
            font-size: 1.6rem;
        }
    }
</style>
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