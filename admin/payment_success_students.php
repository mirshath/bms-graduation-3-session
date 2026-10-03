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

/* ------------------------------------------------------------------
   Load everything first, so the summary cards and the table use the
   same data. (Same queries and receipt-PDF lookup as before.)
------------------------------------------------------------------ */
$programs  = [];
$rows      = [];
$loadError = false;

// ✅ Unique programs for the filter
try {
    $pq = mysqli_query($conn, "SELECT DISTINCT program_name FROM payment_records WHERE program_name IS NOT NULL AND program_name != '' ORDER BY program_name ASC");
    while ($pq && ($pr = mysqli_fetch_assoc($pq))) {
        $programs[] = $pr['program_name'];
    }
} catch (Throwable $e) {
    error_log("Program filter error: " . $e->getMessage());
}

// ✅ Payment records
try {
    $result = mysqli_query($conn, "SELECT p.student_id, r.name_in_full, p.program_name, p.extra_ticket_count,
                p.total_amount, p.receipt_number, p.payment_date, p.created_by
            FROM payment_records p
            LEFT JOIN registered_students r ON p.student_id = r.student_id
            ORDER BY p.payment_date DESC");

    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }

    $receipts_dir      = __DIR__ . "/saved_receipts/";
    $receipts_dir_real = realpath($receipts_dir);

    while ($row = mysqli_fetch_assoc($result)) {
        // ✅ Sanitize values for file operations
        $safe_receipt    = preg_replace('/[^a-zA-Z0-9\-_]/', '', (string)$row['receipt_number']);
        $safe_student_id = preg_replace('/[^a-zA-Z0-9\-_]/', '', (string)$row['student_id']);

        // ✅ Search for the PDF with proper validation (prevents directory traversal)
        $pdf_relative_path = '';
        if ($safe_receipt !== '' && $safe_student_id !== '') {
            $matching_files = glob($receipts_dir . "Receipt-" . $safe_receipt . "-" . $safe_student_id . "-*.pdf");
            if (!empty($matching_files) && is_array($matching_files)) {
                $pdf_full_path = realpath($matching_files[0]);
                if (
                    $pdf_full_path && $receipts_dir_real && strpos($pdf_full_path, $receipts_dir_real) === 0
                    && pathinfo($pdf_full_path, PATHINFO_EXTENSION) === 'pdf' && file_exists($pdf_full_path)
                ) {
                    $pdf_relative_path = "saved_receipts/" . basename($pdf_full_path);
                }
            }
        }

        // ✅ Format date properly with error handling
        $ts = !empty($row['payment_date']) ? strtotime($row['payment_date']) : false;

        $rows[] = [
            'student_id' => (string)$row['student_id'],
            'name'       => $row['name_in_full'] ?? 'N/A',
            'program'    => (string)$row['program_name'],
            'extra'      => (int)$row['extra_ticket_count'],
            'amount'     => (float)$row['total_amount'],
            'receipt'    => $safe_receipt,
            'pdf'        => $pdf_relative_path,
            'ts'         => $ts,
            'by'         => ($row['created_by'] ?? '') !== '' ? (string)$row['created_by'] : '-',
        ];
    }
} catch (Throwable $e) {
    $loadError = true;
    error_log("Payment Records Error: " . $e->getMessage());
}

// ✅ Summary numbers (the page script keeps these in sync with the filters)
$totalCount   = count($rows);
$totalAmount  = array_sum(array_column($rows, 'amount'));
$totalExtra   = array_sum(array_column($rows, 'extra'));
$totalMissing = count(array_filter($rows, function ($r) {
    return $r['pdf'] === '';
}));

function pp($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .pp {
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
        zoom: 0.9;
        /* page size: 0.9 = 90%  (change to 0.85 for 85%, 1 for normal) */
        max-width: 1440px;
        margin-inline: auto;
        padding: 8px 0 36px;
        font-family: 'Public Sans', system-ui, sans-serif;
        color: var(--ink);
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
    }

    .pp *,
    .pp *::before,
    .pp *::after {
        box-sizing: border-box;
    }

    /* header */
    .pp-head {
        margin: 16px 0 22px;
    }

    .pp-head h1 {
        margin: 0;
        font-size: clamp(24px, 3vw, 32px);
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.1;
        color: var(--ink);
    }

    .pp-head p {
        margin: 6px 0 0;
        font-size: 14px;
        color: var(--muted);
    }

    /* summary cards */
    .pp-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .pp-kpi {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
    }

    .pp-kic {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 18px;
    }

    .pp-kpi.money .pp-kic {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .pp-kpi.tix .pp-kic {
        background: var(--warn-bg);
        color: var(--warn);
    }

    .pp-kpi.miss .pp-kic {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .pp-kpi.miss.has-miss .pp-kic {
        background: var(--bad-bg);
        color: var(--bad);
    }

    .pp-kpi.miss.has-miss strong {
        color: var(--bad);
    }

    .pp-kpi .k {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
    }

    .pp-kpi strong {
        display: block;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: clamp(19px, 1.7vw, 24px);
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.25;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
        color: var(--ink);
    }

    .pp-kpi>div {
        min-width: 0;
    }

    /* main card */
    .pp-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
        overflow: hidden;
    }

    .pp-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 18px 18px 0;
        padding: 12px 14px;
        border-radius: 10px;
        background: var(--bad-bg);
        color: var(--bad);
        font-size: 14px;
        font-weight: 600;
    }

    /* filters */
    .pp-filters {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 14px;
        padding: 18px;
        border-bottom: 1px solid var(--line);
    }

    .pp-f {
        flex: 1 1 320px;
        max-width: 520px;
        min-width: 0;
    }

    .pp-f label {
        display: block;
        margin: 0 0 6px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .pp-reset {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 42px;
        padding: 0 16px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff;
        font: inherit;
        font-size: 14px;
        font-weight: 600;
        color: var(--ink);
        cursor: pointer;
        transition: border-color .15s ease, color .15s ease, background-color .15s ease;
    }

    .pp-reset:hover {
        border-color: var(--brand);
        color: var(--brand);
    }

    .pp-reset:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    .pp-finfo {
        display: none;
        align-items: center;
        gap: 8px;
        height: 42px;
        padding: 0 14px;
        border-radius: 10px;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 13px;
        font-weight: 600;
    }

    .pp-finfo.on {
        display: inline-flex;
    }

    /* select2 to match the inputs (the dropdown is rendered outside .pp, so plain colours) */
    .pp .select2-container {
        width: 100% !important;
    }

    .pp .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .pp .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding: 0 40px 0 14px;
        line-height: 40px;
        font-size: 14px;
        font-weight: 500;
        color: #17233d;
    }

    .pp .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #8b95a7;
    }

    .pp .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }

    .pp .select2-container--default .select2-selection--single .select2-selection__clear {
        height: 40px;
        margin-right: 6px;
        font-size: 18px;
        line-height: 40px;
        color: #8b95a7;
    }

    .pp .select2-container--default.select2-container--focus .select2-selection--single,
    .pp .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #1f4bb6;
        box-shadow: 0 0 0 4px rgba(31, 75, 182, .15);
        outline: 0;
    }

    .select2-dropdown {
        border-color: #c4ccd9 !important;
        border-radius: 10px !important;
        overflow: hidden;
        box-shadow: 0 14px 34px rgba(15, 23, 42, .18);
        font-family: 'Public Sans', system-ui, sans-serif;
        font-size: 14px;
    }

    .select2-container--default .select2-results__option {
        padding: 9px 14px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected],
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background: #1f4bb6 !important;
        color: #fff !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #c4ccd9;
        border-radius: 8px;
        padding: 7px 10px;
    }

    /* DataTables toolbar: search on the left, export buttons on the right */
    .pp .dataTables_wrapper {
        padding: 0;
    }

    .pp-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--line);
    }

    .pp-bar-l,
    .pp-bar-r {
        min-width: 0;
    }

    .pp .dataTables_filter {
        margin: 0;
        text-align: left;
    }

    .pp .dataTables_filter label {
        display: block;
        margin: 0;
    }

    .pp .dataTables_filter input.form-control {
        display: block;
        width: min(340px, 100%) !important;
        height: 40px;
        margin: 0 !important;
        padding: 0 14px 0 40px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%238b95a7' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='7'/%3E%3Cpath d='m20 20-3.5-3.5'/%3E%3C/svg%3E") no-repeat 14px center;
        font-size: 14px;
        color: var(--ink);
        box-shadow: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .pp .dataTables_filter input.form-control:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(31, 75, 182, .15);
        outline: 0;
    }

    .pp .dt-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin: 0 !important;
    }

    .pp .dt-buttons .dt-button,
    .pp .dt-buttons .btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 40px;
        margin: 0 !important;
        padding: 0 14px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff;
        background-image: none;
        font-size: 13px;
        font-weight: 600;
        color: var(--ink);
        box-shadow: none;
        transition: border-color .15s ease, color .15s ease, background-color .15s ease;
    }

    .pp .dt-buttons .dt-button:hover,
    .pp .dt-buttons .btn:hover,
    .pp .dt-buttons .btn:focus {
        border-color: var(--brand);
        background: var(--brand-bg);
        color: var(--brand);
        box-shadow: none;
    }

    .pp .dt-buttons .dt-button:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    .pp .dt-buttons .xl i {
        color: #1b7f4b;
    }

    .pp .dt-buttons .pdf i {
        color: #c0372f;
    }

    .pp .dt-buttons .csv i {
        color: #0f8a8a;
    }

    .pp .dt-buttons .prt i {
        color: #5f6b7e;
    }

    .pp .dt-buttons .cpy i {
        color: #a85d00;
    }

    /* table */
    .pp-scroll {
        overflow-x: auto;
    }

    .pp table.pp-table {
        width: 100% !important;
        min-width: 900px;
        margin: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13.5px;
        color: var(--ink);
    }

    .pp table.pp-table thead th {
        padding: 12px 22px 12px 12px;
        border: 0 !important;
        border-bottom: 1px solid var(--line) !important;
        background: var(--soft);
        font-size: 11.5px;
        font-weight: 700;
        line-height: 1.3;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .pp table.pp-table tbody td {
        padding: 13px 12px;
        border: 0;
        border-bottom: 1px solid var(--line);
        vertical-align: middle;
        background: #fff;
        color: var(--ink);
    }

    .pp table.pp-table tbody tr:hover td {
        background: #f8faff;
    }

    .pp table.pp-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .pp table.pp-table .num {
        text-align: right;
    }

    .pp-n {
        color: var(--muted);
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12px;
    }

    .pp-id {
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

    .pp-name {
        font-weight: 600;
        min-width: 130px;
    }

    .pp-name.na {
        font-weight: 500;
        color: var(--muted);
    }

    .pp-prog {
        min-width: 170px;
        max-width: 240px;
        line-height: 1.4;
        color: #3b4760;
    }

    .pp-x {
        display: inline-grid;
        place-items: center;
        min-width: 30px;
        height: 24px;
        padding: 0 9px;
        border-radius: 999px;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12px;
        font-weight: 700;
    }

    .pp-x.on {
        background: var(--brand-bg);
        color: var(--brand);
    }

    .pp-x.off {
        background: var(--soft);
        color: #8b95a7;
    }

    .pp-amt {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .pp-rc {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 11px;
        border-radius: 8px;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        transition: background-color .15s ease, color .15s ease;
    }

    .pp-rc:hover {
        background: var(--brand);
        color: #fff;
        text-decoration: none;
    }

    .pp-rc.miss {
        background: var(--bad-bg);
        color: var(--bad);
    }

    .pp-dt {
        white-space: nowrap;
    }

    .pp-d {
        display: block;
        font-weight: 600;
    }

    .pp-t {
        display: block;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12px;
        color: var(--muted);
    }

    .pp-by {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        font-weight: 500;
    }

    .pp-by::before {
        content: attr(data-i);
        display: grid;
        place-items: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--ink);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
    }

    /* footer: info + pagination */
    .pp-foot {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 18px;
        border-top: 1px solid var(--line);
    }

    .pp .dataTables_info {
        padding: 0 !important;
        font-size: 13px;
        color: var(--muted);
    }

    .pp .dataTables_paginate {
        margin: 0;
        padding: 0;
    }

    .pp .pagination {
        margin: 0;
        gap: 4px;
    }

    .pp .pagination .page-link {
        min-width: 36px;
        padding: 6px 12px;
        border: 1px solid var(--line);
        border-radius: 8px !important;
        background: #fff;
        font-size: 13px;
        font-weight: 600;
        text-align: center;
        color: var(--ink);
        box-shadow: none;
    }

    .pp .pagination .page-link:hover {
        border-color: var(--brand);
        background: var(--brand-bg);
        color: var(--brand);
    }

    .pp .pagination .page-item.active .page-link {
        border-color: var(--brand);
        background: var(--brand);
        color: #fff;
    }

    .pp .pagination .page-item.disabled .page-link {
        background: #fff;
        color: #a4adbd;
    }

    @media (max-width: 1279px) {
        .pp-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575px) {
        .pp-kpis {
            grid-template-columns: 1fr;
        }

        .pp-f {
            flex-basis: 100%;
            max-width: none;
        }

        .pp .dataTables_filter input.form-control {
            width: 100% !important;
        }

        .pp-bar-l,
        .pp-bar-r {
            width: 100%;
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

            <div class="container-fluid">
                <div class="pp">

                    <header class="pp-head">
                        <h1>Paid students</h1>
                        <p>Graduation payment records, extra tickets and receipts.</p>
                    </header>

                    <!-- Summary (follows the filters and the search box) -->
                    <section class="pp-kpis" aria-label="Payment summary">
                        <div class="pp-kpi">
                            <span class="pp-kic" aria-hidden="true"><i class="fas fa-receipt"></i></span>
                            <div>
                                <span class="k">Payments</span>
                                <strong id="kpiCount"><?php echo number_format($totalCount); ?></strong>
                            </div>
                        </div>
                        <div class="pp-kpi money">
                            <span class="pp-kic" aria-hidden="true"><i class="fas fa-money-bill-wave"></i></span>
                            <div>
                                <span class="k">Total collected (Rs.)</span>
                                <strong id="kpiAmount"><?php echo number_format($totalAmount, 2); ?></strong>
                            </div>
                        </div>
                        <div class="pp-kpi tix">
                            <span class="pp-kic" aria-hidden="true"><i class="fas fa-ticket-alt"></i></span>
                            <div>
                                <span class="k">Extra tickets</span>
                                <strong id="kpiExtra"><?php echo number_format($totalExtra); ?></strong>
                            </div>
                        </div>
                        <div class="pp-kpi miss<?php echo $totalMissing > 0 ? ' has-miss' : ''; ?>" id="kpiMissCard">
                            <span class="pp-kic" aria-hidden="true"><i class="fas fa-exclamation-triangle"></i></span>
                            <div>
                                <span class="k">Missing receipt PDFs</span>
                                <strong id="kpiMissing"><?php echo number_format($totalMissing); ?></strong>
                            </div>
                        </div>
                    </section>

                    <div class="pp-card">

                        <?php if ($loadError): ?>
                            <div class="pp-alert" role="alert">
                                <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                                Error loading payment records. Please try again later.
                            </div>
                        <?php endif; ?>

                        <!-- ✅ Program filter -->
                        <div class="pp-filters">
                            <div class="pp-f">
                                <label for="programFilter">Program</label>
                                <select id="programFilter">
                                    <option value="">All programs</option>
                                    <?php foreach ($programs as $program): ?>
                                        <option value="<?php echo pp($program); ?>"><?php echo pp($program); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="button" id="resetFilter" class="pp-reset">
                                <i class="fas fa-redo" aria-hidden="true"></i> Reset
                            </button>
                            <span id="filterInfo" class="pp-finfo" role="status"></span>
                        </div>

                        <table class="pp-table" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Program Name</th>
                                    <th class="num">Extra Tickets</th>
                                    <th class="num">Total Amount (Rs.)</th>
                                    <th>Receipt</th>
                                    <th>Payment Date</th>
                                    <th>Payment By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $i => $r):
                                    $has_pdf = $r['pdf'] !== '';
                                    $by_i    = function_exists('mb_substr') ? mb_strtoupper(mb_substr($r['by'], 0, 1, 'UTF-8'), 'UTF-8') : strtoupper(substr($r['by'], 0, 1));
                                ?>
                                    <tr data-amount="<?php echo number_format($r['amount'], 2, '.', ''); ?>"
                                        data-extra="<?php echo (int)$r['extra']; ?>"
                                        data-missing="<?php echo $has_pdf ? 0 : 1; ?>">
                                        <td class="pp-n"><?php echo $i + 1; ?></td>
                                        <td><span class="pp-id"><?php echo pp($r['student_id']); ?></span></td>
                                        <td class="pp-name<?php echo $r['name'] === 'N/A' ? ' na' : ''; ?>"><?php echo pp($r['name']); ?></td>
                                        <td class="pp-prog"><?php echo pp($r['program']); ?></td>
                                        <td class="num"><span class="pp-x <?php echo $r['extra'] > 0 ? 'on' : 'off'; ?>"><?php echo (int)$r['extra']; ?></span></td>
                                        <td class="num pp-amt"><?php echo number_format($r['amount'], 2); ?></td>
                                        <td>
                                            <?php if ($has_pdf): ?>
                                                <a class="pp-rc" href="<?php echo pp($r['pdf']); ?>" target="_blank" rel="noopener" title="Open receipt PDF">
                                                    <i class="fas fa-file-pdf" aria-hidden="true"></i> <?php echo pp($r['receipt']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="pp-rc miss"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i> Missing PDF</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($r['ts'] !== false): ?>
                                            <td class="pp-dt" data-order="<?php echo (int)$r['ts']; ?>"><span class="pp-d"><?php echo date("Y-m-d", $r['ts']); ?></span> <span class="pp-t"><?php echo date("H:i:s", $r['ts']); ?></span></td>
                                        <?php else: ?>
                                            <td class="pp-dt" data-order="0">Invalid Date</td>
                                        <?php endif; ?>
                                        <td><span class="pp-by" data-i="<?php echo pp($by_i); ?>"><?php echo pp($r['by']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

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

<script>
    $(document).ready(function() {
        // ✅ Error handling for DataTable initialization with Export Buttons
        try {
            // ✅ Initialize Select2 on Program Filter
            $('#programFilter').select2({
                placeholder: 'All programs',
                allowClear: true,
                width: '100%'
            });

            // Receipt column (6) is left out of every export
            var exportCols = [0, 1, 2, 3, 4, 5, 7, 8];
            var exportTitle = 'Graduation Payment Records';

            var table = $('#dataTable').DataTable({
                "pageLength": 200,
                "order": [
                    [7, "desc"]
                ], // ✅ Sort by Payment Date (column 7)
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search payments…",
                    "emptyTable": "No payment records available",
                    "zeroRecords": "No matching records found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ payments",
                    "infoEmpty": "Showing 0 to 0 of 0 payments",
                    "infoFiltered": "(filtered from _MAX_ total payments)"
                },
                "columnDefs": [{
                    "orderable": false,
                    "targets": 6
                }], // Disable sorting on Receipt column
                // ✅ Search + export buttons on top, table, info + pages at the bottom
                "dom": "<'pp-bar'<'pp-bar-l'f><'pp-bar-r'B>>t<'pp-foot'<'pp-info'i><'pp-pg'p>>",
                "buttons": {
                    "dom": {
                        "button": {
                            "className": "dt-button"
                        }
                    },
                    "buttons": [{
                            extend: 'excelHtml5',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            className: 'xl',
                            title: exportTitle,
                            exportOptions: {
                                columns: exportCols
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            className: 'pdf',
                            title: exportTitle,
                            orientation: 'landscape',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: exportCols
                            },
                            customize: function(doc) {
                                doc.defaultStyle.fontSize = 8;
                                doc.styles.tableHeader.fontSize = 9;
                                doc.styles.tableHeader.fillColor = '#17233d';
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            text: '<i class="fas fa-file-csv"></i> CSV',
                            className: 'csv',
                            title: exportTitle,
                            exportOptions: {
                                columns: exportCols
                            }
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print"></i> Print',
                            className: 'prt',
                            title: exportTitle,
                            exportOptions: {
                                columns: exportCols
                            },
                            customize: function(win) {
                                $(win.document.body).find('table').addClass('display').css('font-size', '12px');
                                $(win.document.body).find('h1').css('text-align', 'center');
                            }
                        },
                        {
                            extend: 'copy',
                            text: '<i class="fas fa-copy"></i> Copy',
                            className: 'cpy',
                            exportOptions: {
                                columns: exportCols
                            }
                        }
                    ]
                }
            });

            // Let the table scroll sideways on small screens (toolbar stays put)
            $('#dataTable').wrap('<div class="pp-scroll"></div>');

            // ✅ Summary cards follow the current filter + search
            function fmt(n, d) {
                return n.toLocaleString('en-US', {
                    minimumFractionDigits: d || 0,
                    maximumFractionDigits: d || 0
                });
            }

            function updateSummary() {
                var count = 0,
                    amount = 0,
                    extra = 0,
                    missing = 0;

                table.rows({
                    search: 'applied'
                }).nodes().to$().each(function() {
                    var $tr = $(this);
                    count++;
                    amount += parseFloat($tr.attr('data-amount')) || 0;
                    extra += parseInt($tr.attr('data-extra'), 10) || 0;
                    missing += parseInt($tr.attr('data-missing'), 10) || 0;
                });

                $('#kpiCount').text(fmt(count));
                $('#kpiAmount').text(fmt(amount, 2));
                $('#kpiExtra').text(fmt(extra));
                $('#kpiMissing').text(fmt(missing));
                $('#kpiMissCard').toggleClass('has-miss', missing > 0);
            }

            // ✅ Function to update filter info
            function updateFilterInfo() {
                var selectedProgram = $('#programFilter').val();
                var info = table.page.info();
                var $box = $('#filterInfo');

                if (selectedProgram) {
                    $box.empty()
                        .append('<i class="fas fa-info-circle" aria-hidden="true"></i> ')
                        .append($('<span>').text('Showing '))
                        .append($('<strong>').text(info.recordsDisplay))
                        .append($('<span>').text(' payment(s) for '))
                        .append($('<strong>').text(selectedProgram))
                        .addClass('on');
                } else {
                    $box.removeClass('on').empty();
                }
            }

            // ✅ Program Filter Functionality
            $('#programFilter').on('change', function() {
                var selectedProgram = $(this).val();

                if (!selectedProgram) {
                    // Show all records
                    table.column(3).search('').draw();
                } else {
                    // Filter by selected program (column 3 = Program Name), exact match
                    table.column(3).search('^' + $.fn.dataTable.util.escapeRegex(selectedProgram) + '$', true, false).draw();
                }
            });

            // ✅ Reset Filter Button
            $('#resetFilter').on('click', function() {
                $('#programFilter').val('').trigger('change'); // keeps Select2 UI in sync
                table.search('').draw();
            });

            // ✅ Keep everything in sync on every draw (filter, search, sort, paging)
            table.on('draw', function() {
                updateFilterInfo();
                updateSummary();
            });
            updateSummary();

        } catch (e) {
            console.error("DataTable initialization error:", e);
            alert("Error loading table features. Please refresh the page.");
        }
    });
</script>

</body>