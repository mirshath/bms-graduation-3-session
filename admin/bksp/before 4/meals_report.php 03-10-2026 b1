<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_name'] === 'Unknown Admin') {
    header("Location: login");
    exit();
}

include("../database/connection.php");

$role = $_SESSION['role'] ?? '';

// -------------------------
// 0️⃣ Filters setup
// -------------------------
$validMeals = ['Vegetarian', 'Non-Vegetarian'];
$programFilter = $_GET['program'] ?? '';
$mealFilters = [
    'student_meals'  => $_GET['student_meal'] ?? '',
    'guest_meals'    => $_GET['guest_meal'] ?? '',
    'guest_meals_02' => $_GET['guest_meal_02'] ?? '',
];

$whereClauses = [];
if ($programFilter !== '') {
    $whereClauses[] = "program_name = '" . $conn->real_escape_string($programFilter) . "'";
}
foreach ($mealFilters as $col => $val) {
    if (in_array($val, $validMeals, true)) {
        $whereClauses[] = "$col = '" . $conn->real_escape_string($val) . "'";
    } else {
        $mealFilters[$col] = '';
    }
}
$globalCondition = $whereClauses ? implode(' AND ', $whereClauses) : '1=1';

// -------------------------
// 1️⃣ Meal counts: Student, Guest 01 (guest_meals), Guest 02 (guest_meals_02)
// -------------------------
$groups = ['student_meals' => 'Student', 'guest_meals' => 'Guest 01', 'guest_meals_02' => 'Guest 02'];
$counts = [];
foreach ($groups as $col => $label) {
    $res = $conn->query("
        SELECT COUNT(*) AS total,
               COALESCE(SUM(`$col` = 'Vegetarian'), 0) AS veg,
               COALESCE(SUM(`$col` = 'Non-Vegetarian'), 0) AS nonveg
        FROM registered_students
        WHERE `$col` IS NOT NULL AND `$col` != '' AND $globalCondition
    ");
    $counts[$col] = $res ? array_map('intval', $res->fetch_assoc()) : ['total' => 0, 'veg' => 0, 'nonveg' => 0];
}

// -------------------------
// 2️⃣ Combined totals
// -------------------------
$grandTotalMeals = array_sum(array_column($counts, 'total'));
$totalVeg = array_sum(array_column($counts, 'veg'));
$totalNonVeg = array_sum(array_column($counts, 'nonveg'));
$vegPct = $grandTotalMeals ? round($totalVeg / $grandTotalMeals * 100) : 0;
$nonVegPct = $grandTotalMeals ? 100 - $vegPct : 0;

// -------------------------
// 3️⃣ Detailed report
// -------------------------
$mealPresent = "(student_meals IS NOT NULL AND student_meals != '')
    OR (guest_meals IS NOT NULL AND guest_meals != '')
    OR (guest_meals_02 IS NOT NULL AND guest_meals_02 != '')";
$detailsCondition = "($mealPresent)" . ($whereClauses ? ' AND ' . implode(' AND ', $whereClauses) : '');

$detailsQuery = "
    SELECT student_id, name_in_full, program_name, student_meals, guest_meals, guest_meals_02
    FROM registered_students
    WHERE $detailsCondition
    ORDER BY created_at DESC
";

function mealPill($v)
{
    if ($v === 'Vegetarian') return '<span class="mr-pill v">Vegetarian</span>';
    if ($v === 'Non-Vegetarian') return '<span class="mr-pill n">Non-Vegetarian</span>';
    return '<span class="mr-none">-</span>';
}

// -------------------------
// 5️⃣ Export to CSV Logic
// -------------------------
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $filename = "Meals_Report_" . date('Ymd') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    // Add BOM to fix UTF-8 in Excel
    fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

    // --- Export Summary Info First ---
    fputcsv($output, ['Summary Report']);
    fputcsv($output, []);
    foreach ($groups as $col => $label) {
        fputcsv($output, [$label . ' Meals', 'Count']);
        fputcsv($output, ['Vegetarian', $counts[$col]['veg']]);
        fputcsv($output, ['Non-Vegetarian', $counts[$col]['nonveg']]);
        fputcsv($output, ['Total ' . $label . ' Meals', $counts[$col]['total']]);
        fputcsv($output, []);
    }
    fputcsv($output, ['Overall Vegetarian', $totalVeg]);
    fputcsv($output, ['Overall Non-Vegetarian', $totalNonVeg]);
    fputcsv($output, ['Overall Combined Total', $grandTotalMeals]);
    fputcsv($output, []);
    fputcsv($output, ['---------------------------------------------------']);
    fputcsv($output, []);

    // --- Detailed List ---
    fputcsv($output, ['Student ID', 'Student Name', 'Program Name', 'Student Meal', 'Guest 01 Meal', 'Guest 02 Meal']);

    $exportResult = $conn->query($detailsQuery);
    if ($exportResult && $exportResult->num_rows > 0) {
        while ($row = $exportResult->fetch_assoc()) {
            fputcsv($output, [
                $row['student_id'],
                $row['name_in_full'],
                $row['program_name'],
                $row['student_meals'] ?: '-',
                $row['guest_meals'] ?: '-',
                $row['guest_meals_02'] ?: '-'
            ]);
        }
    }
    fclose($output);
    exit();
}

$detailsResult = $conn->query($detailsQuery);

// Fetch programs for filter dropdown
$programsQuery = "SELECT DISTINCT program_name FROM registered_students WHERE program_name IS NOT NULL AND program_name != ''";
$programsResult = $conn->query($programsQuery);
$programsList = [];
if ($programsResult) {
    while ($row = $programsResult->fetch_assoc()) {
        $programsList[] = $row['program_name'];
    }
}

include("includes/header.php");
?>

<script>
    // Automatically clear filters if the user uses the browser's native Refresh (F5) button
    if (window.performance) {
        const navEntries = performance.getEntriesByType("navigation");
        if (navEntries.length > 0 && navEntries[0].type === "reload") {
            if (window.location.search && !window.location.search.includes('export=csv')) {
                window.location.href = window.location.pathname;
            }
        } else if (performance.navigation && performance.navigation.type === 1) {
            if (window.location.search && !window.location.search.includes('export=csv')) {
                window.location.href = window.location.pathname;
            }
        }
    }
</script>

<style>
    body.dashboard-page {
        overflow-x: hidden;
    }

    #sidebarToggleMobile {
        border-radius: 12px;
        font-weight: 600;
    }

    #sidebarOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease;
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
            transition: transform 0.3s ease;
            z-index: 1040;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        body.dashboard-page.sidebar-open #accordionSidebar {
            transform: translateX(0);
        }

        body.dashboard-page #content-wrapper {
            margin-left: 0 !important;
        }
    }

    @media (max-width: 576px) {
        .card {
            border-radius: 12px;
        }

        .table-responsive {
            overflow-x: auto;
        }
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,600&family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    .mr {
        --ink: #17233d;
        --muted: #5f6b7e;
        --line: #e2e6ee;
        --brand: #1f4bb6;
        --brand-d: #173a91;
        --veg: #2f9e66;
        --veg-bg: #e6f4ec;
        --veg-ink: #17663f;
        --non: #d0453c;
        --non-bg: #fdecea;
        --non-ink: #9c2a22;
        font-family: 'Public Sans', system-ui, sans-serif;
        color: var(--ink);
        max-width: 1240px;
        padding-bottom: 40px;
    }

    .mr * {
        box-sizing: border-box;
    }

    .mr-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .mr-head h1 {
        font-family: 'Newsreader', Georgia, serif;
        font-weight: 600;
        font-size: 2.1rem;
        letter-spacing: -.02em;
        margin: 0 0 4px;
    }

    .mr-head p {
        margin: 0;
        color: var(--muted);
    }

    .mr-actions {
        display: flex;
        gap: 10px;
    }

    .mr-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 40px;
        padding: 0 16px;
        font: inherit;
        font-weight: 600;
        font-size: .92rem;
        color: var(--ink);
        background: #fff;
        border: 1px solid #c7cfdc;
        border-radius: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: background .15s, transform .1s;
    }

    .mr-btn:hover {
        background: #f1f4f9;
        color: var(--ink);
    }

    .mr-btn:active {
        transform: scale(.98);
    }

    .mr-btn:focus-visible,
    .mr select:focus-visible {
        outline: 3px solid rgba(31, 75, 182, .35);
        outline-offset: 2px;
    }

    .mr-solid {
        background: var(--brand);
        border-color: var(--brand);
        color: #fff;
    }

    .mr-solid:hover {
        background: var(--brand-d);
        color: #fff;
    }

    .mr-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 12px;
    }

    .mr-summary {
        display: grid;
        grid-template-columns: 5fr 7fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .mr-total {
        padding: 24px 28px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .mr-k {
        color: var(--muted);
        font-weight: 500;
    }

    .mr-big {
        font-size: 4.2rem;
        line-height: 1;
        font-weight: 600;
        letter-spacing: -.03em;
        font-variant-numeric: tabular-nums;
        margin: 6px 0 20px;
    }

    .mr-bar {
        display: flex;
        height: 12px;
        border-radius: 6px;
        overflow: hidden;
        background: #eceff5;
    }

    .mr-bar i.v,
    .dot.v {
        background: var(--veg);
    }

    .mr-bar i.n,
    .dot.n {
        background: var(--non);
    }

    .mr-bar.mini {
        height: 6px;
        margin-top: 8px;
        max-width: 220px;
    }

    .mr-legend {
        list-style: none;
        margin: 16px 0 0;
        padding: 0;
        display: grid;
        gap: 8px;
    }

    .mr-legend li {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .mr-legend strong {
        margin-left: auto;
        font-variant-numeric: tabular-nums;
    }

    .mr-legend span {
        color: var(--muted);
        width: 44px;
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        display: inline-block;
    }

    .mr-break {
        padding: 8px 24px;
        overflow-x: auto;
    }

    .mr-break table {
        width: 100%;
        border-collapse: collapse;
    }

    .mr-break th {
        padding: 14px 8px 10px;
        font-size: .82rem;
        font-weight: 600;
        color: var(--muted);
        border-bottom: 1px solid var(--line);
        text-align: right;
    }

    .mr-break th:first-child,
    .mr-break td:first-child {
        text-align: left;
        padding-left: 0;
    }

    .mr-break td {
        padding: 16px 8px;
        border-bottom: 1px solid var(--line);
        vertical-align: middle;
    }

    .mr-break .num {
        text-align: right;
        font-size: 1.35rem;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }

    .mr-break .num.v {
        color: var(--veg-ink);
    }

    .mr-break .num.n {
        color: var(--non-ink);
    }

    .mr-name {
        font-weight: 600;
    }

    .mr-break tfoot td {
        border-bottom: 0;
        font-weight: 600;
        background: #f7f8fb;
    }

    .mr-break tfoot td:first-child {
        padding-left: 8px;
    }

    .mr-filter {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 14px;
        align-items: end;
        padding: 18px 20px;
        margin-bottom: 20px;
    }

    .mr-f label {
        display: block;
        margin-bottom: 6px;
        font-size: .84rem;
        font-weight: 600;
        color: var(--muted);
    }

    .mr select {
        width: 100%;
        height: 42px;
        padding: 0 12px;
        font: inherit;
        color: var(--ink);
        background: #fff;
        border: 1px solid #c4ccd9;
        border-radius: 8px;
    }

    .mr select:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 75, 182, .18);
        outline: 0;
    }

    .mr-f.btns {
        display: flex;
        gap: 8px;
    }

    .mr-f.btns .mr-btn {
        height: 42px;
    }

    .mr-list {
        padding: 4px 0 8px;
    }

    .mr-list-head {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: 18px 24px 8px;
    }

    .mr-list-head h2 {
        font-family: 'Newsreader', Georgia, serif;
        font-weight: 600;
        font-size: 1.4rem;
        margin: 0;
    }

    .mr-list-head span {
        color: var(--muted);
        font-size: .9rem;
    }

    .mr-table {
        margin: 0 !important;
    }

    .mr-table thead th {
        font-size: .8rem;
        font-weight: 600;
        color: var(--muted);
        background: #f7f8fb;
        border-bottom: 1px solid var(--line) !important;
        padding: 12px 16px;
        white-space: nowrap;
    }

    .mr-table td {
        padding: 13px 16px;
        border-color: var(--line);
        vertical-align: middle;
    }

    .mr-table tbody tr:hover {
        background: #f7f9fe;
    }

    .mr-pill {
        display: inline-block;
        padding: 3px 11px;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .mr-pill.v {
        background: var(--veg-bg);
        color: var(--veg-ink);
    }

    .mr-pill.n {
        background: var(--non-bg);
        color: var(--non-ink);
    }

    .mr-none {
        color: #9aa3b5;
    }

    .mr-list .dataTables_wrapper {
        padding: 0 20px 8px;
    }

    @media (max-width: 991px) {
        .mr-summary {
            grid-template-columns: 1fr;
        }

        .mr-filter {
            grid-template-columns: 1fr 1fr;
        }

        .mr-f.wide,
        .mr-f.btns {
            grid-column: 1 / -1;
        }

        .mr-big {
            font-size: 3.4rem;
        }
    }

    @media (max-width: 575px) {
        .mr-head h1 {
            font-size: 1.7rem;
        }

        .mr-actions {
            width: 100%;
        }

        .mr-actions .mr-btn {
            flex: 1;
            justify-content: center;
        }

        .mr-filter {
            grid-template-columns: 1fr;
            padding: 16px;
        }

        .mr-total {
            padding: 20px;
        }

        .mr-break {
            padding: 4px 14px;
        }

        .mr-break .num {
            font-size: 1.1rem;
        }

        .mr-list-head {
            padding: 16px 16px 8px;
        }
    }

    @media print {
        .mr-hide-print {
            display: none !important;
        }

        .mr-card {
            border: 1px solid #cfd5e0;
            break-inside: avoid;
        }

        .mr-summary {
            grid-template-columns: 1fr 1fr;
        }

        .mr-pill {
            border: 1px solid #000;
            color: #000 !important;
            background: transparent !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .mr * {
            transition: none !important;
        }
    }
</style>

<!-- Page Wrapper -->
<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="sidebarOverlay" class="d-md-none"></div>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <div class="container-fluid mt-4">
                <button id="sidebarToggleMobile"
                    class="btn btn-light border d-inline-flex align-items-center gap-2 d-lg-none mb-3 shadow-sm">
                    <i class="fas fa-bars"></i>
                    <span class="fw-semibold">Menu</span>
                </button>
                <div class="mr">
                    <div class="mr-head">
                        <div>
                            <h1>Meals report</h1>
                            <p>Meal choices of registered students and their two guests.</p>
                        </div>
                        <div class="mr-actions mr-hide-print">
                            <a class="mr-btn" href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'csv'])); ?>"><i class="fas fa-file-csv"></i> Export CSV</a>
                            <button type="button" class="mr-btn mr-solid" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                        </div>
                    </div>

                    <section class="mr-summary">
                        <div class="mr-card mr-total">
                            <span class="mr-k">Total meals</span>
                            <strong class="mr-big"><?php echo $grandTotalMeals; ?></strong>
                            <div class="mr-bar" role="img" aria-label="<?php echo $vegPct; ?>% vegetarian, <?php echo $nonVegPct; ?>% non-vegetarian">
                                <i class="v" style="width:<?php echo $vegPct; ?>%"></i><i class="n" style="width:<?php echo $nonVegPct; ?>%"></i>
                            </div>
                            <ul class="mr-legend">
                                <li><b class="dot v"></b>Vegetarian <strong><?php echo $totalVeg; ?></strong> <span><?php echo $vegPct; ?>%</span></li>
                                <li><b class="dot n"></b>Non-Vegetarian <strong><?php echo $totalNonVeg; ?></strong> <span><?php echo $nonVegPct; ?>%</span></li>
                            </ul>
                        </div>

                        <div class="mr-card mr-break">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Meals for</th>
                                        <th>Total</th>
                                        <th>Vegetarian</th>
                                        <th>Non-Vegetarian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($groups as $col => $label):
                                        $c = $counts[$col];
                                        $vp = $c['total'] ? round($c['veg'] / $c['total'] * 100) : 0;
                                        $np = $c['total'] ? 100 - $vp : 0; ?>
                                        <tr>
                                            <td>
                                                <span class="mr-name"><?php echo $label; ?></span>
                                                <div class="mr-bar mini"><i class="v" style="width:<?php echo $vp; ?>%"></i><i class="n" style="width:<?php echo $np; ?>%"></i></div>
                                            </td>
                                            <td class="num"><?php echo $c['total']; ?></td>
                                            <td class="num v"><?php echo $c['veg']; ?></td>
                                            <td class="num n"><?php echo $c['nonveg']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>All meals</td>
                                        <td class="num"><?php echo $grandTotalMeals; ?></td>
                                        <td class="num v"><?php echo $totalVeg; ?></td>
                                        <td class="num n"><?php echo $totalNonVeg; ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </section>

                    <form method="GET" action="meals_report.php#detailedReportSection" class="mr-card mr-filter mr-hide-print">
                        <div class="mr-f wide">
                            <label for="programFilter">Program</label>
                            <select id="programFilter" name="program">
                                <option value="">All programs</option>
                                <?php foreach ($programsList as $prog): ?>
                                    <option value="<?php echo htmlspecialchars($prog); ?>" <?php echo $programFilter === $prog ? 'selected' : ''; ?>><?php echo htmlspecialchars($prog); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php
                        $mealSelects = [
                            'student_meal'  => ['Student meal', $mealFilters['student_meals']],
                            'guest_meal'    => ['Guest 01 meal', $mealFilters['guest_meals']],
                            'guest_meal_02' => ['Guest 02 meal', $mealFilters['guest_meals_02']],
                        ];
                        foreach ($mealSelects as $name => [$label, $current]): ?>
                            <div class="mr-f">
                                <label for="f_<?php echo $name; ?>"><?php echo $label; ?></label>
                                <select id="f_<?php echo $name; ?>" name="<?php echo $name; ?>">
                                    <option value="">All</option>
                                    <?php foreach ($validMeals as $m): ?>
                                        <option value="<?php echo $m; ?>" <?php echo $current === $m ? 'selected' : ''; ?>><?php echo $m; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>
                        <div class="mr-f btns">
                            <button type="submit" class="mr-btn mr-solid">Apply</button>
                            <a href="meals_report.php" class="mr-btn">Clear</a>
                        </div>
                    </form>

                    <section class="mr-card mr-list" id="detailedReportSection">
                        <div class="mr-list-head">
                            <h2>Detailed list</h2>
                            <span><?php echo $detailsResult ? $detailsResult->num_rows : 0; ?> students</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mr-table" id="mealsTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID</th>
                                        <th>Student name</th>
                                        <th>Program</th>
                                        <th>Student meal</th>
                                        <th>Guest 01 meal</th>
                                        <th>Guest 02 meal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($detailsResult && $detailsResult->num_rows > 0):
                                        $index = 1;
                                        while ($row = $detailsResult->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $index++; ?></td>
                                                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                                <td><?php echo htmlspecialchars($row['name_in_full']); ?></td>
                                                <td><?php echo htmlspecialchars($row['program_name']); ?></td>
                                                <td><?php echo mealPill($row['student_meals']); ?></td>
                                                <td><?php echo mealPill($row['guest_meals']); ?></td>
                                                <td><?php echo mealPill($row['guest_meals_02']); ?></td>
                                            </tr>
                                        <?php endwhile;
                                    else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No meal preferences found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('body').addClass('dashboard-page');

        const sidebarOverlay = $('#sidebarOverlay');

        $('#sidebarToggleMobile').on('click', function() {
            $('body').toggleClass('sidebar-open');
        });

        sidebarOverlay.on('click', function() {
            $('body').removeClass('sidebar-open');
        });

        // Initialize DataTables if available in index structure
        if ($.fn.DataTable && <?php echo ($detailsResult && $detailsResult->num_rows > 0) ? 'true' : 'false'; ?>) {
            $('#mealsTable').DataTable({
                "pageLength": 25,
                "ordering": false
            });
        }
    });
</script>

<style>
    @media print {

        #sidebarToggleMobile,
        .bg-primary,
        .d-none.d-sm-inline-block {
            display: none !important;
        }

        #accordionSidebar,
        #sidebarOverlay,
        .navbar {
            display: none !important;
        }

        #content-wrapper {
            margin-left: 0 !important;
            width: 100% !important;
        }

        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }

        .badge {
            border: 1px solid #000;
            color: #000 !important;
            background-color: transparent !important;
        }
    }
</style>

</body>

</html>