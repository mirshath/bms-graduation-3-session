<?php
session_start();


if (!isset($_SESSION['admin_id']) || $_SESSION['admin_name'] === 'Unknown Admin') {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");

$summaryQuery = $conn->query("
    SELECT 
        SUM(is_payment) AS total_payments,
        SUM(grad_fee) AS total_graduation_fee,
        SUM(extra_fee) AS total_extra_ticket_fee,
        SUM(total_amt) AS total_collected,
        SUM(extra_qty) AS total_extra_tickets_sold
    FROM (
        SELECT 
            1 AS is_payment,
            graduation_fee AS grad_fee,
            extra_ticket_fee AS extra_fee,
            total_amount AS total_amt,
            extra_ticket_count AS extra_qty,
            payment_date AS summary_date
        FROM payment_records
        
        UNION ALL
        
        SELECT 
            0 AS is_payment,
            0 AS grad_fee,
            total_added AS extra_fee,
            total_added AS total_amt,
            added_tickets AS extra_qty,
            added_on AS summary_date
        FROM extra_ticket_log
    ) AS combined_summary
    WHERE DATE(summary_date) = CURDATE()
");
$summary = $summaryQuery->fetch_assoc();

$adminId = intval($_SESSION['admin_id'] ?? 0);

// inlcuded additionally for the today summary 
$adminIdStr = strval($adminId);

$adminName = $_SESSION['admin_name'] ?? 'Unknown Admin';
$myStmt = $conn->prepare("
    SELECT 
        SUM(is_payment) AS total_payments,
        SUM(grad_fee) AS total_graduation_fee,
        SUM(extra_fee) AS total_extra_ticket_fee,
        SUM(total_amt) AS total_collected,
        SUM(extra_qty) AS total_extra_tickets
    FROM (
        SELECT 
            1 AS is_payment,
            graduation_fee AS grad_fee,
            extra_ticket_fee AS extra_fee,
            total_amount AS total_amt,
            extra_ticket_count AS extra_qty,
            payment_date AS summary_date
        FROM payment_records
        
        WHERE (created_by = ? OR created_by = ?)
        
        UNION ALL
        
        SELECT 
            0 AS is_payment,
            0 AS grad_fee,
            total_added AS extra_fee,
            total_added AS total_amt,
            added_tickets AS extra_qty,
            added_on AS summary_date
        FROM extra_ticket_log
        WHERE (added_by = ? OR added_by = ?)
    ) AS combined_summary
    WHERE DATE(summary_date) = CURDATE()
");
// $myStmt->bind_param("ii", $adminId, $adminId);

// inlcuded for the today summary 
$myStmt->bind_param("ssii", $adminName, $adminIdStr, $adminId, $adminId);

$myStmt->execute();
$myResult = $myStmt->get_result();
$mySummary = $myResult->fetch_assoc();


// ---- display helpers (design only) ----
$fmt = function ($v) {
    return number_format((float)($v ?? 0), 2);
};
$todayLabel   = date('l, j F Y');
$adminInitial = strtoupper(substr(trim((string)$adminName), 0, 1));
if ($adminInitial === '') {
    $adminInitial = 'A';
}
?>

<script>
    function checkSession() {
        fetch('check_session.php')
            .then(response => response.json())
            .then(data => {
                if (!data.valid) {
                    // Redirect instantly if session is invalid or Unknown Admin
                    window.location.href = 'login';
                }
            })
            .catch(error => console.error('Session check failed:', error));
    }

    // Check every 5 seconds (5000 ms)
    setInterval(checkSession, 5000);
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&family=Geist+Mono:wght@400;500;600&display=swap"
    rel="stylesheet">

<style>
    .pr {
        --ink: #17233d;
        --muted: #5f6b7e;
        --line: #e2e6ee;
        --soft: #f4f6fb;
        --brand: #1f4bb6;
        --brand-d: #173a91;
        --brand-bg: #e8eefb;
        --veg: #2f9e66;
        --veg-bg: #e6f4ec;
        --veg-ink: #17663f;
        --non: #d0453c;
        --non-bg: #fdecea;
        --non-ink: #9c2a22;
        --ease: cubic-bezier(0.16, 1, 0.3, 1);

        font-family: 'Public Sans', system-ui, sans-serif;
        color: var(--ink);
        max-width: 1240px;
        margin: 0 auto;
        padding: 28px 24px 48px;
        -webkit-font-smoothing: antialiased;
    }

    .pr *,
    .pr *::before,
    .pr *::after {
        box-sizing: border-box;
    }

    .pr [style*="--i"] {
        animation: pr-rise .6s var(--ease) backwards;
        animation-delay: calc(var(--i) * 70ms);
    }

    @keyframes pr-rise {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
    }

    .pr-mono {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-variant-numeric: tabular-nums;
    }

    /* ---------- page head ---------- */
    .pr-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 26px;
    }

    .pr-eyebrow {
        margin: 0 0 6px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--brand);
    }

    .pr-title {
        margin: 0;
        font-size: clamp(26px, 3.4vw, 36px);
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.1;
        color: var(--ink);
    }

    .pr-date {
        margin: 6px 0 0;
        font-size: 14px;
        color: var(--muted);
    }

    .pr-admin {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 6px 14px 6px 6px;
        border: 1px solid var(--line);
        border-radius: 999px;
        background: #fff;
        font-size: 13px;
        font-weight: 600;
    }

    .pr-avatar {
        display: grid;
        place-items: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--brand);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
    }

    /* ---------- section label ---------- */
    .pr-label {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 12px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.02em;
        color: var(--ink);
    }

    .pr-label small {
        font-weight: 500;
        color: var(--muted);
    }

    /* ---------- KPI bento ---------- */
    .pr-kpis {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 30px;
    }

    .pr-kpi {
        grid-column: span 4;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 18px;
        padding: 20px 22px;
        border: 1px solid var(--line);
        border-radius: 14px;
        background: #fff;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s var(--ease);
    }

    .pr-kpi:hover {
        border-color: #c9d0de;
        box-shadow: 0 6px 18px rgba(23, 35, 61, 0.07);
        transform: translateY(-2px);
    }

    .pr-kpi-top {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pr-ico {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 14px;
    }

    .pr-kpi-name {
        font-size: 13px;
        font-weight: 500;
        color: var(--muted);
        line-height: 1.3;
    }

    .pr-kpi-val {
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 6px;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: clamp(24px, 2.6vw, 32px);
        font-weight: 600;
        letter-spacing: -0.03em;
        font-variant-numeric: tabular-nums;
        line-height: 1.1;
        color: var(--ink);
    }

    .pr-cur {
        font-family: 'Public Sans', system-ui, sans-serif;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.04em;
        color: var(--muted);
    }

    .pr-kpi--hero {
        grid-row: span 2;
        background: var(--ink);
        border-color: var(--ink);
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .pr-kpi--hero::after {
        content: "";
        position: absolute;
        right: -60px;
        top: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(31, 75, 182, .55), transparent 70%);
        pointer-events: none;
    }

    .pr-kpi--hero:hover {
        border-color: var(--ink);
        box-shadow: 0 10px 26px rgba(23, 35, 61, 0.28);
    }

    .pr-kpi--hero .pr-ico {
        background: rgba(255, 255, 255, .12);
        color: #fff;
    }

    .pr-kpi--hero .pr-kpi-name,
    .pr-kpi--hero .pr-cur {
        color: rgba(255, 255, 255, .68);
    }

    .pr-kpi--hero .pr-kpi-val {
        color: #fff;
        font-size: clamp(30px, 3.4vw, 42px);
    }

    .pr-kpi--hero .pr-kpi-foot {
        font-size: 12px;
        color: rgba(255, 255, 255, .6);
    }

    /* ---------- my summary ---------- */
    .pr-me {
        margin-bottom: 36px;
        border: 1px solid var(--line);
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
    }

    .pr-me-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 22px;
        border-bottom: 1px solid var(--line);
        background: var(--soft);
    }

    .pr-me-who {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pr-me-who .pr-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
    }

    .pr-me-name {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.2;
    }

    .pr-me-sub {
        margin: 2px 0 0;
        font-size: 12px;
        color: var(--muted);
    }

    .pr-me-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }

    .pr-me-cell {
        padding: 18px 22px;
        border-right: 1px solid var(--line);
        min-width: 0;
    }

    .pr-me-cell:last-child {
        border-right: 0;
        background: var(--brand-bg);
    }

    .pr-me-cell:last-child .pr-me-k {
        color: var(--brand-d);
    }

    .pr-me-k {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 500;
        color: var(--muted);
    }

    .pr-me-v {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 19px;
        font-weight: 600;
        letter-spacing: -0.02em;
        font-variant-numeric: tabular-nums;
        overflow-wrap: anywhere;
    }

    .pr-me-cell:last-child .pr-me-v {
        color: var(--brand-d);
    }

    /* ---------- scan desk ---------- */
    .pr-scan {
        max-width: 760px;
        margin: 0 auto 34px;
        padding: clamp(26px, 4vw, 44px);
        border: 1px solid var(--line);
        border-radius: 18px;
        text-align: center;
        background:
            radial-gradient(40rem 14rem at 50% -30%, rgba(31, 75, 182, .09), transparent 70%),
            #fff;
    }

    .pr-scan-mark {
        display: inline-grid;
        place-items: center;
        width: 56px;
        height: 56px;
        margin-bottom: 16px;
        border-radius: 16px;
        background: var(--brand);
        color: #fff;
        font-size: 24px;
    }

    .pr-scan-title {
        margin: 0;
        font-size: clamp(26px, 4vw, 40px);
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.08;
        color: var(--ink);
        text-wrap: balance;
    }

    .pr-scan-sub {
        margin: 10px auto 26px;
        max-width: 46ch;
        font-size: 14px;
        color: var(--muted);
        text-wrap: pretty;
    }

    .pr-input {
        display: block;
        width: 100%;
        padding: 18px 20px;
        border: 1.5px solid #c9d0de;
        border-radius: 12px;
        background: #fff;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 26px;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-align: center;
        color: var(--ink);
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .pr-input::placeholder {
        font-family: 'Public Sans', system-ui, sans-serif;
        font-size: 16px;
        font-weight: 400;
        letter-spacing: 0;
        color: #9aa4b5;
    }

    .pr-input:hover {
        border-color: var(--brand);
    }

    .pr-input:focus {
        outline: 0;
        border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(31, 75, 182, .14);
    }

    .pr-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        margin-top: 14px;
        padding: 15px 22px;
        border: 0;
        border-radius: 10px;
        background: var(--brand);
        color: #fff;
        font: inherit;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color .2s ease, transform .15s ease;
    }

    .pr-btn:hover {
        background: var(--brand-d);
    }

    .pr-btn:active {
        transform: scale(0.99);
    }

    .pr-btn:focus-visible {
        outline: 2px solid var(--brand);
        outline-offset: 3px;
    }

    .pr-hint {
        margin: 14px 0 0;
        font-size: 12px;
        color: var(--muted);
    }

    .pr-kbd {
        display: inline-block;
        padding: 1px 7px;
        border: 1px solid var(--line);
        border-bottom-width: 2px;
        border-radius: 5px;
        background: var(--soft);
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 11px;
        color: var(--ink);
    }

    /* ---------- results / notices / error modal (theme colors) ---------- */
    #result {
        margin-top: 4px;
    }

    #result .alert,
    #errorModal .alert {
        margin: 0;
        padding: 30px 26px;
        border-radius: 14px;
        border: 1px solid var(--line, #e2e6ee);
        font-family: 'Public Sans', system-ui, sans-serif;
        color: #17233d;
    }

    #result .alert .fa-8x,
    #errorModal .alert .fa-8x {
        font-size: 3.2rem !important;
    }

    #result .alert .alert-heading,
    #errorModal .alert .alert-heading {
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    #result .alert h3,
    #errorModal .alert h3 {
        max-width: 34ch;
        margin: 6px auto 0;
        font-size: clamp(17px, 2vw, 21px);
        font-weight: 700;
        letter-spacing: -0.01em;
        line-height: 1.35;
    }

    #result .alert-warning {
        background: #e8eefb;
        border-color: #cfdaf5;
    }

    #result .alert-warning .alert-heading {
        color: #1f4bb6;
    }

    #result .alert-warning .fa-info-circle {
        color: #1f4bb6 !important;
    }

    #result .alert-warning .btn-success {
        margin-top: 18px !important;
        padding: 11px 20px;
        border: 0;
        border-radius: 10px;
        background: #1f4bb6;
        font-weight: 600;
    }

    #result .alert-warning .btn-success:hover {
        background: #173a91;
    }

    #errorModal .alert-danger {
        background: #fdecea;
        border-color: #f5c9c5;
    }

    #errorModal .alert-danger .alert-heading {
        color: #9c2a22;
    }

    #errorModal .alert-danger .fa-exclamation-circle {
        color: #d0453c !important;
    }

    #errorModal .modal-content {
        border: 0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(23, 35, 61, .25);
    }

    .margin_btm {
        margin-bottom: 0px;
    }

    /* ---------- responsive ---------- */
    @media (max-width: 991px) {
        .pr-kpi {
            grid-column: span 6;
        }

        .pr-kpi--hero {
            grid-column: span 12;
            grid-row: auto;
        }

        .pr-me-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .pr-me-cell {
            border-bottom: 1px solid var(--line);
        }

        .pr-me-cell:last-child {
            grid-column: span 2;
            border-bottom: 0;
        }
    }

    @media (max-width: 575px) {
        .pr {
            padding: 18px 14px 36px;
        }

        .pr-kpi {
            grid-column: span 12;
        }

        .pr-me-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .pr-me-cell,
        .pr-me-cell:last-child {
            grid-column: auto;
            border-right: 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .pr [style*="--i"] {
            animation: none;
        }

        .pr-kpi,
        .pr-btn,
        .pr-input {
            transition: none;
        }
    }
</style>

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
            <div class="pr">

                <!-- Page head -->
                <header class="pr-head" style="--i:0">
                    <div>
                        <p class="pr-eyebrow">Graduation 2026</p>
                        <h1 class="pr-title">Payment desk</h1>
                        <p class="pr-date"><?php echo htmlspecialchars($todayLabel); ?></p>
                    </div>
                    <span class="pr-admin" title="Signed in admin">
                        <span class="pr-avatar" aria-hidden="true"><?php echo htmlspecialchars($adminInitial); ?></span>
                        <?php echo htmlspecialchars($adminName); ?>
                    </span>
                </header>

                <!-- Today: all counters -->
                <h2 class="pr-label" style="--i:1">Today's collections <small>all admins</small></h2>
                <section class="pr-kpis" aria-label="Today's collections">

                    <!-- Total Amount Collected -->
                    <div class="pr-kpi pr-kpi--hero" style="--i:1">
                        <div class="pr-kpi-top">
                            <span class="pr-ico" aria-hidden="true"><i class="fas fa-wallet"></i></span>
                            <span class="pr-kpi-name">Total amount collected</span>
                        </div>
                        <div>
                            <div class="pr-kpi-val"><span class="pr-cur">LKR</span><?php echo $fmt($summary['total_collected'] ?? 0); ?></div>
                            <div class="pr-kpi-foot" style="margin-top:8px;">Graduation fees plus extra tickets, today</div>
                        </div>
                    </div>

                    <!-- Total Payments -->
                    <div class="pr-kpi" style="--i:2">
                        <div class="pr-kpi-top">
                            <span class="pr-ico" aria-hidden="true"><i class="fas fa-receipt"></i></span>
                            <span class="pr-kpi-name">Total payments</span>
                        </div>
                        <div class="pr-kpi-val"><?php echo (int)($summary['total_payments'] ?? 0); ?></div>
                    </div>

                    <!-- Graduation Fees Collected -->
                    <div class="pr-kpi" style="--i:3">
                        <div class="pr-kpi-top">
                            <span class="pr-ico" aria-hidden="true"><i class="fas fa-graduation-cap"></i></span>
                            <span class="pr-kpi-name">Graduation fees collected</span>
                        </div>
                        <div class="pr-kpi-val"><span class="pr-cur">LKR</span><?php echo $fmt($summary['total_graduation_fee'] ?? 0); ?></div>
                    </div>

                    <!-- Extra Tickets Sold -->
                    <div class="pr-kpi" style="--i:4">
                        <div class="pr-kpi-top">
                            <span class="pr-ico" aria-hidden="true"><i class="fas fa-ticket-alt"></i></span>
                            <span class="pr-kpi-name">Extra tickets sold</span>
                        </div>
                        <div class="pr-kpi-val"><?php echo (int)($summary['total_extra_tickets_sold'] ?? 0); ?></div>
                    </div>

                    <!-- Extra Ticket Fees Collected -->
                    <div class="pr-kpi" style="--i:5">
                        <div class="pr-kpi-top">
                            <span class="pr-ico" aria-hidden="true"><i class="fas fa-coins"></i></span>
                            <span class="pr-kpi-name">Extra ticket fees collected</span>
                        </div>
                        <div class="pr-kpi-val"><span class="pr-cur">LKR</span><?php echo $fmt($summary['total_extra_ticket_fee'] ?? 0); ?></div>
                    </div>

                </section>

                <!-- My Today Summary -->
                <section class="pr-me" style="--i:6" aria-label="My today summary">
                    <div class="pr-me-head">
                        <div class="pr-me-who">
                            <span class="pr-avatar" aria-hidden="true"><?php echo htmlspecialchars($adminInitial); ?></span>
                            <div>
                                <p class="pr-me-name"><?php echo htmlspecialchars($adminName); ?></p>
                                <p class="pr-me-sub">My summary today</p>
                            </div>
                        </div>
                    </div>
                    <div class="pr-me-grid">
                        <div class="pr-me-cell">
                            <span class="pr-me-k">Payments</span>
                            <span class="pr-me-v"><?php echo (int)($mySummary['total_payments'] ?? 0); ?></span>
                        </div>
                        <div class="pr-me-cell">
                            <span class="pr-me-k">Graduation fees collected</span>
                            <span class="pr-me-v"><?php echo $fmt($mySummary['total_graduation_fee'] ?? 0); ?></span>
                        </div>
                        <div class="pr-me-cell">
                            <span class="pr-me-k">Extra tickets sold</span>
                            <span class="pr-me-v"><?php echo (int)($mySummary['total_extra_tickets'] ?? 0); ?></span>
                        </div>
                        <div class="pr-me-cell">
                            <span class="pr-me-k">Extra ticket fees collected</span>
                            <span class="pr-me-v"><?php echo $fmt($mySummary['total_extra_ticket_fee'] ?? 0); ?></span>
                        </div>
                        <div class="pr-me-cell">
                            <span class="pr-me-k">Total collected (LKR)</span>
                            <span class="pr-me-v"><?php echo $fmt($mySummary['total_collected'] ?? 0); ?></span>
                        </div>
                    </div>
                </section>

                <!-- Scan desk -->
                <section class="pr-scan" style="--i:7">
                    <form id="studentIDForm" autocomplete="off">
                        <span class="pr-scan-mark" aria-hidden="true"><i class="fas fa-qrcode"></i></span>
                        <h2 class="pr-scan-title">Scan QR for payment</h2>
                        <p class="pr-scan-sub">Scan the student's invitation QR, or type the student ID, then confirm.</p>

                        <input type="text" class="pr-input" id="studentID" name="studentID"
                            placeholder="Student ID" aria-label="Student ID" required>

                        <button type="button" class="pr-btn" onclick="checkStudentID()">
                            <i class="fas fa-search" aria-hidden="true"></i> Scan for payment
                        </button>
                        <p class="pr-hint">Press <span class="pr-kbd">Enter</span> after scanning to continue</p>
                    </form>

                    <script>
                        // Trigger checkStudentID function when Enter key is pressed in the input field
                        $('#studentID').on('keypress', function(event) {
                            if (event.which === 13) { // 13 is the Enter key
                                event.preventDefault(); // Prevent the default form submission
                                checkStudentID(); // Call the function
                            }
                        });
                    </script>
                </section>

                <!-- Result Div -->
                <div id="result"></div>

            </div>

            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

<script>
    // Function to check student ID
    function checkStudentID() {
        var student_id = $('#studentID').val();

        $.ajax({
            url: 'Payment_Check_and_Id_check.php',
            method: 'POST',
            data: {
                student_id: student_id
            },
            success: function(response) {
                $('#result').html(response);
                // bring the result into view so the desk always lands on it
                var r = document.getElementById('result');
                if (r && r.firstElementChild) {
                    r.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    }

    // Focus the scan field on page load
    $(document).ready(function() {
        $('#studentID').focus();
        if (typeof fetchAttendedUsers === 'function') {
            fetchAttendedUsers();
        }
    });
</script>


<!-- -------------------------------------------------------------------------  -->
<!-- modal section here  -->
<!-- -------------------------------------------------------------------------  -->

<!-- Bootstrap Modal for Error Messages -->
<div class="modal fade margin_btm" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel">
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-heade">
                <!-- <h5 class="modal-title" id="errorModalLabel">Error</h5> -->
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-bod" id="modalErrorMessage">
                <!-- The error message will be inserted here dynamically -->
            </div>
            <div class="modal-foote">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
            </div>
        </div>
    </div>
</div>


<!-- -------------------------------------------------------------------------  -->
<!-- -------------------------------------------------------------------------  -->


<!-- Page level plugins -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">

<!-- Page level custom scripts -->
<script src="js/demo/datatables-demo.js"></script>

</body>

</html>