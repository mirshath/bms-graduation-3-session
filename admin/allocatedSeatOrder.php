<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");

function so_h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

// "SESSION_01" -> "Session 01"
function so_label($v): string
{
    return ucwords(strtolower(str_replace('_', ' ', (string)$v)));
}

// Load sessions dynamically from data_tables.session
$sessions = [];
$loadError = false;
$sql = "SELECT DISTINCT session FROM data_tables
        WHERE session IS NOT NULL AND session <> '' %s
        ORDER BY session ASC";
// Prefer active sessions only; fall back if the `active` column is not there
$res = @$conn->query(sprintf($sql, "AND active = 1"));
if (!$res) {
    $res = $conn->query(sprintf($sql, ""));
}
if ($res) {
    while ($s = $res->fetch_assoc()) $sessions[] = $s['session'];
} else {
    $loadError = true;
    error_log("Allocated seat order - session load error: " . $conn->error);
}
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">

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
       Seat allocation by session  (same design language as the other pages)
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

    /* picker */
    .so-pick {
        padding: 14px 16px;
        margin-bottom: 14px;
    }

    .so-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: end;
    }

    .so-field label {
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

    .so-field label i {
        color: #97a2b6;
        font-size: 10px;
    }

    .so-sel {
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

    .so-btn {
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

    .so-btn i {
        color: var(--brand);
    }

    .so-btn:hover {
        background: var(--soft);
    }

    .so-btn[disabled] {
        opacity: .6;
        cursor: not-allowed;
    }

    .so-btn:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    /* result card */
    .so-result {
        overflow: hidden;
    }

    .so-result-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--line);
    }

    .so-result-head h2 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.01em;
    }

    .so-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .so-pill {
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

    .so-pill.count {
        background: var(--soft);
        color: var(--muted);
        font-family: 'Geist Mono', ui-monospace, monospace;
    }

    .so-body {
        padding: 16px;
        min-height: 160px;
    }

    .so-empty,
    .so-loading {
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

    .so-empty i,
    .so-loading i {
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 17px;
    }

    .so-empty i.bad {
        background: var(--bad-bg);
        color: var(--bad);
    }

    .so-empty strong {
        color: var(--ink);
        font-size: 14px;
    }

    /* table returned by load_session_data.php */
    #sessionData .alert {
        border-radius: 10px;
        font-size: 13px;
    }

    #sessionTable {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: collapse;
        border: 0;
    }

    #sessionTable thead th {
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

    #sessionTable tbody td {
        padding: 8px 10px;
        border-top: 0;
        border-bottom: 1px solid var(--line);
        font-size: 12.5px;
        color: var(--ink);
        vertical-align: middle;
    }

    #sessionTable tbody tr:hover td {
        background: #fafbfe;
    }

    #sessionTable tbody tr:last-child td {
        border-bottom: 0;
    }

    #sessionTable.dataTable.no-footer {
        border-bottom: 0;
    }

    #sessionTable.dataTable thead .sorting,
    #sessionTable.dataTable thead .sorting_asc,
    #sessionTable.dataTable thead .sorting_desc {
        background-color: var(--soft);
    }

    #sessionTable.table-striped tbody tr:nth-of-type(odd) {
        background: transparent;
    }

    .so-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }

    .so-btns {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .so-btns .btn.so-b {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 0;
        padding: 5px 11px;
        border: 1px solid #c4ccd9;
        border-radius: 8px;
        background: #fff;
        color: var(--ink);
        font-size: 12px;
        font-weight: 600;
        box-shadow: none;
    }

    .so-btns .btn.so-b:hover {
        background: var(--soft);
        border-color: #aeb8ca;
        color: var(--ink);
    }

    .so-btns .btn.so-b i {
        font-size: 12px;
    }

    .so-btns .so-xl i {
        color: #1b7f4b;
    }

    .so-btns .so-pdf i {
        color: #c0372f;
    }

    .so-btns .so-csv i {
        color: #a85d00;
    }

    .so-btns .so-prt i,
    .so-btns .so-cp i {
        color: var(--brand);
    }

    #sessionData .dataTables_filter label {
        margin: 0;
        font-size: 12px;
        color: var(--muted);
        font-weight: 500;
    }

    #sessionData .dataTables_filter input {
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

    #sessionData .dataTables_filter input:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .14);
    }

    .so-scroll {
        overflow-x: auto;
        border: 1px solid var(--line);
        border-radius: 10px;
    }

    .so-foot {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 12px;
    }

    .so-foot .dataTables_info {
        padding: 0;
        font-size: 12px;
        color: var(--muted);
    }

    .so-foot .dataTables_paginate {
        padding: 0;
    }

    .so-foot .dataTables_paginate .paginate_button {
        padding: 4px 10px !important;
        margin: 0 1px !important;
        border: 1px solid transparent !important;
        border-radius: 8px !important;
        font-size: 12px;
        font-weight: 600;
        color: var(--ink) !important;
        background: transparent !important;
    }

    .so-foot .dataTables_paginate .paginate_button:hover {
        background: var(--soft) !important;
        border-color: var(--line) !important;
        color: var(--ink) !important;
    }

    .so-foot .dataTables_paginate .paginate_button.current,
    .so-foot .dataTables_paginate .paginate_button.current:hover {
        background: var(--brand) !important;
        border-color: var(--brand) !important;
        color: #fff !important;
    }

    .so-foot .dataTables_paginate .paginate_button.disabled,
    .so-foot .dataTables_paginate .paginate_button.disabled:hover {
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

    @media (max-width: 575.98px) {
        .so-row {
            grid-template-columns: minmax(0, 1fr);
        }

        .so-btn {
            width: 100%;
        }

        #sessionData .dataTables_filter,
        #sessionData .dataTables_filter input {
            width: 100%;
        }

        .so-body {
            padding: 12px;
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
                        <h1>Session wise student list</h1>
                        <p>Students of a session after their seats are allocated, in seat order.</p>
                    </header>

                    <?php if ($loadError): ?>
                        <div class="at-alert" role="alert">
                            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                            Sessions could not be loaded. Refresh the page or contact the administrator.
                        </div>
                    <?php endif; ?>

                    <!-- session picker -->
                    <section class="at-card so-pick">
                        <div class="so-row">
                            <div class="so-field">
                                <label for="sessionSelect"><i class="fas fa-calendar-alt" aria-hidden="true"></i> Session</label>
                                <select id="sessionSelect" class="so-sel">
                                    <option value="">Select a session</option>
                                    <?php foreach ($sessions as $sv): ?>
                                        <option value="<?= so_h($sv) ?>"><?= so_h(so_label($sv)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="button" id="refreshBtn" class="so-btn" disabled>
                                <i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh
                            </button>
                        </div>
                    </section>

                    <!-- results -->
                    <section class="at-card so-result">
                        <div class="so-result-head">
                            <h2>Student details</h2>
                            <div class="so-meta" id="soMeta"></div>
                        </div>
                        <div class="so-body">
                            <div id="sessionData">
                                <div class="so-empty">
                                    <i class="fas fa-chair" aria-hidden="true"></i>
                                    <strong>No session selected</strong>
                                    <span>Choose a session to list its students.</span>
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
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    // Custom seat sorting: "A 12" -> letter first, then number (A 1, A 2, A 10, B 1 ...)
    $.fn.dataTable.ext.type.order['seat-sort-pre'] = function(data) {
        var m = String(data).replace(/<[^>]*>/g, '').trim().match(/^([A-Za-z]*)\s*(\d+)$/);
        if (!m) return 0;
        var prefix = m[1] ? m[1].toUpperCase().charCodeAt(0) : 0;
        return prefix * 100000 + parseInt(m[2], 10);
    };

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
            $out = $('#sessionData'),
            $meta = $('#soMeta'),
            $refresh = $('#refreshBtn'),
            currentXhr = null, // request in flight
            requestId = 0; // ignore answers from older requests

        $session.select2({
            placeholder: 'Select a session',
            allowClear: true,
            width: '100%'
        });

        function esc(v) {
            return $('<div>').text(v).html();
        }

        function label(v) {
            return String(v).replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, function(c) {
                return c.toUpperCase();
            });
        }

        function emptyState(icon, title, text, bad) {
            return '<div class="so-empty"><i class="fas ' + icon + (bad ? ' bad' : '') + '" aria-hidden="true"></i><strong>' + esc(title) + '</strong><span>' + esc(text) + '</span></div>';
        }

        // Remove the previous session's table so nothing old is left behind
        function clearResult() {
            if ($.fn.DataTable.isDataTable('#sessionTable')) {
                $('#sessionTable').DataTable().destroy();
            }
            $out.empty();
            $meta.empty();
        }

        function loadSession(session) {
            if (currentXhr) {
                currentXhr.abort();
                currentXhr = null;
            }
            var myId = ++requestId;
            clearResult();

            if (!session) {
                $refresh.prop('disabled', true);
                $out.html(emptyState('fa-chair', 'No session selected', 'Choose a session to list its students.'));
                return;
            }

            $refresh.prop('disabled', true);
            $out.html('<div class="so-loading"><i class="fas fa-spinner fa-spin" aria-hidden="true"></i><span>Loading ' + esc(label(session)) + '…</span></div>');

            currentXhr = $.ajax({
                url: 'load_session_data.php',
                type: 'POST',
                cache: false,
                timeout: 60000,
                data: {
                    session: session
                },
                success: function(response) {
                    if (myId !== requestId) return;
                    $out.html(response);

                    if (!$('#sessionTable').length) {
                        // The server sent a message instead of a table; keep it, tidy.
                        if (!$.trim($out.text())) {
                            $out.html(emptyState('fa-user-slash', 'No students found', 'No seats are allocated in ' + label(session) + ' yet.'));
                        }
                        return;
                    }

                    var title = 'Seat allocation - ' + label(session);
                    var table = $('#sessionTable').DataTable({
                        autoWidth: false,
                        dom: "<'so-bar'<'so-btns'B><'so-search'f>><'so-scroll'rt><'so-foot'<'so-info'i><'so-pg'p>>",
                        pageLength: 500,
                        lengthMenu: [
                            [500, -1, 10, 25, 50, 100],
                            [500, 'All', 10, 25, 50, 100]
                        ],
                        columnDefs: [{
                            targets: 3,
                            type: 'seat-sort'
                        }], // Seat No column
                        language: {
                            search: '',
                            searchPlaceholder: 'Search students…',
                            zeroRecords: 'No matching students found',
                            info: 'Showing _START_ to _END_ of _TOTAL_ students',
                            infoEmpty: 'Showing 0 to 0 of 0 students',
                            infoFiltered: '(filtered from _MAX_ total students)'
                        },
                        buttons: [{
                                extend: 'excel',
                                text: '<i class="fas fa-file-excel"></i> Excel',
                                className: 'btn so-b so-xl',
                                title: title
                            },
                            {
                                extend: 'pdf',
                                text: '<i class="fas fa-file-pdf"></i> PDF',
                                className: 'btn so-b so-pdf',
                                title: title,
                                orientation: 'landscape',
                                pageSize: 'A4',
                                customize: function(doc) {
                                    doc.defaultStyle.fontSize = 8;
                                    doc.styles.tableHeader.fontSize = 9;
                                    doc.styles.tableHeader.fillColor = '#1f4bb6';
                                }
                            },
                            {
                                extend: 'csv',
                                text: '<i class="fas fa-file-csv"></i> CSV',
                                className: 'btn so-b so-csv',
                                title: title
                            },
                            {
                                extend: 'print',
                                text: '<i class="fas fa-print"></i> Print',
                                className: 'btn so-b so-prt',
                                title: title
                            },
                            {
                                extend: 'copy',
                                text: '<i class="fas fa-copy"></i> Copy',
                                className: 'btn so-b so-cp'
                            }
                        ]
                    });

                    $meta.html(
                        '<span class="so-pill"><i class="fas fa-calendar-alt" aria-hidden="true"></i> ' + esc(label(session)) + '</span>' +
                        '<span class="so-pill count">' + table.rows().count() + ' students</span>'
                    );
                },
                error: function(xhr, status) {
                    if (status === 'abort' || myId !== requestId) return;
                    $out.html(emptyState('fa-exclamation-triangle',
                        'Could not load students',
                        status === 'timeout' ? 'The server took too long to answer. Click Refresh to try again.' : 'Check your connection and click Refresh to try again.',
                        true));
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
    });
</script>