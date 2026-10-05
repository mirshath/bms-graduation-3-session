<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");

function bd_h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

// Programs + their sessions (raw values; escaped only on output)
$programs = [];
$loadError = false;
$result = mysqli_query($conn, "SELECT programName, session FROM data_tables ORDER BY programName ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $programs[$row['programName']] = $row['session'];
    }
} else {
    $loadError = true;
    error_log("Bulk data page - program load error: " . mysqli_error($conn));
}
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600&display=swap" rel="stylesheet">
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
       Bulk data upload  (same design language as the student pages)
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
        max-width: 1100px;
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
        color: var(--ink);
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

    .bd-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    /* upload form */
    .bd-form {
        padding: 22px 24px 24px;
    }

    .bd-form h2,
    .bd-side h2 {
        margin: 0 0 16px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.01em;
    }

    .bd-field {
        margin-bottom: 18px;
    }

    .bd-field label {
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

    .bd-field label i {
        color: #97a2b6;
        font-size: 11px;
    }

    .bd-field label .req {
        color: var(--bad);
    }

    .bd-input {
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
        outline: 0;
        transition: border-color .15s, box-shadow .15s;
    }

    .bd-input[readonly] {
        background: var(--soft);
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 13px;
    }

    .bd-input:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .14);
    }

    /* file drop area */
    .bd-drop {
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px;
        border: 1.5px dashed #b4bed0;
        border-radius: 12px;
        background: var(--soft);
        cursor: pointer;
        transition: border-color .15s, background .15s;
    }

    .bd-drop:hover,
    .bd-drop.drag {
        border-color: var(--brand);
        background: var(--brand-bg);
    }

    .bd-drop:focus-within {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .14);
    }

    .bd-drop.has-file {
        border-style: solid;
        border-color: var(--ok);
        background: var(--ok-bg);
    }

    .bd-drop input[type=file] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .bd-drop-ic {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #fff;
        color: var(--brand);
        font-size: 17px;
    }

    .bd-drop.has-file .bd-drop-ic {
        color: var(--ok);
    }

    .bd-drop-txt {
        min-width: 0;
    }

    .bd-drop-txt strong {
        display: block;
        font-size: 14px;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .bd-drop-txt span {
        font-size: 13px;
        color: var(--muted);
    }

    .bd-err {
        display: none;
        margin-top: 8px;
        font-size: 13px;
        font-weight: 500;
        color: var(--bad);
    }

    .bd-err.show {
        display: block;
    }

    .bd-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-top: 4px;
        padding-top: 18px;
        border-top: 1px solid var(--line);
    }

    .bd-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
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

    .bd-btn:hover {
        background: var(--brand-d);
    }

    .bd-btn:focus-visible,
    .bd-sample:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    .bd-btn[disabled] {
        opacity: .65;
        cursor: not-allowed;
    }

    .bd-hint {
        font-size: 13px;
        color: var(--muted);
    }

    /* side panel */
    .bd-side {
        padding: 22px 24px;
    }

    .bd-steps {
        margin: 0 0 18px;
        padding: 0;
        list-style: none;
        counter-reset: s;
    }

    .bd-steps li {
        position: relative;
        counter-increment: s;
        padding: 0 0 12px 36px;
        font-size: 14px;
        color: var(--ink);
    }

    .bd-steps li::before {
        content: counter(s);
        position: absolute;
        left: 0;
        top: 0;
        display: grid;
        place-items: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--brand-bg);
        color: var(--brand);
        font-size: 12px;
        font-weight: 700;
    }

    .bd-cols {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin: 6px 0 0;
    }

    .bd-cols code {
        padding: 2px 8px;
        border-radius: 6px;
        background: var(--soft);
        border: 1px solid var(--line);
        color: var(--ink);
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12px;
    }

    .bd-sub {
        margin: 16px 0 0;
        padding-top: 16px;
        border-top: 1px solid var(--line);
    }

    .bd-sub p {
        margin: 0 0 10px;
        font-size: 13px;
        color: var(--muted);
    }

    .bd-sample {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border: 1px solid #c4ccd9;
        border-radius: 10px;
        background: #fff;
        color: var(--ink);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: background .15s, border-color .15s;
    }

    .bd-sample i {
        color: var(--ok);
    }

    .bd-sample:hover {
        background: var(--soft);
        border-color: #aeb8ca;
        color: var(--ink);
        text-decoration: none;
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

    @media (max-width: 860px) {
        .bd-layout {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 575.98px) {

        .bd-form,
        .bd-side {
            padding: 18px 16px;
        }

        .bd-btn {
            width: 100%;
            justify-content: center;
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
                        <h1>Bulk data upload</h1>
                        <p>Upload a CSV file to add seat allocations and results for a program.</p>
                    </header>

                    <?php if ($loadError): ?>
                        <div class="at-alert" role="alert">
                            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                            Programs could not be loaded. Refresh the page or contact the administrator.
                        </div>
                    <?php endif; ?>

                    <div class="bd-layout">

                        <!-- upload form -->
                        <section class="at-card bd-form">
                            <h2>Upload file</h2>
                            <form id="bulkUploadForm" action="upload_bulk_data.php" method="POST" enctype="multipart/form-data">

                                <div class="bd-field">
                                    <label for="programSelect"><i class="fas fa-graduation-cap" aria-hidden="true"></i> Program <span class="req">*</span></label>
                                    <select name="program_name" class="bd-input" id="programSelect" required>
                                        <option value="">Select a program</option>
                                        <?php foreach ($programs as $name => $session): ?>
                                            <option value="<?php echo bd_h($name); ?>"><?php echo bd_h($name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="bd-field">
                                    <label for="programSession"><i class="fas fa-calendar-alt" aria-hidden="true"></i> Session</label>
                                    <input type="text" name="session_time" id="programSession" class="bd-input" placeholder="Shown after you pick a program" readonly>
                                </div>

                                <div class="bd-field">
                                    <label for="bulkFile"><i class="fas fa-file-csv" aria-hidden="true"></i> CSV file <span class="req">*</span></label>
                                    <div class="bd-drop" id="dropZone">
                                        <input type="file" name="bulk_file" id="bulkFile" accept=".csv" required>
                                        <span class="bd-drop-ic"><i class="fas fa-cloud-upload-alt" aria-hidden="true"></i></span>
                                        <div class="bd-drop-txt">
                                            <strong id="fileName">Choose a CSV file or drop it here</strong>
                                            <span id="fileMeta">Only .csv files are accepted</span>
                                        </div>
                                    </div>
                                    <div class="bd-err" id="fileError" role="alert">Please choose a .csv file.</div>
                                </div>

                                <div class="bd-actions">
                                    <button type="submit" class="bd-btn" id="uploadBtn">
                                        <i class="fas fa-upload" aria-hidden="true"></i> Upload file
                                    </button>
                                    <span class="bd-hint">Check the program before you upload.</span>
                                </div>
                            </form>
                        </section>

                        <!-- instructions -->
                        <aside class="at-card bd-side">
                            <h2>How it works</h2>
                            <ol class="bd-steps">
                                <li>Download the sample CSV file.</li>
                                <li>
                                    Fill in the student data using these columns:
                                    <div class="bd-cols">
                                        <code>student_id</code>
                                        <code>seat_no</code>
                                        <code>student_result</code>
                                        <code>calling_name</code>
                                    </div>
                                </li>
                                <li>Select the program and upload the completed file.</li>
                            </ol>

                            <div class="bd-sub">
                                <p>Not sure about the format?</p>
                                <a href="./bulk-data-excel-final.csv" class="bd-sample" download>
                                    <i class="fas fa-download" aria-hidden="true"></i> Download sample CSV
                                </a>
                            </div>
                        </aside>

                    </div>
                </div>

            </div> <!-- /container-fluid -->
        </div> <!-- /content -->
    </div> <!-- /content-wrapper -->
</div> <!-- /wrapper -->

<!-- jQuery -->
<script src="vendor/jquery/jquery.min.js"></script>

<!-- Bootstrap Bundle <<<<<<<<<<<<<<<<< HIDDEN: THE LINK IS NOT WORKING >>>>>>>>>>>>>>>>> -->
<!-- <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->

<!-- DataTables -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Select2 JS -->
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

        // Program -> session mapping (keys are raw program names)
        var programSessions = <?php echo json_encode($programs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        if ($.fn.select2) {
            $('#programSelect').select2({
                placeholder: 'Select a program',
                allowClear: true,
                width: '100%'
            });
        }

        $('#programSelect').on('change', function() {
            var p = $(this).val();
            $('#programSession').val(p && programSessions[p] ? programSessions[p] : '');
        });

        // File picker feedback + .csv check
        var $drop = $('#dropZone'),
            $file = $('#bulkFile'),
            $err = $('#fileError');

        function formatSize(b) {
            return b < 1024 ? b + ' B' : b < 1048576 ? (b / 1024).toFixed(1) + ' KB' : (b / 1048576).toFixed(1) + ' MB';
        }

        function showFile() {
            var f = $file[0].files[0];
            if (!f) {
                $drop.removeClass('has-file');
                $('#fileName').text('Choose a CSV file or drop it here');
                $('#fileMeta').text('Only .csv files are accepted');
                return true;
            }
            if (!/\.csv$/i.test(f.name)) {
                $file.val('');
                $drop.removeClass('has-file');
                $('#fileName').text('Choose a CSV file or drop it here');
                $('#fileMeta').text('Only .csv files are accepted');
                $err.text('"' + f.name + '" is not a .csv file.').addClass('show');
                return false;
            }
            $err.removeClass('show');
            $drop.addClass('has-file');
            $('#fileName').text(f.name);
            $('#fileMeta').text(formatSize(f.size) + ' · click to change');
            return true;
        }

        $file.on('change', showFile);
        $drop.on('dragenter dragover', function() {
            $drop.addClass('drag');
        });
        $drop.on('dragleave drop', function() {
            $drop.removeClass('drag');
        });

        // Prevent double submits
        $('#bulkUploadForm').on('submit', function() {
            if (!showFile()) return false;
            $('#uploadBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Uploading…');
        });
    });
</script>