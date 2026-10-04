<?php
// Stores the clothing items handed over to a student as 'collected' in clothing_collections.
//  - student already has a row  -> update that row (only the items sent are changed)
//  - student has no row yet     -> insert a new row
session_start();
header('Content-Type: application/json; charset=utf-8');
include("../database/connection.php");

// Value written to collect_cloak / collect_slashes / collect_hats for an item that was handed over
const CLOTHING_COLLECTED = 'collected';

function respond(array $data, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($data);
    exit();
}

if (!isset($_SESSION['admin_id'])) {
    respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['studentID'])) {
    respond(['status' => 'error', 'message' => 'Invalid request'], 400);
}

$studentID = trim($_POST['studentID']);
$items     = isset($_POST['items']) && is_array($_POST['items']) ? $_POST['items'] : [];

// Map the labels shown on the scan page (Cloak, Slashes, Hat ...) to the table columns
$val = ['cloak' => null, 'slashes' => null, 'hats' => null];
foreach ($items as $label) {
    $l = strtolower(trim((string)$label));
    if (strpos($l, 'cloak') !== false)     $val['cloak']   = CLOTHING_COLLECTED;
    elseif (strpos($l, 'slash') !== false) $val['slashes'] = CLOTHING_COLLECTED;
    elseif (strpos($l, 'hat') !== false)   $val['hats']    = CLOTHING_COLLECTED;
}

if ($val['cloak'] === null && $val['slashes'] === null && $val['hats'] === null) {
    respond(['status' => 'success', 'action' => 'none', 'message' => 'Nothing to store']);
}

try {
    // Name and programme come from the database, not from the browser
    $stmt = $conn->prepare("SELECT name_in_full, program_name FROM registered_students WHERE student_id = ? LIMIT 1");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $stu = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$stu) {
        respond(['status' => 'error', 'message' => 'Student not found'], 404);
    }

    $name    = (string)$stu['name_in_full'];
    $program = (string)($stu['program_name'] ?? '');

    // student_id is UNIQUE in clothing_collections, so one statement covers insert and update.
    // Items that were not sent keep whatever they already have.
    // (REPLACE is not used: deleting from this table is blocked by a trigger.)
    $sql = "INSERT INTO clothing_collections
                (student_id, student_name, program_name, collect_cloak, collect_slashes, collect_hats)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                student_name    = VALUES(student_name),
                program_name    = VALUES(program_name),
                collect_cloak   = IF(VALUES(collect_cloak)   IS NULL, collect_cloak,   VALUES(collect_cloak)),
                collect_slashes = IF(VALUES(collect_slashes) IS NULL, collect_slashes, VALUES(collect_slashes)),
                collect_hats    = IF(VALUES(collect_hats)    IS NULL, collect_hats,    VALUES(collect_hats)),
                collected_at    = NOW()";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $studentID, $name, $program, $val['cloak'], $val['slashes'], $val['hats']);
    $stmt->execute();
    $affected = $stmt->affected_rows; // 1 = inserted, 2 = updated, 0 = nothing changed
    $stmt->close();

    $action = $affected === 1 ? 'inserted' : ($affected === 2 ? 'updated' : 'unchanged');
    respond(['status' => 'success', 'action' => $action]);
} catch (Throwable $e) {
    error_log("save_collected_clothing.php: " . $e->getMessage());
    respond(['status' => 'error', 'message' => 'Could not save collected clothing'], 500);
}
