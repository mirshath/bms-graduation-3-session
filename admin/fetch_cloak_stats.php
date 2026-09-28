<?php
session_start();
include("../database/connection.php");

// Only allow if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

// Total Issued (Any student in clothing_collections who has at least one item collected)
$totalIssued = 0;
$sql = "SELECT COUNT(*) as total FROM clothing_collections 
        WHERE (NULLIF(collect_cloak, '') IS NOT NULL 
           OR NULLIF(collect_slashes, '') IS NOT NULL 
           OR NULLIF(collect_hats, '') IS NOT NULL)";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $totalIssued = $row['total'];
}

// Returned (Fully Returned: all collected items are marked as returned)
$totalReturned = 0;
$sql = "SELECT COUNT(*) as total FROM clothing_collections 
        WHERE (NULLIF(collect_cloak, '') IS NOT NULL OR NULLIF(collect_slashes, '') IS NOT NULL OR NULLIF(collect_hats, '') IS NOT NULL)
          AND (NULLIF(collect_cloak, '') IS NULL OR (NULLIF(collect_cloak, '') IS NOT NULL AND NULLIF(return_cloak, '') IS NOT NULL))
          AND (NULLIF(collect_slashes, '') IS NULL OR (NULLIF(collect_slashes, '') IS NOT NULL AND NULLIF(return_slashes, '') IS NOT NULL))
          AND (NULLIF(collect_hats, '') IS NULL OR (NULLIF(collect_hats, '') IS NOT NULL AND NULLIF(return_hats, '') IS NOT NULL))";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $totalReturned = $row['total'];
}

// Remaining (Students who still have at least one item out)
$totalRemaining = 0;
$sql = "SELECT COUNT(*) as total FROM clothing_collections 
        WHERE (NULLIF(collect_cloak, '') IS NOT NULL AND NULLIF(return_cloak, '') IS NULL)
           OR (NULLIF(collect_slashes, '') IS NOT NULL AND NULLIF(return_slashes, '') IS NULL)
           OR (NULLIF(collect_hats, '') IS NOT NULL AND NULLIF(return_hats, '') IS NULL)";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $totalRemaining = $row['total'];
}

echo json_encode([
    "status" => "success",
    "total_issued" => $totalIssued,
    "total_returned" => $totalReturned,
    "total_remaining" => $totalRemaining
]);

$conn->close();
