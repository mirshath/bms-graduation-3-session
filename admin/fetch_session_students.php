<?php
session_start();
include("../database/connection.php");

$session_time = $_POST['session_time'] ?? '';
if (empty($session_time)) {
    echo json_encode(['status' => 'error', 'message' => 'No session selected']);
    exit;
}
// $sql = "SELECT student_id, program_name, session_time, seat_no, graduated_status 
//         FROM bulk_data_table 
//         WHERE session_time=? 
//         ORDER BY seat_no ASC";
// $sql = "SELECT 
//             bdt.student_id, 
//             rs.name_in_full, 
//             bdt.program_name, 
//             bdt.session_time, 
//             bdt.seat_no, 
//             bdt.graduated_status
//         FROM bulk_data_table bdt
//         LEFT JOIN registered_students rs 
//             ON rs.student_id = bdt.student_id
//         WHERE bdt.session_time = ?
//         ORDER BY bdt.seat_no ASC";


$sql = "SELECT 
            bdt.student_id,
            bdt.*,
            rs.name_in_full, 
            bdt.program_name, 
            bdt.session_time, 
            bdt.seat_no, 
            bdt.graduated_status
        FROM bulk_data_table bdt
        LEFT JOIN registered_students rs 
            ON rs.student_id = bdt.student_id
        WHERE bdt.session_time = ?
        ORDER BY CAST(SUBSTRING_INDEX(TRIM(bdt.seat_no), ' ', -1) AS UNSIGNED) ASC";


$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $session_time);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
