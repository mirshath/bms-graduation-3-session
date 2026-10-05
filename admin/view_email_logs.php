<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");

function el_h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

// Normalise text for comparing: collapse whitespace, trim, lower-case
function el_norm($v): string
{
    return mb_strtolower(trim(preg_replace('/\s+/u', ' ', (string)$v)));
}

function el_initials(string $name): string
{
    $w = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);
    if (!$w) return '?';
    $first = mb_substr($w[0], 0, 1);
    $last  = count($w) > 1 ? mb_substr($w[count($w) - 1], 0, 1) : '';
    return mb_strtoupper($first . $last);
}

$loadError = false;
$stats = ['total_emails' => 0, 'sent_count' => 0, 'failed_count' => 0, 'unique_students' => 0];
$programs = [];
$programSessions = [];
$programSessionsNorm = [];
$sessions = [];
$logs = [];

try {
    $r = $conn->query("SELECT
            COUNT(*) AS total_emails,
            SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) AS sent_count,
            SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) AS failed_count,
            COUNT(DISTINCT student_id) AS unique_students
        FROM email_log");
    if (!$r) throw new Exception($conn->error);
    $stats = array_map('intval', $r->fetch_assoc());

    // program -> session map from data_tables
    $r = $conn->query("SELECT programName, session FROM data_tables ORDER BY session ASC, programName ASC");
    if (!$r) throw new Exception($conn->error);
    while ($p = $r->fetch_assoc()) $programSessions[$p['programName']] = (string)$p['session'];

    // programs that appear in the logs (may include ones no longer in data_tables)
    $r = $conn->query("SELECT DISTINCT program_name FROM email_log WHERE program_name IS NOT NULL AND program_name != ''");
    if (!$r) throw new Exception($conn->error);
    while ($p = $r->fetch_assoc()) {
        if (!isset($programSessions[$p['program_name']])) $programSessions[$p['program_name']] = '';
    }
    ksort($programSessions);
    foreach ($programSessions as $pn => $sn) $programSessionsNorm[el_norm($pn)] = $sn;
    $programs = array_keys($programSessions);

    $sessions = array_values(array_unique(array_filter($programSessions, fn($v) => trim($v) !== '')));
    sort($sessions, SORT_NATURAL);

    $r = $conn->query("SELECT el.*, a.admin_name AS sent_by_name
                       FROM email_log el
                       LEFT JOIN admin a ON el.sent_by = a.id
                       ORDER BY el.sent_at DESC");
    if (!$r) throw new Exception($conn->error);
    while ($row = $r->fetch_assoc()) $logs[] = $row;
} catch (Exception $e) {
    $loadError = true;
    error_log("Email logs error: " . $e->getMessage());
}
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">

<style>
    /* mobile sidebar (same behaviour as the other admin pages) */
    body.at-page {
        overflow-x: hidden;
    }

    #sidebarOverlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .4);
        opacity: 0;
        visibility: hidden;
        transition: opacity .3s ease;
        z-index: 1039;
    }

    body.sidebar-open #sidebarOverlay {
        opacity: 1;
        visibility: visible;
    }

    .at-menu {
        display: none;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
        padding: 9px 16px;
        border: 1px solid #c4ccd9;
        border-radius: 12px;
        background: #fff;
        color: #17233d;
        font-family: 'Public Sans', system-ui, sans-serif;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(23, 35, 61, .08);
        cursor: pointer;
    }

    @media (max-width: 991.98px) {
        .at-menu {
            display: inline-flex;
        }

        body.at-page #accordionSidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            transform: translateX(-100%);
            transition: transform .3s ease;
            z-index: 1040;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        body.at-page.sidebar-open #accordionSidebar {
            transform: translateX(0);
        }

        body.at-page #content-wrapper {
            margin-left: 0 !important;
        }
    }
</style>

<style>
    /* =========================================================
       Email logs  (same design language as the other pages, compact so it fits at 100% zoom)
       ========================================================= */
    .at {
        --ink: #17233d;
        --muted: #5f6b7e;
        --line: #e2e6ee;
        --soft: #f4f6fb;
        --brand: #1f4bb6;
        --brand-d: #173a91;
        --brand-bg: #e8eefb;
        --ok: #1b7f4b;
        --ok-bg: #e6f4ec;
        --warn: #a85d00;
        --warn-bg: #fff4e0;
        --bad: #c0372f;
        --bad-bg: #fdecea;
        font-family: 'Public Sans', system-ui, sans-serif;
        font-size: 13px;
        color: var(--ink);
        -webkit-font-smoothing: antialiased;
        width: 100%;
        max-width: 1600px;
        margin-inline: auto;
        padding: 4px 0 28px;
        line-height: 1.45;
    }

    .at *,
    .at *::before,
    .at *::after {
        box-sizing: border-box;
    }

    .at-head {
        margin-bottom: 16px;
    }

    .at-head h1 {
        margin: 0;
        font-size: clamp(20px, 2.2vw, 26px);
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.15;
    }

    .at-head p {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--muted);
    }

    .at-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
    }

    .at-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        padding: 10px 14px;
        border: 1px solid #f1c3be;
        border-radius: 10px;
        background: var(--bad-bg);
        color: var(--bad);
        font-size: 13px;
        font-weight: 500;
    }

    /* summary cards */
    .at-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .at-kpi {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
    }

    .at-kpi-ic {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        font-size: 15px;
        background: var(--brand-bg);
        color: var(--brand);
    }

    .at-kpi.ok .at-kpi-ic {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .at-kpi.bad .at-kpi-ic {
        background: var(--bad-bg);
        color: var(--bad);
    }

    .at-kpi span.k {
        display: block;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .at-kpi strong {
        display: block;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.15;
        font-variant-numeric: tabular-nums;
    }

    .at-kpi.ok strong {
        color: var(--ok);
    }

    .at-kpi.bad strong {
        color: var(--bad);
    }

    /* filters: one row */
    .at-filters {
        padding: 14px 16px;
        margin-bottom: 14px;
    }

    .at-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
        gap: 10px 12px;
        align-items: end;
    }

    .at-field {
        min-width: 0;
    }

    .at-field label {
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0 0 5px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .at-field label i {
        color: #97a2b6;
        font-size: 10px;
    }

    .at-sel {
        display: block;
        width: 100%;
        height: 36px;
        padding: 0 10px;
        border: 1px solid #c4ccd9;
        border-radius: 9px;
        background: #fff;
        color: var(--ink);
        font: inherit;
        font-size: 13px;
        font-weight: 500;
        outline: 0;
        text-overflow: ellipsis;
        transition: border-color .15s, box-shadow .15s;
    }

    .at-sel:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .14);
    }

    .at-reset {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        height: 36px;
        padding: 0 14px;
        border: 1px solid #c4ccd9;
        border-radius: 9px;
        background: #fff;
        color: var(--ink);
        font: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: background .15s;
    }

    .at-reset:hover {
        background: var(--soft);
    }

    #filterInfo {
        display: none;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--line);
        font-size: 12px;
        color: var(--muted);
    }

    #filterInfo.show {
        display: block;
    }

    #filterInfo i {
        color: var(--brand);
        margin-right: 4px;
    }

    #filterInfo strong {
        color: var(--ink);
    }

    /* table card */
    .at-table-card {
        padding: 0;
        overflow: hidden;
    }

    .at-table-card .dataTables_wrapper {
        padding: 0;
    }

    .at-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--line);
    }

    .at-table-card .dataTables_length label,
    .at-table-card .dataTables_filter label {
        margin: 0;
        font-size: 12px;
        color: var(--muted);
        font-weight: 500;
    }

    .at-table-card .dataTables_length select {
        height: 32px;
        margin: 0 6px;
        padding: 0 6px;
        border: 1px solid #c4ccd9;
        border-radius: 8px;
        background: #fff;
        color: var(--ink);
        font-size: 13px;
    }

    .at-table-card .dataTables_filter input {
        width: 240px;
        max-width: 100%;
        height: 36px;
        margin: 0;
        padding: 0 10px;
        border: 1px solid #c4ccd9;
        border-radius: 9px;
        font-size: 13px;
        color: var(--ink);
        outline: 0;
    }

    .at-table-card .dataTables_filter input:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .14);
    }

    .at-scroll {
        overflow-x: auto;
    }

    table#emailLogsTable {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: collapse;
        border: 0;
        table-layout: auto;
    }

    table#emailLogsTable thead th {
        padding: 9px 8px;
        background: var(--soft);
        border-bottom: 1px solid var(--line);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .03em;
        text-transform: uppercase;
        color: var(--muted);
        white-space: nowrap;
        text-align: left;
    }

    table#emailLogsTable thead th:first-child,
    table#emailLogsTable tbody td:first-child {
        padding-left: 16px;
    }

    table#emailLogsTable thead th:last-child,
    table#emailLogsTable tbody td:last-child {
        padding-right: 16px;
    }

    table#emailLogsTable tbody td {
        padding: 8px;
        border-bottom: 1px solid var(--line);
        font-size: 12px;
        color: var(--ink);
        vertical-align: middle;
    }

    table#emailLogsTable tbody tr:hover td {
        background: #fafbfe;
    }

    table#emailLogsTable tbody tr:last-child td {
        border-bottom: 0;
    }

    table#emailLogsTable.dataTable.no-footer {
        border-bottom: 0;
    }

    table#emailLogsTable.dataTable thead .sorting,
    table#emailLogsTable.dataTable thead .sorting_asc,
    table#emailLogsTable.dataTable thead .sorting_desc {
        background-color: var(--soft);
    }

    table#emailLogsTable.dataTable thead th {
        padding-right: 18px;
    }

    table#emailLogsTable.dataTable thead th:last-child {
        padding-right: 16px;
    }

    /* column widths (the wrapping columns take the leftover space) */
    table#emailLogsTable th:nth-child(1) {
        width: 44px;
    }

    table#emailLogsTable th:nth-child(2) {
        width: 84px;
    }

    table#emailLogsTable th:nth-child(3) {
        width: 26%;
    }

    table#emailLogsTable th:nth-child(4) {
        width: 24%;
    }

    table#emailLogsTable th:nth-child(5) {
        width: 86px;
    }

    table#emailLogsTable th:nth-child(6) {
        width: 56px;
    }

    table#emailLogsTable th:nth-child(7) {
        width: 64px;
    }

    table#emailLogsTable th:nth-child(8) {
        width: 78px;
    }

    table#emailLogsTable th:nth-child(9) {
        width: 92px;
    }

    table#emailLogsTable th:nth-child(10) {
        width: 92px;
    }

    .at-mono {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 11.5px;
        white-space: nowrap;
    }

    .at-em {
        display: block;
        margin-top: 1px;
        color: var(--muted);
        font-size: 11.5px;
        overflow-wrap: anywhere;
    }

    .at-prog {
        display: block;
        font-size: 12px;
        line-height: 1.35;
    }

    .at-na {
        color: #97a2b6;
    }

    .at-person {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    .at-person>div {
        min-width: 0;
    }

    .at-ini {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 10.5px;
        font-weight: 700;
    }

    .at-person .nm {
        display: block;
        font-weight: 600;
        line-height: 1.3;
    }

    .at-when {
        display: block;
        line-height: 1.3;
    }

    .at-when small {
        display: block;
        color: var(--muted);
        font-size: 11px;
    }

    .at-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        background: var(--soft);
        color: var(--muted);
    }

    .at-pill.ok {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .at-pill.bad {
        background: var(--bad-bg);
        color: var(--bad);
    }

    .at-pill.type {
        background: var(--brand-bg);
        color: var(--brand-d);
    }

    .at-act {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 9px;
        border: 1px solid #c4ccd9;
        border-radius: 8px;
        background: #fff;
        color: var(--ink);
        font: inherit;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: background .15s;
    }

    .at-act:hover {
        background: var(--soft);
    }

    .at-act i {
        color: var(--brand);
        font-size: 11px;
    }

    .at-act.err {
        border-color: #f1c3be;
        color: var(--bad);
    }

    .at-act.err i {
        color: var(--bad);
    }

    .at-act.err:hover {
        background: var(--bad-bg);
    }

    .at-act:focus-visible,
    .at-reset:focus-visible,
    .at-x:focus-visible,
    .at-sel:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    .at-foot {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 16px;
        border-top: 1px solid var(--line);
    }

    .at-foot .dataTables_info {
        padding: 0;
        font-size: 12px;
        color: var(--muted);
    }

    .at-foot .dataTables_paginate {
        padding: 0;
    }

    .at-foot .dataTables_paginate .paginate_button {
        padding: 4px 10px !important;
        margin: 0 1px !important;
        border: 1px solid transparent !important;
        border-radius: 8px !important;
        font-size: 12px;
        font-weight: 600;
        color: var(--ink) !important;
        background: transparent !important;
    }

    .at-foot .dataTables_paginate .paginate_button:hover {
        background: var(--soft) !important;
        border-color: var(--line) !important;
        color: var(--ink) !important;
    }

    .at-foot .dataTables_paginate .paginate_button.current,
    .at-foot .dataTables_paginate .paginate_button.current:hover {
        background: var(--brand) !important;
        border-color: var(--brand) !important;
        color: #fff !important;
    }

    .at-foot .dataTables_paginate .paginate_button.disabled,
    .at-foot .dataTables_paginate .paginate_button.disabled:hover {
        color: #aab3c3 !important;
        background: transparent !important;
        border-color: transparent !important;
    }

    /* details / error dialog */
    .at-modal {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(23, 35, 61, .5);
    }

    .at-modal.open {
        display: flex;
    }

    .at-dialog {
        width: 100%;
        max-width: 480px;
        max-height: 90vh;
        overflow-y: auto;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 24px 60px rgba(23, 35, 61, .3);
        font-family: 'Public Sans', system-ui, sans-serif;
        font-size: 13px;
        color: var(--ink);
    }

    .at-dialog-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--line);
    }

    .at-dialog-head h2 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.01em;
    }

    .at-x {
        display: grid;
        place-items: center;
        width: 30px;
        height: 30px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: var(--muted);
        font-size: 14px;
        cursor: pointer;
    }

    .at-x:hover {
        background: var(--soft);
        color: var(--ink);
    }

    .at-dialog-body {
        padding: 14px 16px 18px;
    }

    .at-dl {
        display: grid;
        grid-template-columns: 96px minmax(0, 1fr);
        gap: 8px 12px;
        margin: 0;
    }

    .at-dl dt {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
        padding-top: 2px;
    }

    .at-dl dd {
        margin: 0;
        font-size: 13px;
        font-weight: 500;
        overflow-wrap: anywhere;
    }

    .at-errbox {
        padding: 12px 14px;
        border: 1px solid #f1c3be;
        border-radius: 10px;
        background: var(--bad-bg);
        color: var(--bad);
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12px;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    /* tighter screens: drop the DB id column so the rest still fits without scrolling */
    @media (max-width: 1280px) {

        table#emailLogsTable th:nth-child(1),
        table#emailLogsTable td:nth-child(1) {
            display: none;
        }

        table#emailLogsTable thead th:nth-child(2),
        table#emailLogsTable tbody td:nth-child(2) {
            padding-left: 16px;
        }
    }

    @media (max-width: 991.98px) {
        .at-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .at-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .at-reset {
            grid-column: 1 / -1;
        }

        table#emailLogsTable {
            min-width: 860px;
        }
    }

    @media (max-width: 575.98px) {
        .at-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .at-kpis {
            grid-template-columns: minmax(0, 1fr);
        }

        .at-table-card .dataTables_filter,
        .at-table-card .dataTables_filter input {
            width: 100%;
        }

        .at-dl {
            grid-template-columns: minmax(0, 1fr);
            gap: 2px;
        }

        .at-dl dd {
            margin-bottom: 8px;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .at *,
        #sidebarOverlay,
        body.at-page #accordionSidebar {
            transition: none !important;
        }
    }
</style>

<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="sidebarOverlay"></div>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <div class="container-fluid mt-4">
                <button type="button" id="sidebarToggleMobile" class="at-menu" aria-label="Open menu">
                    <i class="fas fa-bars" aria-hidden="true"></i> <span>Menu</span>
                </button>

                <div class="at">

                    <header class="at-head">
                        <h1>Email logs for seat numbers</h1>
                        <p>Every seat number email that was sent or failed, with the reason for each failure.</p>
                    </header>

                    <?php if ($loadError): ?>
                        <div class="at-alert" role="alert">
                            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                            Error loading email logs. Please try again later.
                        </div>
                    <?php endif; ?>

                    <!-- summary -->
                    <section class="at-kpis">
                        <div class="at-card at-kpi">
                            <span class="at-kpi-ic"><i class="fas fa-envelope" aria-hidden="true"></i></span>
                            <div><span class="k">Total emails</span><strong><?= number_format($stats['total_emails']) ?></strong></div>
                        </div>
                        <div class="at-card at-kpi ok">
                            <span class="at-kpi-ic"><i class="fas fa-check-circle" aria-hidden="true"></i></span>
                            <div><span class="k">Sent</span><strong><?= number_format($stats['sent_count']) ?></strong></div>
                        </div>
                        <div class="at-card at-kpi bad">
                            <span class="at-kpi-ic"><i class="fas fa-times-circle" aria-hidden="true"></i></span>
                            <div><span class="k">Failed</span><strong><?= number_format($stats['failed_count']) ?></strong></div>
                        </div>
                        <div class="at-card at-kpi">
                            <span class="at-kpi-ic"><i class="fas fa-users" aria-hidden="true"></i></span>
                            <div><span class="k">Unique students</span><strong><?= number_format($stats['unique_students']) ?></strong></div>
                        </div>
                    </section>

                    <!-- filters -->
                    <section class="at-card at-filters">
                        <div class="at-grid">
                            <div class="at-field">
                                <label for="filterSession"><i class="fas fa-filter" aria-hidden="true"></i> Session</label>
                                <select id="filterSession" class="at-sel">
                                    <option value="">All sessions</option>
                                    <?php foreach ($sessions as $sess): ?>
                                        <option value="<?= el_h($sess) ?>"><?= el_h($sess) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="at-field">
                                <label for="filterProgram"><i class="fas fa-filter" aria-hidden="true"></i> Program</label>
                                <select id="filterProgram" class="at-sel">
                                    <option value="">All programs</option>
                                    <?php foreach ($programs as $p): ?>
                                        <option value="<?= el_h($p) ?>"><?= el_h($p) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="at-field">
                                <label for="filterStatus"><i class="fas fa-filter" aria-hidden="true"></i> Status</label>
                                <select id="filterStatus" class="at-sel">
                                    <option value="">All status</option>
                                    <option value="sent">Sent</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                            <div class="at-field">
                                <label for="filterType"><i class="fas fa-filter" aria-hidden="true"></i> Email type</label>
                                <select id="filterType" class="at-sel">
                                    <option value="">All types</option>
                                    <option value="single">Single</option>
                                    <option value="bulk">Bulk</option>
                                </select>
                            </div>
                            <button id="resetFilters" type="button" class="at-reset">
                                <i class="fas fa-redo" aria-hidden="true"></i> Reset
                            </button>
                        </div>
                        <div id="filterInfo"></div>
                    </section>

                    <!-- table -->
                    <section class="at-card at-table-card">
                        <table id="emailLogsTable" class="table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student ID</th>
                                    <th>Name / Email</th>
                                    <th>Program</th>
                                    <th>Session</th>
                                    <th>Seat No</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Sent At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $row):
                                    $name = trim((string)($row['name_in_full'] ?? ''));
                                    $failed = ($row['status'] ?? '') === 'failed';
                                    $ts = strtotime((string)($row['sent_at'] ?? ''));
                                    $progKey = el_norm($row['program_name'] ?? '');
                                ?>
                                    <tr data-program="<?= el_h($progKey) ?>"
                                        data-session="<?= el_h(el_norm($row['session_time'] ?? '')) ?>"
                                        data-psession="<?= el_h(el_norm($programSessionsNorm[$progKey] ?? '')) ?>"
                                        data-status="<?= el_h(el_norm($row['status'] ?? '')) ?>"
                                        data-type="<?= el_h(el_norm($row['email_type'] ?? '')) ?>">
                                        <td data-order="<?= (int)$row['id'] ?>"><span class="at-mono"><?= (int)$row['id'] ?></span></td>
                                        <td><span class="at-mono"><?= el_h($row['student_id']) ?></span></td>
                                        <td>
                                            <div class="at-person">
                                                <span class="at-ini" aria-hidden="true"><?= el_h(el_initials($name)) ?></span>
                                                <div>
                                                    <span class="nm"><?= $name !== '' ? el_h($name) : '<span class="at-na">N/A</span>' ?></span>
                                                    <span class="at-em"><?= el_h($row['email_address']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="at-prog"><?= el_h($row['program_name']) ?></span></td>
                                        <td><?php $sv = trim((string)($row['session_time'] ?? ''));
                                            echo $sv !== '' ? '<span class="at-mono">' . el_h($sv) . '</span>' : '<span class="at-na">N/A</span>'; ?></td>
                                        <td><span class="at-mono"><?= el_h($row['seat_no']) ?></span></td>
                                        <td><span class="at-pill type"><?= el_h(ucfirst((string)$row['email_type'])) ?></span></td>
                                        <td>
                                            <span class="at-pill <?= $failed ? 'bad' : 'ok' ?>">
                                                <i class="fas <?= $failed ? 'fa-times-circle' : 'fa-check-circle' ?>" aria-hidden="true"></i>
                                                <?= el_h(ucfirst((string)$row['status'])) ?>
                                            </span>
                                        </td>
                                        <td data-order="<?= (int)$ts ?>"><?php if ($ts): ?><span class="at-when at-mono"><?= date('M d, Y', $ts) ?><small><?= date('h:i A', $ts) ?></small></span><?php else: ?><span class="at-na">N/A</span><?php endif; ?></td>
                                        <td>
                                            <?php if ($failed): ?>
                                                <button type="button" class="at-act err viewError"
                                                    data-error="<?= el_h($row['error_message'] ?? '') ?>">
                                                    <i class="fas fa-exclamation-circle" aria-hidden="true"></i> Error
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="at-act viewDetails"
                                                    data-student="<?= el_h($row['student_id']) ?>"
                                                    data-name="<?= el_h($name) ?>"
                                                    data-email="<?= el_h($row['email_address']) ?>"
                                                    data-program="<?= el_h($row['program_name']) ?>"
                                                    data-seat="<?= el_h($row['seat_no']) ?>"
                                                    data-session="<?= el_h($row['session_time'] ?? '') ?>"
                                                    data-sent-by="<?= el_h($row['sent_by_name'] ?? '') ?>">
                                                    <i class="fas fa-eye" aria-hidden="true"></i> Details
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </section>

                </div>
            </div> <!-- /container-fluid -->
        </div> <!-- /content -->
    </div> <!-- /content-wrapper -->
</div> <!-- /wrapper -->

<!-- Dialog (used for both details and errors) -->
<div class="at-modal" id="atModal" aria-hidden="true">
    <div class="at-dialog" role="dialog" aria-modal="true" aria-labelledby="atModalTitle">
        <div class="at-dialog-head">
            <h2 id="atModalTitle"></h2>
            <button type="button" class="at-x" id="atModalClose" aria-label="Close"><i class="fas fa-times" aria-hidden="true"></i></button>
        </div>
        <div class="at-dialog-body" id="atModalBody"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        // Mobile sidebar: Menu button opens, overlay / Esc / wide screen closes
        $('body').addClass('at-page');
        $('#sidebarToggleMobile').on('click', function() {
            $('body').toggleClass('sidebar-open');
        });
        $('#sidebarOverlay').on('click', function() {
            $('body').removeClass('sidebar-open');
        });
        $(window).on('resize', function() {
            if (window.innerWidth >= 992) $('body').removeClass('sidebar-open');
        });

        function esc(v) {
            return $('<div>').text(v == null ? '' : v).html();
        }

        function show(v) {
            return v ? esc(v) : '<span class="at-na">N/A</span>';
        }

        var table = $('#emailLogsTable').DataTable({
            autoWidth: false,
            pageLength: 50,
            order: [
                [0, 'desc']
            ],
            lengthMenu: [
                [25, 50, 100, 200],
                [25, 50, 100, 200]
            ],
            dom: "<'at-bar'<'at-len'l><'at-search'f>><'at-scroll'rt><'at-foot'<'at-info'i><'at-pg'p>>",
            columnDefs: [{
                orderable: false,
                targets: 9
            }],
            language: {
                search: '',
                searchPlaceholder: 'Search logs…',
                lengthMenu: 'Show _MENU_',
                emptyTable: 'No email logs yet',
                zeroRecords: 'No matching email logs found',
                info: 'Showing _START_ to _END_ of _TOTAL_ emails',
                infoEmpty: 'Showing 0 to 0 of 0 emails',
                infoFiltered: '(filtered from _MAX_ total emails)'
            }
        });

        var programSessions = <?= json_encode($programSessions, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        var allPrograms = Object.keys(programSessions);

        // Program list follows the chosen session (all programs when no session)
        function loadPrograms(sess, keep) {
            var list = allPrograms.filter(function(n) {
                return !sess || programSessions[n] === sess;
            });
            var $p = $('#filterProgram').empty().append(new Option('All programs', ''));
            list.forEach(function(n) {
                $p.append(new Option(n, n));
            });
            $p.val(keep && list.indexOf(keep) !== -1 ? keep : '');
        }

        function norm(v) {
            return $.trim(String(v == null ? '' : v).replace(/\s+/g, ' ')).toLowerCase();
        }

        // Filters read clean values stored on each row (data-*), not the rendered cell text,
        // so spacing / badges / HTML in the cells can never break a match.
        var active = {
            session: '',
            program: '',
            status: '',
            type: ''
        };

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'emailLogsTable') return true;
            var tr = table.row(dataIndex).node();
            if (!tr) return true;
            var $r = $(tr);
            if (active.program && $r.attr('data-program') !== active.program) return false;
            if (active.status && $r.attr('data-status') !== active.status) return false;
            if (active.type && $r.attr('data-type') !== active.type) return false;
            if (active.session) {
                // the log's own session, or the session its program belongs to in data_tables
                if ($r.attr('data-session') !== active.session && $r.attr('data-psession') !== active.session) return false;
            }
            return true;
        });

        function applyFilters() {
            var session = $('#filterSession').val(),
                program = $('#filterProgram').val(),
                status = $('#filterStatus').val(),
                type = $('#filterType').val();

            active.session = norm(session);
            active.program = norm(program);
            active.status = norm(status);
            active.type = norm(type);
            table.draw();

            var label = {
                sent: 'Sent',
                failed: 'Failed',
                single: 'Single',
                bulk: 'Bulk'
            };
            var parts = [];
            if (session) parts.push('Session: <strong>' + esc(session) + '</strong>');
            if (program) parts.push('Program: <strong>' + esc(program) + '</strong>');
            if (status) parts.push('Status: <strong>' + esc(label[status] || status) + '</strong>');
            if (type) parts.push('Type: <strong>' + esc(label[type] || type) + '</strong>');
            $('#filterInfo').toggleClass('show', parts.length > 0).html(parts.length ?
                '<i class="fas fa-info-circle"></i> Showing <strong>' + table.page.info().recordsDisplay + '</strong> email(s) · ' + parts.join(' · ') : '');
        }

        $('#filterSession').on('change', function() {
            loadPrograms($(this).val(), $('#filterProgram').val());
            applyFilters();
        });

        // Choosing a program also selects its session
        $('#filterProgram').on('change', function() {
            var p = $(this).val();
            if (p && programSessions[p] && $('#filterSession').val() !== programSessions[p]) {
                $('#filterSession').val(programSessions[p]);
                loadPrograms(programSessions[p], p);
            }
            applyFilters();
        });

        $('#filterStatus, #filterType').on('change', applyFilters);

        $('#resetFilters').on('click', function() {
            $('#filterSession, #filterStatus, #filterType').val('');
            loadPrograms('', '');
            active = {
                session: '',
                program: '',
                status: '',
                type: ''
            };
            table.search('').draw();
            $('#filterInfo').removeClass('show').empty();
        });

        // Dialog
        var $modal = $('#atModal'),
            lastFocus = null;

        function openModal(title, html) {
            lastFocus = document.activeElement;
            $('#atModalTitle').text(title);
            $('#atModalBody').html(html);
            $modal.addClass('open').attr('aria-hidden', 'false');
            $('#atModalClose').trigger('focus');
        }

        function closeModal() {
            $modal.removeClass('open').attr('aria-hidden', 'true');
            if (lastFocus) lastFocus.focus();
        }

        $('#atModalClose').on('click', closeModal);
        $modal.on('click', function(e) {
            if (e.target === this) closeModal();
        });
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('body').removeClass('sidebar-open');
                if ($modal.hasClass('open')) closeModal();
            }
        });

        $(document).on('click', '.viewDetails', function() {
            var d = $(this).data();
            openModal('Email details',
                '<dl class="at-dl">' +
                '<dt>Student ID</dt><dd><span class="at-mono">' + show(d.student) + '</span></dd>' +
                '<dt>Name</dt><dd>' + show(d.name) + '</dd>' +
                '<dt>Email</dt><dd>' + show(d.email) + '</dd>' +
                '<dt>Program</dt><dd>' + show(d.program) + '</dd>' +
                '<dt>Seat no</dt><dd><span class="at-mono">' + show(d.seat) + '</span></dd>' +
                '<dt>Session</dt><dd>' + show(d.session) + '</dd>' +
                '<dt>Sent by</dt><dd>' + show(d.sentBy) + '</dd>' +
                '</dl>');
        });

        $(document).on('click', '.viewError', function() {
            var err = $(this).data('error');
            openModal('Error details', '<div class="at-errbox">' + esc(err || 'No error details available') + '</div>');
        });
    });
</script>