<?php
session_start();
include("../database/connection.php");

header('Content-Type: application/json');

if (!isset($_POST['studentID'])) {
    echo json_encode(['status' => 'error', 'message' => 'No student ID provided']);
    exit;
}

$studentID = trim($_POST['studentID']);
if ($studentID === '') {
    echo json_encode(['status' => 'error', 'message' => 'No student ID provided']);
    exit;
}

// The session is not stored in clothing_collections, so it is looked up in
// data_tables using the student's programme name (spaces ignored, active row first).
$stmt = $conn->prepare("
    SELECT cc.*,
           (SELECT dt.session
              FROM data_tables dt
             WHERE TRIM(dt.programName) = TRIM(cc.program_name)
             ORDER BY dt.active DESC, dt.id DESC
             LIMIT 1) AS session
      FROM clothing_collections cc
     WHERE cc.student_id = ?
     LIMIT 1
");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $row['session'] = !empty($row['session']) ? $row['session'] : 'None';
    echo json_encode(['status' => 'success', 'data' => $row]);
} else {
    echo json_encode(['status' => 'not_registered']);
}