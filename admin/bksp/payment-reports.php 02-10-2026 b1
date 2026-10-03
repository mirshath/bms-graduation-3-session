<?php
session_start();

// ✅ Set Colombo/Asia timezone
date_default_timezone_set('Asia/Colombo');

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");

// ✅ EXCEL/CSV EXPORT HANDLER
if (isset($_GET['export']) && $_GET['export'] == 'excel') {

    $report_type = $_GET['report_type'] ?? 'daily';
    $date_from = $_GET['date_from'] ?? date('Y-m-01');
    $date_to = $_GET['date_to'] ?? date('Y-m-d');
    $selected_program = $_GET['program'] ?? 'all';

    // Build WHERE clause with all filters
    $where_conditions = ["DATE(p.payment_date) BETWEEN '$date_from' AND '$date_to'"];

    if ($selected_program != 'all') {
        $where_conditions[] = "p.program_name = '$selected_program'";
    }

    // Amount range filter
    if (isset($_GET['min_amount']) && $_GET['min_amount'] !== '') {
        $where_conditions[] = "p.total_amount >= " . (int) $_GET['min_amount'];
    }
    if (isset($_GET['max_amount']) && $_GET['max_amount'] !== '') {
        $where_conditions[] = "p.total_amount <= " . (int) $_GET['max_amount'];
    }

    // Payment status filter
    if (isset($_GET['payment_status'])) {
        switch ($_GET['payment_status']) {
            case 'with_extra':
                $where_conditions[] = "p.extra_ticket_count > 0";
                break;
            case 'no_extra':
                $where_conditions[] = "p.extra_ticket_count = 0";
                break;
        }
    }

    // Receipt search
    if (isset($_GET['receipt_search']) && !empty($_GET['receipt_search'])) {
        $receipt = mysqli_real_escape_string($conn, $_GET['receipt_search']);
        $where_conditions[] = "p.receipt_number LIKE '%$receipt%'";
    }

    // Student search
    if (isset($_GET['student_search']) && !empty($_GET['student_search'])) {
        $student = mysqli_real_escape_string($conn, $_GET['student_search']);
        $where_conditions[] = "(p.student_id LIKE '%$student%' OR 
                                r.name_in_full LIKE '%$student%' OR 
                                o.name LIKE '%$student%')";
    }

    // Extra tickets range
    if (isset($_GET['extra_tickets']) && $_GET['extra_tickets'] !== 'all') {
        switch ($_GET['extra_tickets']) {
            case '0':
                $where_conditions[] = "p.extra_ticket_count = 0";
                break;
            case '1-2':
                $where_conditions[] = "p.extra_ticket_count BETWEEN 1 AND 2";
                break;
            case '3-5':
                $where_conditions[] = "p.extra_ticket_count BETWEEN 3 AND 5";
                break;
            case '5+':
                $where_conditions[] = "p.extra_ticket_count > 5";
                break;
        }
    }

    $where = "WHERE " . implode(" AND ", $where_conditions);
    $extra_program_condition = ($selected_program != 'all') ? "AND COALESCE(r2.program_name, o2.program) = '$selected_program'" : "";

    // Set headers for CSV download
    $filename = "Payment_Report_" . $report_type . "_" . date('Y-m-d_H-i-s') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add BOM for proper Excel UTF-8 display
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Report Header
    fputcsv($output, ['BMS GRADUATION PAYMENT REPORT']);
    fputcsv($output, ['Period: ' . date('d M Y', strtotime($date_from)) . ' to ' . date('d M Y', strtotime($date_to))]);
    fputcsv($output, ['Generated on: ' . date('d M Y H:i:s')]);
    fputcsv($output, []); // Empty row

    // DAILY REPORT
    if ($report_type == 'daily') {
        $query = "
            SELECT 
                d.payment_day,
                COALESCE(p.payment_count,0) as payment_count,
                (COALESCE(p.total_grad_fee,0) + COALESCE(p.extra_fee,0) + COALESCE(e.total_added,0)) as total_revenue,
                COALESCE(p.total_grad_fee,0) as total_grad_fee,
                (COALESCE(p.extra_fee,0) + COALESCE(e.total_added,0)) as total_extra_fee,
                (COALESCE(p.extra_tickets,0) + COALESCE(e.added_tickets,0)) as total_extra_tickets
            FROM (
                SELECT day as payment_day FROM (
                    SELECT DATE(p.payment_date) as day
                    FROM payment_records p
                    LEFT JOIN registered_students r ON p.student_id = r.student_id
                    LEFT JOIN old_student_db o ON p.student_id = o.student_id
                    $where
                    UNION
                    SELECT DATE(e.added_on) as day
                    FROM extra_ticket_log e
                    LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                    LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                    WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to' $extra_program_condition
                ) x
            ) d
            LEFT JOIN (
                SELECT 
                    DATE(p.payment_date) as payment_day,
                    COUNT(*) as payment_count,
                    SUM(p.total_amount) as total_amount,
                    SUM(p.graduation_fee) as total_grad_fee,
                    SUM(p.extra_ticket_fee) as extra_fee,
                    SUM(p.extra_ticket_count) as extra_tickets
                FROM payment_records p
                LEFT JOIN registered_students r ON p.student_id = r.student_id
                LEFT JOIN old_student_db o ON p.student_id = o.student_id
                $where
                GROUP BY DATE(p.payment_date)
            ) p ON p.payment_day = d.payment_day
            LEFT JOIN (
                SELECT 
                    DATE(e.added_on) as payment_day,
                    SUM(e.total_added) as total_added,
                    SUM(e.added_tickets) as added_tickets
                FROM extra_ticket_log e
                LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to' $extra_program_condition
                GROUP BY DATE(e.added_on)
            ) e ON e.payment_day = d.payment_day
            ORDER BY d.payment_day DESC
        ";

        $result = mysqli_query($conn, $query);

        fputcsv($output, ['Date', 'Day', 'Payment Count', 'Total Revenue (Rs.)', 'Graduation Fee (Rs.)', 'Extra Ticket Fee (Rs.)', 'Extra Tickets', 'Programs']);

        $grand_total = 0;
        $grand_count = 0;
        $grand_grad = 0;
        $grand_extra = 0;
        $grand_tickets = 0;

        while ($data = mysqli_fetch_assoc($result)) {
            $grand_total += $data['total_revenue'];
            $grand_count += $data['payment_count'];
            $grand_grad += $data['total_grad_fee'];
            $grand_extra += $data['total_extra_fee'];
            $grand_tickets += $data['total_extra_tickets'];

            $prog_query = "
                SELECT p.program_name, COUNT(*) as count
                FROM payment_records p
                WHERE DATE(p.payment_date) = '{$data['payment_day']}'
                GROUP BY p.program_name
            ";
            $prog_result = mysqli_query($conn, $prog_query);
            $programs = [];
            while ($p = mysqli_fetch_assoc($prog_result)) {
                $programs[] = $p['program_name'] . " (" . $p['count'] . ")";
            }

            fputcsv($output, [
                date('d-M-Y', strtotime($data['payment_day'])),
                date('l', strtotime($data['payment_day'])),
                $data['payment_count'],
                number_format($data['total_revenue'], 2),
                number_format($data['total_grad_fee'], 2),
                number_format($data['total_extra_fee'], 2),
                $data['total_extra_tickets'],
                implode(', ', $programs)
            ]);
        }

        fputcsv($output, [
            'GRAND TOTAL',
            '',
            $grand_count,
            number_format($grand_total, 2),
            number_format($grand_grad, 2),
            number_format($grand_extra, 2),
            $grand_tickets,
            ''
        ]);
    }

    // MONTHLY REPORT
    elseif ($report_type == 'monthly') {
        $query = "
            SELECT 
                d.payment_month,
                COALESCE(p.payment_count,0) as payment_count,
                (COALESCE(p.total_grad_fee,0) + COALESCE(p.extra_fee,0) + COALESCE(e.total_added,0)) as total_revenue,
                COALESCE(p.total_grad_fee,0) as total_grad_fee,
                (COALESCE(p.extra_fee,0) + COALESCE(e.total_added,0)) as total_extra_fee,
                (COALESCE(p.extra_tickets,0) + COALESCE(e.added_tickets,0)) as total_extra_tickets
            FROM (
                SELECT m as payment_month FROM (
                    SELECT DATE_FORMAT(p.payment_date, '%Y-%m') as m
                    FROM payment_records p
                    LEFT JOIN registered_students r ON p.student_id = r.student_id
                    LEFT JOIN old_student_db o ON p.student_id = o.student_id
                    $where
                    UNION
                    SELECT DATE_FORMAT(e.added_on, '%Y-%m') as m
                    FROM extra_ticket_log e
                    LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                    LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                    WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to' $extra_program_condition
                ) x
            ) d
            LEFT JOIN (
                SELECT 
                    DATE_FORMAT(p.payment_date, '%Y-%m') as payment_month,
                    COUNT(*) as payment_count,
                    SUM(p.total_amount) as total_amount,
                    SUM(p.graduation_fee) as total_grad_fee,
                    SUM(p.extra_ticket_fee) as extra_fee,
                    SUM(p.extra_ticket_count) as extra_tickets
                FROM payment_records p
                LEFT JOIN registered_students r ON p.student_id = r.student_id
                LEFT JOIN old_student_db o ON p.student_id = o.student_id
                $where
                GROUP BY DATE_FORMAT(p.payment_date, '%Y-%m')
            ) p ON p.payment_month = d.payment_month
            LEFT JOIN (
                SELECT 
                    DATE_FORMAT(e.added_on, '%Y-%m') as payment_month,
                    SUM(e.total_added) as total_added,
                    SUM(e.added_tickets) as added_tickets
                FROM extra_ticket_log e
                LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to' $extra_program_condition
                GROUP BY DATE_FORMAT(e.added_on, '%Y-%m')
            ) e ON e.payment_month = d.payment_month
            ORDER BY d.payment_month DESC
        ";

        $result = mysqli_query($conn, $query);

        fputcsv($output, ['Month', 'Payment Count', 'Total Revenue (Rs.)', 'Graduation Fee (Rs.)', 'Extra Ticket Fee (Rs.)', 'Extra Tickets']);

        $grand_total = 0;
        $grand_count = 0;

        while ($data = mysqli_fetch_assoc($result)) {
            $grand_total += $data['total_revenue'];
            $grand_count += $data['payment_count'];

            fputcsv($output, [
                date('F Y', strtotime($data['payment_month'] . '-01')),
                $data['payment_count'],
                number_format($data['total_revenue'], 2),
                number_format($data['total_grad_fee'], 2),
                number_format($data['total_extra_fee'], 2),
                $data['total_extra_tickets']
            ]);
        }

        fputcsv($output, ['TOTAL', $grand_count, number_format($grand_total, 2), '', '', '']);
    }

    // PROGRAM-WISE REPORT
    elseif ($report_type == 'program') {
        $query = "
            SELECT 
                d.program_name,
                COALESCE(p.student_count,0) as student_count,
                (COALESCE(p.total_grad_fee,0) + COALESCE(p.extra_fee,0) + COALESCE(e.total_added,0)) as total_revenue,
                COALESCE(p.total_grad_fee,0) as total_grad_fee,
                (COALESCE(p.extra_fee,0) + COALESCE(e.total_added,0)) as total_extra_fee,
                (COALESCE(p.extra_tickets,0) + COALESCE(e.added_tickets,0)) as total_extra_tickets,
                COALESCE(p.avg_extra_tickets,0) as avg_extra_tickets
            FROM (
                SELECT program_name FROM (
                    SELECT p.program_name
                    FROM payment_records p
                    LEFT JOIN registered_students r ON p.student_id = r.student_id
                    LEFT JOIN old_student_db o ON p.student_id = o.student_id
                    $where
                    GROUP BY p.program_name
                    UNION
                    SELECT COALESCE(r2.program_name, o2.program) AS program_name
                    FROM extra_ticket_log e
                    LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                    LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                    WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                    " . ($selected_program != 'all' ? "AND COALESCE(r2.program_name, o2.program) = '$selected_program'" : "") . "
                    GROUP BY COALESCE(r2.program_name, o2.program)
                ) x
            ) d
            LEFT JOIN (
                SELECT 
                    p.program_name,
                    COUNT(*) as student_count,
                    SUM(p.total_amount) as total_amount,
                    SUM(p.graduation_fee) as total_grad_fee,
                    SUM(p.extra_ticket_fee) as extra_fee,
                    SUM(p.extra_ticket_count) as extra_tickets,
                    AVG(p.extra_ticket_count) as avg_extra_tickets
                FROM payment_records p
                LEFT JOIN registered_students r ON p.student_id = r.student_id
                LEFT JOIN old_student_db o ON p.student_id = o.student_id
                $where
                GROUP BY p.program_name
            ) p ON p.program_name = d.program_name
            LEFT JOIN (
                SELECT 
                    COALESCE(r2.program_name, o2.program) AS program_name,
                    SUM(e.total_added) as total_added,
                    SUM(e.added_tickets) as added_tickets
                FROM extra_ticket_log e
                LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                " . ($selected_program != 'all' ? "AND COALESCE(r2.program_name, o2.program) = '$selected_program'" : "") . "
                GROUP BY COALESCE(r2.program_name, o2.program)
            ) e ON e.program_name = d.program_name
            ORDER BY total_revenue DESC
        ";

        $result = mysqli_query($conn, $query);

        fputcsv($output, ['Program', 'Student Count', 'Total Revenue (Rs.)', 'Graduation Fee (Rs.)', 'Extra Ticket Fee (Rs.)', 'Total Extra Tickets', 'Avg Extra Tickets']);

        $grand_total = 0;
        $grand_students = 0;

        while ($data = mysqli_fetch_assoc($result)) {
            $grand_total += $data['total_revenue'];
            $grand_students += $data['student_count'];

            fputcsv($output, [
                $data['program_name'],
                $data['student_count'],
                number_format($data['total_revenue'], 2),
                number_format($data['total_grad_fee'], 2),
                number_format($data['total_extra_fee'], 2),
                $data['total_extra_tickets'],
                round($data['avg_extra_tickets'], 2)
            ]);
        }

        fputcsv($output, ['TOTAL', $grand_students, number_format($grand_total, 2), '', '', '', '']);
    }

    // OVERALL SUMMARY
    elseif ($report_type == 'overall') {
        $query = "
            SELECT 
                COUNT(*) as total_payments,
                COUNT(DISTINCT p.student_id) as unique_students,
                (
                    SUM(p.total_amount) +
                    (
                        SELECT COALESCE(SUM(e.total_added),0)
                        FROM extra_ticket_log e
                        LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                        LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                        WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                        $extra_program_condition
                    )
                ) as total_revenue,
                SUM(p.graduation_fee) as total_grad_fee,
                (
                    SUM(p.extra_ticket_fee) +
                    (
                        SELECT COALESCE(SUM(e.total_added),0)
                        FROM extra_ticket_log e
                        LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                        LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                        WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                        $extra_program_condition
                    )
                ) as total_extra_fee,
                (
                    SUM(p.extra_ticket_count) +
                    (
                        SELECT COALESCE(SUM(e.added_tickets),0)
                        FROM extra_ticket_log e
                        LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                        LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                        WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                        $extra_program_condition
                    )
                ) as total_extra_tickets,
                AVG(p.total_amount) as avg_payment,
                MAX(p.total_amount) as max_payment,
                MIN(p.total_amount) as min_payment
            FROM payment_records p
            LEFT JOIN registered_students r ON p.student_id = r.student_id
            LEFT JOIN old_student_db o ON p.student_id = o.student_id
            $where
        ";

        $result = mysqli_query($conn, $query);
        $overall = mysqli_fetch_assoc($result);

        fputcsv($output, ['Metric', 'Value']);

        $metrics = [
            'Total Payments' => $overall['total_payments'],
            'Unique Students' => $overall['unique_students'],
            'Total Revenue (Rs.)' => number_format($overall['total_revenue'], 2),
            'Total Graduation Fees (Rs.)' => number_format($overall['total_grad_fee'], 2),
            'Total Extra Ticket Fees (Rs.)' => number_format($overall['total_extra_fee'], 2),
            'Total Extra Tickets Sold' => $overall['total_extra_tickets'],
            'Average Payment (Rs.)' => number_format($overall['avg_payment'], 2),
            'Highest Payment (Rs.)' => number_format($overall['max_payment'], 2),
            'Lowest Payment (Rs.)' => number_format($overall['min_payment'], 2)
        ];

        foreach ($metrics as $metric => $value) {
            fputcsv($output, [$metric, $value]);
        }
    }

    // YEARLY SUMMARY
    elseif ($report_type == 'yearly') {
        $query = "
            SELECT 
                d.payment_year,
                COALESCE(p.payment_count,0) as payment_count,
                (COALESCE(p.total_grad_fee,0) + COALESCE(p.extra_fee,0) + COALESCE(e.total_added,0)) as total_revenue
            FROM (
                SELECT y as payment_year FROM (
                    SELECT YEAR(p.payment_date) as y
                    FROM payment_records p
                    GROUP BY YEAR(p.payment_date)
                    UNION
                    SELECT YEAR(e.added_on) as y
                    FROM extra_ticket_log e
                    GROUP BY YEAR(e.added_on)
                ) x
            ) d
            LEFT JOIN (
                SELECT 
                    YEAR(payment_date) as payment_year,
                    COUNT(*) as payment_count,
                    SUM(graduation_fee) as total_grad_fee,
                    SUM(extra_ticket_fee) as extra_fee
                FROM payment_records
                GROUP BY YEAR(payment_date)
            ) p ON p.payment_year = d.payment_year
            LEFT JOIN (
                SELECT 
                    YEAR(added_on) as payment_year,
                    SUM(total_added) as total_added
                FROM extra_ticket_log
                GROUP BY YEAR(added_on)
            ) e ON e.payment_year = d.payment_year
            ORDER BY d.payment_year DESC
        ";

        $result = mysqli_query($conn, $query);

        fputcsv($output, ['Year', 'Payment Count', 'Total Revenue (Rs.)']);

        while ($data = mysqli_fetch_assoc($result)) {
            fputcsv($output, [
                $data['payment_year'],
                $data['payment_count'],
                number_format($data['total_revenue'], 2)
            ]);
        }
    }

    // DETAILED PAYMENT LIST
    elseif ($report_type == 'detailed') {
        $query = "
            SELECT 
                p.id,
                p.student_id,
                COALESCE(r.name_in_full, o.name, 'N/A') as student_name,
                p.program_name,
                p.graduation_fee,
                p.free_ticket_count,
                (
                    p.extra_ticket_count +
                    (
                        SELECT COALESCE(SUM(e.added_tickets),0)
                        FROM extra_ticket_log e
                        WHERE e.student_id = p.student_id
                          AND DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                    )
                ) as extra_ticket_count,
                (
                    p.extra_ticket_fee +
                    (
                        SELECT COALESCE(SUM(e.total_added),0)
                        FROM extra_ticket_log e
                        WHERE e.student_id = p.student_id
                          AND DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                    )
                ) as extra_ticket_fee,
                p.total_amount,
                p.receipt_number,
                p.payment_date
            FROM payment_records p
            LEFT JOIN registered_students r ON p.student_id = r.student_id
            LEFT JOIN old_student_db o ON p.student_id = o.student_id
            $where
            ORDER BY p.payment_date DESC
        ";

        $result = mysqli_query($conn, $query);

        fputcsv($output, [
            '#',
            'Receipt Number',
            'Student ID',
            'Student Name',
            'Program',
            'Graduation Fee (Rs.)',
            'Free Tickets',
            'Extra Tickets',
            'Extra Ticket Fee (Rs.)',
            'Total Amount (Rs.)',
            'Payment Date',
            'Payment Time'
        ]);

        $counter = 1;
        $grand_total = 0;
        $total_grad_fee = 0;
        $total_extra_fee = 0;
        $total_extra_tickets = 0;

        while ($data = mysqli_fetch_assoc($result)) {
            $grand_total += $data['total_amount'];
            $total_grad_fee += $data['graduation_fee'];
            $total_extra_fee += $data['extra_ticket_fee'];
            $total_extra_tickets += $data['extra_ticket_count'];

            fputcsv($output, [
                $counter++,
                $data['receipt_number'],
                $data['student_id'],
                $data['student_name'],
                $data['program_name'],
                number_format($data['graduation_fee'], 2),
                $data['free_ticket_count'],
                $data['extra_ticket_count'],
                number_format($data['extra_ticket_fee'], 2),
                number_format($data['total_amount'], 2),
                date('d-M-Y', strtotime($data['payment_date'])),
                date('h:i A', strtotime($data['payment_date']))
            ]);
        }

        fputcsv($output, []);
        fputcsv($output, [
            'TOTAL',
            '',
            '',
            mysqli_num_rows($result) . ' Students',
            '',
            number_format($total_grad_fee, 2),
            '',
            $total_extra_tickets,
            number_format($total_extra_fee, 2),
            number_format($grand_total, 2),
            '',
            ''
        ]);
    }

    fclose($output);
    exit();
}

include("includes/header.php");

// Get filter parameters
$report_type = $_GET['report_type'] ?? 'daily';
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-d');
$selected_program = $_GET['program'] ?? 'all';
$min_amount = $_GET['min_amount'] ?? '';
$max_amount = $_GET['max_amount'] ?? '';
$payment_status = $_GET['payment_status'] ?? 'all';
$receipt_search = $_GET['receipt_search'] ?? '';
$student_search = $_GET['student_search'] ?? '';
$extra_tickets = $_GET['extra_tickets'] ?? 'all';
$sort_by = $_GET['sort_by'] ?? 'date_desc';

// Build export URL with all parameters
$export_params = http_build_query([
    'report_type' => $report_type,
    'date_from' => $date_from,
    'date_to' => $date_to,
    'program' => $selected_program,
    'min_amount' => $min_amount,
    'max_amount' => $max_amount,
    'payment_status' => $payment_status,
    'receipt_search' => $receipt_search,
    'student_search' => $student_search,
    'extra_tickets' => $extra_tickets,
    'export' => 'excel'
]);
?>

<!-- Page Wrapper -->
<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <div class="p-3">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h4 class="h4 mb-0 text-gray-800">
                        <i class="fas fa-chart-line"></i> Payment Reports & Analytics
                    </h4>
                    <div>
                        <a href="#" onclick="window.print()" class="btn btn-secondary btn-sm mr-2">
                            <i class="fas fa-print"></i> Print
                        </a>
                        <a href="?<?= $export_params ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                    </div>
                </div>
            </div>

            <div class="container-fluid">

                <!-- Filter Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-filter"></i> Report Filters
                            <button type="button" class="btn btn-sm btn-outline-primary float-right"
                                onclick="toggleAdvancedFilters()">
                                <i class="fas fa-sliders-h"></i> <span id="toggleText">Show Advanced Filters</span>
                            </button>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="" id="filterForm">
                            <!-- Basic Filters Row -->
                            <div class="row">
                                <div class="col-md-3">
                                    <label><i class="fas fa-file-alt"></i> Report Type</label>
                                    <select name="report_type" class="form-control">
                                        <option value="daily" <?= $report_type == 'daily' ? 'selected' : '' ?>>Daily Report
                                        </option>
                                        <option value="monthly" <?= $report_type == 'monthly' ? 'selected' : '' ?>>Monthly
                                            Report</option>
                                        <option value="yearly" <?= $report_type == 'yearly' ? 'selected' : '' ?>>Yearly
                                            Report</option>
                                        <option value="program" <?= $report_type == 'program' ? 'selected' : '' ?>>
                                            Program-wise</option>
                                        <option value="overall" <?= $report_type == 'overall' ? 'selected' : '' ?>>Overall
                                            Summary</option>
                                        <option value="detailed" <?= $report_type == 'detailed' ? 'selected' : '' ?>>
                                            Detailed Payment List</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label><i class="fas fa-clock"></i> Quick Date Range</label>
                                    <select class="form-control" onchange="setQuickDate(this.value)">
                                        <option value="">Custom Range</option>
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="this_week">This Week</option>
                                        <option value="last_week">Last Week</option>
                                        <option value="this_month">This Month</option>
                                        <option value="last_month">Last Month</option>
                                        <option value="last_30_days">Last 30 Days</option>
                                        <option value="this_year">This Year</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label><i class="fas fa-calendar"></i> Date From</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control"
                                        value="<?= $date_from ?>">
                                </div>

                                <div class="col-md-3">
                                    <label><i class="fas fa-calendar"></i> Date To</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control"
                                        value="<?= $date_to ?>">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label><i class="fas fa-graduation-cap"></i> Program</label>
                                    <select name="program" class="form-control">
                                        <option value="all">All Programs</option>
                                        <?php
                                        $prog_query = "SELECT DISTINCT program_name FROM payment_records ORDER BY program_name";
                                        $prog_result = mysqli_query($conn, $prog_query);
                                        while ($prog = mysqli_fetch_assoc($prog_result)) {
                                            $selected = ($selected_program == $prog['program_name']) ? 'selected' : '';
                                            echo "<option value='{$prog['program_name']}' $selected>{$prog['program_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label><i class="fas fa-check-circle"></i> Payment Status</label>
                                    <select name="payment_status" class="form-control">
                                        <option value="all" <?= $payment_status == 'all' ? 'selected' : '' ?>>All Payments
                                        </option>
                                        <option value="with_extra" <?= $payment_status == 'with_extra' ? 'selected' : '' ?>>With Extra Tickets</option>
                                        <option value="no_extra" <?= $payment_status == 'no_extra' ? 'selected' : '' ?>>No
                                            Extra Tickets</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label><i class="fas fa-ticket-alt"></i> Extra Tickets Range</label>
                                    <select name="extra_tickets" class="form-control">
                                        <option value="all" <?= $extra_tickets == 'all' ? 'selected' : '' ?>>All</option>
                                        <option value="0" <?= $extra_tickets == '0' ? 'selected' : '' ?>>0 tickets</option>
                                        <option value="1-2" <?= $extra_tickets == '1-2' ? 'selected' : '' ?>>1-2 tickets
                                        </option>
                                        <option value="3-5" <?= $extra_tickets == '3-5' ? 'selected' : '' ?>>3-5 tickets
                                        </option>
                                        <option value="5+" <?= $extra_tickets == '5+' ? 'selected' : '' ?>>5+ tickets
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Advanced Filters (Initially Hidden) -->
                            <div id="advancedFilters" style="display:none;">
                                <hr class="my-3">
                                <h6 class="text-primary"><i class="fas fa-filter"></i> Advanced Filters</h6>

                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <label><i class="fas fa-money-bill"></i> Min Amount (Rs.)</label>
                                        <input type="number" name="min_amount" class="form-control" placeholder="0"
                                            value="<?= $min_amount ?>" min="0">
                                    </div>

                                    <div class="col-md-3">
                                        <label><i class="fas fa-money-bill-wave"></i> Max Amount (Rs.)</label>
                                        <input type="number" name="max_amount" class="form-control" placeholder="Any"
                                            value="<?= $max_amount ?>" min="0">
                                    </div>

                                    <div class="col-md-3">
                                        <label><i class="fas fa-receipt"></i> Receipt Number</label>
                                        <input type="text" name="receipt_search" class="form-control"
                                            placeholder="Search receipt..."
                                            value="<?= htmlspecialchars($receipt_search) ?>">
                                    </div>

                                    <div class="col-md-3">
                                        <label><i class="fas fa-user-search"></i> Student ID/Name</label>
                                        <input type="text" name="student_search" class="form-control"
                                            placeholder="Search student..."
                                            value="<?= htmlspecialchars($student_search) ?>">
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label><i class="fas fa-sort"></i> Sort By</label>
                                        <select name="sort_by" class="form-control">
                                            <option value="date_desc" <?= $sort_by == 'date_desc' ? 'selected' : '' ?>>
                                                Latest First</option>
                                            <option value="date_asc" <?= $sort_by == 'date_asc' ? 'selected' : '' ?>>Oldest
                                                First</option>
                                            <option value="amount_desc" <?= $sort_by == 'amount_desc' ? 'selected' : '' ?>>
                                                Highest Amount</option>
                                            <option value="amount_asc" <?= $sort_by == 'amount_asc' ? 'selected' : '' ?>>
                                                Lowest Amount</option>
                                            <option value="student_id" <?= $sort_by == 'student_id' ? 'selected' : '' ?>>
                                                Student ID</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Generate Report
                                    </button>
                                    <a href="?" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i> Reset All Filters
                                    </a>

                                    <?php
                                    // Count active filters
                                    $active_filters = 0;
                                    if ($selected_program != 'all')
                                        $active_filters++;
                                    if ($payment_status != 'all')
                                        $active_filters++;
                                    if ($extra_tickets != 'all')
                                        $active_filters++;
                                    if (!empty($min_amount))
                                        $active_filters++;
                                    if (!empty($max_amount))
                                        $active_filters++;
                                    if (!empty($receipt_search))
                                        $active_filters++;
                                    if (!empty($student_search))
                                        $active_filters++;

                                    if ($active_filters > 0) {
                                        echo '<span class="badge badge-info ml-2" style="font-size:14px;"><i class="fas fa-filter"></i> ' . $active_filters . ' Active Filter(s)</span>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php
                // Build WHERE clause with all filters
                $where_conditions = ["DATE(p.payment_date) BETWEEN '$date_from' AND '$date_to'"];

                if ($selected_program != 'all') {
                    $where_conditions[] = "p.program_name = '$selected_program'";
                }

                // Amount range filter
                if (!empty($min_amount)) {
                    $where_conditions[] = "p.total_amount >= " . (int) $min_amount;
                }
                if (!empty($max_amount)) {
                    $where_conditions[] = "p.total_amount <= " . (int) $max_amount;
                }

                // Payment status filter
                if ($payment_status != 'all') {
                    switch ($payment_status) {
                        case 'with_extra':
                            $where_conditions[] = "p.extra_ticket_count > 0";
                            break;
                        case 'no_extra':
                            $where_conditions[] = "p.extra_ticket_count = 0";
                            break;
                    }
                }

                // Receipt search
                if (!empty($receipt_search)) {
                    $receipt = mysqli_real_escape_string($conn, $receipt_search);
                    $where_conditions[] = "p.receipt_number LIKE '%$receipt%'";
                }

                // Student search
                if (!empty($student_search)) {
                    $student = mysqli_real_escape_string($conn, $student_search);
                    $where_conditions[] = "(p.student_id LIKE '%$student%' OR 
                                            r.name_in_full LIKE '%$student%' OR 
                                            o.name LIKE '%$student%')";
                }

                // Extra tickets range
                if ($extra_tickets != 'all') {
                    switch ($extra_tickets) {
                        case '0':
                            $where_conditions[] = "p.extra_ticket_count = 0";
                            break;
                        case '1-2':
                            $where_conditions[] = "p.extra_ticket_count BETWEEN 1 AND 2";
                            break;
                        case '3-5':
                            $where_conditions[] = "p.extra_ticket_count BETWEEN 3 AND 5";
                            break;
                        case '5+':
                            $where_conditions[] = "p.extra_ticket_count > 5";
                            break;
                    }
                }

                $where = "WHERE " . implode(" AND ", $where_conditions);
                $extra_program_condition = ($selected_program != 'all') ? "AND COALESCE(r2.program_name, o2.program) = '$selected_program'" : "";

                // Add sorting
                $order_by = "ORDER BY ";
                switch ($sort_by) {
                    case 'date_asc':
                        $order_by .= "p.payment_date ASC";
                        break;
                    case 'amount_desc':
                        $order_by .= "p.total_amount DESC";
                        break;
                    case 'amount_asc':
                        $order_by .= "p.total_amount ASC";
                        break;
                    case 'student_id':
                        $order_by .= "p.student_id ASC";
                        break;
                    default:
                        $order_by .= "p.payment_date DESC";
                }

                // DAILY REPORT
                if ($report_type == 'daily') {
                    $daily_query = "
                        SELECT 
                            d.payment_day,
                            COALESCE(p.payment_count,0) as payment_count,
                            (COALESCE(p.total_grad_fee,0) + COALESCE(p.extra_fee,0) + COALESCE(e.total_added,0)) as total_revenue,
                            COALESCE(p.total_grad_fee,0) as total_grad_fee,
                            (COALESCE(p.extra_fee,0) + COALESCE(e.total_added,0)) as total_extra_fee,
                            (COALESCE(p.extra_tickets,0) + COALESCE(e.added_tickets,0)) as total_extra_tickets
                        FROM (
                            SELECT day as payment_day FROM (
                                SELECT DATE(p.payment_date) as day
                                FROM payment_records p
                                LEFT JOIN registered_students r ON p.student_id = r.student_id
                                LEFT JOIN old_student_db o ON p.student_id = o.student_id
                                $where
                                UNION
                                SELECT DATE(e.added_on) as day
                                FROM extra_ticket_log e
                                LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                                LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                                WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to' $extra_program_condition
                            ) x
                        ) d
                        LEFT JOIN (
                            SELECT 
                                DATE(p.payment_date) as payment_day,
                                COUNT(*) as payment_count,
                                SUM(p.total_amount) as total_amount,
                                SUM(p.graduation_fee) as total_grad_fee,
                                SUM(p.extra_ticket_fee) as extra_fee,
                                SUM(p.extra_ticket_count) as extra_tickets
                            FROM payment_records p
                            LEFT JOIN registered_students r ON p.student_id = r.student_id
                            LEFT JOIN old_student_db o ON p.student_id = o.student_id
                            $where
                            GROUP BY DATE(p.payment_date)
                        ) p ON p.payment_day = d.payment_day
                        LEFT JOIN (
                            SELECT 
                                DATE(e.added_on) as payment_day,
                                SUM(e.total_added) as total_added,
                                SUM(e.added_tickets) as added_tickets
                            FROM extra_ticket_log e
                            LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                            LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                            WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to' $extra_program_condition
                            GROUP BY DATE(e.added_on)
                        ) e ON e.payment_day = d.payment_day
                        ORDER BY d.payment_day DESC
                    ";
                    $daily_result = mysqli_query($conn, $daily_query);
                    ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">📅 Daily Payment Report</h6>
                        </div>
                        <div class="card-body">
                            <?php
                            $total_records = mysqli_num_rows($daily_result);
                            if ($total_records > 0) {
                                // Calculate quick stats
                                $quick_stats_query = "
                                    SELECT 
                                        COUNT(*) as payment_count,
                                        (
                                            SUM(p.total_amount) +
                                            (
                                                SELECT COALESCE(SUM(e.total_added),0)
                                                FROM extra_ticket_log e
                                                LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                                                LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                                                WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                                                $extra_program_condition
                                            )
                                        ) as total_revenue
                                    FROM payment_records p
                                    LEFT JOIN registered_students r ON p.student_id = r.student_id
                                    LEFT JOIN old_student_db o ON p.student_id = o.student_id
                                    $where
                                ";
                                $stats_result = mysqli_query($conn, $quick_stats_query);
                                $stats = mysqli_fetch_assoc($stats_result);
                                ?>

                                <!-- Commmented for Hide here  , Not showing the Extra tickey Total also -->

                                <!-- <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle"></i> <strong>Results:</strong> 
                                    Showing <?= $total_records ?> day(s) with <?= $stats['payment_count'] ?> payments 
                                    totaling <strong>Rs. <?= number_format($stats['total_revenue'], 2) ?></strong>
                                </div> -->
                            <?php } ?>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" style="font-size: 13px;">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Date</th>
                                            <th>Payment Count</th>
                                            <th>Total Revenue (Rs.)</th>
                                            <th>Graduation Fee (Rs.)</th>
                                            <th>Extra Ticket Fee (Rs.)</th>
                                            <th>Extra Tickets Sold</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($total_records == 0) {
                                            echo '<tr><td colspan="6" class="text-center text-muted">No records found for the selected filters</td></tr>';
                                        } else {
                                            $grand_total = 0;
                                            $grand_grad = 0;
                                            $grand_extra = 0;
                                            $grand_tickets = 0;
                                            $grand_count = 0;

                                            while ($row = mysqli_fetch_assoc($daily_result)) {
                                                $grand_total += $row['total_revenue'];
                                                $grand_grad += $row['total_grad_fee'];
                                                $grand_extra += $row['total_extra_fee'];
                                                $grand_tickets += $row['total_extra_tickets'];
                                                $grand_count += $row['payment_count'];
                                                ?>
                                                <tr>
                                                    <td><?= date('d M Y (l)', strtotime($row['payment_day'])) ?></td>
                                                    <td><span class="badge badge-info"><?= $row['payment_count'] ?></span></td>
                                                    <td><strong>Rs. <?= number_format($row['total_revenue'], 2) ?></strong></td>
                                                    <td>Rs. <?= number_format($row['total_grad_fee'], 2) ?></td>
                                                    <td>Rs. <?= number_format($row['total_extra_fee'], 2) ?></td>
                                                    <td><?= $row['total_extra_tickets'] ?></td>
                                                </tr>

                                                <?php
                                                // Program breakdown
                                                $prog_day_query = "
                                                    SELECT program_name, SUM(cnt) AS count
                                                    FROM (
                                                        SELECT p.program_name AS program_name, COUNT(*) AS cnt
                                                        FROM payment_records p
                                                        WHERE DATE(p.payment_date) = '{$row['payment_day']}'
                                                        GROUP BY p.program_name
                                                        UNION ALL
                                                        SELECT COALESCE(r2.program_name, o2.program) AS program_name, COUNT(*) AS cnt
                                                        FROM extra_ticket_log e
                                                        LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                                                        LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                                                        WHERE DATE(e.added_on) = '{$row['payment_day']}'
                                                        GROUP BY COALESCE(r2.program_name, o2.program)
                                                    ) t
                                                    GROUP BY program_name
                                                ";
                                                $prog_day_result = mysqli_query($conn, $prog_day_query);
                                                ?>
                                                <tr class="bg-light">
                                                    <td colspan="6" style="padding-left: 40px;">
                                                        <small><strong>Programs:</strong>
                                                            <?php
                                                            $programs = [];
                                                            while ($p = mysqli_fetch_assoc($prog_day_result)) {
                                                                $programs[] = $p['program_name'] . " (" . $p['count'] . ")";
                                                            }
                                                            echo implode(', ', $programs);
                                                            ?>
                                                        </small>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr class="table-success font-weight-bold">
                                                <td>TOTAL</td>
                                                <td><span class="badge badge-success"><?= $grand_count ?></span></td>
                                                <td><strong>Rs. <?= number_format($grand_total, 2) ?></strong></td>
                                                <td>Rs. <?= number_format($grand_grad, 2) ?></td>
                                                <td>Rs. <?= number_format($grand_extra, 2) ?></td>
                                                <td><?= $grand_tickets ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                }

                // MONTHLY REPORT
                if ($report_type == 'monthly') {
                    $monthly_query = "
                        SELECT 
                            d.payment_month,
                            COALESCE(p.payment_count,0) as payment_count,
                            (COALESCE(p.total_grad_fee,0) + COALESCE(p.extra_fee,0) + COALESCE(e.total_added,0)) as total_revenue,
                            COALESCE(p.total_grad_fee,0) as total_grad_fee,
                            (COALESCE(p.extra_fee,0) + COALESCE(e.total_added,0)) as total_extra_fee,
                            (COALESCE(p.extra_tickets,0) + COALESCE(e.added_tickets,0)) as total_extra_tickets
                        FROM (
                            SELECT m as payment_month FROM (
                                SELECT DATE_FORMAT(p.payment_date, '%Y-%m') as m
                                FROM payment_records p
                                LEFT JOIN registered_students r ON p.student_id = r.student_id
                                LEFT JOIN old_student_db o ON p.student_id = o.student_id
                                $where
                                UNION
                                SELECT DATE_FORMAT(e.added_on, '%Y-%m') as m
                                FROM extra_ticket_log e
                                LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                                LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                                WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to' $extra_program_condition
                            ) x
                        ) d
                        LEFT JOIN (
                            SELECT 
                                DATE_FORMAT(p.payment_date, '%Y-%m') as payment_month,
                                COUNT(*) as payment_count,
                                SUM(p.total_amount) as total_amount,
                                SUM(p.graduation_fee) as total_grad_fee,
                                SUM(p.extra_ticket_fee) as extra_fee,
                                SUM(p.extra_ticket_count) as extra_tickets
                            FROM payment_records p
                            LEFT JOIN registered_students r ON p.student_id = r.student_id
                            LEFT JOIN old_student_db o ON p.student_id = o.student_id
                            $where
                            GROUP BY DATE_FORMAT(p.payment_date, '%Y-%m')
                        ) p ON p.payment_month = d.payment_month
                        LEFT JOIN (
                            SELECT 
                                DATE_FORMAT(e.added_on, '%Y-%m') as payment_month,
                                SUM(e.total_added) as total_added,
                                SUM(e.added_tickets) as added_tickets
                            FROM extra_ticket_log e
                            LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                            LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                            WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to' $extra_program_condition
                            GROUP BY DATE_FORMAT(e.added_on, '%Y-%m')
                        ) e ON e.payment_month = d.payment_month
                        ORDER BY d.payment_month DESC
                    ";
                    $monthly_result = mysqli_query($conn, $monthly_query);
                    ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-success text-white">
                            <h6 class="m-0 font-weight-bold">📆 Monthly Payment Report</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" style="font-size: 13px;">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Month</th>
                                            <th>Payment Count</th>
                                            <th>Total Revenue (Rs.)</th>
                                            <th>Graduation Fee (Rs.)</th>
                                            <th>Extra Ticket Fee (Rs.)</th>
                                            <th>Extra Tickets Sold</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (mysqli_num_rows($monthly_result) == 0) {
                                            echo '<tr><td colspan="6" class="text-center text-muted">No records found for the selected filters</td></tr>';
                                        } else {
                                            $grand_total = 0;
                                            $grand_count = 0;
                                            while ($row = mysqli_fetch_assoc($monthly_result)) {
                                                $grand_total += $row['total_revenue'];
                                                $grand_count += $row['payment_count'];
                                                ?>
                                                <tr>
                                                    <td><?= date('F Y', strtotime($row['payment_month'] . '-01')) ?></td>
                                                    <td><span class="badge badge-info"><?= $row['payment_count'] ?></span></td>
                                                    <td><strong>Rs. <?= number_format($row['total_revenue'], 2) ?></strong></td>
                                                    <td>Rs. <?= number_format($row['total_grad_fee'], 2) ?></td>
                                                    <td>Rs. <?= number_format($row['total_extra_fee'], 2) ?></td>
                                                    <td><?= $row['total_extra_tickets'] ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr class="table-success font-weight-bold">
                                                <td>TOTAL</td>
                                                <td><span class="badge badge-success"><?= $grand_count ?></span></td>
                                                <td colspan="4"><strong>Rs. <?= number_format($grand_total, 2) ?></strong></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                }

                // PROGRAM-WISE REPORT
                if ($report_type == 'program') {
                    $program_query = "
                        SELECT 
                            p.program_name,
                            COUNT(*) as student_count,
                            (
                                SUM(p.graduation_fee) +
                                (
                                    SUM(p.extra_ticket_fee) +
                                    (
                                        SELECT COALESCE(SUM(e.total_added),0)
                                        FROM extra_ticket_log e
                                        LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                                        LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                                        WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                                          AND COALESCE(r2.program_name, o2.program) = p.program_name
                                    )
                                )
                            ) as total_revenue,
                            SUM(p.graduation_fee) as total_grad_fee,
                            (
                                SUM(p.extra_ticket_fee) +
                                (
                                    SELECT COALESCE(SUM(e.total_added),0)
                                    FROM extra_ticket_log e
                                    LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                                    LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                                    WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                                      AND COALESCE(r2.program_name, o2.program) = p.program_name
                                )
                            ) as total_extra_fee,
                            (
                                SUM(p.extra_ticket_count) +
                                (
                                    SELECT COALESCE(SUM(e.added_tickets),0)
                                    FROM extra_ticket_log e
                                    LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                                    LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                                    WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                                      AND COALESCE(r2.program_name, o2.program) = p.program_name
                                )
                            ) as total_extra_tickets,
                            AVG(p.extra_ticket_count) as avg_extra_tickets
                        FROM payment_records p
                        LEFT JOIN registered_students r ON p.student_id = r.student_id
                        LEFT JOIN old_student_db o ON p.student_id = o.student_id
                        $where
                        GROUP BY p.program_name
                        ORDER BY total_revenue DESC
                    ";
                    $program_result = mysqli_query($conn, $program_query);
                    ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-info text-white">
                            <h6 class="m-0 font-weight-bold">🎓 Program-wise Payment Report</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" style="font-size: 13px;">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Program</th>
                                            <th>Student Count</th>
                                            <th>Total Revenue (Rs.)</th>
                                            <th>Graduation Fee (Rs.)</th>
                                            <th>Extra Ticket Fee (Rs.)</th>
                                            <th>Total Extra Tickets</th>
                                            <th>Avg Extra Tickets/Student</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (mysqli_num_rows($program_result) == 0) {
                                            echo '<tr><td colspan="7" class="text-center text-muted">No records found for the selected filters</td></tr>';
                                        } else {
                                            $grand_total = 0;
                                            $grand_students = 0;
                                            while ($row = mysqli_fetch_assoc($program_result)) {
                                                $grand_total += $row['total_revenue'];
                                                $grand_students += $row['student_count'];
                                                ?>
                                                <tr>
                                                    <td><strong><?= htmlspecialchars($row['program_name']) ?></strong></td>
                                                    <td><span class="badge badge-primary"><?= $row['student_count'] ?></span></td>
                                                    <td><strong>Rs. <?= number_format($row['total_revenue'], 2) ?></strong></td>
                                                    <td>Rs. <?= number_format($row['total_grad_fee'], 2) ?></td>
                                                    <td>Rs. <?= number_format($row['total_extra_fee'], 2) ?></td>
                                                    <td><?= $row['total_extra_tickets'] ?></td>
                                                    <td><?= number_format($row['avg_extra_tickets'], 1) ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr class="table-success font-weight-bold">
                                                <td>TOTAL</td>
                                                <td><span class="badge badge-success"><?= $grand_students ?></span></td>
                                                <td colspan="5"><strong>Rs. <?= number_format($grand_total, 2) ?></strong></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                }

                // OVERALL SUMMARY
                if ($report_type == 'overall') {
                    $overall_query = "
                        SELECT 
                            COUNT(*) as total_payments,
                            COUNT(DISTINCT p.student_id) as unique_students,
                            (
                                SUM(p.total_amount) +
                                (
                                    SELECT COALESCE(SUM(e.total_added),0)
                                    FROM extra_ticket_log e
                                    LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                                    LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                                    WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                                    $extra_program_condition
                                )
                            ) as total_revenue,
                            SUM(p.graduation_fee) as total_grad_fee,
                            (
                                SUM(p.extra_ticket_fee) +
                                (
                                    SELECT COALESCE(SUM(e.total_added),0)
                                    FROM extra_ticket_log e
                                    LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                                    LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                                    WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                                    $extra_program_condition
                                )
                            ) as total_extra_fee,
                            (
                                SUM(p.extra_ticket_count) +
                                (
                                    SELECT COALESCE(SUM(e.added_tickets),0)
                                    FROM extra_ticket_log e
                                    LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                                    LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                                    WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                                    $extra_program_condition
                                )
                            ) as total_extra_tickets,
                            AVG(p.total_amount) as avg_payment,
                            MAX(p.total_amount) as max_payment,
                            MIN(p.total_amount) as min_payment
                        FROM payment_records p
                        LEFT JOIN registered_students r ON p.student_id = r.student_id
                        LEFT JOIN old_student_db o ON p.student_id = o.student_id
                        $where
                    ";
                    $overall_result = mysqli_query($conn, $overall_query);
                    $overall = mysqli_fetch_assoc($overall_result);
                    ?>
                    <div class="row">
                        <!-- Summary Cards -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total
                                                Revenue</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs.
                                                <?= number_format($overall['total_revenue'], 2) ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total
                                                Payments</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $overall['total_payments'] ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Unique
                                                Students</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $overall['unique_students'] ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Extra
                                                Tickets Sold</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $overall['total_extra_tickets'] ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Breakdown -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-warning text-white">
                            <h6 class="m-0 font-weight-bold">📊 Overall Summary Report</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Total Graduation Fees</th>
                                            <td class="text-right"><strong>Rs.
                                                    <?= number_format($overall['total_grad_fee'], 2) ?></strong></td>
                                        </tr>
                                        <tr>
                                            <th>Total Extra Ticket Fees</th>
                                            <td class="text-right"><strong>Rs.
                                                    <?= number_format($overall['total_extra_fee'], 2) ?></strong></td>
                                        </tr>
                                        <tr class="table-success">
                                            <th>Grand Total Revenue</th>
                                            <td class="text-right"><strong>Rs.
                                                    <?= number_format($overall['total_revenue'], 2) ?></strong></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Average Payment</th>
                                            <td class="text-right">Rs. <?= number_format($overall['avg_payment'], 2) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Highest Payment</th>
                                            <td class="text-right">Rs. <?= number_format($overall['max_payment'], 2) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Lowest Payment</th>
                                            <td class="text-right">Rs. <?= number_format($overall['min_payment'], 2) ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }

                // YEARLY SUMMARY
                if ($report_type == 'yearly') {
                    $yearly_query = "
                        SELECT 
                            YEAR(payment_date) as payment_year,
                            COUNT(*) as payment_count,
                            (
                                SUM(total_amount) +
                                (
                                    SELECT COALESCE(SUM(e.total_added),0)
                                    FROM extra_ticket_log e
                                    WHERE YEAR(e.added_on) = YEAR(payment_date)
                                )
                            ) as total_revenue
                        FROM payment_records
                        GROUP BY YEAR(payment_date)
                        ORDER BY payment_year DESC
                    ";
                    $yearly_result = mysqli_query($conn, $yearly_query);
                    ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-dark text-white">
                            <h6 class="m-0 font-weight-bold">📅 Yearly Payment Report</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Year</th>
                                            <th>Payment Count</th>
                                            <th>Total Revenue (Rs.)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (mysqli_num_rows($yearly_result) == 0) {
                                            echo '<tr><td colspan="3" class="text-center text-muted">No records found</td></tr>';
                                        } else {
                                            while ($row = mysqli_fetch_assoc($yearly_result)) {
                                                ?>
                                                <tr>
                                                    <td><strong><?= $row['payment_year'] ?></strong></td>
                                                    <td><span class="badge badge-info"><?= $row['payment_count'] ?></span></td>
                                                    <td><strong>Rs. <?= number_format($row['total_revenue'], 2) ?></strong></td>
                                                </tr>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                }

                // DETAILED PAYMENT LIST
                if ($report_type == 'detailed') {
                    $detailed_query = "
                        SELECT 
                            p.id,
                            p.student_id,
                            COALESCE(r.name_in_full, o.name, 'N/A') as student_name,
                            p.program_name,
                            p.graduation_fee,
                            p.free_ticket_count,
                            p.extra_ticket_count,
                            p.extra_ticket_fee,
                            p.total_amount,
                            p.receipt_number,
                            p.payment_date
                        FROM payment_records p
                        LEFT JOIN registered_students r ON p.student_id = r.student_id
                        LEFT JOIN old_student_db o ON p.student_id = o.student_id
                        $where
                        $order_by
                    ";
                    $detailed_result = mysqli_query($conn, $detailed_query);
                    ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 text-white"
                            style="background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);">
                            <h6 class="m-0 font-weight-bold">📋 Detailed Payment List - All Students</h6>
                        </div>
                        <div class="card-body">
                            <?php
                            $total_records = mysqli_num_rows($detailed_result);
                            if ($total_records > 0) {
                                // Calculate totals
                                $totals_query = "
                                    SELECT 
                                        (
                                            SUM(p.total_amount) +
                                            (
                                                SELECT COALESCE(SUM(e.total_added),0)
                                                FROM extra_ticket_log e
                                                LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                                                LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                                                WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                                                $extra_program_condition
                                            )
                                        ) as grand_total,
                                        SUM(p.graduation_fee) as total_grad,
                                        (
                                            SUM(p.extra_ticket_fee) +
                                            (
                                                SELECT COALESCE(SUM(e.total_added),0)
                                                FROM extra_ticket_log e
                                                LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                                                LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                                                WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                                                $extra_program_condition
                                            )
                                        ) as total_extra,
                                        (
                                            SUM(p.extra_ticket_count) +
                                            (
                                                SELECT COALESCE(SUM(e.added_tickets),0)
                                                FROM extra_ticket_log e
                                                LEFT JOIN registered_students r2 ON e.student_id = r2.student_id
                                                LEFT JOIN old_student_db o2 ON e.student_id = o2.student_id
                                                WHERE DATE(e.added_on) BETWEEN '$date_from' AND '$date_to'
                                                $extra_program_condition
                                            )
                                        ) as total_tickets
                                    FROM payment_records p
                                    LEFT JOIN registered_students r ON p.student_id = r.student_id
                                    LEFT JOIN old_student_db o ON p.student_id = o.student_id
                                    $where
                                ";
                                $totals_result = mysqli_query($conn, $totals_query);
                                $totals = mysqli_fetch_assoc($totals_result);
                                ?>
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle"></i> <strong>Results:</strong>
                                    Showing <?= $total_records ?> payment record(s)
                                    totaling <strong>Rs. <?= number_format($totals['grand_total'], 2) ?></strong>
                                </div>
                            <?php } ?>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-striped" style="font-size: 12px;">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Receipt No.</th>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Program</th>
                                            <th>Grad Fee (Rs.)</th>
                                            <th>Free Tickets</th>
                                            <th>Extra Tickets</th>
                                            <th>Extra Fee (Rs.)</th>
                                            <th>Total (Rs.)</th>
                                            <th>Payment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($total_records == 0) {
                                            echo '<tr><td colspan="11" class="text-center text-muted">No records found for the selected filters</td></tr>';
                                        } else {
                                            $counter = 1;
                                            $grand_total = 0;
                                            $total_grad_fee = 0;
                                            $total_extra_fee = 0;
                                            $total_extra_tickets = 0;

                                            while ($row = mysqli_fetch_assoc($detailed_result)) {
                                                $grand_total += $row['total_amount'];
                                                $total_grad_fee += $row['graduation_fee'];
                                                $total_extra_fee += $row['extra_ticket_fee'];
                                                $total_extra_tickets += $row['extra_ticket_count'];
                                                ?>
                                                <tr>
                                                    <td><?= $counter++ ?></td>
                                                    <td><strong><?= htmlspecialchars($row['receipt_number']) ?></strong></td>
                                                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                                                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                                                    <td><span
                                                            class="badge badge-primary"><?= htmlspecialchars($row['program_name']) ?></span>
                                                    </td>
                                                    <td class="text-right"><?= number_format($row['graduation_fee'], 2) ?></td>
                                                    <td class="text-center"><?= $row['free_ticket_count'] ?></td>
                                                    <td class="text-center">
                                                        <?php if ($row['extra_ticket_count'] > 0) { ?>
                                                            <span class="badge badge-warning"><?= $row['extra_ticket_count'] ?></span>
                                                        <?php } else { ?>
                                                            0
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-right"><?= number_format($row['extra_ticket_fee'], 2) ?></td>
                                                    <td class="text-right">
                                                        <strong><?= number_format($row['total_amount'], 2) ?></strong></td>
                                                    <td><?= date('d M Y h:i A', strtotime($row['payment_date'])) ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr class="table-success font-weight-bold" style="font-size: 13px;">
                                                <td colspan="4" class="text-right">GRAND TOTAL (<?= $total_records ?> Students)
                                                </td>
                                                <td></td>
                                                <td class="text-right">Rs. <?= number_format($total_grad_fee, 2) ?></td>
                                                <td></td>
                                                <td class="text-center"><?= $total_extra_tickets ?></td>
                                                <td class="text-right">Rs. <?= number_format($total_extra_fee, 2) ?></td>
                                                <td class="text-right"><strong>Rs.
                                                        <?= number_format($grand_total, 2) ?></strong></td>
                                                <td></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>

            </div>
        </div>
    </div>
</div>

<script>
    // Toggle Advanced Filters
    function toggleAdvancedFilters() {
        const advFilters = document.getElementById('advancedFilters');
        const toggleText = document.getElementById('toggleText');

        if (advFilters.style.display === 'none') {
            advFilters.style.display = 'block';
            toggleText.textContent = 'Hide Advanced Filters';
        } else {
            advFilters.style.display = 'none';
            toggleText.textContent = 'Show Advanced Filters';
        }
    }

    // Quick Date Range Selector
    function setQuickDate(period) {
        const today = new Date();
        let from, to;

        switch (period) {
            case 'today':
                from = to = formatDate(today);
                break;

            case 'yesterday':
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                from = to = formatDate(yesterday);
                break;

            case 'this_week':
                const startOfWeek = new Date(today);
                startOfWeek.setDate(today.getDate() - today.getDay());
                from = formatDate(startOfWeek);
                to = formatDate(today);
                break;

            case 'last_week':
                const lastWeekEnd = new Date(today);
                lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
                const lastWeekStart = new Date(lastWeekEnd);
                lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
                from = formatDate(lastWeekStart);
                to = formatDate(lastWeekEnd);
                break;

            case 'this_month':
                from = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
                to = formatDate(today);
                break;

            case 'last_month':
                const lastMonthStart = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
                from = formatDate(lastMonthStart);
                to = formatDate(lastMonthEnd);
                break;

            case 'last_30_days':
                const thirtyDaysAgo = new Date(today);
                thirtyDaysAgo.setDate(today.getDate() - 30);
                from = formatDate(thirtyDaysAgo);
                to = formatDate(today);
                break;

            case 'this_year':
                from = formatDate(new Date(today.getFullYear(), 0, 1));
                to = formatDate(today);
                break;
        }

        if (from && to) {
            document.getElementById('date_from').value = from;
            document.getElementById('date_to').value = to;
        }
    }

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Auto-show advanced filters if any advanced filter is active
    window.onload = function () {
        const minAmount = '<?= $min_amount ?>';
        const maxAmount = '<?= $max_amount ?>';
        const receiptSearch = '<?= $receipt_search ?>';
        const studentSearch = '<?= $student_search ?>';

        if (minAmount || maxAmount || receiptSearch || studentSearch) {
            toggleAdvancedFilters();
        }
    };
</script>

<style>
    @media print {

        .btn,
        .card-header,
        form,
        nav,
        .sidebar,
        #advancedFilters {
            display: none !important;
        }

        .card {
            border: 1px solid #000 !important;
            page-break-inside: avoid;
        }

        body {
            font-size: 11px;
        }

        .table {
            font-size: 10px;
        }
    }

    .badge {
        font-size: 11px;
        padding: 4px 8px;
    }


    .bg-primary {}

    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
    }

    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }

    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }

    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }

    /* Smooth transition for advanced filters */
    #advancedFilters {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
        }

        to {
            opacity: 1;
            max-height: 500px;
        }
    }

    /* Enhanced filter card styling */
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    /* Better button spacing */
    .btn {
        margin-right: 5px;
    }

    /* Responsive table improvements */
    .table-responsive {
        border-radius: 5px;
    }

    /* Alert styling */
    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }

    /* No records message */
    .text-muted {
        font-style: italic;
        padding: 20px 0;
    }

    /* Print optimization */
    @media print {
        @page {
            size: landscape;
            margin: 1cm;
        }

        .no-print {
            display: none !important;
        }

        h4,
        h6 {
            color: #000 !important;
        }
    }
</style>

</body>

</html>