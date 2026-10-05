<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");

function be_h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

$programs = [];
$loadError = false;
$result = mysqli_query($conn, "SELECT programName, session FROM data_tables ORDER BY session ASC, programName ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $programs[$row['programName']] = (string)$row['session'];
    }
} else {
    $loadError = true;
    error_log("Bulk data email page - program load error: " . mysqli_error($conn));
}

// Distinct sessions (SESSION_01, SESSION_02, ...) from data_tables
$sessions = array_values(array_unique(array_filter($programs, fn($v) => trim($v) !== '')));
sort($sessions, SORT_NATURAL);
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

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
       Bulk email for seat numbers  (same design language as the student pages)
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

    .at-head {
        margin-bottom: 22px;
    }

    .at-head h1 {
        margin: 0;
        font-size: clamp(24px, 3vw, 32px);
        font-weight: 700;
        letter-spacing: -.03em;
        line-height: 1.1;
    }

    .at-head p {
        margin: 6px 0 0;
        font-size: 14px;
        color: var(--muted);
    }

    .at-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
    }

    .at-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        padding: 12px 16px;
        border: 1px solid #f1c3be;
        border-radius: 12px;
        background: var(--bad-bg);
        color: var(--bad);
        font-size: 14px;
        font-weight: 500;
    }

    /* picker */
    .be-pick {
        padding: 18px 20px;
        margin-bottom: 18px;
    }

    .be-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 2fr) auto;
        gap: 16px;
        align-items: end;
    }

    .be-field label {
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

    .be-field label i {
        color: #97a2b6;
        font-size: 11px;
    }

    .be-field label .req {
        color: var(--bad);
    }

    .be-sel {
        display: block;
        width: 100%;
        height: 40px;
        padding: 0 12px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff;
        color: var(--ink);
        font: inherit;
        font-size: 14px;
        font-weight: 500;
    }

    .be-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 40px;
        padding: 0 20px;
        border: 1px solid var(--brand);
        border-radius: 10px;
        background: var(--brand);
        color: #fff;
        font: inherit;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s;
    }

    .be-btn:hover {
        background: var(--brand-d);
    }

    .be-btn:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    .be-btn[disabled] {
        opacity: .65;
        cursor: not-allowed;
    }

    .be-note {
        margin-top: 6px;
        font-size: 13px;
        color: var(--muted);
    }

    .be-msg {
        display: none;
        margin-top: 8px;
        font-size: 13px;
        font-weight: 500;
        color: var(--bad);
    }

    .be-msg.show {
        display: block;
    }

    /* result area */
    .be-result {
        padding: 20px;
        min-height: 160px;
        overflow-x: auto;
    }

    .be-empty,
    .be-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 120px;
        text-align: center;
        color: var(--muted);
        font-size: 14px;
    }

    .be-empty i,
    .be-loading i {
        display: grid;
        place-items: center;
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 18px;
    }

    .be-empty strong {
        color: var(--ink);
        font-size: 15px;
    }

    /* content returned by fetch_program_data.php */
    .be-top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 16px;
    }

    .be-top-txt {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .be-top-txt h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -.02em;
        overflow-wrap: anywhere;
    }

    .be-top-act {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
    }

    .be-status {
        font-size: 13px;
        color: var(--muted);
    }

    .be-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .be-kpi {
        padding: 12px 16px;
        border: 1px solid var(--line);
        border-radius: 12px;
        background: var(--soft);
    }

    .be-kpi .k {
        display: block;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .be-kpi strong {
        display: block;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -.03em;
        font-variant-numeric: tabular-nums;
    }

    .be-kpi.ok strong {
        color: var(--ok);
    }

    .be-kpi.warn strong {
        color: var(--warn);
    }

    .be-kpi.bad strong {
        color: var(--bad);
    }

    .be-notice {
        display: none;
        margin-bottom: 16px;
        padding: 12px 16px;
        border: 1px solid #c9d6f3;
        border-radius: 12px;
        background: var(--brand-bg);
        color: var(--brand-d);
        font-size: 14px;
        font-weight: 500;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .be-notice.show {
        display: block;
    }

    .be-notice.err {
        border-color: #f1c3be;
        background: var(--bad-bg);
        color: var(--bad);
    }

    .be-bad-ic {
        background: var(--bad-bg) !important;
        color: var(--bad) !important;
    }

    #programDataTable {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: collapse;
        border: 0;
    }

    #programDataTable thead th {
        padding: 12px 14px;
        background: var(--soft);
        border-bottom: 1px solid var(--line);
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
        white-space: nowrap;
        text-align: left;
    }

    #programDataTable tbody td {
        padding: 11px 14px;
        border-bottom: 1px solid var(--line);
        font-size: 14px;
        color: var(--ink);
        vertical-align: middle;
    }

    #programDataTable tbody tr:hover td {
        background: #fafbfe;
    }

    #programDataTable tbody tr:last-child td {
        border-bottom: 0;
    }

    #programDataTable.dataTable.no-footer {
        border-bottom: 0;
    }

    #programDataTable.dataTable thead .sorting,
    #programDataTable.dataTable thead .sorting_asc,
    #programDataTable.dataTable thead .sorting_desc {
        background-color: var(--soft);
    }

    .be-mono {
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 13px;
        white-space: nowrap;
    }

    .be-em {
        font-size: 13px;
        color: var(--muted);
        overflow-wrap: anywhere;
    }

    .be-na {
        color: #97a2b6;
    }

    .be-person {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 170px;
    }

    .be-ini {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 12px;
        font-weight: 700;
    }

    .be-person .nm {
        font-weight: 600;
    }

    .be-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        background: var(--soft);
        color: var(--muted);
    }

    .be-pill.ok {
        background: var(--ok-bg);
        color: var(--ok);
    }

    .be-pill.bad {
        background: var(--bad-bg);
        color: var(--bad);
    }

    .be-pill.type {
        background: var(--brand-bg);
        color: var(--brand-d);
    }

    .be-act {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 12px;
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

    .be-act i {
        color: var(--brand);
    }

    .be-act:hover {
        background: var(--soft);
    }

    .be-act[disabled] {
        opacity: .6;
        cursor: not-allowed;
        background: var(--soft);
    }

    .be-act:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    .be-tbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .be-result .dataTables_length label,
    .be-result .dataTables_filter label {
        margin: 0;
        font-size: 13px;
        color: var(--muted);
        font-weight: 500;
    }

    .be-result .dataTables_length select {
        height: 36px;
        margin: 0 6px;
        padding: 0 8px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff;
        color: var(--ink);
    }

    .be-result .dataTables_filter input {
        width: 280px;
        max-width: 100%;
        height: 40px;
        margin: 0;
        padding: 0 12px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        font-size: 14px;
        color: var(--ink);
        outline: 0;
    }

    .be-result .dataTables_filter input:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .14);
    }

    .be-scroll {
        overflow-x: auto;
        border: 1px solid var(--line);
        border-radius: 12px;
    }

    .be-tfoot {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 12px;
    }

    .be-tfoot .dataTables_info {
        padding: 0;
        font-size: 13px;
        color: var(--muted);
    }

    .be-tfoot .dataTables_paginate {
        padding: 0;
    }

    .be-tfoot .dataTables_paginate .paginate_button {
        padding: 5px 12px !important;
        margin: 0 2px !important;
        border: 1px solid transparent !important;
        border-radius: 8px !important;
        font-size: 13px;
        font-weight: 600;
        color: var(--ink) !important;
        background: transparent !important;
    }

    .be-tfoot .dataTables_paginate .paginate_button:hover {
        background: var(--soft) !important;
        border-color: var(--line) !important;
        color: var(--ink) !important;
    }

    .be-tfoot .dataTables_paginate .paginate_button.current,
    .be-tfoot .dataTables_paginate .paginate_button.current:hover {
        background: var(--brand) !important;
        border-color: var(--brand) !important;
        color: #fff !important;
    }

    .be-tfoot .dataTables_paginate .paginate_button.disabled,
    .be-tfoot .dataTables_paginate .paginate_button.disabled:hover {
        color: #aab3c3 !important;
        background: transparent !important;
        border-color: transparent !important;
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
        background: #e8eefb !important;
        color: #173a91 !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #f4f6fb !important;
        font-weight: 600;
    }

    @media (max-width: 575.98px) {
        .be-row {
            grid-template-columns: minmax(0, 1fr);
        }

        .be-btn {
            width: 100%;
        }

        .be-result {
            padding: 14px;
        }

        .be-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .be-top-act,
        .be-top-act .be-btn {
            width: 100%;
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
                        <h1>Bulk email for seat numbers</h1>
                        <p>Pick a program to see its students and send their seat number emails.</p>
                    </header>

                    <?php if ($loadError): ?>
                        <div class="at-alert" role="alert">
                            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                            Programs could not be loaded. Refresh the page or contact the administrator.
                        </div>
                    <?php endif; ?>

                    <!-- program picker -->
                    <section class="at-card be-pick">
                        <div class="be-row">
                            <div class="be-field">
                                <label for="sessionSelect"><i class="fas fa-calendar-alt" aria-hidden="true"></i> Session</label>
                                <select name="session" class="be-sel" id="sessionSelect">
                                    <option value="">All sessions</option>
                                    <?php foreach ($sessions as $sess): ?>
                                        <option value="<?php echo be_h($sess); ?>"><?php echo be_h($sess); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="be-field">
                                <label for="programSelect"><i class="fas fa-graduation-cap" aria-hidden="true"></i> Program <span class="req">*</span></label>
                                <select name="program_name" class="be-sel" id="programSelect" required>
                                    <option value="">Select a program</option>
                                    <?php foreach ($programs as $name => $sess): ?>
                                        <option value="<?php echo be_h($name); ?>"><?php echo be_h($name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="button" id="fetchProgramData" class="be-btn">
                                <i class="fas fa-search" aria-hidden="true"></i> Fetch data
                            </button>
                        </div>
                        <div class="be-note" id="programNote">Pick a session to see only its programs.</div>
                        <div class="be-msg" id="pickError" role="alert">Select a program first.</div>
                    </section>

                    <!-- results (filled by fetch_program_data.php) -->
                    <section class="at-card be-result">
                        <div id="programDataResult">
                            <div class="be-empty">
                                <i class="fas fa-envelope-open-text" aria-hidden="true"></i>
                                <strong>No program loaded</strong>
                                <span>Select a program and choose Fetch data to list its students.</span>
                            </div>
                        </div>
                    </section>

                </div>
            </div> <!-- /container-fluid -->
        </div> <!-- /content -->
    </div> <!-- /content-wrapper -->
</div> <!-- /wrapper -->

<!-- JS Libraries (jQuery comes from includes/header.php, as before) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') $('body').removeClass('sidebar-open');
        });
        $(window).on('resize', function() {
            if (window.innerWidth >= 992) $('body').removeClass('sidebar-open');
        });

        var programSessions = <?php echo json_encode($programs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        var allPrograms = Object.keys(programSessions);
        var $session = $('#sessionSelect'),
            $program = $('#programSelect');

        $session.select2({
            placeholder: 'All sessions',
            allowClear: true,
            width: '100%'
        });
        $program.select2({
            placeholder: 'Select a program',
            allowClear: true,
            width: '100%'
        });

        // Rebuild the program list for the chosen session (all programs when no session)
        function loadPrograms(sess, keep) {
            var list = allPrograms.filter(function(n) {
                return !sess || programSessions[n] === sess;
            });
            $program.empty().append(new Option('Select a program', '', false, false));
            list.forEach(function(n) {
                $program.append(new Option(n, n, false, false));
            });
            $program.val(keep && list.indexOf(keep) !== -1 ? keep : '').trigger('change.select2');
            $('#programNote').text(sess ?
                list.length + ' program' + (list.length === 1 ? '' : 's') + ' in ' + sess + '.' :
                'Pick a session to see only its programs.');
        }

        $session.on('change', function() {
            loadPrograms($(this).val(), $program.val());
        });

        // Choosing a program fills in its session
        $program.on('change', function() {
            $('#pickError').removeClass('show');
            var p = $(this).val();
            if (p && programSessions[p] && $session.val() !== programSessions[p]) {
                $session.val(programSessions[p]).trigger('change.select2');
                loadPrograms(programSessions[p], p);
            }
        });

        var $btn = $('#fetchProgramData'),
            $out = $('#programDataResult'),
            $msg = $('#pickError'),
            btnHtml = $btn.html();

        $btn.on('click', function() {
            var programName = $('#programSelect').val();
            if (!programName) {
                $msg.addClass('show');
                return;
            }
            $msg.removeClass('show');

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Fetching…');
            $out.html('<div class="be-loading"><i class="fas fa-spinner fa-spin" aria-hidden="true"></i><span>Loading students…</span></div>');

            $.ajax({
                url: 'fetch_program_data.php',
                type: 'POST',
                data: {
                    program_name: programName,
                    session: $session.val() || programSessions[programName] || ''
                },
                success: function(response) {
                    $out.html(response);
                },
                error: function() {
                    $out.html('<div class="be-empty"><i class="fas fa-exclamation-triangle" aria-hidden="true" style="background:#fdecea;color:#c0372f"></i><strong>Could not load students</strong><span>Check your connection and try Fetch data again.</span></div>');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(btnHtml);
                }
            });
        });
    });
</script>