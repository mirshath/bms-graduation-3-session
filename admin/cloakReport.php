<?php
session_start();
// Check if the admin is logged in by checking session variable
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
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

// SESSION_01 / SESSION_02 / SESSION_03 ... -> s1 / s2 / s3 colour pill
function at_sesCls(string $s): string
{
    if (preg_match('/(\d+)/', $s, $m)) {
        return 's' . ((((int)$m[1] - 1) % 3 + 3) % 3 + 1);
    }
    return '';
}

// Per-item pill colour (Cloak / Slashes / Hats)
function at_itemCls(string $status): string
{
    if ($status === 'Returned') return 'ok';
    if ($status === 'Issued')   return 'warn';
    return 'off';
}

// Overall return-status pill colour
function at_overallCls(string $status): string
{
    if ($status === 'Fully Returned')   return 'ok';
    if ($status === 'Partial Returned') return 'info';
    if ($status === 'Issued')           return 'warn';
    return 'off';
}

// Small item chips: $kind = iss (issued) | ret (returned) | mis (missing) | req (required)
function at_chips(array $items, string $kind): string
{
    if (!$items) return '<span class="at-na">—</span>';
    $out = '';
    foreach ($items as $i) {
        $out .= '<span class="at-item ' . $kind . '">' . at_h($i) . '</span>';
    }
    return $out;
}

function at_person(string $name): string
{
    $name = trim($name);
    if ($name === '') return '<span class="at-na">N/A</span>';
    return '<div class="at-person"><span class="at-ini" data-i="' . at_h(at_initials($name)) . '" aria-hidden="true"></span><span class="nm">' . at_h($name) . '</span></div>';
}

function at_val(?string $v, string $cls = ''): string
{
    $v = trim((string)$v);
    if ($v === '') return '<span class="at-na">N/A</span>';
    return $cls !== '' ? '<span class="' . $cls . '">' . at_h($v) . '</span>' : at_h($v);
}

function at_session($s): string
{
    $s = trim((string)$s);
    if ($s === '') return '<span class="at-na">N/A</span>';
    return '<span class="at-ses ' . at_sesCls($s) . '">' . at_h($s) . '</span>';
}

// Filters
$sessionFilter = isset($_GET['session']) ? trim($_GET['session']) : '';
$returnFilter  = isset($_GET['return_status']) ? trim($_GET['return_status']) : '';
$programFilter = isset($_GET['program']) ? trim($_GET['program']) : '';
$issueFilter   = isset($_GET['issue_status']) ? trim($_GET['issue_status']) : '';

// Sessions = unique values of data_tables.session (SESSION_01, SESSION_02, SESSION_03 ...)
$allowedSessions = [];
$sessRes = mysqli_query($conn, "SELECT DISTINCT session FROM data_tables WHERE session IS NOT NULL AND session <> '' ORDER BY session");
if ($sessRes) {
    while ($sr = mysqli_fetch_assoc($sessRes)) {
        $allowedSessions[] = $sr['session'];
    }
}

$allowedReturn   = ['issued', 'returned', 'not_returned', 'fully_returned', 'partial_returned'];
// NEW: "did not get cloak" checks (paid students vs. required clothing per program)
$allowedIssue    = ['not_issued', 'partial_issued', 'any_missing'];

if (!in_array($sessionFilter, $allowedSessions)) {
    $sessionFilter = '';
}
if (!in_array($returnFilter, $allowedReturn)) {
    $returnFilter = '';
}
if (!in_array($issueFilter, $allowedIssue)) {
    $issueFilter = '';
}
// When the "not issued" check is used, the return-status filter doesn't apply
$missingMode = ($issueFilter !== '');
if ($missingMode) {
    $returnFilter = '';
}

// Programs for the Program dropdown (data_tables) - only the chosen session's programs
$programs = [];
$progSql = "SELECT DISTINCT programName FROM data_tables WHERE programName IS NOT NULL AND programName <> ''";
if ($sessionFilter !== '') {
    $progSql .= " AND session = '" . mysqli_real_escape_string($conn, $sessionFilter) . "'";
}
$progSql .= " ORDER BY programName";
$progRes = mysqli_query($conn, $progSql);
if ($progRes) {
    while ($row = mysqli_fetch_assoc($progRes)) {
        $programs[] = $row['programName'];
    }
}
// A program that isn't in the chosen session is ignored
if ($programFilter !== '' && !in_array($programFilter, $programs, true)) {
    $programFilter = '';
}

// Build WHERE clause
$where = [];
if ($sessionFilter !== '') {
    $where[] = "dt.session = '" . mysqli_real_escape_string($conn, $sessionFilter) . "'";
}
if ($programFilter !== '') {
    $where[] = "cc.program_name = '" . mysqli_real_escape_string($conn, $programFilter) . "'";
}
$col_cnt = "(CASE WHEN COALESCE(cc.collect_cloak,'')!='' THEN 1 ELSE 0 END + CASE WHEN COALESCE(cc.collect_slashes,'')!='' THEN 1 ELSE 0 END + CASE WHEN COALESCE(cc.collect_hats,'')!='' THEN 1 ELSE 0 END)";
$ret_cnt = "(CASE WHEN COALESCE(cc.return_cloak,'')!='' THEN 1 ELSE 0 END + CASE WHEN COALESCE(cc.return_slashes,'')!='' THEN 1 ELSE 0 END + CASE WHEN COALESCE(cc.return_hats,'')!='' THEN 1 ELSE 0 END)";
if ($returnFilter === 'issued') {
    $where[] = "($col_cnt > 0)";
}
if ($returnFilter === 'returned') {
    $where[] = "($ret_cnt > 0)";
}
if ($returnFilter === 'not_returned') {
    $where[] = "($col_cnt > 0 AND $ret_cnt = 0)";
}
if ($returnFilter === 'fully_returned') {
    $where[] = "($col_cnt > 0 AND $ret_cnt > 0 AND $col_cnt = $ret_cnt)";
}
if ($returnFilter === 'partial_returned') {
    $where[] = "($col_cnt > 0 AND $ret_cnt > 0 AND $ret_cnt < $col_cnt)";
}
$where_sql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

// Main query
$query = "
    SELECT cc.id, cc.student_id, cc.student_name, cc.program_name,
           cc.collect_cloak, cc.collect_slashes, cc.collect_hats, cc.collected_at,
           cc.return_cloak, cc.return_slashes, cc.return_hats,
           dt.session,
           rs.phone_no
    FROM clothing_collections cc
    LEFT JOIN data_tables dt ON cc.program_name = dt.programName
    LEFT JOIN registered_students rs ON rs.student_id = cc.student_id
    $where_sql
    ORDER BY cc.collected_at DESC
";
$collections = $missingMode ? false : mysqli_query($conn, $query);


// Counters
$totalRecords      = 0;
$totalIssued       = 0;
$totalReturned     = 0;
$totalNotReturned  = 0;
$rows = [];

if ($collections && mysqli_num_rows($collections) > 0) {
    while ($c = mysqli_fetch_assoc($collections)) {
        $totalRecords++;

        $collected_items = [];
        if (!empty($c['collect_cloak'])) {
            $collected_items[] = 'Cloak';
        }
        if (!empty($c['collect_slashes'])) {
            $collected_items[] = 'Slashes';
        }
        if (!empty($c['collect_hats'])) {
            $collected_items[] = 'Hats';
        }

        $returned_items = [];
        if (!empty($c['return_cloak'])) {
            $returned_items[] = 'Cloak';
        }
        if (!empty($c['return_slashes'])) {
            $returned_items[] = 'Slashes';
        }
        if (!empty($c['return_hats'])) {
            $returned_items[] = 'Hats';
        }

        $collected_count = count($collected_items);
        $returned_count  = count($returned_items);

        if ($collected_count > 0) {
            $totalIssued++;
        }
        if ($returned_count > 0) {
            $totalReturned++;
        }
        if ($collected_count > 0 && $returned_count === 0) {
            $totalNotReturned++;
        }

        $cloak_status   = !empty($c['collect_cloak']) ? (!empty($c['return_cloak']) ? 'Returned' : 'Issued') : 'Not Issued';
        $slashes_status = !empty($c['collect_slashes']) ? (!empty($c['return_slashes']) ? 'Returned' : 'Issued') : 'Not Issued';
        $hats_status    = !empty($c['collect_hats']) ? (!empty($c['return_hats']) ? 'Returned' : 'Issued') : 'Not Issued';

        $overall_status = 'N/A';
        if ($collected_count > 0) {
            if ($returned_count === 0) {
                $overall_status = 'Issued';
            } elseif ($returned_count >= $collected_count) {
                $overall_status = 'Fully Returned';
            } else {
                $overall_status = 'Partial Returned';
            }
        }

        $cloak_class   = $cloak_status === 'Returned' ? 'badge-success' : ($cloak_status === 'Issued' ? 'badge-warning' : 'badge-secondary');
        $slashes_class = $slashes_status === 'Returned' ? 'badge-success' : ($slashes_status === 'Issued' ? 'badge-warning' : 'badge-secondary');
        $hats_class    = $hats_status === 'Returned' ? 'badge-success' : ($hats_status === 'Issued' ? 'badge-warning' : 'badge-secondary');
        $overall_class = $overall_status === 'Fully Returned' ? 'badge-success' : ($overall_status === 'Partial Returned' ? 'badge-info' : ($overall_status === 'Issued' ? 'badge-warning' : 'badge-secondary'));

        $rows[] = [
            'raw'             => $c,
            'collected_items' => $collected_items,
            'returned_items'  => $returned_items,
            'cloak_status'    => $cloak_status,
            'slashes_status'  => $slashes_status,
            'hats_status'     => $hats_status,
            'overall_status'  => $overall_status,
            'cloak_class'     => $cloak_class,
            'slashes_class'   => $slashes_class,
            'hats_class'      => $hats_class,
            'overall_class'   => $overall_class,
        ];
    }
}

// =====================================================================
// NEW: "Not issued" check
// Source of truth for "who should get clothing":
//   payment_records  -> student has paid the graduation fee
//   data_tables      -> which items (cloak / slashes / hats) that program-batch needs
//   clothing_collections -> what the student has actually collected
// =====================================================================
$missingRows = [];
$progSummary = [];
$missTotals  = ['eligible' => 0, 'complete' => 0, 'none' => 0, 'partial' => 0];
$mRes        = null;

if ($missingMode) {
    $mWhere = ["(dt.cloak > 0 OR dt.slashes > 0 OR dt.hats > 0)"];   // program needs at least 1 item
    if ($sessionFilter !== '') {
        $mWhere[] = "dt.session = '" . mysqli_real_escape_string($conn, $sessionFilter) . "'";
    }
    if ($programFilter !== '') {
        $mWhere[] = "pr.program_name = '" . mysqli_real_escape_string($conn, $programFilter) . "'";
    }

    $mSql = "
        SELECT pr.student_id, pr.program_name, pr.total_paid, pr.last_payment_date, pr.receipts,
               dt.session, dt.cloak AS req_cloak, dt.slashes AS req_slashes, dt.hats AS req_hats,
               rs.name_in_full, rs.phone_no,
               cc.collect_cloak, cc.collect_slashes, cc.collect_hats, cc.collected_at
        FROM (
            SELECT student_id, program_name,
                   SUM(total_amount)  AS total_paid,
                   MAX(payment_date)  AS last_payment_date,
                   GROUP_CONCAT(DISTINCT receipt_number ORDER BY receipt_number SEPARATOR ', ') AS receipts
            FROM payment_records
            GROUP BY student_id, program_name
        ) pr
        INNER JOIN (
            SELECT programName, MAX(session) AS session,
                   MAX(cloak) AS cloak, MAX(slashes) AS slashes, MAX(hats) AS hats
            FROM data_tables
            GROUP BY programName
        ) dt ON dt.programName = pr.program_name
        LEFT JOIN registered_students    rs ON rs.student_id = pr.student_id
        LEFT JOIN clothing_collections   cc ON cc.student_id = pr.student_id
        WHERE " . implode(' AND ', $mWhere) . "
        ORDER BY pr.program_name, pr.student_id
    ";
    $mRes = mysqli_query($conn, $mSql);

    if ($mRes) {
        while ($m = mysqli_fetch_assoc($mRes)) {
            // Items this program-batch requires  => was it collected?
            $required = [];
            if ((int)$m['req_cloak']   > 0) {
                $required['Cloak']   = !empty($m['collect_cloak']);
            }
            if ((int)$m['req_slashes'] > 0) {
                $required['Slashes'] = !empty($m['collect_slashes']);
            }
            if ((int)$m['req_hats']    > 0) {
                $required['Hats']    = !empty($m['collect_hats']);
            }

            $missingItems   = array_keys(array_filter($required, function ($got) {
                return !$got;
            }));
            $anyCollected   = !empty($m['collect_cloak']) || !empty($m['collect_slashes']) || !empty($m['collect_hats']);

            if (count($missingItems) === 0) {
                $state = 'complete';      // got everything the program needs
            } elseif (!$anyCollected) {
                $state = 'none';          // paid, but nothing issued at all
            } else {
                $state = 'partial';       // paid, some items issued, some still missing
            }

            // Per program-batch summary (counted BEFORE the issue filter is applied)
            $pn = $m['program_name'];
            if (!isset($progSummary[$pn])) {
                $progSummary[$pn] = ['session' => $m['session'], 'eligible' => 0, 'complete' => 0, 'none' => 0, 'partial' => 0];
            }
            $progSummary[$pn]['eligible']++;
            $progSummary[$pn][$state]++;
            $missTotals['eligible']++;
            $missTotals[$state]++;

            // Apply the chosen issue filter to the detail list
            $show = ($issueFilter === 'any_missing'    && $state !== 'complete')
                || ($issueFilter === 'not_issued'     && $state === 'none')
                || ($issueFilter === 'partial_issued' && $state === 'partial');
            if (!$show) {
                continue;
            }

            $missingRows[] = [
                'raw'      => $m,
                'required' => array_keys($required),
                'missing'  => $missingItems,
                'state'    => $state,
            ];
        }
    }
    // Program-batches with the most missing students first
    uasort($progSummary, function ($a, $b) {
        return ($b['none'] + $b['partial']) <=> ($a['none'] + $a['partial']);
    });
}

// Query failures: show a friendly message and log the real error
$loadError = $missingMode ? ($mRes === false) : (!$collections);
if ($loadError) {
    error_log("Cloak report load error: " . mysqli_error($conn));
}
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Page level plugins -->
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">
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

    table.at-table {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13.5px;
    }

    table.at-table thead th {
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

    table.at-table tbody td {
        padding: 12px 16px;
        border: 0;
        border-bottom: 1px solid #eef1f6;
        background: #fff;
        vertical-align: middle;
        color: var(--ink);
    }

    table.at-table tbody tr:hover td {
        background: #f8faff;
    }

    table.at-table tbody tr:last-child td {
        border-bottom: 0;
    }

    table.at-table.dataTable>thead>tr>th.sorting::before,
    table.at-table.dataTable>thead>tr>th.sorting::after,
    table.at-table.dataTable>thead>tr>th.sorting_asc::before,
    table.at-table.dataTable>thead>tr>th.sorting_asc::after,
    table.at-table.dataTable>thead>tr>th.sorting_desc::before,
    table.at-table.dataTable>thead>tr>th.sorting_desc::after {
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
        table.at-table {
            font-size: 13px;
        }

        table.at-table thead th {
            padding: 11px 10px;
            font-size: 11px;
            letter-spacing: .03em;
        }

        table.at-table tbody td {
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

    /* phones + tablets: every student becomes a card (14 columns do not fit narrow screens) */
    @media (max-width: 1099px) {
        .at-scroll {
            overflow: visible;
        }

        table.at-table,
        table.at-table tbody {
            display: block;
            width: 100% !important;
        }

        table.at-table thead {
            display: none;
        }

        table.at-table tbody tr {
            display: block;
            width: auto;
            margin: 12px;
            padding: 4px 14px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
        }

        table.at-table tbody td {
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

        table.at-table tbody tr:last-child td {
            border-bottom: 1px dashed var(--line);
        }

        table.at-table tbody td:last-child {
            border-bottom: 0;
        }

        table.at-table tbody td::before {
            content: attr(data-label);
            flex: 0 0 auto;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: var(--muted);
            text-align: left;
        }

        table.at-table tbody td[data-label="#"] {
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
    /* registered students specific */
    .at-meal {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.25;
    }

    .at-meal::before {
        content: "";
        flex: 0 0 auto;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .at-meal.veg {
        background: #e6f4ec;
        color: #1b7f4b;
    }

    .at-meal.non {
        background: #fff0e4;
        color: #b4540a;
    }

    .at-meal.other {
        background: var(--soft);
        color: var(--muted);
    }

    .at-st.warn {
        background: var(--warn-bg);
        color: var(--warn);
    }

    .at-st.bad {
        background: #fdecea;
        color: var(--bad);
    }

    .at-dt,
    .at-tm {
        display: block;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 11.5px;
        line-height: 1.35;
    }

    .at-tm {
        color: #8a95a8;
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
</style>



<style>
    /* =========================================================
       Cloak report specific
       ========================================================= */
    .at-kpi.bad .at-kpi-ic {
        background: #fdecea;
        color: var(--bad);
    }

    .at-kpi.bad strong {
        color: var(--bad);
    }

    .at-st.info {
        background: var(--brand-bg);
        color: var(--brand);
    }

    a.at-reset {
        text-decoration: none;
    }

    a.at-reset:hover {
        color: var(--ink);
        text-decoration: none;
    }

    .at-hint {
        margin-top: 5px;
        font-size: 12px;
        color: var(--muted);
    }

    .at-sel:disabled,
    .at .select2-container--disabled .select2-selection--single {
        background: var(--soft) !important;
        opacity: .65;
        cursor: not-allowed;
    }

    /* section header inside a table card */
    .at-sec {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--line);
    }

    .at-sec h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.01em;
        color: var(--ink);
    }

    .at-sec h2 i {
        margin-right: 8px;
        color: var(--brand);
        font-size: 15px;
    }

    .at-cnt {
        padding: 3px 12px;
        border-radius: 999px;
        background: var(--soft);
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12px;
        font-weight: 700;
        color: var(--muted);
        white-space: nowrap;
    }

    .at-table-card+.at-table-card {
        margin-top: 18px;
    }

    /* item chips: Cloak / Slashes / Hats */
    .at-item {
        display: inline-flex;
        align-items: center;
        margin: 0 4px 4px 0;
        padding: 2px 9px;
        border: 1px solid var(--line);
        border-radius: 6px;
        background: var(--soft);
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
        color: var(--muted);
        white-space: nowrap;
    }

    .at-item.iss {
        background: var(--brand-bg);
        border-color: #cfdbf5;
        color: var(--brand);
    }

    .at-item.ret {
        background: var(--ok-bg);
        border-color: #c5e6d3;
        color: var(--ok);
    }

    .at-item.mis {
        background: #fdecea;
        border-color: #f6cdc9;
        color: var(--bad);
    }

    /* cells */
    td.at-c {
        text-align: center;
    }

    .at-amt {
        font-weight: 600;
    }

    .at-wrap {
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .at-link {
        font-weight: 600;
        color: var(--brand);
        text-decoration: none;
    }

    .at-link:hover {
        color: var(--brand-d);
        text-decoration: underline;
    }

    /* coverage bar in the per-program summary */
    .at-cov {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 130px;
    }

    .at-cov-bar {
        flex: 1;
        height: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: var(--soft);
        border: 1px solid var(--line);
    }

    .at-cov-bar i {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: var(--ok);
    }

    .at-cov b {
        min-width: 38px;
        font-family: 'Geist Mono', ui-monospace, monospace;
        font-size: 12px;
        text-align: right;
    }

    /* desktop: keep 11-12 columns readable, scroll sideways only if truly needed */
    @media (min-width: 1100px) {
        table.at-table {
            font-size: 12.5px;
        }

        table.at-table thead th {
            padding: 11px 10px;
            font-size: 11px;
            letter-spacing: .03em;
        }

        table.at-table tbody td {
            padding: 10px;
        }

        table.at-table .at-person {
            min-width: 150px;
        }

        table.at-table .at-prog {
            min-width: 170px;
        }

        table.at-table .at-st {
            padding: 3px 9px;
            font-size: 11.5px;
        }
    }

    @media (min-width: 1100px) and (max-width: 1599px) {
        table.at-table .at-ini {
            display: none;
        }
    }

    @media (max-width: 575px) {
        .at-sec {
            padding: 14px 16px;
        }
    }

    /* =========================================================
       Tables fit the card width on desktop: fixed layout,
       set column widths, text wraps, no sideways scrolling
       ========================================================= */
    @media (min-width: 1100px) {
        .at-scroll {
            overflow-x: visible;
        }

        table.at-table {
            table-layout: fixed;
            width: 100% !important;
            font-size: 12px;
        }

        table.at-table thead th {
            padding: 10px 18px 10px 8px;
            font-size: 10px;
            letter-spacing: .01em;
            white-space: normal;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }

        table.at-table.dataTable>thead>tr>th.sorting::before,
        table.at-table.dataTable>thead>tr>th.sorting::after,
        table.at-table.dataTable>thead>tr>th.sorting_asc::before,
        table.at-table.dataTable>thead>tr>th.sorting_asc::after,
        table.at-table.dataTable>thead>tr>th.sorting_desc::before,
        table.at-table.dataTable>thead>tr>th.sorting_desc::after {
            right: 4px;
        }

        table.at-table tbody td {
            padding: 9px 8px;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        table.at-table .at-person {
            min-width: 0;
            gap: 6px;
        }

        table.at-table .at-ini {
            display: none;
        }

        table.at-table .at-prog {
            min-width: 0;
            font-size: 11.5px;
        }

        table.at-table .at-mono,
        table.at-table .at-id {
            white-space: normal;
            overflow-wrap: anywhere;
            font-size: 11px;
        }

        table.at-table .at-id {
            padding: 2px 5px;
        }

        table.at-table .at-st,
        table.at-table .at-ses {
            padding: 3px 7px;
            font-size: 10.5px;
            white-space: normal;
            gap: 5px;
        }

        table.at-table .at-ses::before,
        table.at-table .at-st::before {
            flex: 0 0 auto;
            width: 6px;
            height: 6px;
        }

        table.at-table .at-item {
            margin: 0 3px 3px 0;
            padding: 1px 6px;
            font-size: 10.5px;
        }

        table.at-table .at-cov {
            min-width: 0;
            gap: 6px;
        }

        table.at-table .at-cov b {
            min-width: 32px;
            font-size: 11px;
        }

        /* Issued / returned records (12 columns) */
        #collectionsTable th:nth-child(1) {
            width: 7%;
        }

        #collectionsTable th:nth-child(2) {
            width: 11%;
        }

        #collectionsTable th:nth-child(3) {
            width: 14%;
        }

        #collectionsTable th:nth-child(4) {
            width: 7%;
        }

        #collectionsTable th:nth-child(5) {
            width: 9%;
        }

        #collectionsTable th:nth-child(6) {
            width: 8%;
        }

        #collectionsTable th:nth-child(7) {
            width: 9%;
        }

        #collectionsTable th:nth-child(8) {
            width: 6.5%;
        }

        #collectionsTable th:nth-child(9) {
            width: 6.5%;
        }

        #collectionsTable th:nth-child(10) {
            width: 6%;
        }

        #collectionsTable th:nth-child(11) {
            width: 8%;
        }

        #collectionsTable th:nth-child(12) {
            width: 8%;
        }

        /* Students missing clothing (11 columns) */
        #missingTable th:nth-child(1) {
            width: 7%;
        }

        #missingTable th:nth-child(2) {
            width: 12%;
        }

        #missingTable th:nth-child(3) {
            width: 15%;
        }

        #missingTable th:nth-child(4) {
            width: 7%;
        }

        #missingTable th:nth-child(5) {
            width: 9%;
        }

        #missingTable th:nth-child(6) {
            width: 7%;
        }

        #missingTable th:nth-child(7) {
            width: 8%;
        }

        #missingTable th:nth-child(8) {
            width: 9%;
        }

        #missingTable th:nth-child(9) {
            width: 9%;
        }

        #missingTable th:nth-child(10) {
            width: 8%;
        }

        #missingTable th:nth-child(11) {
            width: 9%;
        }

        /* Missing by program (8 columns) */
        #programSummaryTable th:nth-child(1) {
            width: 26%;
        }

        #programSummaryTable th:nth-child(2) {
            width: 9%;
        }

        #programSummaryTable th:nth-child(3) {
            width: 10%;
        }

        #programSummaryTable th:nth-child(4) {
            width: 10%;
        }

        #programSummaryTable th:nth-child(5) {
            width: 10%;
        }

        #programSummaryTable th:nth-child(6) {
            width: 10%;
        }

        #programSummaryTable th:nth-child(7) {
            width: 15%;
        }

        #programSummaryTable th:nth-child(8) {
            width: 10%;
        }
    }

    /* roomy screens: a little more breathing room, avatars back */
    @media (min-width: 1500px) {
        table.at-table {
            font-size: 12.5px;
        }

        table.at-table thead th {
            font-size: 10.5px;
        }

        table.at-table .at-ini {
            display: grid;
            width: 26px;
            height: 26px;
            font-size: 10px;
        }
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
                            <h1>Cloak Report</h1>
                            <p>
                                <?php if ($missingMode): ?>
                                    Paid students who have not collected the clothing their program requires.
                                <?php else: ?>
                                    Cloak, slashes and hats issued to students, and what has come back.
                                <?php endif; ?>
                            </p>
                        </div>
                    </header>

                    <?php if ($loadError): ?>
                        <div class="at-alert" role="alert">
                            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                            Error loading the report. Please try again later.
                        </div>
                    <?php endif; ?>

                    <!-- summary -->
                    <section class="at-kpis">
                        <?php if ($missingMode): ?>
                            <div class="at-card at-kpi">
                                <span class="at-kpi-ic"><i class="fas fa-receipt" aria-hidden="true"></i></span>
                                <div><span class="k">Paid &amp; eligible</span><strong><?php echo (int)$missTotals['eligible']; ?></strong></div>
                            </div>
                            <div class="at-card at-kpi ok">
                                <span class="at-kpi-ic"><i class="fas fa-check-circle" aria-hidden="true"></i></span>
                                <div><span class="k">Fully issued</span><strong><?php echo (int)$missTotals['complete']; ?></strong></div>
                            </div>
                            <div class="at-card at-kpi bad">
                                <span class="at-kpi-ic"><i class="fas fa-user-times" aria-hidden="true"></i></span>
                                <div><span class="k">Not issued</span><strong><?php echo (int)$missTotals['none']; ?></strong></div>
                            </div>
                            <div class="at-card at-kpi warn">
                                <span class="at-kpi-ic"><i class="fas fa-hourglass-half" aria-hidden="true"></i></span>
                                <div><span class="k">Partially issued</span><strong><?php echo (int)$missTotals['partial']; ?></strong></div>
                            </div>
                        <?php else: ?>
                            <div class="at-card at-kpi">
                                <span class="at-kpi-ic"><i class="fas fa-clipboard-list" aria-hidden="true"></i></span>
                                <div><span class="k">Total records</span><strong><?php echo (int)$totalRecords; ?></strong></div>
                            </div>
                            <div class="at-card at-kpi warn">
                                <span class="at-kpi-ic"><i class="fas fa-tshirt" aria-hidden="true"></i></span>
                                <div><span class="k">Issued</span><strong><?php echo (int)$totalIssued; ?></strong></div>
                            </div>
                            <div class="at-card at-kpi ok">
                                <span class="at-kpi-ic"><i class="fas fa-undo-alt" aria-hidden="true"></i></span>
                                <div><span class="k">Returned</span><strong><?php echo (int)$totalReturned; ?></strong></div>
                            </div>
                            <div class="at-card at-kpi bad">
                                <span class="at-kpi-ic"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i></span>
                                <div><span class="k">Not returned</span><strong><?php echo (int)$totalNotReturned; ?></strong></div>
                            </div>
                        <?php endif; ?>
                    </section>

                    <!-- filters -->
                    <?php
                    $issueLabels  = ['not_issued' => 'Not issued', 'partial_issued' => 'Partially issued', 'any_missing' => 'Any missing'];
                    $returnLabels = ['issued' => 'Issued', 'not_returned' => 'Not returned', 'partial_returned' => 'Partial returned', 'fully_returned' => 'Fully returned', 'returned' => 'Returned (any)'];
                    $active = [];
                    if ($sessionFilter !== '') $active[] = 'Session: <strong>' . at_h($sessionFilter) . '</strong>';
                    if ($programFilter !== '') $active[] = 'Program: <strong>' . at_h($programFilter) . '</strong>';
                    if ($issueFilter !== '')   $active[] = 'Paid, not collected: <strong>' . at_h($issueLabels[$issueFilter] ?? $issueFilter) . '</strong>';
                    if ($returnFilter !== '')  $active[] = 'Return status: <strong>' . at_h($returnLabels[$returnFilter] ?? $returnFilter) . '</strong>';

                    $exportQuery = http_build_query(array_filter([
                        'session'       => $sessionFilter,
                        'program'       => $programFilter,
                        'return_status' => $returnFilter
                    ]));
                    ?>
                    <section class="at-card at-filters">
                        <form id="filterForm" method="get">
                            <div class="at-grid">
                                <div class="at-field">
                                    <label for="session"><i class="fas fa-calendar-alt"></i> Session</label>
                                    <select name="session" id="session" class="at-sel">
                                        <option value="">All Sessions</option>
                                        <?php foreach ($allowedSessions as $sess): ?>
                                            <option value="<?php echo at_h($sess); ?>" <?php echo $sessionFilter === $sess ? 'selected' : ''; ?>><?php echo at_h($sess); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="at-field">
                                    <label for="program"><i class="fas fa-graduation-cap"></i> Program</label>
                                    <select name="program" id="program" class="at-sel">
                                        <option value="">All Programs</option>
                                        <?php foreach ($programs as $prog): ?>
                                            <option value="<?php echo at_h($prog); ?>" <?php echo $programFilter === $prog ? 'selected' : ''; ?>><?php echo at_h($prog); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="at-field">
                                    <label for="issue_status"><i class="fas fa-user-times"></i> Paid, didn't get cloak</label>
                                    <select name="issue_status" id="issue_status" class="at-sel">
                                        <option value="">Off — show all records</option>
                                        <option value="not_issued" <?php echo $issueFilter === 'not_issued' ? 'selected' : ''; ?>>Not issued (nothing collected)</option>
                                        <!-- <option value="partial_issued" <?php echo $issueFilter === 'partial_issued' ? 'selected' : ''; ?>>Partially issued (items missing)</option> -->
                                        <!-- <option value="any_missing" <?php echo $issueFilter === 'any_missing' ? 'selected' : ''; ?>>Any missing (not / partially issued)</option> -->
                                    </select>
                                </div>

                                <div class="at-field">
                                    <label for="return_status"><i class="fas fa-undo-alt"></i> Return status</label>
                                    <select name="return_status" id="return_status" class="at-sel" <?php echo $missingMode ? 'disabled' : ''; ?>>
                                        <option value="">All Status</option>
                                        <option value="issued" <?php echo $returnFilter === 'issued' ? 'selected' : ''; ?>>Issued</option>
                                        <option value="not_returned" <?php echo $returnFilter === 'not_returned' ? 'selected' : ''; ?>>Not Returned</option>
                                        <option value="partial_returned" <?php echo $returnFilter === 'partial_returned' ? 'selected' : ''; ?>>Partial Returned</option>
                                        <option value="fully_returned" <?php echo $returnFilter === 'fully_returned' ? 'selected' : ''; ?>>Fully Returned</option>
                                        <option value="returned" <?php echo $returnFilter === 'returned' ? 'selected' : ''; ?>>Returned (any)</option>
                                    </select>
                                    <?php if ($missingMode): ?>
                                        <div class="at-hint">Not used while the “didn't get cloak” check is on.</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="at-filter-foot">
                                <a href="cloakReport.php" class="at-reset">
                                    <i class="fas fa-redo" aria-hidden="true"></i> Reset filters
                                </a>
                                <?php if (!$missingMode): ?>
                                    <a href="cloakReportExport.php?<?php echo at_h($exportQuery); ?>" class="at-reset" target="_blank" rel="noopener">
                                        <i class="fas fa-file-export" aria-hidden="true"></i> Full export
                                    </a>
                                <?php endif; ?>
                                <span id="filterInfo">
                                    <?php if ($active): ?>
                                        <i class="fas fa-info-circle"></i> <?php echo implode(' · ', $active); ?>
                                    <?php else: ?>
                                        <i class="fas fa-info-circle"></i> No filters applied — filters apply as you pick them.
                                    <?php endif; ?>
                                </span>
                            </div>
                        </form>
                    </section>

                    <?php if ($missingMode): ?>

                        <!-- missing by program -->
                        <section class="at-card at-table-card">
                            <div class="at-sec">
                                <h2><i class="fas fa-layer-group" aria-hidden="true"></i> Missing by program</h2>
                                <span class="at-cnt"><?php echo count($progSummary); ?> programs</span>
                            </div>
                            <div class="at-scroll">
                                <table class="table at-table" id="programSummaryTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Program - Batch</th>
                                            <th>Session</th>
                                            <th>Paid &amp; Eligible</th>
                                            <th>Fully Issued</th>
                                            <th>Not Issued</th>
                                            <th>Partially Issued</th>
                                            <th>Coverage</th>
                                            <th>Total Missing</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($progSummary as $pn => $ps):
                                            $totalMissing = $ps['none'] + $ps['partial'];
                                            $pct = $ps['eligible'] > 0 ? (int)round($ps['complete'] / $ps['eligible'] * 100) : 0;
                                            $progLink = '?' . http_build_query(array_filter([
                                                'issue_status' => $issueFilter,
                                                'session'      => $sessionFilter,
                                                'program'      => $pn,
                                            ]));
                                        ?>
                                            <tr>
                                                <td data-label="Program">
                                                    <div class="at-prog"><a class="at-link" href="<?php echo at_h($progLink); ?>"><?php echo at_h($pn); ?></a></div>
                                                </td>
                                                <td data-label="Session"><?php echo at_session($ps['session'] ?? ''); ?></td>
                                                <td data-label="Paid &amp; eligible" class="at-c"><span class="at-mono"><?php echo (int)$ps['eligible']; ?></span></td>
                                                <td data-label="Fully issued" class="at-c"><span class="at-mono"><?php echo (int)$ps['complete']; ?></span></td>
                                                <td data-label="Not issued" class="at-c"><span class="at-mono"><?php echo (int)$ps['none']; ?></span></td>
                                                <td data-label="Partially issued" class="at-c"><span class="at-mono"><?php echo (int)$ps['partial']; ?></span></td>
                                                <td data-label="Coverage" data-order="<?php echo $pct; ?>">
                                                    <div class="at-cov"><span class="at-cov-bar"><i style="width:<?php echo $pct; ?>%"></i></span><b><?php echo $pct; ?>%</b></div>
                                                </td>
                                                <td data-label="Total missing" class="at-c" data-order="<?php echo (int)$totalMissing; ?>">
                                                    <span class="at-st <?php echo $totalMissing > 0 ? 'bad' : 'ok'; ?>"><?php echo (int)$totalMissing; ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <!-- students missing clothing -->
                        <section class="at-card at-table-card">
                            <div class="at-sec">
                                <h2><i class="fas fa-user-times" aria-hidden="true"></i> Students missing clothing</h2>
                                <span class="at-cnt"><?php echo count($missingRows); ?> students</span>
                            </div>
                            <table class="table at-table" id="missingTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Program</th>
                                        <th>Session</th>
                                        <th>Receipt No</th>
                                        <th>Paid (Rs.)</th>
                                        <th>Paid On</th>
                                        <th>Required Items</th>
                                        <th>Missing Items</th>
                                        <th>Status</th>
                                        <th>Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($missingRows as $mr):
                                        $m = $mr['raw'];
                                        $isNone = $mr['state'] === 'none';
                                        $paidOn = trim((string)($m['last_payment_date'] ?? ''));
                                    ?>
                                        <tr>
                                            <td data-label="Student ID"><?php echo trim((string)$m['student_id']) !== '' ? '<span class="at-id">' . at_h($m['student_id']) . '</span>' : '<span class="at-na">N/A</span>'; ?></td>
                                            <td data-label="Name"><?php echo at_person((string)($m['name_in_full'] ?? '')); ?></td>
                                            <td data-label="Program"><?php echo trim((string)$m['program_name']) !== '' ? '<div class="at-prog">' . at_h($m['program_name']) . '</div>' : '<span class="at-na">N/A</span>'; ?></td>
                                            <td data-label="Session"><?php echo at_session($m['session'] ?? ''); ?></td>
                                            <td data-label="Receipt no"><?php echo at_val($m['receipts'] ?? '', 'at-mono at-wrap'); ?></td>
                                            <td data-label="Paid (Rs.)" data-order="<?php echo (float)$m['total_paid']; ?>"><span class="at-mono at-amt"><?php echo number_format((float)$m['total_paid']); ?></span></td>
                                            <td data-label="Paid on"><?php echo at_val($paidOn, 'at-mono'); ?></td>
                                            <td data-label="Required"><?php echo at_chips($mr['required'], 'req'); ?></td>
                                            <td data-label="Missing"><?php echo at_chips($mr['missing'], 'mis'); ?></td>
                                            <td data-label="Status"><span class="at-st <?php echo $isNone ? 'bad' : 'warn'; ?>"><?php echo $isNone ? 'Not Issued' : 'Partially Issued'; ?></span></td>
                                            <td data-label="Phone"><?php echo at_val($m['phone_no'] ?? '', 'at-mono'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </section>

                    <?php else: ?>

                        <!-- issued / returned records -->
                        <section class="at-card at-table-card">
                            <table class="table at-table" id="collectionsTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Program</th>
                                        <th>Session</th>
                                        <th>Issued Items</th>
                                        <th>Issued At</th>
                                        <th>Returned Items</th>
                                        <th>Cloak</th>
                                        <th>Slashes</th>
                                        <th>Hats</th>
                                        <th>Return Status</th>
                                        <th>Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row):
                                        $c       = $row['raw'];
                                        $issued  = trim((string)($c['collected_at'] ?? ''));
                                        $iDate   = $issued !== '' ? substr($issued, 0, 10) : '';
                                        $iTime   = strlen($issued) > 11 ? substr($issued, 11) : '';
                                    ?>
                                        <tr>
                                            <td data-label="Student ID"><?php echo trim((string)$c['student_id']) !== '' ? '<span class="at-id">' . at_h($c['student_id']) . '</span>' : '<span class="at-na">N/A</span>'; ?></td>
                                            <td data-label="Name"><?php echo at_person((string)($c['student_name'] ?? '')); ?></td>
                                            <td data-label="Program"><?php echo trim((string)$c['program_name']) !== '' ? '<div class="at-prog">' . at_h($c['program_name']) . '</div>' : '<span class="at-na">N/A</span>'; ?></td>
                                            <td data-label="Session"><?php echo at_session($c['session'] ?? ''); ?></td>
                                            <td data-label="Issued items"><?php echo at_chips($row['collected_items'], 'iss'); ?></td>
                                            <td data-label="Issued at" data-order="<?php echo at_h($issued); ?>">
                                                <?php if ($iDate !== ''): ?>
                                                    <span class="at-dt"><?php echo at_h($iDate); ?></span> <span class="at-tm"><?php echo at_h($iTime); ?></span>
                                                <?php else: ?>
                                                    <span class="at-na">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="Returned items"><?php echo at_chips($row['returned_items'], 'ret'); ?></td>
                                            <td data-label="Cloak"><span class="at-st <?php echo at_itemCls($row['cloak_status']); ?>"><?php echo at_h($row['cloak_status']); ?></span></td>
                                            <td data-label="Slashes"><span class="at-st <?php echo at_itemCls($row['slashes_status']); ?>"><?php echo at_h($row['slashes_status']); ?></span></td>
                                            <td data-label="Hats"><span class="at-st <?php echo at_itemCls($row['hats_status']); ?>"><?php echo at_h($row['hats_status']); ?></span></td>
                                            <td data-label="Return status"><span class="at-st <?php echo at_overallCls($row['overall_status']); ?>"><?php echo at_h($row['overall_status']); ?></span></td>
                                            <td data-label="Phone"><?php echo at_val($c['phone_no'] ?? '', 'at-mono'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </section>

                    <?php endif; ?>

                </div> <!-- /at -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

<!-- ========== JS ========== -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="vendor/jquery/jquery.min.js"></script>
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

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(function() {
        // Mobile sidebar
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
                $('.at-sel').select2({
                    width: '100%',
                    minimumResultsForSearch: 8
                });
            }

            // Exports: chips are joined with ", " so multi-item cells stay readable in Excel / PDF / CSV
            var exportFormat = {
                body: function(data, row, col, node) {
                    var chips = $(node).find('.at-item');
                    if (chips.length) {
                        return chips.map(function() {
                            return $(this).text().trim();
                        }).get().join(', ');
                    }
                    return $(node).text().replace(/\s+/g, ' ').trim();
                }
            };

            function buildButtons(title, filename) {
                var opts = {
                    columns: ':visible',
                    format: exportFormat
                };
                return [{
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-sm at-b at-xl',
                        title: title,
                        filename: filename,
                        exportOptions: opts
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-sm at-b at-pdf',
                        title: title,
                        filename: filename,
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: opts,
                        customize: function(doc) {
                            doc.defaultStyle.fontSize = 7;
                            doc.styles.tableHeader.fontSize = 8;
                            doc.styles.tableHeader.fillColor = '#1f4bb6';
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-sm at-b at-csv',
                        title: title,
                        filename: filename,
                        bom: true,
                        exportOptions: opts
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-sm at-b at-prt',
                        title: title,
                        exportOptions: opts,
                        customize: function(win) {
                            $(win.document.body).find('table').addClass('display').css('font-size', '11px');
                            $(win.document.body).find('h1').css('text-align', 'center');
                        }
                    },
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn btn-sm at-b at-cp',
                        exportOptions: opts
                    }
                ];
            }

            function language(noun) {
                return {
                    search: '',
                    searchPlaceholder: 'Search ' + noun + '…',
                    emptyTable: 'No ' + noun + ' found.',
                    zeroRecords: 'No matching ' + noun + ' found',
                    info: 'Showing _START_ to _END_ of _TOTAL_ ' + noun,
                    infoEmpty: 'Showing 0 to 0 of 0 ' + noun,
                    infoFiltered: '(filtered from _MAX_ total)'
                };
            }

            var fullDom = "<'at-bar'<'at-btns'B><'at-search'f>><'at-scroll'rt><'at-foot'<'at-info'i><'at-pg'p>>";

            if ($('#collectionsTable').length) {
                $('#collectionsTable').DataTable({
                    autoWidth: false,
                    pageLength: 200,
                    order: [
                        [5, 'desc']
                    ], // newest issued first
                    dom: fullDom,
                    language: language('records'),
                    buttons: buildButtons('Cloak Issued & Return Report', 'cloak_report')
                });
            }

            if ($('#programSummaryTable').length) {
                $('#programSummaryTable').DataTable({
                    autoWidth: false,
                    paging: false,
                    searching: false,
                    info: false,
                    order: [
                        [7, 'desc']
                    ], // most missing first
                    dom: "<'at-scroll'rt>",
                    language: language('programs')
                });
            }

            if ($('#missingTable').length) {
                $('#missingTable').DataTable({
                    autoWidth: false,
                    pageLength: 200,
                    order: [
                        [2, 'asc'],
                        [0, 'asc']
                    ], // program, then student id
                    dom: fullDom,
                    language: language('students'),
                    buttons: buildButtons('Paid Students Not Issued Clothing', 'cloak_not_issued_report')
                });
            }

            // Filters apply as soon as they change
            var form = document.getElementById('filterForm');
            $('#session').on('change', function() {
                // new session -> the Program list is rebuilt for that session
                $('#program').val('');
                form.submit();
            });
            $('#program, #return_status, #issue_status').on('change', function() {
                form.submit();
            });

        } catch (e) {
            console.error("DataTable initialization error:", e);
            alert("Error loading table features. Please refresh the page.");
        }
    });
</script>

</body>

</html>