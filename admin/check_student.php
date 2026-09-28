<?php
session_start();
require_once __DIR__ . "/../database/connection.php";

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$studentID = trim($_POST['studentID'] ?? '');
if ($studentID === '') {
    echo json_encode(["status" => "error", "message" => "No ID provided"]);
    exit;
}

try {
    // 1) registered student
    $stmt = $conn->prepare("SELECT * FROM registered_students WHERE student_id = ? LIMIT 1");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $rs = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$rs) {
        echo json_encode(["status" => "error", "message" => "Student not found"]);
        $conn->close();
        exit;
    }

    // 2) seat / session info (may not exist yet)
    $stmt = $conn->prepare("SELECT * FROM bulk_data_table WHERE student_id = ? LIMIT 1");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $bd = $stmt->get_result()->fetch_assoc() ?: [];
    $stmt->close();

    // Merge: bulk_data_table fields added on top, but the core student fields
    // always come from registered_students (a LEFT JOIN with bd.* used to
    // overwrite them with NULL when no seat row existed).
    $row = array_merge($rs, $bd);
    foreach (['student_id', 'name_in_full', 'program_name', 'attend'] as $k) {
        $row[$k] = $rs[$k] ?? null;
    }

    $data = [
        "student_id"                => $row['student_id'],
        "full_name"                 => $row['name_in_full'],
        "program_name"              => $row['program_name'],
        "seat_no"                   => $row['seat_no'] ?? 'N/A',
        "in_no"                     => $row['in_no'] ?? 'N/A',
        "session"                   => $row['session_time'] ?? 'N/A',
        "graduation_payment_status" => $row['graduation_payment_status'] ?? 'N/A',
    ];

    // Same rule the stats use: attend = 'attended'
    if (($row['attend'] ?? '') === 'attended') {
        $data['attend'] = $row['attend'];
        echo json_encode(["status" => "attended", "data" => $data]);
    } else {
        echo json_encode(["status" => "success", "data" => $data]);
    }
} catch (Throwable $e) {
    error_log($e->getMessage());
    echo json_encode(["status" => "error", "message" => "Server error"]);
}

$conn->close();
