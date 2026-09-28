<?php

/**
 * Returns paid / attended / remaining counts (unique students) for each session.
 *   paid      = students with a payment record
 *   attended  = students marked attended
 *   remaining = students who paid but have not attended
 * Used by BOTH scan.php (first page load) and fetch_session_stats.php (auto-refresh),
 * so the numbers always match.
 */
function getSessionStats(mysqli $conn): array
{
    $sessions = ['SESSION_01', 'SESSION_02', 'SESSION_03'];

    // default zeros so every session always exists in the result
    $stats = [];
    foreach ($sessions as $s) {
        $stats[strtolower($s)] = ['paid' => 0, 'attended' => 0, 'remaining' => 0];
    }

    $sql = "
        SELECT dt.session AS session_code,
               COUNT(DISTINCT CASE WHEN pr.student_id IS NOT NULL
                                   THEN rs.student_id END) AS total_paid,
               COUNT(DISTINCT CASE WHEN rs.attend = 'attended'
                                   THEN rs.student_id END) AS total_attended,
               COUNT(DISTINCT CASE WHEN pr.student_id IS NOT NULL
                                    AND (rs.attend IS NULL OR rs.attend != 'attended')
                                   THEN rs.student_id END) AS total_remaining
        FROM registered_students rs
        INNER JOIN data_tables dt ON rs.program_name = dt.programName
        LEFT JOIN payment_records pr ON rs.student_id = pr.student_id
        WHERE dt.session IN ('SESSION_01', 'SESSION_02', 'SESSION_03')
        GROUP BY dt.session
    ";

    $result = $conn->query($sql);
    if ($result === false) {
        throw new RuntimeException('Session stats query failed: ' . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        $key = strtolower(trim($row['session_code']));
        if (isset($stats[$key])) {
            $stats[$key] = [
                'paid'      => (int)$row['total_paid'],
                'attended'  => (int)$row['total_attended'],
                'remaining' => (int)$row['total_remaining'],
            ];
        }
    }

    return $stats;
}
