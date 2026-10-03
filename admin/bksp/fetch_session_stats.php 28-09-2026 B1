<?php
session_start();
include("../database/connection.php");

// Set JSON header
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$response = [
    'status' => 'success',
    'morning' => [
        'paid' => 0,
        'attended' => 0,
        'remaining' => 0
    ],
    'evening' => [
        'paid' => 0,
        'attended' => 0,
        'remaining' => 0
    ]
];

// Morning session students who paid (unique students only)
$sql = "
    SELECT COUNT(DISTINCT rs.student_id) AS total_paid
    FROM registered_students rs
    INNER JOIN data_tables dt ON rs.program_name = dt.programName
    INNER JOIN payment_records pr ON rs.student_id = pr.student_id
    WHERE dt.session = 'MORNING'
";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $response['morning']['paid'] = (int)($row['total_paid'] ?? 0);
}

// Morning session students who attended (unique students only)
$sql = "
    SELECT COUNT(DISTINCT rs.student_id) AS total_attended
    FROM registered_students rs
    INNER JOIN data_tables dt ON rs.program_name = dt.programName
    WHERE dt.session = 'MORNING' AND rs.attend = 'attended'
";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $response['morning']['attended'] = (int)($row['total_attended'] ?? 0);
}

// Morning session students who paid but not attended (unique students only)
$sql = "
    SELECT COUNT(DISTINCT rs.student_id) AS total_not_attended
    FROM registered_students rs
    INNER JOIN data_tables dt ON rs.program_name = dt.programName
    INNER JOIN payment_records pr ON rs.student_id = pr.student_id
    WHERE dt.session = 'MORNING' AND (rs.attend IS NULL OR rs.attend != 'attended')
";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $response['morning']['remaining'] = (int)($row['total_not_attended'] ?? 0);
}

// Evening session students who paid (unique students only)
$sql = "
    SELECT COUNT(DISTINCT rs.student_id) AS total_paid
    FROM registered_students rs
    INNER JOIN data_tables dt ON rs.program_name = dt.programName
    INNER JOIN payment_records pr ON rs.student_id = pr.student_id
    WHERE dt.session = 'EVENING'
";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $response['evening']['paid'] = (int)($row['total_paid'] ?? 0);
}

// Evening session students who attended (unique students only)
$sql = "
    SELECT COUNT(DISTINCT rs.student_id) AS total_attended
    FROM registered_students rs
    INNER JOIN data_tables dt ON rs.program_name = dt.programName
    WHERE dt.session = 'EVENING' AND rs.attend = 'attended'
";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $response['evening']['attended'] = (int)($row['total_attended'] ?? 0);
}

// Evening session students who paid but not attended (unique students only)
$sql = "
    SELECT COUNT(DISTINCT rs.student_id) AS total_not_attended
    FROM registered_students rs
    INNER JOIN data_tables dt ON rs.program_name = dt.programName
    INNER JOIN payment_records pr ON rs.student_id = pr.student_id
    WHERE dt.session = 'EVENING' AND (rs.attend IS NULL OR rs.attend != 'attended')
";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $response['evening']['remaining'] = (int)($row['total_not_attended'] ?? 0);
}

echo json_encode($response);
$conn->close();
?>

