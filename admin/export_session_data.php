<?php
include("../database/connection.php");

// Check if session was posted
if (!isset($_POST['session'])) {
    die("Session not provided.");
}

$session = $_POST['session'];

// ---------------------------
// Build query to export full data
// ---------------------------
$query = "
    SELECT 
        rs.student_id AS 'Student ID',
        rs.name_in_full AS 'Full Name',
        rs.program_name AS 'Program Name',
        dt.session AS 'Session',
        rs.email_address AS 'Email',
        rs.phone_no AS 'Phone',
        rs.graduation_payment_status AS 'Graduation Payment Status',
        pr.receipt_number AS 'Receipt No',
        pr.payment_date AS 'Payment Date',
        pr.graduation_fee AS 'Graduation Fee',
        pr.free_ticket_count AS 'Free Tickets',
        IFNULL(pr.extra_ticket_count, 0) AS 'Initial Extra Tickets',
        IFNULL(etl.log_added_tickets, 0) AS 'Later Added Tickets',
        (IFNULL(pr.extra_ticket_count, 0) + IFNULL(etl.log_added_tickets, 0)) AS 'Total Extra Tickets',
        IFNULL(pr.extra_ticket_fee, 0) AS 'Initial Extra Ticket Fee',
        (IFNULL(pr.total_amount, 0) + IFNULL(etl.log_total_added, 0)) AS 'Total Amount'
    FROM registered_students rs
    LEFT JOIN payment_records pr ON rs.student_id = pr.student_id
    LEFT JOIN data_tables dt ON rs.program_name = dt.programName
    LEFT JOIN (
        SELECT student_id, SUM(added_tickets) as log_added_tickets, SUM(total_added) as log_total_added
        FROM extra_ticket_log
        GROUP BY student_id
    ) etl ON rs.student_id = etl.student_id
    WHERE dt.session = ?
    ORDER BY rs.student_id ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $session);
$stmt->execute();
$result = $stmt->get_result();

// ---------------------------
// CSV export setup
// ---------------------------
$filename = "{$session}_Session_Export_" . date('Y-m-d_H-i-s') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=\"$filename\"");

// Open output stream
$output = fopen('php://output', 'w');
// Add BOM to fix UTF-8 in Excel
fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

// ---------------------------
// Calculate Summaries for Header
// ---------------------------

// Calculate Paid Students Total
$paidQuery = "
    SELECT COUNT(DISTINCT pr.student_id) as total_paid
    FROM registered_students rs
    JOIN payment_records pr ON rs.student_id = pr.student_id
    JOIN data_tables dt ON rs.program_name = dt.programName
    WHERE dt.session = '$session'
";
$paidResult = $conn->query($paidQuery);
$totalPaid = ($paidResult && $row = $paidResult->fetch_assoc()) ? ($row['total_paid'] ?? 0) : 0;

// Calculate Free Tickets Total
$freeQuery = "
    SELECT SUM(pr.free_ticket_count) as total_free
    FROM registered_students rs
    JOIN payment_records pr ON rs.student_id = pr.student_id
    JOIN data_tables dt ON rs.program_name = dt.programName
    WHERE dt.session = '$session'
";
$freeResult = $conn->query($freeQuery);
$totalFree = ($freeResult && $row = $freeResult->fetch_assoc()) ? ($row['total_free'] ?? 0) : 0;

// Calculate Initial Extra Tickets
$extraInitialQuery = "
    SELECT SUM(pr.extra_ticket_count) as total_initial
    FROM registered_students rs
    JOIN payment_records pr ON rs.student_id = pr.student_id
    JOIN data_tables dt ON rs.program_name = dt.programName
    WHERE dt.session = '$session'
";
$extraInitialResult = $conn->query($extraInitialQuery);
$totalInitialExtra = ($extraInitialResult && $row = $extraInitialResult->fetch_assoc()) ? ($row['total_initial'] ?? 0) : 0;

// Calculate Added Tickets
$totalAddedExtra = 0;
if ($session === 'MORNING') {
    $extraAddedQuery = "SELECT SUM(added_tickets) as total_added FROM extra_ticket_log";
    $extraAddedResult = $conn->query($extraAddedQuery);
    $totalAddedExtra = ($extraAddedResult && $row = $extraAddedResult->fetch_assoc()) ? ($row['total_added'] ?? 0) : 0;
}
else {
    $extraAddedQuery = "
        SELECT SUM(etl.added_tickets) as total_added
        FROM extra_ticket_log etl
        JOIN registered_students rs ON etl.student_id = rs.student_id
        JOIN data_tables dt ON rs.program_name = dt.programName
        WHERE dt.session = '$session'
    ";
    $extraAddedResult = $conn->query($extraAddedQuery);
    $totalAddedExtra = ($extraAddedResult && $row = $extraAddedResult->fetch_assoc()) ? ($row['total_added'] ?? 0) : 0;
}

$grandTotalExtra = $totalInitialExtra + $totalAddedExtra;
$totalSummation = $totalPaid + $totalFree + $grandTotalExtra;

// Write Top Summary to CSV
fputcsv($output, ["$session Session - Ticket Summary Report"]);
fputcsv($output, []);
fputcsv($output, ['Total Paid Students:', $totalPaid]);
fputcsv($output, ['Total Free Tickets:', $totalFree]);
fputcsv($output, ['Total Extra Tickets:', "$totalInitialExtra (Initial) + $totalAddedExtra (Added) = $grandTotalExtra"]);
fputcsv($output, []);
fputcsv($output, ['Overall Total (Paid + Free + Extra):', $totalSummation]);
fputcsv($output, []);
fputcsv($output, ['---------------------------------------------------']);
fputcsv($output, []);
fputcsv($output, ['Detailed Student Records']);
fputcsv($output, []);

// Write CSV column headers
$headers = [
    'Student ID', 'Full Name', 'Program Name', 'Session', 'Email', 'Phone',
    'Graduation Payment Status', 'Receipt No', 'Payment Date', 'Graduation Fee',
    'Free Tickets', 'Initial Extra Tickets', 'Later Added Tickets', 'Total Extra Tickets',
    'Initial Extra Ticket Fee', 'Total Amount'
];
fputcsv($output, $headers);

// Write each row to CSV
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

// ---------------------------
// Append Extra Ticket Log Data
// ---------------------------
fputcsv($output, []);
fputcsv($output, ['---------------------------------------------------']);
fputcsv($output, []);
fputcsv($output, ['Detailed Extra Ticket Log Records']);
fputcsv($output, []);

$logHeaders = ['Log ID', 'Student ID', 'Added Tickets', 'Ticket Price', 'Total Added', 'Added By (Admin ID)', 'Added On'];
fputcsv($output, $logHeaders);

// If morning session, show all logs (including unassigned). If evening, only show logs joined to evening students.
if ($session === 'MORNING') {
    $logQuery = "SELECT id, student_id, added_tickets, ticket_price, total_added, added_by, added_on FROM extra_ticket_log";
    $logResult = $conn->query($logQuery);
}
else {
    $logQuery = "
        SELECT etl.id, etl.student_id, etl.added_tickets, etl.ticket_price, etl.total_added, etl.added_by, etl.added_on 
        FROM extra_ticket_log etl
        INNER JOIN registered_students rs ON etl.student_id = rs.student_id
        INNER JOIN data_tables dt ON rs.program_name = dt.programName
        WHERE dt.session = '$session'
    ";
    $logResult = $conn->query($logQuery);
}

if ($logResult && $logResult->num_rows > 0) {
    while ($logRow = $logResult->fetch_assoc()) {
        fputcsv($output, [
            $logRow['id'],
            $logRow['student_id'] ? $logRow['student_id'] : 'Unassigned',
            $logRow['added_tickets'],
            $logRow['ticket_price'],
            $logRow['total_added'],
            $logRow['added_by'],
            $logRow['added_on']
        ]);
    }
}
else {
    fputcsv($output, ['No extra tickets logged for this session.']);
}

fclose($output);
exit;
?>
