<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");

function mg_h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

// "SESSION_01" -> "Session 01"
function mg_label($v): string
{
    return ucwords(strtolower(str_replace('_', ' ', (string)$v)));
}

$sessions = [];
$loadError = false;
$res = $conn->query("SELECT DISTINCT session_time FROM bulk_data_table WHERE session_time IS NOT NULL AND session_time <> '' ORDER BY session_time ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) $sessions[] = $row['session_time'];
} else {
    $loadError = true;
    error_log("Mark graduate - session load error: " . $conn->error);
}
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
       Mark graduated students  (same design language as the other pages)
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
        max-width: 1400px;
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

    /* picker */
    .mg-pick {
        padding: 14px 16px;
        margin-bottom: 14px;
    }

    .mg-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: end;
    }

    .mg-field label {
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

    .mg-field label i {
        color: #97a2b6;
        font-size: 10px;
    }

    .mg-sel {
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
    }

    .mg-btn {
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

    .mg-btn i {
        color: var(--brand);
    }

    .mg-btn:hover {
        background: var(--soft);
    }

    .mg-btn[disabled] {
        opacity: .6;
        cursor: not-allowed;
    }

    .mg-btn:focus-visible,
    .mg-grad:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    /* summary */
    .mg-kpis {
        display: none;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .mg-kpis.show {
        display: grid;
    }

    .mg-kpi {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
    }

    .mg-kpi-ic {
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

    .mg-kpi.ok .mg-kpi-ic {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .mg-kpi.warn .mg-kpi-ic {
        background: var(--warn-bg);
        color: var(--warn);
    }

    .mg-kpi span.k {
        display: block;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .mg-kpi strong {
        display: block;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.15;
        font-variant-numeric: tabular-nums;
    }

    .mg-kpi.ok strong {
        color: var(--ok);
    }

    .mg-kpi.warn strong {
        color: var(--warn);
    }

    /* result card */
    .mg-result {
        overflow: hidden;
    }

    .mg-result-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--line);
    }

    .mg-result-head h2 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.01em;
    }

    .mg-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        background: var(--brand-bg);
        color: var(--brand-d);
    }

    .mg-body {
        padding: 16px;
        min-height: 160px;
    }

    .mg-notice {
        display: none;
        margin-bottom: 12px;
        padding: 10px 14px;
        border: 1px solid #f1c3be;
        border-radius: 10px;
        background: var(--bad-bg);
        color: var(--bad);
        font-size: 13px;
        font-weight: 500;
    }

    .mg-notice.show {
        display: block;
    }

    .mg-empty,
    .mg-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 130px;
        text-align: center;
        color: var(--muted);
        font-size: 13px;
    }

    .mg-empty i,
    .mg-loading i {
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 17px;
    }

    .mg-empty i.bad {
        background: var(--bad-bg);
        color: var(--bad);
    }

    .mg-empty strong {
        color: var(--ink);
        font-size: 14px;
    }

    .mg-search-bar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 12px;
    }

    #studentTable_wrapper .dataTables_filter label {
        margin: 0;
        font-size: 12px;
        color: var(--muted);
        font-weight: 500;
    }

    #studentTable_wrapper .dataTables_filter input {
        width: 260px;
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

    #studentTable_wrapper .dataTables_filter input:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .14);
    }

    .mg-scroll {
        overflow-x: auto;
        border: 1px solid var(--line);
        border-radius: 10px;
    }

    #studentTable {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: collapse;
        border: 0;
    }

    #studentTable thead th {
        padding: 9px 10px;
        background: var(--soft);
        border-bottom: 1px solid var(--line);
        border-top: 0;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .03em;
        text-transform: uppercase;
        color: var(--muted);
        white-space: nowrap;
        text-align: left;
    }

    #studentTable tbody td {
        padding: 8px 10px;
        border-top: 0;
        border-bottom: 1px solid var(--line);
        font-size: 12.5px;
        color: var(--ink);
        vertical-align: middle;
    }

    #studentTable tbody tr:hover td {
        background: #fafbfe;
    }

    #studentTable tbody tr:last-child td {
        border-bottom: 0;
    }

    #studentTable.dataTable.no-footer {
        border-bottom: 0;
    }

    #studentTable.dataTable thead .sorting,
    #studentTable.dataTable thead .sorting_asc,
    #studentTable.dataTable thead .sorting_desc {
        background-color: var(--soft);
    }

    #studentTable th:nth-child(1) {
        width: 44px;
    }

    #studentTable th:last-child {
        width: 130px;
    }

    .mg-mono {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12px;
        white-space: nowrap;
    }

    .mg-na {
        color: #97a2b6;
    }

    .mg-person {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    .mg-ini {
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

    .mg-person .nm {
        font-weight: 600;
    }

    /* graduate button: grey "Graduate" -> green "Graduated" */
    .mg-grad {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-width: 104px;
        padding: 5px 12px;
        border: 1px solid #c4ccd9;
        border-radius: 8px;
        background: #fff;
        color: var(--ink);
        font: inherit;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: background .15s, border-color .15s;
    }

    .mg-grad i {
        color: var(--brand);
        font-size: 11px;
    }

    .mg-grad:hover {
        background: var(--brand-bg);
        border-color: var(--brand);
    }

    .mg-grad.done {
        background: var(--ok-bg);
        border-color: #bfe0cd;
        color: var(--ok);
        cursor: default;
    }

    .mg-grad.done i {
        color: var(--ok);
    }

    .mg-grad[disabled] {
        opacity: .7;
        cursor: wait;
    }

    .mg-grad.done[disabled] {
        opacity: 1;
        cursor: default;
    }

    .mg-foot {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 12px;
    }

    #studentTable_wrapper .dataTables_info {
        padding: 0;
        font-size: 12px;
        color: var(--muted);
    }

    #studentTable_wrapper .dataTables_paginate {
        padding: 0;
    }

    #studentTable_wrapper .dataTables_paginate .paginate_button {
        padding: 4px 10px !important;
        margin: 0 1px !important;
        border: 1px solid transparent !important;
        border-radius: 8px !important;
        font-size: 12px;
        font-weight: 600;
        color: var(--ink) !important;
        background: transparent !important;
    }

    #studentTable_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--soft) !important;
        border-color: var(--line) !important;
        color: var(--ink) !important;
    }

    #studentTable_wrapper .dataTables_paginate .paginate_button.current,
    #studentTable_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: var(--brand) !important;
        border-color: var(--brand) !important;
        color: #fff !important;
    }

    #studentTable_wrapper .dataTables_paginate .paginate_button.disabled,
    #studentTable_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        color: #aab3c3 !important;
        background: transparent !important;
        border-color: transparent !important;
    }

    /* Select2 to match */
    .at .select2-container {
        width: 100% !important;
    }

    .at .select2-container--default .select2-selection--single {
        height: 36px;
        border: 1px solid #c4ccd9;
        border-radius: 9px;
        background: #fff;
        outline: 0;
    }

    .at .select2-container--default.select2-container--focus .select2-selection--single,
    .at .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .14);
    }

    .at .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        padding-left: 10px;
        padding-right: 40px;
        font-size: 13px;
        font-weight: 500;
        color: var(--ink);
    }

    .at .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #7b879b;
    }

    .at .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
        right: 6px;
    }

    .at .select2-container--default .select2-selection--single .select2-selection__clear {
        margin-right: 8px;
        color: #7b879b;
        font-size: 17px;
    }

    .select2-dropdown {
        border-color: #c4ccd9 !important;
        border-radius: 10px !important;
        overflow: hidden;
        font-family: 'Public Sans', system-ui, sans-serif;
        font-size: 13px;
        box-shadow: 0 12px 30px rgba(23, 35, 61, .14);
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: #e8eefb !important;
        color: #173a91 !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #f4f6fb !important;
        font-weight: 600;
    }

    @media (max-width: 767.98px) {
        .mg-kpis {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .mg-kpi {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
            padding: 10px 12px;
        }
    }

    /* phones: each student becomes a card */
    @media (max-width: 575.98px) {
        .mg-row {
            grid-template-columns: minmax(0, 1fr);
        }

        .mg-btn {
            width: 100%;
        }

        .mg-body {
            padding: 12px;
        }

        .mg-search-bar,
        #studentTable_wrapper .dataTables_filter,
        #studentTable_wrapper .dataTables_filter input {
            width: 100%;
        }

        .mg-scroll {
            border: 0;
            border-radius: 0;
            overflow: visible;
        }

        #studentTable,
        #studentTable tbody {
            display: block;
        }

        #studentTable thead {
            display: none;
        }

        #studentTable tbody tr {
            display: block;
            margin-bottom: 10px;
            padding: 8px 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
        }

        #studentTable tbody td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 6px 0;
            border-bottom: 1px dashed var(--line);
            text-align: right;
        }

        #studentTable tbody td:last-child {
            border-bottom: 0;
            padding-top: 10px;
        }

        #studentTable tbody td::before {
            content: attr(data-label);
            flex: 0 0 auto;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .mg-grad {
            width: 100%;
        }

        .mg-kpis {
            grid-template-columns: minmax(0, 1fr);
        }

        .mg-kpi {
            flex-direction: row;
            align-items: center;
            gap: 12px;
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
                        <h1>Graduated student mark</h1>
                        <p>Pick a session, then mark each student as graduated.</p>
                    </header>

                    <?php if ($loadError): ?>
                        <div class="at-alert" role="alert">
                            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                            Sessions could not be loaded. Refresh the page or contact the administrator.
                        </div>
                    <?php endif; ?>

                    <!-- session picker -->
                    <section class="at-card mg-pick">
                        <div class="mg-row">
                            <div class="mg-field">
                                <label for="sessionSelect"><i class="fas fa-calendar-alt" aria-hidden="true"></i> Session</label>
                                <select id="sessionSelect" class="mg-sel">
                                    <option value="">Select a session</option>
                                    <?php foreach ($sessions as $sv): ?>
                                        <option value="<?= mg_h($sv) ?>"><?= mg_h(mg_label($sv)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="button" id="refreshBtn" class="mg-btn" disabled>
                                <i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh
                            </button>
                        </div>
                    </section>

                    <!-- summary -->
                    <section class="mg-kpis" id="mgKpis">
                        <div class="at-card mg-kpi">
                            <span class="mg-kpi-ic"><i class="fas fa-users" aria-hidden="true"></i></span>
                            <div><span class="k">Students</span><strong id="kTotal">0</strong></div>
                        </div>
                        <div class="at-card mg-kpi ok">
                            <span class="mg-kpi-ic"><i class="fas fa-user-graduate" aria-hidden="true"></i></span>
                            <div><span class="k">Graduated</span><strong id="kDone">0</strong></div>
                        </div>
                        <div class="at-card mg-kpi warn">
                            <span class="mg-kpi-ic"><i class="fas fa-user-clock" aria-hidden="true"></i></span>
                            <div><span class="k">Not yet</span><strong id="kPending">0</strong></div>
                        </div>
                    </section>

                    <!-- list -->
                    <section class="at-card mg-result">
                        <div class="mg-result-head">
                            <h2>Graduated student mark list</h2>
                            <div id="mgMeta"></div>
                        </div>
                        <div class="mg-body">
                            <div class="mg-notice" id="mgNotice" role="alert"></div>

                            <div id="mgState">
                                <div class="mg-empty">
                                    <i class="fas fa-user-graduate" aria-hidden="true"></i>
                                    <strong>No session selected</strong>
                                    <span>Choose a session to list its students.</span>
                                </div>
                            </div>

                            <div id="mgTableWrap" style="display:none">
                                <div class="mg-scroll">
                                    <table class="table" id="studentTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Student ID</th>
                                                <th>Calling Name</th>
                                                <th>Program</th>
                                                <th>Seat No</th>
                                                <th>Graduated</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div> <!-- /container-fluid -->
        </div> <!-- /content -->
    </div> <!-- /content-wrapper -->
</div> <!-- /wrapper -->

<!-- ========== JS ========== -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') $('body').removeClass('sidebar-open');
        });
        $(window).on('resize', function() {
            if (window.innerWidth >= 992) $('body').removeClass('sidebar-open');
        });

        var $session = $('#sessionSelect'),
            $refresh = $('#refreshBtn'),
            $state = $('#mgState'),
            $wrap = $('#mgTableWrap'),
            $notice = $('#mgNotice'),
            $kpis = $('#mgKpis'),
            currentXhr = null, // request in flight
            requestId = 0, // ignore answers from older requests
            status = {}; // student_id -> graduated (true/false) for the summary

        $session.select2({
            placeholder: 'Select a session',
            allowClear: true,
            width: '100%'
        });

        function esc(v) {
            return $('<div>').text(v == null ? '' : v).html();
        }

        function label(v) {
            return String(v).replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, function(c) {
                return c.toUpperCase();
            });
        }

        function initials(name) {
            var w = $.trim(String(name || '')).split(/\s+/).filter(Boolean);
            if (!w.length) return '?';
            return (w[0].charAt(0) + (w.length > 1 ? w[w.length - 1].charAt(0) : '')).toUpperCase();
        }

        function na(v) {
            return $.trim(String(v == null ? '' : v)) ? esc(v) : '<span class="mg-na">N/A</span>';
        }

        function stateBox(icon, title, text, bad) {
            return '<div class="mg-empty"><i class="fas ' + icon + (bad ? ' bad' : '') + '" aria-hidden="true"></i><strong>' + esc(title) + '</strong><span>' + esc(text) + '</span></div>';
        }

        function gradButton(id, done) {
            return done ?
                '<button type="button" class="mg-grad done" data-student="' + esc(id) + '" disabled><i class="fas fa-check-circle" aria-hidden="true"></i> Graduated</button>' :
                '<button type="button" class="mg-grad" data-student="' + esc(id) + '"><i class="fas fa-user-graduate" aria-hidden="true"></i> Graduate</button>';
        }

        function updateSummary() {
            var total = 0,
                done = 0;
            $.each(status, function(id, g) {
                total++;
                if (g) done++;
            });
            $('#kTotal').text(total);
            $('#kDone').text(done);
            $('#kPending').text(total - done);
        }

        var labels = ['#', 'Student ID', 'Name', 'Program', 'Seat No', 'Graduated'];
        var table = $('#studentTable').DataTable({
            autoWidth: false,
            pageLength: 500,
            ordering: true,
            dom: "<'mg-search-bar'f><'mg-tbl'rt><'mg-foot'<'mg-info'i><'mg-pg'p>>",
            columnDefs: [{
                orderable: false,
                targets: 5
            }],
            createdRow: function(row) {
                $('td', row).each(function(i) {
                    $(this).attr('data-label', labels[i]);
                });
            },
            language: {
                search: '',
                searchPlaceholder: 'Search students…',
                emptyTable: 'No students in this session',
                zeroRecords: 'No matching students found',
                info: 'Showing _START_ to _END_ of _TOTAL_ students',
                infoEmpty: 'Showing 0 to 0 of 0 students',
                infoFiltered: '(filtered from _MAX_ total students)'
            }
        });

        function resetView() {
            table.clear().draw();
            status = {};
            $wrap.hide();
            $kpis.removeClass('show');
            $('#mgMeta').empty();
            $notice.removeClass('show').empty();
        }

        function loadSession(sess) {
            if (currentXhr) {
                currentXhr.abort();
                currentXhr = null;
            }
            var myId = ++requestId;
            resetView();

            if (!sess) {
                $refresh.prop('disabled', true);
                $state.html(stateBox('fa-user-graduate', 'No session selected', 'Choose a session to list its students.')).show();
                return;
            }

            $refresh.prop('disabled', true);
            $state.html('<div class="mg-loading"><i class="fas fa-spinner fa-spin" aria-hidden="true"></i><span>Loading ' + esc(label(sess)) + '…</span></div>').show();

            currentXhr = $.ajax({
                url: 'fetch_session_students.php',
                type: 'POST',
                dataType: 'json',
                cache: false,
                timeout: 60000,
                data: {
                    session_time: sess
                },
                success: function(response) {
                    if (myId !== requestId) return;

                    if (!response || response.status !== 'success' || !response.data || !response.data.length) {
                        $state.html(stateBox('fa-user-slash', 'No students found',
                            (response && response.message) || 'No records found for ' + label(sess) + '.')).show();
                        return;
                    }

                    var rows = [];
                    $.each(response.data, function(i, r) {
                        var done = r.graduated_status === 'Yes';
                        status[String(r.student_id)] = done;
                        rows.push([
                            i + 1,
                            '<span class="mg-mono">' + esc(r.student_id) + '</span>',
                            '<div class="mg-person"><span class="mg-ini" aria-hidden="true">' + esc(initials(r.calling_name)) + '</span><span class="nm">' + na(r.calling_name) + '</span></div>',
                            na(r.program_name),
                            '<span class="mg-mono">' + na(r.seat_no) + '</span>',
                            gradButton(r.student_id, done)
                        ]);
                    });

                    table.clear().rows.add(rows).draw();
                    $state.hide();
                    $wrap.show();
                    table.columns.adjust();
                    $kpis.addClass('show');
                    $('#mgMeta').html('<span class="mg-pill"><i class="fas fa-calendar-alt" aria-hidden="true"></i> ' + esc(label(sess)) + '</span>');
                    updateSummary();
                },
                error: function(xhr, st) {
                    if (st === 'abort' || myId !== requestId) return;
                    $state.html(stateBox('fa-exclamation-triangle', 'Could not load students',
                        st === 'timeout' ? 'The server took too long to answer. Click Refresh to try again.' : 'Check your connection and click Refresh to try again.',
                        true)).show();
                },
                complete: function() {
                    if (myId !== requestId) return;
                    currentXhr = null;
                    $refresh.prop('disabled', !$session.val());
                }
            });
        }

        // Choosing a session loads it straight away; Refresh reloads the current one
        $session.on('change', function() {
            loadSession($(this).val());
        });
        $refresh.on('click', function() {
            loadSession($session.val());
        });

        // Mark one student as graduated
        $('#studentTable tbody').on('click', '.mg-grad', function() {
            var btn = $(this);
            if (btn.hasClass('done') || btn.prop('disabled')) return;

            var studentId = btn.attr('data-student');
            var idle = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Saving…');
            $notice.removeClass('show').empty();

            $.ajax({
                url: 'mark_graduate_action.php',
                type: 'POST',
                data: {
                    student_id: studentId
                },
                success: function(response) {
                    var res;
                    try {
                        res = (typeof response === 'string') ? JSON.parse(response) : response;
                    } catch (e) {
                        console.error('Error parsing response:', response);
                        btn.prop('disabled', false).html(idle);
                        $notice.text('Something went wrong. The server sent an unexpected answer.').addClass('show');
                        return;
                    }

                    if (res && res.status === 'success') {
                        btn.addClass('done').html('<i class="fas fa-check-circle" aria-hidden="true"></i> Graduated');
                        status[studentId] = true;
                        updateSummary();
                    } else {
                        btn.prop('disabled', false).html(idle);
                        $notice.text((res && res.message) || 'Could not mark this student as graduated.').addClass('show');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html(idle);
                    $notice.text('Could not save. Check your connection and try again.').addClass('show');
                }
            });
        });
    });
</script>