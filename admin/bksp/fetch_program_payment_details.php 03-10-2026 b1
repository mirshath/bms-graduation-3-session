<?php
include("../database/connection.php");

$program_name = $_POST['program_name'] ?? '';

$response = ['success' => false];

if (!empty($program_name)) {
    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM data_tables WHERE programName = ?");
    $stmt->bind_param("s", $program_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Send entire row as "data"
        $response = [
            'success' => true,
            'data' => $row
        ];
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
