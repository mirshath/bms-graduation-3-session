<?php
session_start();
if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_name'] ?? '') === 'Unknown Admin') {
    header("Location: login");
    exit();
}

include("../database/connection.php");

// token for the "add" request
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

// Optional: link to your payment page (e.g. 'payment.php'). Leave '' to hide the link.
$paymentUrl = '';

// extra_ticket_log may not have the new columns yet (add_extra_ticket_log_columns.sql)
$sel = [];
$columnsMissing = false;
foreach (['student_name', 'program_name', 'session'] as $c) {
    $r = $conn->query("SHOW COLUMNS FROM `extra_ticket_log` LIKE '$c'");
    $has = ($r && $r->num_rows > 0);
    $sel[] = $has ? "e.`$c`" : "NULL AS `$c`";
    if (!$has) {
        $columnsMissing = true;
    }
}
$selSql = implode(', ', $sel);

$todayRows = [];
$totalTicketsToday = 0;
$totalAmountToday = 0.0;
$resToday = $conn->query("
    SELECT e.id, e.student_id, $selSql, e.added_tickets, e.ticket_price, e.total_added, e.added_on,
           COALESCE(a.admin_name, e.added_by) AS added_by_name
    FROM extra_ticket_log e
    LEFT JOIN admin a ON e.added_by REGEXP '^[0-9]+$' AND a.id = CAST(e.added_by AS UNSIGNED)
    WHERE DATE(e.added_on) = CURDATE()
    ORDER BY e.added_on DESC
");
if ($resToday) {
    while ($r = $resToday->fetch_assoc()) {
        $todayRows[] = $r;
        $totalTicketsToday += (int)$r['added_tickets'];
        $totalAmountToday += (float)$r['total_added'];
    }
}

function et_session_label($v)
{
    $v = trim((string)$v);
    return $v === '' ? '-' : ucwords(strtolower(str_replace('_', ' ', $v)));
}

include("includes/header.php");
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,600&family=Public+Sans:wght@400;500;600;700;800&family=Geist+Mono:wght@500;600;700&display=swap" rel="stylesheet">

<style>
    body.dashboard-page {
        overflow-x: hidden;
    }

    #sidebarOverlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .4);
        opacity: 0;
        visibility: hidden;
        transition: opacity .3s;
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
            transition: transform .3s;
            z-index: 1040;
            overflow-y: auto;
        }

        body.dashboard-page.sidebar-open #accordionSidebar {
            transform: translateX(0);
        }

        body.dashboard-page #content-wrapper {
            margin-left: 0 !important;
        }
    }

    .et {
        --ink: #17233d;
        --muted: #5f6b7e;
        --line: #e2e6ee;
        --soft: #f4f6fb;
        --brand: #1f4bb6;
        --brand-d: #173a91;
        --brand-bg: #e8eefb;
        --ok: #1b7f4b;
        --ok-bg: #e6f4ec;
        --bad: #c0372f;
        --bad-bg: #fdecea;
        font-family: 'Public Sans', system-ui, sans-serif;
        color: var(--ink);
        max-width: 1100px;
        margin: 0 auto;
        padding-bottom: 56px;
        -webkit-font-smoothing: antialiased;
    }

    .et * {
        box-sizing: border-box;
    }

    .et-mono {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-variant-numeric: tabular-nums;
    }

    .et-head h1 {
        font-family: 'Newsreader', Georgia, serif;
        font-weight: 600;
        font-size: 2rem;
        letter-spacing: -.02em;
        margin: 0 0 4px;
    }

    .et-head p {
        margin: 0 0 20px;
        color: var(--muted);
        max-width: 64ch;
    }

    .et-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 20px 22px;
        min-width: 0;
    }

    .et-warn {
        margin-bottom: 16px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #fff3c4;
        color: #6b4a00;
        font-size: .9rem;
    }

    /* scan */
    .et-scan {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 18px;
    }

    .et-scan label {
        font-weight: 600;
    }

    .et-row {
        display: flex;
        gap: 10px;
    }

    .et-row input {
        flex: 1;
        min-width: 0;
        height: 50px;
        padding: 0 14px;
        font: inherit;
        font-size: 1.05rem;
        color: var(--ink);
        border: 1px solid #c4ccd9;
        border-radius: 8px;
        outline: 0;
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
    }

    .et-row input:focus,
    .et-f select:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .18);
    }

    .et-hint {
        margin: 0;
        font-size: .86rem;
        color: var(--muted);
        min-height: 1.3em;
    }

    .et-hint.bad {
        color: var(--bad);
    }

    .et-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 50px;
        padding: 0 22px;
        font: inherit;
        font-weight: 700;
        font-size: .95rem;
        color: var(--ink);
        background: #fff;
        border: 1px solid #c4ccd9;
        border-radius: 8px;
        cursor: pointer;
        transition: background .15s, transform .1s;
    }

    .et-btn:hover {
        background: #f1f4f9;
    }

    .et-btn:active {
        transform: scale(.98);
    }

    .et-btn.solid {
        background: var(--brand);
        border-color: var(--brand);
        color: #fff;
    }

    .et-btn.solid:hover {
        background: var(--brand-d);
    }

    .et-btn.ok {
        background: var(--ok);
        border-color: var(--ok);
        color: #fff;
    }

    .et-btn.ok:hover {
        background: #166b3f;
    }

    .et-btn:disabled {
        opacity: .6;
        cursor: not-allowed;
    }

    .et-btn:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    .et-link {
        align-self: flex-start;
        padding: 0;
        border: 0;
        background: none;
        color: var(--brand);
        font: inherit;
        font-weight: 600;
        cursor: pointer;
    }

    .et-empty {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 30px 24px;
        margin-bottom: 18px;
        color: var(--muted);
        border: 1px dashed #c4ccd9;
        border-radius: 14px;
        background: #fff;
    }

    .et-empty i {
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: var(--soft);
        color: #7d8aa6;
        font-size: 18px;
    }

    .et-empty.bad {
        border-color: #f0c4c0;
        background: var(--bad-bg);
        color: #7a1b15;
    }

    .et-empty.bad i {
        background: #fff;
        color: var(--bad);
    }

    .et-empty strong {
        display: block;
        color: var(--ink);
    }

    /* result: the student's ticket */
    .et {
        --page: #f8f9fc;
    }

    .et [hidden] {
        display: none !important;
    }

    #result {
        scroll-margin-top: 14px;
        margin-bottom: 18px;
    }

    .et-k {
        display: block;
        margin-bottom: 6px;
        font-size: 12.5px;
        font-weight: 500;
        color: var(--muted);
    }

    .et-ticket {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        margin-bottom: 14px;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
        box-shadow: 0 1px 2px rgba(23, 35, 61, .05);
    }

    .et-main {
        display: flex;
        flex-direction: column;
        gap: 18px;
        min-width: 0;
        padding: 24px 26px;
    }

    .et-who {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .et-avatar {
        flex: none;
        display: grid;
        place-items: center;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 20px;
        font-weight: 700;
    }

    .et-name {
        margin: 0;
        font-size: clamp(20px, 2.6vw, 28px);
        font-weight: 800;
        letter-spacing: -.03em;
        line-height: 1.15;
        overflow-wrap: anywhere;
    }

    .et-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .et-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 4px 11px;
        border-radius: 999px;
        background: var(--soft);
        font-size: 12.5px;
        font-weight: 500;
    }

    .et-prog p {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        letter-spacing: -.015em;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .et-pay {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 12px;
    }

    .et-pay>i {
        flex: none;
        font-size: 20px;
    }

    .et-pay b {
        display: block;
        font-weight: 700;
    }

    .et-pay span {
        display: block;
        font-size: .85rem;
    }

    .et-pay.ok {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .et-pay.ok span {
        color: #2f5a44;
    }

    .et-pay.bad {
        background: var(--bad-bg);
        color: var(--bad);
    }

    .et-pay.bad span {
        color: #7a1b15;
    }

    .et-facts {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        padding-top: 16px;
        border-top: 1px solid var(--line);
    }

    .et-fact {
        padding: 0 16px;
        border-left: 1px solid var(--line);
    }

    .et-fact:first-child {
        padding-left: 0;
        border-left: 0;
    }

    .et-fact strong {
        display: block;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 1.6rem;
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.1;
    }

    .et-fact span {
        font-size: 12.5px;
        color: var(--muted);
    }

    /* session stub, torn off the ticket along a perforated edge */
    .et-stub {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 6px;
        padding: 26px 26px 26px 34px;
        background: var(--brand);
        color: #fff;
        border-left: 3px dashed #fff;
        border-radius: 0 15px 15px 0;
    }

    .et-stub::before,
    .et-stub::after {
        content: "";
        position: absolute;
        left: -14px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--page);
    }

    .et-stub::before {
        top: -12px;
    }

    .et-stub::after {
        bottom: -12px;
    }

    .et-stub .et-k {
        color: rgba(255, 255, 255, .82);
        margin: 0;
    }

    .et-stub strong {
        font-family: 'Newsreader', Georgia, serif;
        font-size: clamp(34px, 4.6vw, 50px);
        font-weight: 600;
        letter-spacing: -.02em;
        line-height: 1.05;
    }

    .et-stub small {
        color: rgba(255, 255, 255, .85);
        font-size: 13px;
    }

    .et-ticket.blocked .et-stub {
        background: #6b7587;
    }

    .et-ticket .et-stub.missing {
        background: var(--bad);
    }

    /* buying */
    .et-buy {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 28px;
    }

    .et-buy h2 {
        margin: 0 0 16px;
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .et-lab {
        display: block;
        margin-bottom: 8px;
        font-size: .86rem;
        font-weight: 600;
        color: var(--muted);
    }

    .et-qty {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 8px;
        max-width: 420px;
    }

    .et-qty label {
        position: relative;
        margin: 0;
    }

    .et-qty input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        opacity: 0;
        cursor: pointer;
    }

    .et-qty span {
        display: grid;
        place-items: center;
        height: 56px;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 1.3rem;
        font-weight: 700;
        background: #fff;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        transition: background .12s, color .12s, border-color .12s;
    }

    .et-qty input:hover:not(:disabled)+span {
        border-color: var(--brand);
    }

    .et-qty input:checked+span {
        background: var(--brand);
        border-color: var(--brand);
        color: #fff;
    }

    .et-qty input:focus-visible+span {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    .et-qty input:disabled+span {
        opacity: .5;
    }

    .et-price {
        margin: 14px 0 0;
        font-size: .88rem;
        color: var(--muted);
    }

    .et-price b {
        font-family: 'Geist Mono', ui-monospace, monospace;
        color: var(--ink);
    }

    .et-sum {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-radius: 12px;
        background: var(--soft);
    }

    .et-total {
        display: block;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.15;
    }

    .et-calc {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: .84rem;
        color: var(--muted);
    }

    #addBtn {
        width: 100%;
        height: 56px;
        font-size: 1.02rem;
    }

    /* no payment yet */
    .et-block {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 22px;
        border: 1px solid #f0c4c0;
        border-radius: 14px;
        background: var(--bad-bg);
    }

    .et-block>i {
        flex: none;
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #fff;
        color: var(--bad);
        font-size: 18px;
    }

    .et-block strong {
        display: block;
        margin-bottom: 2px;
        color: #7a1b15;
        font-size: 1.05rem;
    }

    .et-block p {
        margin: 0;
        color: #7a1b15;
        max-width: 60ch;
    }

    .et-block>div:nth-child(2) {
        flex: 1;
        min-width: 0;
    }

    .et-block-act {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .et-btn.link {
        text-decoration: none;
    }

    .et-dlg {
        width: min(440px, calc(100vw - 32px));
        padding: 0;
        border: 0;
        border-radius: 18px;
        color: var(--ink);
        box-shadow: 0 24px 60px rgba(23, 35, 61, .35);
    }

    .et-dlg::backdrop {
        background: rgba(23, 35, 61, .55);
    }

    .et-dlg form {
        padding: 28px 26px 24px;
    }

    .et-dlg-ic {
        display: grid;
        place-items: center;
        width: 52px;
        height: 52px;
        margin-bottom: 14px;
        border-radius: 50%;
        background: var(--bad-bg);
        color: var(--bad);
        font-size: 22px;
    }

    .et-dlg h2 {
        margin: 0 0 8px;
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .et-dlg p {
        margin: 0 0 22px;
        color: var(--muted);
        line-height: 1.5;
    }

    .et-dlg-act {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px;
    }

    .et-dlg-act .et-btn {
        height: 46px;
    }

    .et-dlg-ic.ok {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .et-dlg.ok {
        border-top: 6px solid var(--ok);
    }

    .et-dlg-sum {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        margin: 0 0 22px;
        padding: 14px 16px;
        border-radius: 12px;
        background: var(--ok-bg);
        color: var(--ok);
        font-weight: 600;
    }

    .et-dlg-sum strong {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 1.45rem;
        font-weight: 700;
        letter-spacing: -.03em;
        white-space: nowrap;
    }

    /* today */
    .et-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin: 26px 0 14px;
    }

    .et-stat span {
        display: block;
        font-size: .84rem;
        color: var(--muted);
    }

    .et-stat strong {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 1.9rem;
        font-weight: 700;
        letter-spacing: -.03em;
    }

    .et-list {
        padding: 4px 0 10px;
    }

    .et-list h2 {
        font-family: 'Newsreader', Georgia, serif;
        font-weight: 600;
        font-size: 1.4rem;
        margin: 0;
        padding: 18px 22px 10px;
    }

    .et-table {
        margin: 0 !important;
        font-size: .9rem;
    }

    .et-table thead th {
        font-size: .78rem;
        font-weight: 600;
        color: var(--muted);
        background: var(--soft);
        border-bottom: 1px solid var(--line) !important;
        padding: 12px 14px;
        white-space: nowrap;
    }

    .et-table td {
        padding: 12px 14px;
        border-color: var(--line);
        vertical-align: middle;
    }

    .et-table tbody tr:hover {
        background: #f7f9fe;
    }

    .et-sess {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 999px;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: .78rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .et-list .dataTables_wrapper {
        padding: 0 18px 8px;
    }

    .et-toast {
        position: fixed;
        left: 50%;
        bottom: calc(24px + env(safe-area-inset-bottom, 0px));
        transform: translate(-50%, 20px);
        z-index: 2000;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        border-radius: 12px;
        background: var(--ok);
        color: #fff;
        font-weight: 600;
        box-shadow: 0 14px 34px rgba(0, 0, 0, .25);
        opacity: 0;
        visibility: hidden;
        transition: opacity .25s, transform .25s, visibility .25s;
        max-width: 92vw;
    }

    .et-toast.show {
        opacity: 1;
        visibility: visible;
        transform: translate(-50%, 0);
    }

    .et-toast.bad {
        background: var(--bad);
    }

    @media (max-width: 991px) {
        .et-ticket {
            grid-template-columns: 1fr;
        }

        .et-stub {
            padding: 26px;
            border-left: 0;
            border-top: 3px dashed #fff;
            border-radius: 0 0 15px 15px;
        }

        .et-stub::before,
        .et-stub::after {
            top: -14px;
            bottom: auto;
        }

        .et-stub::before {
            left: -12px;
        }

        .et-stub::after {
            left: auto;
            right: -12px;
        }

        .et-buy {
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .et-block {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 575px) {
        .et-row {
            flex-direction: column;
        }

        .et-row .et-btn {
            width: 100%;
        }

        .et-head h1 {
            font-size: 1.7rem;
        }

        .et-main {
            padding: 20px;
        }

        .et-avatar {
            width: 48px;
            height: 48px;
            font-size: 17px;
        }

        .et-fact {
            padding: 0 10px;
        }

        .et-fact strong {
            font-size: 1.35rem;
        }

        .et-stats {
            grid-template-columns: 1fr;
        }

        .et-block-act,
        .et-block-act .et-btn {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .et * {
            transition: none !important;
        }
    }
</style>

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

                <div class="et">
                    <header class="et-head">
                        <h1>Extra ticket buying</h1>
                        <p>Scan the student's QR code, check their programme and session, then add the extra tickets they want to buy.</p>
                    </header>

                    <?php if ($columnsMissing): ?>
                        <div class="et-warn" role="alert">The extra ticket log needs three new columns before tickets can be added. Run <b>add_extra_ticket_log_columns.sql</b> in phpMyAdmin.</div>
                    <?php endif; ?>

                    <!-- 1. scan -->
                    <form id="idForm" class="et-card et-scan" autocomplete="off">
                        <label for="sid">Student ID</label>
                        <div class="et-row">
                            <input type="text" id="sid" placeholder="Scan the QR code or type the Student ID" autofocus>
                            <button type="submit" class="et-btn solid">Look up</button>
                        </div>
                        <p class="et-hint" id="hint">Click the box, then scan the student's QR code with the scanner.</p>
                        <button type="button" id="clearBtn" class="et-link" hidden>Clear and scan next student</button>
                    </form>

                    <div id="msg" class="et-empty" hidden></div>

                    <!-- 2. student, programme, payment and session -->
                    <div id="result" hidden>
                        <div class="et-ticket" id="r_ticket">
                            <div class="et-main">
                                <div class="et-who">
                                    <div class="et-avatar" id="r_ini" aria-hidden="true"></div>
                                    <div style="min-width:0">
                                        <h2 class="et-name" id="r_name"></h2>
                                        <div class="et-chips" id="r_chips"></div>
                                    </div>
                                </div>

                                <div class="et-prog">
                                    <span class="et-k">Programme</span>
                                    <p id="r_prog"></p>
                                </div>

                                <div class="et-pay" id="r_pay" role="status"><i class="fas" aria-hidden="true"></i>
                                    <div><b></b><span></span></div>
                                </div>

                                <div class="et-facts" id="r_facts">
                                    <div class="et-fact"><strong id="f_free">0</strong><span>Free tickets</span></div>
                                    <div class="et-fact"><strong id="f_pay">0</strong><span>Extra tickets with payment</span></div>
                                    <div class="et-fact"><strong id="f_added">0</strong><span>Extra tickets added since</span></div>
                                </div>
                            </div>

                            <div class="et-stub" id="r_sessbox">
                                <span class="et-k">Session</span>
                                <strong id="r_session"></strong>
                                <small id="r_sessnote"></small>
                            </div>
                        </div>

                        <!-- 3a. buying (payment found) -->
                        <section class="et-card et-buy" id="buyBox">
                            <div>
                                <h2>Add extra tickets</h2>
                                <span class="et-lab" id="qtyLab">How many tickets?</span>
                                <div class="et-qty" id="qtyGroup" role="radiogroup" aria-labelledby="qtyLab">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <label><input type="radio" name="qty" value="<?= $i ?>" <?= $i === 1 ? 'checked' : '' ?>><span><?= $i ?></span></label>
                                    <?php endfor; ?>
                                </div>
                                <p class="et-price">Each ticket costs <b id="price"></b>, the extra ticket fee for this programme.</p>
                            </div>
                            <div class="et-sum" aria-live="polite">
                                <div>
                                    <span class="et-k">Total to collect</span>
                                    <strong class="et-total" id="total">Rs. 0.00</strong>
                                    <span class="et-calc" id="calc"></span>
                                </div>
                                <button type="button" id="addBtn" class="et-btn ok"><i class="fas fa-plus-circle" aria-hidden="true"></i> <span>Add extra tickets</span></button>
                            </div>
                        </section>

                        <!-- 3b. no payment yet -->
                        <section class="et-block" id="blockBox" hidden>
                            <i class="fas fa-file-invoice-dollar" aria-hidden="true"></i>
                            <div>
                                <strong>Make the payment first</strong>
                                <p>Extra tickets can be added only after the graduation payment is recorded. Collect the payment, then scan this student again.</p>
                            </div>
                            <div class="et-block-act">
                                <?php if ($paymentUrl !== ''): ?><a class="et-btn solid link" href="<?= htmlspecialchars($paymentUrl, ENT_QUOTES) ?>">Go to payment</a><?php endif; ?>
                                <button type="button" id="nextBtn" class="et-btn">Scan next student</button>
                            </div>
                        </section>
                    </div>

                    <dialog id="payDlg" class="et-dlg" aria-labelledby="payDlgT">
                        <form method="dialog">
                            <div class="et-dlg-ic"><i class="fas fa-triangle-exclamation" aria-hidden="true"></i></div>
                            <h2 id="payDlgT">Make the payment first</h2>
                            <p id="dlgText"></p>
                            <div class="et-dlg-act">
                                <?php if ($paymentUrl !== ''): ?><a class="et-btn link" href="<?= htmlspecialchars($paymentUrl, ENT_QUOTES) ?>">Go to payment</a><?php endif; ?>
                                <button class="et-btn solid" value="ok" autofocus>Got it</button>
                            </div>
                        </form>
                    </dialog>

                    <dialog id="okDlg" class="et-dlg ok" aria-labelledby="okDlgT">
                        <form method="dialog">
                            <div class="et-dlg-ic ok"><i class="fas fa-check" aria-hidden="true"></i></div>
                            <h2 id="okDlgT">Extra tickets added</h2>
                            <p id="okText"></p>
                            <div class="et-dlg-sum"><span>Total collected</span><strong id="okTotal"></strong></div>
                            <div class="et-dlg-act">
                                <button class="et-btn ok" value="done" autofocus>Done</button>
                            </div>
                        </form>
                    </dialog>

                    <!-- today -->
                    <div class="et-stats">
                        <div class="et-card et-stat"><span>Extra tickets today</span><strong><?= (int)$totalTicketsToday ?></strong></div>
                        <div class="et-card et-stat"><span>Amount today</span><strong>Rs. <?= number_format($totalAmountToday, 2) ?></strong></div>
                    </div>

                    <section class="et-card et-list">
                        <h2>Today's added tickets</h2>
                        <div class="table-responsive">
                            <table class="table et-table" id="todayExtraTickets">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Time</th>
                                        <th>Student ID</th>
                                        <th>Student</th>
                                        <th>Programme</th>
                                        <th>Session</th>
                                        <th>Tickets</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Admin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($todayRows)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">No tickets added today</td>
                                        </tr>
                                        <?php else:
                                        $n = 1;
                                        foreach ($todayRows as $row): ?>
                                            <tr>
                                                <td><?= $n++ ?></td>
                                                <td data-order="<?= strtotime($row['added_on']) ?>"><?= date('h:i A', strtotime($row['added_on'])) ?></td>
                                                <td class="et-mono"><?= htmlspecialchars((string)($row['student_id'] ?? '-')) ?></td>
                                                <td><?= htmlspecialchars((string)($row['student_name'] ?? '-')) ?></td>
                                                <td><?= htmlspecialchars((string)($row['program_name'] ?? '-')) ?></td>
                                                <td><?php if (trim((string)$row['session']) !== ''): ?><span class="et-sess"><?= htmlspecialchars(et_session_label($row['session'])) ?></span><?php else: ?>-<?php endif; ?></td>
                                                <td class="et-mono"><?= (int)$row['added_tickets'] ?></td>
                                                <td class="et-mono">Rs. <?= number_format($row['ticket_price'], 2) ?></td>
                                                <td class="et-mono"><strong>Rs. <?= number_format($row['total_added'], 2) ?></strong></td>
                                                <td><?= htmlspecialchars((string)$row['added_by_name']) ?></td>
                                            </tr>
                                    <?php endforeach;
                                    endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

            </div> <!-- /container-fluid -->
        </div> <!-- /content -->
    </div> <!-- /content-wrapper -->
</div> <!-- /wrapper -->

<div class="et-toast" id="toast" role="status"><i class="fas fa-circle-check" aria-hidden="true"></i><span></span></div>

<!-- ========== JS & CSS ========== -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
    (function() {
        var CSRF = '<?php echo htmlspecialchars($_SESSION['csrf'], ENT_QUOTES); ?>';
        var $ = function(id) {
            return document.getElementById(id);
        };
        var cur = null,
            fee = 0,
            loading = false;

        document.body.classList.add('dashboard-page');
        $('sidebarToggleMobile').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-open');
        });
        $('sidebarOverlay').addEventListener('click', function() {
            document.body.classList.remove('sidebar-open');
        });

        function money(v) {
            return Number(v).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function say(t, bad) {
            $('hint').textContent = t;
            $('hint').className = 'et-hint' + (bad ? ' bad' : '');
        }

        function toast(t, bad) {
            var el = $('toast');
            el.classList.toggle('bad', !!bad);
            el.querySelector('i').className = 'fas ' + (bad ? 'fa-circle-exclamation' : 'fa-circle-check');
            el.querySelector('span').textContent = t;
            el.classList.add('show');
            clearTimeout(toast.t);
            toast.t = setTimeout(function() {
                el.classList.remove('show');
            }, 4500);
        }

        function chip(cls, icon, html) {
            var s = document.createElement('span');
            s.className = 'et-chip ' + cls;
            s.innerHTML = '<i class="fas ' + icon + '" aria-hidden="true"></i> ';
            var t = document.createElement('span');
            t.textContent = html;
            s.appendChild(t);
            return s;
        }

        function sessionLabel(v) {
            return v ? v.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, function(c) {
                return c.toUpperCase();
            }) : '';
        }

        function qty() {
            var c = document.querySelector('input[name="qty"]:checked');
            return c ? parseInt(c.value, 10) : 1;
        }

        function recalc() {
            var n = qty();
            $('total').textContent = 'Rs. ' + money(n * fee);
            $('calc').textContent = n + ' \u00d7 Rs. ' + money(fee);
        }

        // ---------- show a message instead of a student ----------
        function showMsg(title, text, bad, icon) {
            var m = $('msg');
            m.className = 'et-empty' + (bad ? ' bad' : '');
            m.innerHTML = '<i class="fas ' + (icon || 'fa-user-slash') + '" aria-hidden="true"></i><span><strong></strong></span>';
            m.querySelector('strong').textContent = title;
            if (text) m.querySelector('span').appendChild(document.createTextNode(text));
            m.hidden = false;
            $('result').hidden = true;
        }

        // ---------- fill the result cards ----------
        var dlg = $('payDlg');

        function setPay(ok, title, sub) {
            var p = $('r_pay');
            p.className = 'et-pay ' + (ok ? 'ok' : 'bad');
            p.querySelector('i').className = 'fas ' + (ok ? 'fa-circle-check' : 'fa-circle-exclamation');
            p.querySelector('b').textContent = title;
            p.querySelector('span').textContent = sub;
        }

        function show(s) {
            cur = s;
            fee = s.fee || 0;
            var paid = !!s.paid;
            var words = s.name.trim().split(/\s+/);
            $('r_ini').textContent = (words[0][0] + (words.length > 1 ? words[words.length - 1][0] : '')).toUpperCase();
            $('r_name').textContent = s.name;
            $('r_prog').textContent = s.program || 'Not set';

            var chips = $('r_chips');
            chips.textContent = '';
            chips.appendChild(chip('et-mono', 'fa-id-badge', s.student_id));
            if (s.in_no) chips.appendChild(chip('', 'fa-envelope-open-text', 'Invitation ' + s.in_no));

            // payment check (payment_records)
            $('r_ticket').classList.toggle('blocked', !paid);
            if (paid) setPay(true, 'Graduation payment received', 'Receipt ' + s.receipt + ', paid on ' + s.paid_on);
            else setPay(false, 'No graduation payment on record', 'This student has not paid yet.');
            $('r_facts').hidden = !paid;
            $('f_free').textContent = s.free_tickets;
            $('f_pay').textContent = s.paid_extra;
            $('f_added').textContent = s.bought;

            $('r_sessbox').classList.toggle('missing', !s.session);
            $('r_session').textContent = s.session ? sessionLabel(s.session) : 'Not assigned';
            $('r_sessnote').textContent = s.session ? 'Ceremony session for this programme' : 'No session is set for this programme';

            $('buyBox').hidden = !paid;
            $('blockBox').hidden = paid;
            $('msg').hidden = true;
            $('result').hidden = false;
            $('result').scrollIntoView({
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                block: 'start'
            });

            if (!paid) {
                say('Payment not found. Make the payment first.', true);
                $('dlgText').textContent = s.name + ' (' + s.student_id + ') has no graduation payment on record. Collect the payment first, then add extra tickets.';
                if (dlg.showModal) {
                    if (!dlg.open) dlg.showModal();
                } else {
                    alert('Payment not found. Make the payment first.');
                }
                return;
            }

            var ok = fee > 0;
            $('price').textContent = ok ? 'Rs. ' + money(fee) : 'not set';
            $('addBtn').disabled = !ok;
            document.querySelectorAll('input[name="qty"]').forEach(function(i) {
                i.disabled = !ok;
            });
            document.querySelector('input[name="qty"][value="1"]').checked = true;
            recalc();
            if (!ok) toast('The extra ticket fee is not set for this programme.', true);
            else document.querySelector('input[name="qty"]:checked').focus({
                preventScroll: true
            });
        }

        // ---------- look up (scanner or typing) ----------
        function lookup(raw) {
            var id = String(raw || '').trim();
            if (!id || loading) return;
            loading = true;
            $('clearBtn').hidden = false;
            $('sid').value = id;
            say('Looking up ' + id + '…');
            fetch('extra_ticket_api.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'lookup',
                        student_id: id
                    })
                })
                .then(function(r) {
                    if (r.status === 401) {
                        location.href = 'login';
                        throw new Error('auth');
                    }
                    return r.json();
                })
                .then(function(j) {
                    if (!j.ok) throw new Error(j.error || 'Lookup failed.');
                    if (!j.found) {
                        cur = null;
                        showMsg('No registered student found for "' + id + '"', 'Check the ID or ask the student to register.', true);
                        say('Not found. Scan again or type another ID.', true);
                        return;
                    }
                    say('Student found.');
                    show(j.student);
                })
                .catch(function(e) {
                    if (e.message !== 'auth') {
                        say(e.message || 'Could not reach the server.', true);
                    }
                })
                .finally(function() {
                    loading = false;
                });
        }

        function reset() {
            cur = null;
            $('sid').value = '';
            $('result').hidden = true;
            $('msg').hidden = true;
            $('clearBtn').hidden = true;
            say('Click the box, then scan the student\'s QR code with the scanner.');
            $('sid').focus();
        }

        $('idForm').addEventListener('submit', function(e) {
            e.preventDefault();
            lookup($('sid').value);
        });
        $('clearBtn').addEventListener('click', reset);
        $('nextBtn').addEventListener('click', reset);
        $('qtyGroup').addEventListener('change', recalc);
        dlg.addEventListener('close', function() {
            $('sid').focus();
            $('sid').select();
        });

        // ---------- add the tickets (one click) ----------
        $('addBtn').addEventListener('click', function() {
            var btn = this;
            if (!cur || !cur.paid || btn.disabled) return;
            var n = qty();
            var label = btn.querySelector('span');
            btn.disabled = true;
            label.textContent = 'Saving…';
            fetch('extra_ticket_api.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'add',
                        student_id: cur.student_id,
                        count: n,
                        csrf: CSRF
                    })
                })
                .then(function(r) {
                    if (r.status === 401) {
                        location.href = 'login';
                        throw new Error('auth');
                    }
                    return r.json();
                })
                .then(function(j) {
                    if (!j.ok) throw new Error(j.error || 'Could not save.');
                    // reload so today's totals and table are fresh, then confirm
                    sessionStorage.setItem('et_ok', JSON.stringify({
                        count: j.count,
                        name: j.name,
                        total: j.total
                    }));
                    location.reload();
                })
                .catch(function(e) {
                    if (e.message === 'auth') return;
                    toast(e.message || 'Could not save. Try again.', true);
                    btn.disabled = false;
                    label.textContent = 'Add extra tickets';
                });
        });

        // success alert after the page reloads
        var okDlg = $('okDlg');
        okDlg.addEventListener('close', function() {
            location.reload();
        }); // fresh page for the next student
        var done = sessionStorage.getItem('et_ok');
        if (done) {
            sessionStorage.removeItem('et_ok');
            try {
                var d = JSON.parse(done);
                $('okText').textContent = d.count + ' extra ticket' + (d.count === 1 ? ' was' : 's were') + ' added for ' + d.name + '.';
                $('okTotal').textContent = 'Rs. ' + money(d.total);
                if (okDlg.showModal) okDlg.showModal();
                else toast($('okText').textContent);
            } catch (e) {}
        }
    })();

    $(document).ready(function() {
        if ($('#todayExtraTickets').length && $('#todayExtraTickets tbody tr td[colspan]').length === 0) {
            $('#todayExtraTickets').DataTable({
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100]
            });
        }
    });
</script>

</body>

</html>