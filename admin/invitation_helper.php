<?php

/**
 * Shared invitation update, used by issue_invitation.php (0 extra tickets)
 * and by issue_extra_tickets.php / issue_log_tickets.php (extra tickets).
 *
 *   UPDATE registered_students
 *      SET in_no = ?, invitation_collected = 'collected'
 *    WHERE student_id = ?
 *      AND LOWER(TRIM(COALESCE(invitation_collected, ''))) <> 'collected'
 *
 * in_no = same number the live panel shows (old_student_db.in_no first, then registered_students.in_no).
 * The caller owns the transaction (begin / commit / rollback).
 */

/**
 * @param mysqli $conn
 * @param string $student_id
 * @param string $admin          logged-in admin name (from the session)
 * @param bool   $stamp_payment  true  = also write payment_records.issued_by (invitation button, 0 extra tickets)
 *                               false = leave payment_records alone (extra-ticket flow already sets issued_by)
 * @return array ['ok'=>bool, 'already'=>bool, 'error'=>string]
 */
function inv_issue_invitation($conn, $student_id, $admin, $stamp_payment = false)
{
    $student_id = trim((string)$student_id);
    $admin      = trim((string)$admin);

    // the student
    $st = $conn->prepare("SELECT in_no, invitation_collected FROM registered_students WHERE student_id = ?");
    $st->bind_param("s", $student_id);
    $st->execute();
    $reg = $st->get_result()->fetch_assoc();
    $st->close();
    if (!$reg) {
        return ['ok' => false, 'already' => false, 'error' => 'Student not found.'];
    }

    // already collected: nothing to write
    if (strtolower(trim((string)$reg['invitation_collected'])) === 'collected') {
        return ['ok' => true, 'already' => true, 'error' => ''];
    }

    // invitation number
    $st = $conn->prepare("SELECT in_no FROM old_student_db WHERE student_id = ?");
    $st->bind_param("s", $student_id);
    $st->execute();
    $old = $st->get_result()->fetch_assoc();
    $st->close();
    $in_no = !empty($old['in_no']) ? (int)$old['in_no'] : (int)($reg['in_no'] ?? 0);
    if ($in_no <= 0) {
        return ['ok' => false, 'already' => false, 'error' => 'No invitation number found for this student.'];
    }

    // registered_students
    $st = $conn->prepare(
        "UPDATE registered_students
            SET in_no = ?, invitation_collected = 'collected'
          WHERE student_id = ?
            AND LOWER(TRIM(COALESCE(invitation_collected, ''))) <> 'collected'"
    );
    $st->bind_param("is", $in_no, $student_id);
    $st->execute();
    $changed = $st->affected_rows;
    $st->close();

    // payment_records.issued_by: only the latest payment of this student, and only if it is still empty
    if ($stamp_payment && $changed > 0) {
        $st = $conn->prepare(
            "SELECT id FROM payment_records WHERE student_id = ? ORDER BY payment_date DESC, id DESC LIMIT 1"
        );
        $st->bind_param("s", $student_id);
        $st->execute();
        $pay = $st->get_result()->fetch_assoc();
        $st->close();

        if ($pay) {
            $pid = (int)$pay['id'];
            $st = $conn->prepare(
                "UPDATE payment_records
                    SET issued_by = ?
                  WHERE id = ? AND (issued_by IS NULL OR TRIM(issued_by) = '')"
            );
            $st->bind_param("si", $admin, $pid);
            $st->execute();
            $st->close();
        }
    }

    return ['ok' => true, 'already' => ($changed === 0), 'error' => ''];
}
