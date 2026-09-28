<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");

$sessionFilter  = isset($_GET['session']) ? strtoupper(trim($_GET['session'])) : '';
$returnFilter   = isset($_GET['return_status']) ? trim($_GET['return_status']) : '';
$programFilter  = isset($_GET['program']) ? trim($_GET['program']) : '';

$allowedSessions = ['MORNING', 'EVENING'];
$allowedReturn   = ['issued', 'returned', 'not_returned', 'fully_returned', 'partial_returned'];

if (!in_array($sessionFilter, $allowedSessions)) $sessionFilter = '';
if (!in_array($returnFilter, $allowedReturn))   $returnFilter = '';

$where = [];
if ($sessionFilter !== '') {
    $where[] = "dt.session = '" . mysqli_real_escape_string($conn, $sessionFilter) . "'";
}
if ($programFilter !== '') {
    $where[] = "cc.program_name = '" . mysqli_real_escape_string($conn, $programFilter) . "'";
}
$col_cnt = "(CASE WHEN COALESCE(cc.collect_cloak,'')!='' THEN 1 ELSE 0 END + CASE WHEN COALESCE(cc.collect_slashes,'')!='' THEN 1 ELSE 0 END + CASE WHEN COALESCE(cc.collect_hats,'')!='' THEN 1 ELSE 0 END)";
$ret_cnt = "(CASE WHEN COALESCE(cc.return_cloak,'')!='' THEN 1 ELSE 0 END + CASE WHEN COALESCE(cc.return_slashes,'')!='' THEN 1 ELSE 0 END + CASE WHEN COALESCE(cc.return_hats,'')!='' THEN 1 ELSE 0 END)";
if ($returnFilter === 'issued')         $where[] = "($col_cnt > 0)";
if ($returnFilter === 'returned')       $where[] = "($ret_cnt > 0)";
if ($returnFilter === 'not_returned')   $where[] = "($col_cnt > 0 AND $ret_cnt = 0)";
if ($returnFilter === 'fully_returned') $where[] = "($col_cnt > 0 AND $ret_cnt > 0 AND $col_cnt = $ret_cnt)";
if ($returnFilter === 'partial_returned') $where[] = "($col_cnt > 0 AND $ret_cnt > 0 AND $ret_cnt < $col_cnt)";

$where_sql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

$query = "
    SELECT cc.student_id, cc.student_name, cc.program_name,
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
$result = mysqli_query($conn, $query);

$filename = 'cloak-report-' . date('Y-m-d-His') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$out = fopen('php://output', 'w');

fputcsv($out, ['Student ID', 'Name', 'Program', 'Session', 'Phone', 'Issued Items', 'Issued At', 'Returned Items', 'Cloak', 'Slashes', 'Hats', 'Return Status']);

if ($result && mysqli_num_rows($result) > 0) {
    while ($c = mysqli_fetch_assoc($result)) {
        $collected_items = [];
        if (!empty($c['collect_cloak'])) $collected_items[] = 'Cloak';
        if (!empty($c['collect_slashes'])) $collected_items[] = 'Slashes';
        if (!empty($c['collect_hats'])) $collected_items[] = 'Hats';
        $returned_items = [];
        if (!empty($c['return_cloak'])) $returned_items[] = 'Cloak';
        if (!empty($c['return_slashes'])) $returned_items[] = 'Slashes';
        if (!empty($c['return_hats'])) $returned_items[] = 'Hats';

        $cloak_s   = !empty($c['collect_cloak']) ? (!empty($c['return_cloak']) ? 'Returned' : 'Issued') : 'Not Issued';
        $slashes_s = !empty($c['collect_slashes']) ? (!empty($c['return_slashes']) ? 'Returned' : 'Issued') : 'Not Issued';
        $hats_s    = !empty($c['collect_hats']) ? (!empty($c['return_hats']) ? 'Returned' : 'Issued') : 'Not Issued';
        $collected_count = count($collected_items);
        $returned_count  = count($returned_items);
        $overall = 'N/A';
        if ($collected_count > 0) {
            if ($returned_count === 0) $overall = 'Issued';
            elseif ($returned_count >= $collected_count) $overall = 'Fully Returned';
            else $overall = 'Partial Returned';
        }

        fputcsv($out, [
            $c['student_id'],
            $c['student_name'],
            $c['program_name'],
            $c['session'] ?? '',
            $c['phone_no'] ?? '',
            implode(', ', $collected_items),
            $c['collected_at'],
            implode(', ', $returned_items),
            $cloak_s,
            $slashes_s,
            $hats_s,
            $overall
        ]);
    }
}

fclose($out);
exit;
