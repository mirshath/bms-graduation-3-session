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
$programFilter = $_GET['program'] ?? '';
$studentMealFilter = $_GET['student_meal'] ?? '';
$guestMealFilter = $_GET['guest_meal'] ?? '';

$whereClauses = [];
if (!empty($programFilter)) {
    $whereClauses[] = "program_name = '" . $conn->real_escape_string($programFilter) . "'";
}
if (!empty($studentMealFilter)) {
    $whereClauses[] = "student_meals = '" . $conn->real_escape_string($studentMealFilter) . "'";
}
if (!empty($guestMealFilter)) {
    $whereClauses[] = "guest_meals = '" . $conn->real_escape_string($guestMealFilter) . "'";
}

$globalCondition = "1=1";
if (!empty($whereClauses)) {
    $globalCondition = implode(" AND ", $whereClauses);
}

// -------------------------
// 1️⃣ Student Meals Breakdown
// -------------------------
$studentMealsQuery = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN student_meals = 'Vegetarian' THEN 1 ELSE 0 END) as veg_count,
        SUM(CASE WHEN student_meals = 'Non-Vegetarian' THEN 1 ELSE 0 END) as non_veg_count
    FROM registered_students
    WHERE student_meals IS NOT NULL AND student_meals != '' AND $globalCondition
";
$result = $conn->query($studentMealsQuery);
$studentMealsData = $result ? $result->fetch_assoc() : ['total' => 0, 'veg_count' => 0, 'non_veg_count' => 0];

$totalStudentMeals = $studentMealsData['total'] ?? 0;
$studentVeg = $studentMealsData['veg_count'] ?? 0;
$studentNonVeg = $studentMealsData['non_veg_count'] ?? 0;

// -------------------------
// 2️⃣ Guest Meals Breakdown
// -------------------------
$guestMealsQuery = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN guest_meals = 'Vegetarian' THEN 1 ELSE 0 END) as veg_count,
        SUM(CASE WHEN guest_meals = 'Non-Vegetarian' THEN 1 ELSE 0 END) as non_veg_count
    FROM registered_students
    WHERE guest_meals IS NOT NULL AND guest_meals != '' AND $globalCondition
";
$result = $conn->query($guestMealsQuery);
$guestMealsData = $result ? $result->fetch_assoc() : ['total' => 0, 'veg_count' => 0, 'non_veg_count' => 0];

$totalGuestMeals = $guestMealsData['total'] ?? 0;
$guestVeg = $guestMealsData['veg_count'] ?? 0;
$guestNonVeg = $guestMealsData['non_veg_count'] ?? 0;

// -------------------------
// 3️⃣ Combined Meals Total
// -------------------------
$totalVeg = $studentVeg + $guestVeg;
$totalNonVeg = $studentNonVeg + $guestNonVeg;
$grandTotalMeals = $totalStudentMeals + $totalGuestMeals;

// -------------------------
// 4️⃣ Filter & Detailed Report
// -------------------------
$baseCondition = "((student_meals IS NOT NULL AND student_meals != '') OR (guest_meals IS NOT NULL AND guest_meals != ''))";

if (!empty($whereClauses)) {
    $detailsCondition = $baseCondition . " AND " . implode(" AND ", $whereClauses);
}
else {
    $detailsCondition = $baseCondition;
}

$detailsQuery = "
    SELECT student_id, name_in_full, program_name, student_meals, guest_meals
    FROM registered_students
    WHERE $detailsCondition
    ORDER BY created_at DESC
";

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
    fputcsv($output, ['Student Meals', 'Count']);
    fputcsv($output, ['Vegetarian', $studentVeg]);
    fputcsv($output, ['Non-Vegetarian', $studentNonVeg]);
    fputcsv($output, ['Total Student Meals', $totalStudentMeals]);
    fputcsv($output, []);
    fputcsv($output, ['Guest Meals', 'Count']);
    fputcsv($output, ['Vegetarian', $guestVeg]);
    fputcsv($output, ['Non-Vegetarian', $guestNonVeg]);
    fputcsv($output, ['Total Guest Meals', $totalGuestMeals]);
    fputcsv($output, []);
    fputcsv($output, ['Overall Combined Total', $grandTotalMeals]);
    fputcsv($output, []);
    fputcsv($output, ['---------------------------------------------------']);
    fputcsv($output, []);

    // --- Detailed List Header ---
    fputcsv($output, ['Student ID', 'Student Name', 'Program Name', 'Student Meal', 'Guest Meal']);

    $exportResult = $conn->query($detailsQuery);
    if ($exportResult && $exportResult->num_rows > 0) {
        while ($row = $exportResult->fetch_assoc()) {
            fputcsv($output, [
                $row['student_id'],
                $row['name_in_full'],
                $row['program_name'],
                $row['student_meals'] ? $row['student_meals'] : '-',
                $row['guest_meals'] ? $row['guest_meals'] : '-'
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
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h3 class="mb-0 text-primary fw-bold">Meals Report</h3>
                </div>

                <!-- Summary Cards Section -->
                <div class="row text-center mb-4">
                    
                    <!-- Grand Total Meals Summary -->
                    <div class="col-md-12 mb-4">
                        <div class="card shadow border-left-success py-3">
                            <div class="card-body">
                                <h5 class="text-success fw-bold mb-4">Overall Combined Meals (Student + Guest)</h5>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h6 class="text-dark fw-bold">Grand Total Meals</h6>
                                        <h2 class="fw-bold text-dark"><?php echo $grandTotalMeals; ?></h2>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="text-success fw-bold">Total Vegetarian</h6>
                                        <h2 class="fw-bold text-dark"><?php echo $totalVeg; ?></h2>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="text-danger fw-bold">Total Non-Vegetarian</h6>
                                        <h2 class="fw-bold text-dark"><?php echo $totalNonVeg; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Meals Summary -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow border-left-primary py-3 h-100">
                            <div class="card-body">
                                <h5 class="text-primary fw-bold mb-4">Student Meals</h5>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h6 class="text-dark fw-bold">Total</h6>
                                        <h2 class="fw-bold text-dark"><?php echo $totalStudentMeals; ?></h2>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="text-success fw-bold">Vegetarian</h6>
                                        <h2 class="fw-bold text-dark"><?php echo $studentVeg; ?></h2>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="text-danger fw-bold">Non-Vegetarian</h6>
                                        <h2 class="fw-bold text-dark"><?php echo $studentNonVeg; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Guest Meals Summary -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow border-left-warning py-3 h-100">
                            <div class="card-body">
                                <h5 class="text-warning fw-bold mb-4">Guest Meals</h5>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h6 class="text-dark fw-bold">Total</h6>
                                        <h2 class="fw-bold text-dark"><?php echo $totalGuestMeals; ?></h2>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="text-success fw-bold">Vegetarian</h6>
                                        <h2 class="fw-bold text-dark"><?php echo $guestVeg; ?></h2>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="text-danger fw-bold">Non-Vegetarian</h6>
                                        <h2 class="fw-bold text-dark"><?php echo $guestNonVeg; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Filter Section -->
                <div class="card shadow mb-4 d-print-none">
                    <div class="card-header bg-light py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Filter Records</h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="meals_report.php#detailedReportSection" class="row gx-3 gy-2 align-items-center">
                            <div class="col-sm-4">
                                <label class="visually-hidden" for="programFilter">Program</label>
                                <select class="form-select w-100 p-2 border rounded" id="programFilter" name="program">
                                    <option value="">All Programs...</option>
                                    <?php foreach ($programsList as $prog): ?>
                                        <option value="<?php echo htmlspecialchars($prog); ?>" <?php if ($programFilter === $prog)
        echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($prog); ?>
                                        </option>
                                    <?php
endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="visually-hidden" for="studentMealFilter">Student Meal</label>
                                <select class="form-select w-100 p-2 border rounded" id="studentMealFilter" name="student_meal">
                                    <option value="">Student Meal (All)</option>
                                    <option value="Vegetarian" <?php if ($studentMealFilter === 'Vegetarian')
    echo 'selected'; ?>>Vegetarian</option>
                                    <option value="Non-Vegetarian" <?php if ($studentMealFilter === 'Non-Vegetarian')
    echo 'selected'; ?>>Non-Vegetarian</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="visually-hidden" for="guestMealFilter">Guest Meal</label>
                                <select class="form-select w-100 p-2 border rounded" id="guestMealFilter" name="guest_meal">
                                    <option value="">Guest Meal (All)</option>
                                    <option value="Vegetarian" <?php if ($guestMealFilter === 'Vegetarian')
    echo 'selected'; ?>>Vegetarian</option>
                                    <option value="Non-Vegetarian" <?php if ($guestMealFilter === 'Non-Vegetarian')
    echo 'selected'; ?>>Non-Vegetarian</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary d-block w-100 p-2 px-3">Filter</button>
                            </div>
                            <div class="col-auto">
                                <a href="meals_report.php" class="btn btn-secondary d-block w-100 p-2 px-3">Clear</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Detailed Table Section -->
                <div class="card shadow mb-5 d-print-block" id="detailedReportSection">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Meal Preferences Detailed Report</h5>
                        <div>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'csv'])); ?>" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm me-2 bg-gradient text-white border-0">
                                <i class="fas fa-file-csv fa-sm text-white-50"></i> Export CSV
                            </a>
                            <a href="#" onclick="window.print()" class="d-none d-sm-inline-block btn btn-sm btn-light shadow-sm text-primary fw-bold">
                                <i class="fas fa-download fa-sm text-primary"></i> Print Report
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle" id="mealsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Program Name</th>
                                        <th>Student Meal</th>
                                        <th>Guest Meal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
if ($detailsResult && $detailsResult->num_rows > 0):
    $index = 1;
    while ($row = $detailsResult->fetch_assoc()):
?>
                                        <tr>
                                            <td><?php echo $index++; ?></td>
                                            <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['name_in_full']); ?></td>
                                            <td><?php echo htmlspecialchars($row['program_name']); ?></td>
                                            <td>
                                                <?php
        if ($row['student_meals'] === 'Vegetarian') {
            echo '<span class="badge bg-success">Vegetarian</span>';
        }
        elseif ($row['student_meals'] === 'Non-Vegetarian') {
            echo '<span class="badge bg-danger">Non-Vegetarian</span>';
        }
        else {
            echo '<span class="text-muted">-</span>';
        }
?>
                                            </td>
                                            <td>
                                                <?php
        if ($row['guest_meals'] === 'Vegetarian') {
            echo '<span class="badge bg-success">Vegetarian</span>';
        }
        elseif ($row['guest_meals'] === 'Non-Vegetarian') {
            echo '<span class="badge bg-danger">Non-Vegetarian</span>';
        }
        else {
            echo '<span class="text-muted">-</span>';
        }
?>
                                            </td>
                                        </tr>
                                    <?php
    endwhile;
else:
?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No meal preferences found.</td>
                                        </tr>
                                    <?php
endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
        if ($.fn.DataTable) {
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
