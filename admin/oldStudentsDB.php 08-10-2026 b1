<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

// Database connection with error handling
try {
    include("../database/connection.php");

    if (!isset($conn) || !$conn) {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    die("System Error: Unable to connect to database. Please contact administrator.");
}

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

// "Higher Diploma in Biomedical Science - Batch 29"  ->  "Higher Diploma in Biomedical Science"
function at_base_program(string $p): string
{
    return trim(preg_replace('/\s*-\s*Batch\s*\d+\s*$/i', '', trim($p)));
}

// Load every old student
$rows = [];
$loadError = false;
try {
    // old_student_db has no session, so it is taken from data_tables by matching the program name.
    // data_tables is grouped first so a program listed twice can never duplicate a student row.
    $result = mysqli_query($conn, "
        SELECT o.*, d.session_name
        FROM old_student_db o
        LEFT JOIN (
            SELECT TRIM(programName) AS pname, MIN(session) AS session_name
            FROM data_tables
            WHERE programName IS NOT NULL AND programName != ''
            GROUP BY TRIM(programName)
        ) d ON TRIM(o.program) = d.pname
        ORDER BY o.id DESC
    ");
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }
    while ($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
    }
} catch (Exception $e) {
    $loadError = true;
    error_log("Old Students DB Error: " . $e->getMessage());
}

$totalStudents = count($rows);
$totalRegistered = 0;
$totalPaid = 0;
foreach ($rows as $r) {
    if (strtolower(trim($r['status'] ?? '')) === 'registered') $totalRegistered++;
    if (strtolower(trim($r['payment_status'] ?? '')) === 'paid') $totalPaid++;
}
$totalNotRegistered = $totalStudents - $totalRegistered;

// Unique sessions (and the programs inside each one) from data_tables, used by the cascading filters
$sessionPrograms = [];
try {
    $sp = mysqli_query($conn, "SELECT DISTINCT TRIM(session) AS session, TRIM(programName) AS programName
                               FROM data_tables
                               WHERE session IS NOT NULL AND TRIM(session) != ''
                                 AND programName IS NOT NULL AND TRIM(programName) != ''
                               ORDER BY session ASC, programName ASC");
    if ($sp) {
        while ($r = mysqli_fetch_assoc($sp)) {
            $sessionPrograms[$r['session']][] = $r['programName'];
        }
    }
} catch (Exception $e) {
    error_log("Session filter error: " . $e->getMessage());
}
$sessionList = array_keys($sessionPrograms);

// Unique program names without the batch (for the "Program name" filter)
$programNames = [];
foreach ($rows as $r) {
    $pn = at_base_program((string)($r['program'] ?? ''));
    if ($pn !== '') $programNames[$pn] = true;
}
$programNames = array_keys($programNames);
sort($programNames, SORT_NATURAL | SORT_FLAG_CASE);
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Page level plugins -->
<link rel="stylesheet" href="vendor/datatables/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

<style>
    /* mobile sidebar (same behaviour as the scan page) */
    body.at-page {
        overflow-x: hidden;
    }

    #sidebarOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
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
       All attended students  (same design language as the scan page)
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
        font-family: 'Public Sans', system-ui, sans-serif;
        color: var(--ink);
        -webkit-font-smoothing: antialiased;
        max-width: 1400px;
        margin-inline: auto;
        padding: 8px 0 36px;
        line-height: 1.5;
    }

    .at *,
    .at *::before,
    .at *::after {
        box-sizing: border-box;
    }

    /* header */
    .at-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 22px;
    }

    .at-head h1 {
        margin: 0;
        font-size: clamp(24px, 3vw, 32px);
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.1;
        color: var(--ink);
    }

    .at-head p {
        margin: 6px 0 0;
        font-size: 14px;
        color: var(--muted);
    }

    .at-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff;
        color: var(--ink);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: background .15s, border-color .15s;
    }

    .at-back:hover {
        background: var(--soft);
        border-color: #aeb8ca;
        color: var(--ink);
        text-decoration: none;
    }

    .at-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
    }

    /* summary cards */
    .at-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 18px;
    }

    .at-kpi {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
    }

    .at-kpi-ic {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        font-size: 17px;
        background: var(--brand-bg);
        color: var(--brand);
    }

    .at-kpi.ok .at-kpi-ic {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .at-kpi.warn .at-kpi-ic {
        background: var(--warn-bg);
        color: var(--warn);
    }

    .at-kpi span.k {
        display: block;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .at-kpi strong {
        display: block;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.15;
        font-variant-numeric: tabular-nums;
    }

    .at-kpi.ok strong {
        color: var(--ok);
    }

    .at-kpi.warn strong {
        color: var(--warn);
    }

    /* filters */
    .at-filters {
        padding: 18px 20px 16px;
        margin-bottom: 18px;
    }

    .at-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px 16px;
    }

    .at-field label {
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0 0 6px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .at-field label i {
        color: #97a2b6;
        font-size: 11px;
    }

    .at-filter-foot {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid var(--line);
    }

    .at-reset {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff;
        color: var(--ink);
        font: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s;
    }

    .at-reset:hover {
        background: var(--soft);
    }

    #filterInfo {
        font-size: 13px;
        color: var(--muted);
    }

    #filterInfo i {
        color: var(--brand);
        margin-right: 4px;
    }

    #filterInfo strong {
        color: var(--ink);
    }

    /* Select2 to match */
    .at .select2-container {
        width: 100% !important;
    }

    .at .select2-container--default .select2-selection--single {
        height: 40px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff;
        outline: 0;
    }

    .at .select2-container--default.select2-container--focus .select2-selection--single,
    .at .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .14);
    }

    .at .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
        padding-right: 44px;
        font-size: 14px;
        font-weight: 500;
        color: var(--ink);
    }

    .at .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #7b879b;
    }

    .at .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        right: 6px;
    }

    .at .select2-container--default .select2-selection--single .select2-selection__clear {
        margin-right: 8px;
        color: #7b879b;
        font-size: 18px;
    }

    .select2-dropdown {
        border-color: #c4ccd9 !important;
        border-radius: 10px !important;
        overflow: hidden;
        font-family: 'Public Sans', system-ui, sans-serif;
        font-size: 14px;
        box-shadow: 0 12px 30px rgba(23, 35, 61, .14);
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: var(--brand-bg, #e8eefb) !important;
        color: #173a91 !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #f4f6fb !important;
        font-weight: 600;
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
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--line);
    }

    .at-btns .dt-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin: 0;
    }

    .at .dt-buttons .btn.at-b {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin: 0;
        padding: 7px 14px;
        border: 1px solid #c4ccd9;
        border-radius: 9px;
        background: #fff;
        color: var(--ink);
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        box-shadow: none;
        transition: background .15s, border-color .15s;
    }

    .at .dt-buttons .btn.at-b:hover,
    .at .dt-buttons .btn.at-b:focus {
        background: var(--soft);
        border-color: #aeb8ca;
        color: var(--ink);
        box-shadow: none;
    }

    .at .dt-buttons .btn.at-b i {
        font-size: 13px;
    }

    .at .dt-buttons .at-xl i {
        color: #1d6f42;
    }

    .at .dt-buttons .at-pdf i {
        color: #c0372f;
    }

    .at .dt-buttons .at-csv i {
        color: #0f8a8a;
    }

    .at .dt-buttons .at-prt i {
        color: #5f6b7e;
    }

    .at .dt-buttons .at-cp i {
        color: var(--brand);
    }

    .at-search .dataTables_filter {
        margin: 0;
        text-align: right;
    }

    .at-search .dataTables_filter label {
        display: flex;
        align-items: center;
        margin: 0;
        font-size: 0;
    }

    .at-search .dataTables_filter input {
        width: 260px;
        max-width: 100%;
        height: 40px;
        margin: 0;
        padding: 0 14px 0 38px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%237b879b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='7'/%3E%3Cpath d='m21 21-4.3-4.3'/%3E%3C/svg%3E") no-repeat 12px center;
        font: inherit;
        font-size: 14px;
        color: var(--ink);
        outline: 0;
    }

    .at-search .dataTables_filter input:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .14);
    }

    .at-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    table#dataTable {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13.5px;
    }

    table#dataTable thead th {
        padding: 12px 16px;
        border: 0;
        border-bottom: 1px solid var(--line);
        background: var(--soft);
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        white-space: nowrap;
        color: var(--muted);
        outline: 0;
    }

    table#dataTable tbody td {
        padding: 12px 16px;
        border: 0;
        border-bottom: 1px solid #eef1f6;
        background: #fff;
        vertical-align: middle;
        color: var(--ink);
    }

    table#dataTable tbody tr:hover td {
        background: #f8faff;
    }

    table#dataTable tbody tr:last-child td {
        border-bottom: 0;
    }

    table#dataTable.dataTable>thead>tr>th.sorting::before,
    table#dataTable.dataTable>thead>tr>th.sorting::after,
    table#dataTable.dataTable>thead>tr>th.sorting_asc::before,
    table#dataTable.dataTable>thead>tr>th.sorting_asc::after,
    table#dataTable.dataTable>thead>tr>th.sorting_desc::before,
    table#dataTable.dataTable>thead>tr>th.sorting_desc::after {
        right: 8px;
    }

    .at-n {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12.5px;
        color: #8a95a8;
    }

    .at-id {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 6px;
        background: var(--soft);
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        white-space: nowrap;
    }

    .at-person {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 200px;
    }

    .at-ini {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 12px;
        font-weight: 700;
    }

    /* initials come from CSS so they never end up in search or exports */
    .at-ini::before {
        content: attr(data-i);
    }

    .at-person .nm {
        font-weight: 600;
        line-height: 1.3;
    }

    .at-mono {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12.5px;
        white-space: nowrap;
    }

    .at-seat {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 6px;
        background: var(--ink);
        color: #fff;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .at-na {
        color: #9aa5b8;
    }

    .at-prog {
        min-width: 220px;
        line-height: 1.35;
    }

    .at-ses {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 3px 10px;
        border-radius: 999px;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 11.5px;
        font-weight: 700;
        white-space: nowrap;
        background: var(--soft);
        color: var(--muted);
    }

    .at-ses::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .at-ses.s1 {
        background: #fdeaf2;
        color: #d6336c;
    }

    .at-ses.s2 {
        background: #f0ebfc;
        color: #7048c9;
    }

    .at-ses.s3 {
        background: #e5f6ec;
        color: #1b9a5a;
    }

    .at-st {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .at-st.ok {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .at-st.off {
        background: var(--soft);
        color: var(--muted);
    }

    .at-st::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    /* footer: info + pagination */
    .at-foot {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 20px;
        border-top: 1px solid var(--line);
    }

    .at-foot .dataTables_info {
        padding: 0;
        font-size: 13px;
        color: var(--muted);
    }

    .at-foot .dataTables_paginate {
        margin: 0;
    }

    .at-foot .pagination {
        margin: 0;
        gap: 4px;
    }

    .at-foot .page-link {
        border: 1px solid var(--line);
        border-radius: 8px !important;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
        color: var(--ink);
        box-shadow: none;
    }

    .at-foot .page-item.active .page-link {
        background: var(--brand);
        border-color: var(--brand);
        color: #fff;
    }

    .at-foot .page-item.disabled .page-link {
        color: #aab3c3;
        background: #fff;
    }

    .at-foot .page-link:hover {
        background: var(--soft);
    }

    .at-foot .page-item.active .page-link:hover {
        background: var(--brand-d);
    }

    .at-empty td {
        padding: 38px 16px !important;
        text-align: center;
        color: var(--muted);
    }

    /* laptop / desktop (about 100% zoom): sidebar fixed width, content fills the rest and never overflows */
    @media (min-width: 992px) {
        body.at-page #content-wrapper {
            flex: 1 1 auto;
            min-width: 0;
            width: auto;
        }

        .at {
            width: 100%;
        }
    }

    /* medium desktops: tighter table so all 9 columns fit without a sideways scroll */
    @media (min-width: 992px) and (max-width: 1599px) {
        table#dataTable {
            font-size: 13px;
        }

        table#dataTable thead th {
            padding: 11px 10px;
            font-size: 11px;
            letter-spacing: .03em;
        }

        table#dataTable tbody td {
            padding: 10px;
        }

        .at-person {
            min-width: 150px;
            gap: 8px;
        }

        .at-ini {
            width: 30px;
            height: 30px;
            font-size: 11px;
        }

        .at-prog {
            min-width: 150px;
        }

        .at-filters {
            padding: 16px;
        }

        .at-bar,
        .at-foot {
            padding-left: 16px;
            padding-right: 16px;
        }
    }

    /* responsive */
    @media (max-width: 1199px) {
        .at-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .at-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    /* phones + small tablets: every student becomes a card */
    @media (max-width: 767px) {
        .at-scroll {
            overflow: visible;
        }

        table#dataTable,
        table#dataTable tbody {
            display: block;
            width: 100% !important;
        }

        table#dataTable thead {
            display: none;
        }

        table#dataTable tbody tr {
            display: block;
            width: auto;
            margin: 12px;
            padding: 4px 14px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
        }

        table#dataTable tbody td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            width: 100%;
            padding: 10px 0;
            border: 0;
            border-bottom: 1px dashed var(--line);
            background: transparent !important;
            text-align: right;
        }

        table#dataTable tbody tr:last-child td {
            border-bottom: 1px dashed var(--line);
        }

        table#dataTable tbody td:last-child {
            border-bottom: 0;
        }

        table#dataTable tbody td::before {
            content: attr(data-label);
            flex: 0 0 auto;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: var(--muted);
            text-align: left;
        }

        table#dataTable tbody td[data-label="#"] {
            display: none;
        }

        .at-person {
            min-width: 0;
            justify-content: flex-end;
            text-align: right;
        }

        .at-prog {
            min-width: 0;
        }

        .at-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .at-search .dataTables_filter input {
            width: 100%;
        }

        .at-search .dataTables_filter label {
            width: 100%;
        }
    }

    @media (max-width: 575px) {
        .at-head .at-back {
            width: 100%;
            justify-content: center;
        }

        .at-grid {
            grid-template-columns: 1fr;
        }

        .at-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .at-kpi {
            gap: 10px;
            padding: 12px;
        }

        .at-kpi-ic {
            width: 36px;
            height: 36px;
            font-size: 14px;
        }

        .at-kpi strong {
            font-size: 21px;
        }

        .at-kpi span.k {
            font-size: 10.5px;
        }

        .at-filters {
            padding: 14px;
        }

        .at-btns .dt-buttons {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .at .dt-buttons .btn.at-b {
            justify-content: center;
            padding: 7px 6px;
        }

        .at-foot {
            justify-content: center;
            text-align: center;
        }

        .at-foot .page-item:not(.active):not(.previous):not(.next) {
            display: none;
        }
    }
</style>
<style>
    /* old students specific */
    .at-sel {
        width: 100%;
        height: 40px;
        padding: 0 12px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff;
        font: inherit;
        font-size: 14px;
        font-weight: 500;
        color: var(--ink);
    }

    .at-grid.three {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .at-mail {
        color: var(--brand);
        text-decoration: none;
        overflow-wrap: anywhere;
    }

    .at-mail:hover {
        text-decoration: underline;
        color: var(--brand-d);
    }

    .at-em {
        display: block;
        min-width: 150px;
        max-width: 240px;
        overflow-wrap: anywhere;
        line-height: 1.35;
    }

    .at-st.warn {
        background: var(--warn-bg);
        color: var(--warn);
    }

    .at-st.bad {
        background: #fdecea;
        color: var(--bad);
    }

    .at-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        padding: 12px 16px;
        border: 1px solid #f3c6c2;
        border-radius: 12px;
        background: #fdecea;
        color: var(--bad);
        font-size: 14px;
        font-weight: 600;
    }

    @media (min-width: 992px) and (max-width: 1699px) {
        .at-em {
            min-width: 130px;
            max-width: 190px;
        }
    }

    @media (max-width: 1199px) {
        .at-grid.three {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .at-em {
            min-width: 0;
            max-width: none;
            text-align: right;
        }
    }

    @media (max-width: 575px) {
        .at-grid.three {
            grid-template-columns: 1fr;
        }
    }
</style>

<style>
    /* fit the table to the card: columns share the width and text wraps, so no sideways scroll */
    @media (min-width: 768px) {
        .at-scroll {
            overflow-x: visible;
        }

        table#dataTable {
            table-layout: fixed;
            width: 100% !important;
            font-size: 12.5px;
        }

        table#dataTable thead th {
            padding: 11px 22px 11px 8px;
            font-size: 10.5px;
            letter-spacing: .02em;
            white-space: normal;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }

        table#dataTable tbody td {
            padding: 10px 8px;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        table#dataTable th:nth-child(1) {
            width: 3%;
        }

        table#dataTable th:nth-child(2) {
            width: 6.5%;
        }

        table#dataTable th:nth-child(3) {
            width: 7%;
        }

        table#dataTable th:nth-child(4) {
            width: 13%;
        }

        table#dataTable th:nth-child(5) {
            width: 7%;
        }

        table#dataTable th:nth-child(6) {
            width: 8%;
        }

        table#dataTable th:nth-child(7) {
            width: 12%;
        }

        table#dataTable th:nth-child(8) {
            width: 12%;
        }

        table#dataTable th:nth-child(9) {
            width: 8%;
        }

        table#dataTable th:nth-child(10) {
            width: 8%;
        }

        table#dataTable th:nth-child(11) {
            width: 8.5%;
        }

        table#dataTable th:nth-child(12) {
            width: 7%;
        }

        table#dataTable .at-person {
            min-width: 0;
            gap: 8px;
        }

        table#dataTable .at-person .nm {
            min-width: 0;
        }

        table#dataTable .at-prog,
        table#dataTable .at-em {
            min-width: 0;
            max-width: none;
        }

        table#dataTable .at-mono,
        table#dataTable .at-id {
            white-space: normal;
            overflow-wrap: anywhere;
            font-size: 11.5px;
        }

        table#dataTable .at-id {
            padding: 2px 6px;
        }

        table#dataTable .at-st {
            padding: 3px 9px;
            font-size: 11px;
            white-space: normal;
            gap: 5px;
        }

        table#dataTable .at-ini {
            width: 28px;
            height: 28px;
            font-size: 10.5px;
        }
    }

    @media (min-width: 768px) and (max-width: 1199px) {
        table#dataTable .at-ini {
            display: none;
        }
    }
</style>

<style>
    /* six filters in two tidy rows of three */
    .at-grid.six {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    @media (max-width: 1199px) {
        .at-grid.six {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575px) {
        .at-grid.six {
            grid-template-columns: 1fr;
        }
    }

    table#dataTable .at-ses {
        padding: 3px 8px;
        font-size: 10.5px;
        white-space: normal;
        overflow-wrap: anywhere;
    }
</style>

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
                            <h1>Old Students Database</h1>
                            <p>Previous students with their invitation number, fee status and registration status.</p>
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
                            <div><span class="k">Total students</span><strong id="kpiTotal"><?php echo $totalStudents; ?></strong></div>
                        </div>
                        <div class="at-card at-kpi ok">
                            <span class="at-kpi-ic"><i class="fas fa-user-check" aria-hidden="true"></i></span>
                            <div><span class="k">Registered</span><strong id="kpiReg"><?php echo $totalRegistered; ?></strong></div>
                        </div>
                        <div class="at-card at-kpi warn">
                            <span class="at-kpi-ic"><i class="fas fa-user-clock" aria-hidden="true"></i></span>
                            <div><span class="k">Not registered</span><strong id="kpiNot"><?php echo $totalNotRegistered; ?></strong></div>
                        </div>
                        <div class="at-card at-kpi">
                            <span class="at-kpi-ic"><i class="fas fa-receipt" aria-hidden="true"></i></span>
                            <div><span class="k">Course fee paid</span><strong id="kpiPaid"><?php echo $totalPaid; ?></strong></div>
                        </div>
                    </section>

                    <!-- filters -->
                    <section class="at-card at-filters">
                        <div class="at-grid six">
                            <div class="at-field">
                                <label for="sessionFilter"><i class="fas fa-filter"></i> Session</label>
                                <select id="sessionFilter" class="at-sel">
                                    <option value="">All Sessions</option>
                                    <?php foreach ($sessionList as $ses): ?>
                                        <option value="<?php echo at_h($ses); ?>"><?php echo at_h($ses); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="at-field">
                                <label for="programNameFilter"><i class="fas fa-filter"></i> Program name (no batch)</label>
                                <select id="programNameFilter" class="at-sel">
                                    <option value="">All Program Names</option>
                                    <?php foreach ($programNames as $pn): ?>
                                        <option value="<?php echo at_h($pn); ?>"><?php echo at_h($pn); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="at-field">
                                <label for="programFilter"><i class="fas fa-filter"></i> Program (with batch)</label>
                                <select id="programFilter" class="at-sel">
                                    <option value="">All Programs</option>
                                    <?php
                                    try {
                                        $program_result = mysqli_query($conn, "SELECT DISTINCT program FROM old_student_db WHERE program IS NOT NULL AND program != '' ORDER BY program ASC");
                                        if ($program_result && mysqli_num_rows($program_result) > 0) {
                                            while ($program_row = mysqli_fetch_assoc($program_result)) {
                                                $p = at_h($program_row['program']);
                                                echo "<option value='" . $p . "'>" . $p . "</option>";
                                            }
                                        }
                                    } catch (Exception $e) {
                                        error_log("Program filter error: " . $e->getMessage());
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="at-field">
                                <label for="statusFilter"><i class="fas fa-filter"></i> Registration status</label>
                                <select id="statusFilter" class="at-sel">
                                    <option value="">All Status</option>
                                    <option value="Registered">Registered</option>
                                    <option value="N/A">Not Registered</option>
                                </select>
                            </div>

                            <div class="at-field">
                                <label for="feeFilter"><i class="fas fa-filter"></i> Course fee status</label>
                                <select id="feeFilter" class="at-sel">
                                    <option value="">All Fee Status</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Pending">Pending</option>
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
                                    <th>Invitation Number</th>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Date of Birth</th>
                                    <th>Phone No</th>
                                    <th>Given Email</th>
                                    <th>Program</th>
                                    <th>Session</th>
                                    <th>Course Fee Status</th>
                                    <th>Registered / Not</th>
                                    <th>Active</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $counter = 1;
                                foreach ($rows as $row):
                                    $student_id = trim((string)($row['student_id'] ?? ''));
                                    $name       = trim((string)($row['name'] ?? ''));
                                    $dob        = trim((string)($row['DOB'] ?? ''));
                                    $mobile     = trim((string)($row['mobile_no'] ?? ''));
                                    $email      = trim((string)($row['given_email'] ?? ''));
                                    $program    = trim((string)($row['program'] ?? ''));
                                    $in_no      = trim((string)($row['in_no'] ?? ''));
                                    $payment    = trim((string)($row['payment_status'] ?? ''));
                                    $status     = trim((string)($row['status'] ?? ''));
                                    $session    = trim((string)($row['session_name'] ?? ''));
                                    $sesCls     = '';
                                    if (preg_match('/(\d+)/', $session, $m)) $sesCls = 's' . (int)$m[1];
                                    if ($payment === '') $payment = 'N/A';
                                    if ($status === '')  $status  = 'N/A';

                                    $payCls = 'off';
                                    if (strcasecmp($payment, 'paid') === 0)    $payCls = 'ok';
                                    elseif (strcasecmp($payment, 'pending') === 0) $payCls = 'warn';

                                    $active = trim((string)($row['active'] ?? ''));
                                    if ($active === '') $active = 'N/A';
                                    $activeLower = strtolower($active);
                                    $actCls = 'off';
                                    if (in_array($activeLower, ['completed', 'complete', 'yes', 'active', '1', 'true'], true))                     $actCls = 'ok';
                                    elseif (in_array($activeLower, ['not-completed', 'not completed', 'no', 'inactive', '0', 'false'], true))     $actCls = 'warn';

                                    $stCls = 'off';
                                    if (strcasecmp($status, 'registered') === 0)           $stCls = 'ok';
                                    elseif (strcasecmp($status, 'not registered') === 0)   $stCls = 'bad';
                                ?>
                                    <tr data-pname="<?php echo at_h(at_base_program($program)); ?>"
                                        data-reg="<?php echo $stCls === 'ok' ? 1 : 0; ?>"
                                        data-paid="<?php echo $payCls === 'ok' ? 1 : 0; ?>">
                                        <td data-label="#"><span class="at-n"><?php echo $counter++; ?></span></td>
                                        <td data-label="Invitation no"><?php echo $in_no !== '' ? '<span class="at-mono">' . at_h($in_no) . '</span>' : '<span class="at-na">N/A</span>'; ?></td>
                                        <td data-label="Student ID"><?php echo $student_id !== '' ? '<span class="at-id">' . at_h($student_id) . '</span>' : '<span class="at-na">N/A</span>'; ?></td>
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
                                        <td data-label="Date of birth"><?php echo $dob !== '' ? '<span class="at-mono">' . at_h($dob) . '</span>' : '<span class="at-na">N/A</span>'; ?></td>
                                        <td data-label="Phone"><?php echo $mobile !== '' ? '<span class="at-mono">' . at_h($mobile) . '</span>' : '<span class="at-na">N/A</span>'; ?></td>
                                        <td data-label="Email"><?php echo $email !== '' ? '<span class="at-em"><a class="at-mail" href="mailto:' . at_h($email) . '">' . at_h($email) . '</a></span>' : '<span class="at-na">N/A</span>'; ?></td>
                                        <td data-label="Program"><?php echo $program !== '' ? '<div class="at-prog">' . at_h($program) . '</div>' : '<span class="at-na">N/A</span>'; ?></td>
                                        <td data-label="Session"><?php echo $session !== '' ? '<span class="at-ses ' . $sesCls . '">' . at_h($session) . '</span>' : '<span class="at-na">N/A</span>'; ?></td>
                                        <td data-label="Course fee"><span class="at-st <?php echo $payCls; ?>"><?php echo at_h($payment); ?></span></td>
                                        <td data-label="Registration"><span class="at-st <?php echo $stCls; ?>"><?php echo at_h($status); ?></span></td>
                                        <td data-label="Active"><span class="at-st <?php echo $actCls; ?>"><?php echo at_h($active); ?></span></td>
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
            // Nicer dropdowns when Select2 is available (falls back to the styled native select)
            if ($.fn.select2) {
                $('#sessionFilter').select2({
                    placeholder: 'All Sessions',
                    allowClear: true,
                    width: '100%'
                });
                $('#programNameFilter').select2({
                    placeholder: 'All Program Names',
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
                $('#feeFilter').select2({
                    placeholder: 'All Fee Status',
                    allowClear: true,
                    width: '100%'
                });
                $('#activeFilter').select2({
                    placeholder: 'All Active Status',
                    allowClear: true,
                    width: '100%'
                });
            }

            var table = $('#dataTable').DataTable({
                "autoWidth": false,
                "pageLength": 200,
                "order": [
                    [1, "asc"]
                ],
                "dom": "<'at-bar'<'at-btns'B><'at-search'f>><'at-scroll'rt><'at-foot'<'at-info'i><'at-pg'p>>",
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search students…",
                    "emptyTable": "No student records available",
                    "zeroRecords": "No matching student records found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ students",
                    "infoEmpty": "Showing 0 to 0 of 0 students",
                    "infoFiltered": "(filtered from _MAX_ total students)"
                },
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-sm at-b at-xl',
                        title: 'Old Students Database',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-sm at-b at-pdf',
                        title: 'Old Students Database',
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
                        title: 'Old Students Database',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-sm at-b at-prt',
                        title: 'Old Students Database',
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

            // Column positions: 7 = Program, 8 = Session, 9 = Course fee, 10 = Registered / Not, 11 = Active
            // Sessions and their programs come from data_tables (built in PHP)
            var sessionPrograms = <?php echo json_encode($sessionPrograms, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
            var allPrograms = [];
            $('#programFilter option').each(function() {
                if (this.value !== '') allPrograms.push(this.value);
            });

            // "Higher Diploma in Biomedical Science - Batch 29" -> "Higher Diploma in Biomedical Science"
            function baseName(p) {
                return $.trim(p).replace(/\s*-\s*Batch\s*\d+\s*$/i, '');
            }

            // programs allowed by the chosen session (all programs when no session) and the chosen program name
            function programsFor(session, name) {
                var list = session && sessionPrograms[session] ? sessionPrograms[session] : allPrograms;
                if (name) {
                    list = $.grep(list, function(p) {
                        return baseName(p) === name;
                    });
                }
                return list;
            }

            function fillNames(session) {
                var seen = {},
                    names = [];
                $.each(programsFor(session, ''), function(_, p) {
                    var n = baseName(p);
                    if (n && !seen[n]) {
                        seen[n] = true;
                        names.push(n);
                    }
                });
                names.sort(function(x, y) {
                    return x.localeCompare(y, undefined, {
                        sensitivity: 'base',
                        numeric: true
                    });
                });
                var $n = $('#programNameFilter').empty().append($('<option>').val('').text('All Program Names'));
                $.each(names, function(_, n) {
                    $n.append($('<option>').val(n).text(n));
                });
                $n.val('');
            }

            function fillPrograms(session, name) {
                var $p = $('#programFilter').empty().append($('<option>').val('').text('All Programs'));
                $.each(programsFor(session, name), function(_, p) {
                    $p.append($('<option>').val(p).text(p));
                });
                $p.val('');
            }

            // "Program name" has no column of its own: each row carries its name in data-pname
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'dataTable') return true;
                var name = $('#programNameFilter').val();
                if (!name) return true;
                var tr = settings.aoData[dataIndex] && settings.aoData[dataIndex].nTr;
                return !!tr && tr.getAttribute('data-pname') === name;
            });

            // the four summary cards follow whatever is currently filtered / searched
            function updateKpis() {
                var total = 0,
                    reg = 0,
                    paid = 0;
                table.rows({
                    search: 'applied'
                }).nodes().each(function(tr) {
                    total++;
                    if (tr.getAttribute('data-reg') === '1') reg++;
                    if (tr.getAttribute('data-paid') === '1') paid++;
                });
                $('#kpiTotal').text(total);
                $('#kpiReg').text(reg);
                $('#kpiNot').text(total - reg);
                $('#kpiPaid').text(paid);
            }

            function esc(v) {
                return $('<div>').text(v).html();
            }

            function updateFilterInfo() {
                var session = $('#sessionFilter').val();
                var pname = $('#programNameFilter').val();
                var program = $('#programFilter').val();
                var status = $('#statusFilter').val();
                var fee = $('#feeFilter').val();
                var active = $('#activeFilter').val();
                var info = table.page.info();
                var parts = [];

                if (session) parts.push('Session: <strong>' + esc(session) + '</strong>');
                if (pname) parts.push('Program name: <strong>' + esc(pname) + '</strong>');
                if (program) parts.push('Program: <strong>' + esc(program) + '</strong>');
                if (status) parts.push('Status: <strong>' + (status === 'N/A' ? 'Not Registered' : esc(status)) + '</strong>');
                if (fee) parts.push('Fee: <strong>' + esc(fee) + '</strong>');
                if (active) parts.push('Active: <strong>' + (active === 'completed' ? 'Completed' : 'Not completed') + '</strong>');

                if (parts.length) {
                    $('#filterInfo').html('<i class="fas fa-info-circle"></i> Showing <strong>' + info.recordsDisplay + '</strong> student(s) · ' + parts.join(' · '));
                } else {
                    $('#filterInfo').html('');
                }
            }

            // session -> narrows the program name list and the program list
            $('#sessionFilter').on('change', function() {
                var v = $(this).val() || '';
                fillNames(v);
                fillPrograms(v, '');
                $('#programNameFilter, #programFilter').val('').trigger('change.select2');
                table.column(7).search('');
                table.column(8).search(v === '' ? '' : '^' + $.fn.dataTable.util.escapeRegex(v) + '$', true, false).draw();
            });

            // program name (no batch) -> narrows the program list to its batches
            $('#programNameFilter').on('change', function() {
                var v = $(this).val() || '';
                fillPrograms($('#sessionFilter').val() || '', v);
                $('#programFilter').val('').trigger('change.select2');
                table.column(7).search('').draw();
            });

            $('#programFilter').on('change', function() {
                var v = $(this).val() || '';
                table.column(7).search(v === '' ? '' : '^' + $.fn.dataTable.util.escapeRegex(v) + '$', true, false).draw();
            });

            $('#statusFilter').on('change', function() {
                var v = $(this).val() || '';
                if (v === '') {
                    table.column(10).search('').draw();
                } else if (v === 'N/A') {
                    table.column(10).search('^(N/A|Not Registered)$', true, false).draw();
                } else {
                    table.column(10).search('^' + $.fn.dataTable.util.escapeRegex(v) + '$', true, false).draw();
                }
            });

            $('#feeFilter').on('change', function() {
                var v = $(this).val() || '';
                table.column(9).search(v === '' ? '' : '^' + $.fn.dataTable.util.escapeRegex(v) + '$', true, false).draw();
            });

            $('#activeFilter').on('change', function() {
                var v = $(this).val() || '';
                if (v === '') {
                    table.column(11).search('').draw();
                } else if (v === 'completed') {
                    table.column(11).search('^completed$', true, false).draw();
                } else {
                    table.column(11).search('^not[- ]completed$', true, false).draw();
                }
            });

            $('#resetFilter').on('click', function() {
                fillNames('');
                fillPrograms('', '');
                $('#sessionFilter, #programNameFilter, #programFilter, #statusFilter, #feeFilter, #activeFilter').val('').trigger('change.select2');
                table.columns().search('').draw();
            });

            table.on('draw', function() {
                updateFilterInfo();
                updateKpis();
            });
            updateFilterInfo();
            updateKpis();

        } catch (e) {
            console.error("DataTable initialization error:", e);
            alert("Error loading table features. Please refresh the page.");
        }
    });
</script>

</body>

</html>