<?php

/**
 * Invitation issue button (student has 0 extra tickets).
 * POST: student_id + csrf  ->  JSON {ok, already, by} or {ok:false, error}
 *
 * Updates (in one transaction, via invitation_helper.php):
 *   registered_students : in_no = ?, invitation_collected = 'collected'  (this student only)
 *   payment_records     : issued_by = logged-in admin (latest payment of this student)
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_name'] ?? '') === 'Unknown Admin') {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'unauthorized']);
    exit;
}

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !hash_equals((string)($_SESSION['csrf'] ?? ''), (string)($_POST['csrf'] ?? ''))
) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Session expired. Reload the page and try again.']);
    exit;
}

$admin      = trim((string)($_SESSION['admin_name'] ?? ''));
$student_id = trim((string)($_POST['student_id'] ?? ''));
session_write_close();

if ($student_id === '') {
    echo json_encode(['ok' => false, 'error' => 'Student ID is missing.']);
    exit;
}

include("../database/connection.php");
// find the helper: admin/includes/ first, then the same folder as this file
$invHelper = null;
foreach ([__DIR__ . '/includes/invitation_helper.php', __DIR__ . '/invitation_helper.php'] as $cand) {
    if (is_file($cand)) {
        $invHelper = $cand;
        break;
    }
}
if ($invHelper === null) {
    echo json_encode(['ok' => false, 'error' => 'invitation_helper.php was not found. Put it next to issue_invitation.php (or in the includes folder).']);
    exit;
}
require_once $invHelper;
if (!function_exists('inv_issue_invitation')) {
    echo json_encode(['ok' => false, 'error' => 'invitation_helper.php is the wrong or an empty file.']);
    exit;
}

// payment must exist first (same rule as the panel)
$st = $conn->prepare("SELECT COUNT(*) FROM payment_records WHERE student_id = ?");
$st->bind_param("s", $student_id);
$st->execute();
$st->bind_result($payments);
$st->fetch();
$st->close();
if ((int)$payments === 0) {
    echo json_encode(['ok' => false, 'error' => 'Make the payment first.']);
    exit;
}

try {
    $conn->begin_transaction();
    $r = inv_issue_invitation($conn, $student_id, $admin, true); // true = also stamp payment_records.issued_by
    if (!$r['ok']) {
        $conn->rollback();
        echo json_encode(['ok' => false, 'error' => $r['error']]);
        exit;
    }
    $conn->commit();
    echo json_encode(['ok' => true, 'already' => $r['already'], 'by' => $admin]);
} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['ok' => false, 'error' => 'Could not save. Try again.']);
}
