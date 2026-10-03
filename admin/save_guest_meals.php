<?php
session_start();
include("../database/connection.php");

header('Content-Type: application/json');
$out = ['success' => false, 'message' => ''];

// ---- who is logged in -------------------------------------------------
// ⚠️ CHANGE 'admin_name' if your login stores the username under another key
$entered_by = trim((string)($_SESSION['admin_name'] ?? ''));
if ($entered_by === '') {
    $out['message'] = 'Session expired. Please log in again.';
    echo json_encode($out);
    exit;
}

// ---- input -------------------------------------------------------------
$student_id = trim($_POST['student_id'] ?? '');
$receipt_no = trim($_POST['receipt_number'] ?? '');
$meals      = $_POST['meals'] ?? [];
$allowed    = ['Vegetarian', 'Non-Vegetarian'];

if ($student_id === '' || $receipt_no === '' || !is_array($meals) || count($meals) < 1 || count($meals) > 5) {
    $out['message'] = 'Invalid data.';
    echo json_encode($out);
    exit;
}
foreach ($meals as $m) {
    if (!in_array($m, $allowed, true)) {
        $out['message'] = 'Invalid meal value.';
        echo json_encode($out);
        exit;
    }
}

// ---- name / program / session come from the DB, not from the browser ---
$stmt = $conn->prepare(
    "SELECT rs.name_in_full, rs.program_name,
            COALESCE(NULLIF(TRIM(rs.`session`), ''),
                     (SELECT d.`session` FROM data_tables d
                       WHERE TRIM(d.programName) = TRIM(rs.program_name)
                       ORDER BY d.active DESC, d.id DESC LIMIT 1)) AS `session`
       FROM registered_students rs
      WHERE rs.student_id = ?"
);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$stu = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$stu) {
    $out['message'] = 'Student not found.';
    echo json_encode($out);
    exit;
}

// ---- save: one row per guest -------------------------------------------
try {
    $conn->begin_transaction();

    $ins = $conn->prepare(
        "INSERT INTO extra_guest_meals
            (student_id, student_name, program_name, `session`, receipt_number, guest_type, meal_type, entered_by)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE meal_type = VALUES(meal_type), entered_by = VALUES(entered_by)"
    );

    foreach (array_values($meals) as $i => $meal) {
        $guest_type = 'Guest ' . str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
        $ins->bind_param(
            "ssssssss",
            $student_id,
            $stu['name_in_full'],
            $stu['program_name'],
            $stu['session'],
            $receipt_no,
            $guest_type,
            $meal,
            $entered_by
        );
        if (!$ins->execute()) {
            throw new Exception($ins->error);
        }
    }

    $conn->commit();
    $out['success'] = true;
} catch (Throwable $e) {
    $conn->rollback();
    error_log('save_guest_meals: ' . $e->getMessage());
    $out['message'] = 'Could not save guest meals.';
}

echo json_encode($out);
