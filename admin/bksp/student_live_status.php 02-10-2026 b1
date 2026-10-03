<?php

/**
 * Realtime status endpoint.
 * The payment desk calls this every few seconds for the student on screen.
 * It returns only a tiny JSON when nothing changed, and the fresh panel HTML
 * when something did (graduation status, extra tickets, receipts, session...).
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_name'] ?? '') === 'Unknown Admin') {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'unauthorized']);
    exit;
}

session_write_close(); // polling must not hold the session lock

include("../database/connection.php");
// Find the live panel file: admin/includes/ first, then the same folder as this page
$lvPanel = null;
foreach ([__DIR__ . '/includes/student_live_panel.php', __DIR__ . '/student_live_panel.php'] as $cand) {
    if (is_file($cand)) {
        $lvPanel = $cand;
        break;
    }
}
if ($lvPanel === null) {
    http_response_code(500);
    exit('student_live_panel.php was not found. Put it in ' . __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . ' (or next to this file).');
}
include_once $lvPanel;

$student_id = trim((string)($_POST['student_id'] ?? ''));
$known_ver  = (string)($_POST['version'] ?? '');

if ($student_id === '') {
    echo json_encode(['ok' => false, 'error' => 'empty']);
    exit;
}

$d = lv_load($conn, $student_id);

if (!$d) {
    echo json_encode(['ok' => true, 'found' => false, 'server_time' => date('h:i:s A')]);
    exit;
}

$changed = ($d['version'] !== $known_ver);

$out = [
    'ok'          => true,
    'found'       => true,
    'changed'     => $changed,
    'version'     => $d['version'],
    'state'       => $d['state'],
    'grad_paid'   => $d['grad_paid'],
    'server_time' => date('h:i:s A'),
];

if ($changed) {
    $out['html'] = lv_render($d);
}

echo json_encode($out);
