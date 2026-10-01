<?php

/**
 * Live scan panel (student + programme + session + graduation payment status
 * + payment record / extra tickets).
 *
 * Used by BOTH:
 *   - Payment_Check_and_Id_check.php  (first load after a QR scan)
 *   - student_live_status.php         (realtime refresh every few seconds)
 *
 * so the data shown on first load and on every live refresh is always identical.
 *
 * Tables used:
 *   registered_students  -> student, programme name, course fee status,
 *                           graduation_payment_status
 *   old_student_db       -> invitation number (in_no)
 *   data_tables          -> programme session (+ free ticket entitlement)
 *   payment_records      -> receipt, extra_ticket_count, fees, total, date
 */

if (!function_exists('lv_load')) {

    /** True when the given payment_records column exists (cached). */
    function lv_has_col($conn, $col)
    {
        static $cache = [];
        if (!isset($cache[$col])) {
            $r = $conn->query("SHOW COLUMNS FROM `payment_records` LIKE '" . $conn->real_escape_string($col) . "'");
            $cache[$col] = ($r && $r->num_rows > 0);
        }
        return $cache[$col];
    }

    /** payment_records.issued_ex_ticket exists (add_issued_ex_ticket.sql). */
    function lv_has_issued_col($conn)
    {
        return lv_has_col($conn, 'issued_ex_ticket');
    }

    /**
     * Load everything the live panel needs for one student.
     * Returns null when the student is not registered.
     */
    function lv_load($conn, $student_id)
    {
        $student_id = trim((string)$student_id);
        if ($student_id === '') {
            return null;
        }

        // ---- registered student (graduation_payment_status lives here) ----
        $st = $conn->prepare(
            "SELECT student_id, in_no, dob, name_in_full, program_name, `session`,
                    crsfee_payment_status, graduation_payment_status
             FROM registered_students
             WHERE student_id = ?"
        );
        $st->bind_param("s", $student_id);
        $st->execute();
        $reg = $st->get_result()->fetch_assoc();
        $st->close();
        if (!$reg) {
            return null;
        }

        // ---- invitation number (old_student_db first, same rule as before) ----
        $in_no = '';
        $st = $conn->prepare("SELECT in_no FROM old_student_db WHERE student_id = ?");
        $st->bind_param("s", $student_id);
        $st->execute();
        $old = $st->get_result()->fetch_assoc();
        $st->close();
        $in_no = !empty($old['in_no']) ? $old['in_no'] : ($reg['in_no'] ?? '');

        // ---- programme session + free tickets (data_tables) ----
        $session  = '';
        $free_ent = 0;
        $program  = (string)($reg['program_name'] ?? '');
        $st = $conn->prepare(
            "SELECT `session`, `freeTicket`
             FROM `data_tables`
             WHERE TRIM(`programName`) = TRIM(?)
             ORDER BY `active` DESC, `id` DESC
             LIMIT 1"
        );
        $st->bind_param("s", $program);
        $st->execute();
        if ($prog = $st->get_result()->fetch_assoc()) {
            $session  = trim((string)$prog['session']);
            $free_ent = (int)$prog['freeTicket'];
        }
        $st->close();
        // fall back to the session stored at registration if data_tables has none
        if ($session === '') {
            $session = trim((string)($reg['session'] ?? ''));
        }

        // ---- payment records (extra_ticket_count + issued_ex_ticket live here) ----
        $has_issued = lv_has_issued_col($conn);
        $issuedSel  = $has_issued ? '`issued_ex_ticket`' : 'NULL AS `issued_ex_ticket`';
        $bySel      = lv_has_col($conn, 'issued_by') ? '`issued_by`' : 'NULL AS `issued_by`';
        $payments = [];
        $st = $conn->prepare(
            "SELECT id, receipt_number, graduation_fee, free_ticket_count,
                    extra_ticket_count, extra_ticket_fee, total_amount,
                    payment_date, created_by, " . $issuedSel . ", " . $bySel . "
             FROM payment_records
             WHERE student_id = ?
             ORDER BY payment_date DESC, id DESC"
        );
        $st->bind_param("s", $student_id);
        $st->execute();
        $res = $st->get_result();
        while ($p = $res->fetch_assoc()) {
            $payments[] = [
                'receipt' => (string)$p['receipt_number'],
                'date'    => (string)$p['payment_date'],
                'grad'    => (float)$p['graduation_fee'],
                'free'    => (int)$p['free_ticket_count'],
                'extra'   => (int)$p['extra_ticket_count'],
                'xfee'    => (float)$p['extra_ticket_fee'],
                'total'   => (float)$p['total_amount'],
                'by'      => (string)$p['created_by'],
                'issued'  => strtolower(trim((string)$p['issued_ex_ticket'])) === 'issued',
                'issued_by' => trim((string)$p['issued_by']),
            ];
        }
        $st->close();

        $extra_total   = 0;
        $pending_extra = 0;
        $paid_total    = 0.0;
        foreach ($payments as $p) {
            $extra_total += $p['extra'];
            if (!$p['issued']) {
                $pending_extra += $p['extra'];
            }
            $paid_total  += $p['total'];
        }
        // who issued the extra tickets (names of the admins, without repeats)
        $by_names = [];
        foreach ($payments as $p) {
            if ($p['extra'] > 0 && $p['issued'] && $p['issued_by'] !== '') {
                $by_names[$p['issued_by']] = true;
            }
        }
        $free_total = count($payments) ? $payments[0]['free'] : $free_ent;

        $d = [
            'student_id' => (string)$reg['student_id'],
            'name'       => (string)$reg['name_in_full'],
            'dob'        => (string)$reg['dob'],
            'in_no'      => (string)$in_no,
            'program'    => $program,
            'session'    => $session,
            'crs_raw'    => (string)$reg['crsfee_payment_status'],
            'grad_raw'   => (string)$reg['graduation_payment_status'],
            'crs_paid'   => strtolower(trim((string)$reg['crsfee_payment_status'])) === 'paid',
            'grad_paid'  => strtolower(trim((string)$reg['graduation_payment_status'])) === 'paid',
            'payments'   => $payments,
            'extra'      => $extra_total,
            'free'       => $free_total,
            'guests'     => $free_total + $extra_total,
            'paid_total' => $paid_total,
            'can_issue'  => $has_issued,
            'pending_extra' => $pending_extra,
            'issued_all' => ($extra_total > 0 && $pending_extra === 0),
            'issued_by'  => implode(', ', array_keys($by_names)),
        ];
        // the page re-renders only when this changes
        $d['version'] = md5(json_encode($d));
        // "big" state: when it flips, the whole result card must be reloaded
        $d['state'] = ($d['crs_paid'] ? 'crs1' : 'crs0') . '|' . ($d['grad_paid'] ? 'grad1' : 'grad0');
        return $d;
    }

    function lv_money($v)
    {
        return number_format((float)$v, 2);
    }

    /** Fonts + styles for the live panel. Printed once per result. */
    function lv_styles()
    {
        ob_start(); ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&family=Geist+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            .lv {
                --ink: #17233d;
                --muted: #5f6b7e;
                --line: #e2e6ee;
                --soft: #f4f6fb;
                --brand: #1f4bb6;
                --brand-d: #173a91;
                --brand-bg: #e8eefb;
                --danger: #c0372f;
                --success: #1b7f4b;
                --veg: #2f9e66;
                --veg-bg: #e6f4ec;
                --veg-ink: #17663f;
                --non: #d0453c;
                --non-bg: #fdecea;
                --non-ink: #9c2a22;
                --ease: cubic-bezier(0.16, 1, 0.3, 1);

                font-family: 'Public Sans', system-ui, sans-serif;
                color: var(--ink);
                max-width: 1100px;
                margin: 0 auto 18px;
                -webkit-font-smoothing: antialiased;
            }

            .lv *,
            .lv *::before,
            .lv *::after {
                box-sizing: border-box;
            }

            .lv-mono {
                font-family: 'Geist Mono', ui-monospace, monospace;
                font-variant-numeric: tabular-nums;
            }

            /* ---- live bar ---- */
            .lv-bar {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 10px 16px;
                margin-bottom: 12px;
                font-size: 12px;
                color: var(--muted);
            }

            .lv-live {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                padding: 4px 11px;
                border-radius: 999px;
                background: var(--veg-bg);
                color: var(--veg-ink);
                font-weight: 700;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                font-size: 11px;
            }

            .lv-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: var(--veg);
                box-shadow: 0 0 0 0 rgba(47, 158, 102, .55);
                animation: lv-pulse 1.8s ease-out infinite;
            }

            .lv-live.is-off {
                background: var(--non-bg);
                color: var(--non-ink);
            }

            .lv-live.is-off .lv-dot {
                background: var(--non);
                animation: none;
            }

            @keyframes lv-pulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(47, 158, 102, .55);
                }

                80%,
                100% {
                    box-shadow: 0 0 0 9px rgba(47, 158, 102, 0);
                }
            }

            .lv-bar b {
                color: var(--ink);
                font-weight: 600;
            }

            .lv-bar-sp {
                flex: 1 1 auto;
            }

            /* ---- grid ---- */
            .lv-grid {
                display: grid;
                grid-template-columns: repeat(12, minmax(0, 1fr));
                gap: 14px;
            }

            .lv-card {
                border: 1px solid var(--line);
                border-radius: 14px;
                background: #fff;
                padding: 20px 22px;
                min-width: 0;
                animation: lv-rise .55s var(--ease) backwards;
                animation-delay: calc(var(--i, 0) * 60ms);
            }

            @keyframes lv-rise {
                from {
                    opacity: 0;
                    transform: translateY(12px);
                }
            }

            .lv-k {
                display: block;
                margin: 0 0 6px;
                font-size: 12px;
                font-weight: 500;
                letter-spacing: 0.02em;
                color: var(--muted);
            }

            /* student */
            .lv-who {
                grid-column: span 8;
                display: flex;
                align-items: center;
                gap: 18px;
            }

            .lv-avatar {
                flex: 0 0 auto;
                display: grid;
                place-items: center;
                width: 64px;
                height: 64px;
                border-radius: 18px;
                background: var(--brand);
                color: #fff;
                font-size: 22px;
                font-weight: 700;
                letter-spacing: 0.02em;
            }

            .lv-who-main {
                min-width: 0;
            }

            .lv-name {
                margin: 0;
                font-size: clamp(20px, 2.6vw, 28px);
                font-weight: 800;
                letter-spacing: -0.03em;
                line-height: 1.15;
                color: var(--ink);
                overflow-wrap: anywhere;
            }

            .lv-chips {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 10px;
            }

            .lv-chip {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                padding: 4px 11px;
                border-radius: 999px;
                background: var(--soft);
                color: var(--ink);
                font-size: 12px;
                font-weight: 500;
            }

            .lv-chip i {
                color: var(--muted);
                font-size: 11px;
            }

            .lv-chip.is-ok {
                background: var(--veg-bg);
                color: var(--veg-ink);
            }

            .lv-chip.is-ok i {
                color: var(--veg-ink);
            }

            .lv-chip.is-warn {
                background: var(--brand-bg);
                color: var(--brand);
            }

            .lv-chip.is-warn i {
                color: var(--brand);
            }

            /* graduation payment status */
            .lv-status {
                grid-column: span 4;
                display: flex;
                flex-direction: column;
                justify-content: center;
                gap: 6px;
            }

            .lv-status-row {
                display: flex;
                align-items: center;
                gap: 14px;
            }

            .lv-status-ico {
                display: grid;
                place-items: center;
                width: 48px;
                height: 48px;
                border-radius: 14px;
                font-size: 22px;
            }

            .lv-status-val {
                font-size: clamp(22px, 2.6vw, 30px);
                font-weight: 800;
                letter-spacing: -0.02em;
                line-height: 1.05;
            }

            .lv-status.is-paid {
                background: var(--veg-bg);
                border-color: #cde7d8;
            }

            .lv-status.is-paid .lv-status-ico {
                background: var(--veg);
                color: #fff;
            }

            .lv-status.is-paid .lv-status-val,
            .lv-status.is-paid .lv-k {
                color: var(--veg-ink);
            }

            .lv-status.is-pending {
                background: var(--brand-bg);
                border-color: #cfdaf5;
            }

            .lv-status.is-pending .lv-status-ico {
                background: var(--brand);
                color: #fff;
            }

            .lv-status.is-pending .lv-status-val,
            .lv-status.is-pending .lv-k {
                color: var(--brand);
            }

            /* programme / session / extra */
            .lv-prog {
                grid-column: span 5;
            }

            .lv-prog-val {
                margin: 0;
                font-size: 18px;
                font-weight: 700;
                letter-spacing: -0.015em;
                line-height: 1.35;
                overflow-wrap: anywhere;
            }

            .lv-session {
                grid-column: span 3;
                background: var(--brand-bg);
                border-color: transparent;
            }

            .lv-session .lv-k,
            .lv-session .lv-big {
                color: var(--brand);
            }

            .lv-session.is-missing {
                background: var(--non-bg);
            }

            .lv-session.is-missing .lv-k,
            .lv-session.is-missing .lv-big {
                color: var(--non-ink);
            }

            .lv-big {
                font-family: 'Geist Mono', ui-monospace, monospace;
                font-size: clamp(24px, 2.8vw, 32px);
                font-weight: 700;
                letter-spacing: -0.03em;
                line-height: 1.1;
                font-variant-numeric: tabular-nums;
            }

            /* extra tickets = the highlighted tile */
            .lv-extra {
                grid-column: span 4;
                position: relative;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 10px;
            }

            .lv-extra .lv-num {
                font-family: 'Geist Mono', ui-monospace, monospace;
                font-size: clamp(46px, 5.4vw, 68px);
                font-weight: 700;
                letter-spacing: -0.05em;
                line-height: 1;
                font-variant-numeric: tabular-nums;
            }

            .lv-extra .lv-foot {
                font-size: 12px;
                line-height: 1.5;
            }

            .lv-extra.has-extra,
            .lv-extra.is-none {
                background: var(--danger);
                border-color: var(--danger);
                color: #fff;
                box-shadow: 0 10px 28px rgba(192, 55, 47, .30);
            }

            .lv-extra.has-extra::after,
            .lv-extra.is-none::after,
            .lv-extra.is-issued::after {
                content: "";
                position: absolute;
                right: -70px;
                top: -70px;
                width: 230px;
                height: 230px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(255, 255, 255, .22), transparent 70%);
                pointer-events: none;
            }

            .lv-extra.has-extra .lv-k,
            .lv-extra.has-extra .lv-foot,
            .lv-extra.is-none .lv-k,
            .lv-extra.is-none .lv-foot,
            .lv-extra.is-issued .lv-k,
            .lv-extra.is-issued .lv-foot {
                color: rgba(255, 255, 255, .85);
            }

            .lv-extra.is-zero {
                background: var(--veg-bg);
                border-color: #cde7d8;
            }

            .lv-extra.is-zero .lv-num,
            .lv-extra.is-zero .lv-k,
            .lv-extra.is-zero .lv-foot {
                color: var(--veg-ink);
            }



            /* ---- payment record ---- */
            .lv-rec {
                margin-top: 14px;
                border: 1px solid var(--line);
                border-radius: 14px;
                background: #fff;
                overflow: hidden;
                animation: lv-rise .55s var(--ease) backwards;
                animation-delay: 360ms;
            }

            .lv-rec-head {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                padding: 16px 22px;
                border-bottom: 1px solid var(--line);
                background: var(--soft);
            }

            .lv-rec-title {
                display: flex;
                align-items: center;
                gap: 10px;
                margin: 0;
                font-size: 15px;
                font-weight: 700;
            }

            .lv-rec-title i {
                color: var(--brand);
            }

            .lv-count {
                padding: 2px 9px;
                border-radius: 999px;
                background: var(--brand-bg);
                color: var(--brand);
                font-size: 12px;
                font-weight: 600;
            }

            .lv-tablewrap {
                overflow-x: auto;
            }

            .lv-table {
                width: 100%;
                min-width: 860px;
                border-collapse: collapse;
                font-size: 13px;
            }

            .lv-table th {
                padding: 11px 16px;
                border-bottom: 1px solid var(--line);
                text-align: left;
                font-size: 12px;
                font-weight: 500;
                color: var(--muted);
                white-space: nowrap;
            }

            .lv-table td {
                padding: 13px 16px;
                border-bottom: 1px solid var(--line);
                vertical-align: middle;
            }

            .lv-table tbody tr:last-child td {
                border-bottom: 0;
            }

            .lv-table .r {
                text-align: right;
            }

            .lv-table .c {
                text-align: center;
            }

            .lv-table tfoot td {
                padding: 13px 16px;
                border-top: 1px solid var(--line);
                background: var(--soft);
                font-weight: 700;
            }

            .lv-receipt {
                font-weight: 600;
                color: var(--brand);
            }

            .lv-xchip {
                display: inline-block;
                min-width: 34px;
                padding: 3px 10px;
                border-radius: 999px;
                background: var(--brand);
                color: #fff;
                font-family: 'Geist Mono', ui-monospace, monospace;
                font-size: 13px;
                font-weight: 700;
                text-align: center;
            }

            .lv-xchip.is-zero {
                background: var(--soft);
                color: var(--muted);
            }

            .lv-empty {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 26px 22px;
                color: var(--muted);
                font-size: 14px;
            }

            .lv-empty i {
                display: grid;
                place-items: center;
                width: 42px;
                height: 42px;
                border-radius: 12px;
                background: var(--soft);
                font-size: 17px;
            }

            /* notes */
            .lv-notes {
                display: grid;
                gap: 8px;
                margin-top: 14px;
            }

            .lv-note {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                padding: 12px 16px;
                border-radius: 12px;
                font-size: 13px;
                line-height: 1.5;
                font-weight: 500;
            }

            .lv-note i {
                margin-top: 3px;
            }

            .lv-note.is-bad {
                background: var(--non-bg);
                color: var(--non-ink);
            }

            .lv-note.is-info {
                background: var(--brand-bg);
                color: var(--brand-d);
            }

            /* no payment record: full-width alert */
            .lv-alert {
                grid-column: 1 / -1;
                display: flex;
                align-items: center;
                gap: 16px;
                padding: 16px 20px;
                border: 1px solid #f0c4c0;
                border-left: 5px solid var(--danger);
                border-radius: 14px;
                background: var(--non-bg);
                color: var(--non-ink);
                animation: lv-rise .55s var(--ease) backwards;
            }

            .lv-alert-ico {
                flex: 0 0 auto;
                display: grid;
                place-items: center;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: var(--danger);
                color: #fff;
                font-size: 20px;
            }

            .lv-alert strong {
                display: block;
                font-size: clamp(18px, 2.4vw, 22px);
                font-weight: 800;
                letter-spacing: -0.02em;
                color: var(--danger);
            }

            .lv-alert div>span {
                font-size: 13px;
                line-height: 1.5;
            }

            /* extra tickets: issue button + issued state */
            .lv-extra.is-issued {
                background: var(--success);
                border-color: var(--success);
                color: #fff;
                box-shadow: 0 10px 28px rgba(27, 127, 75, .28);
            }



            .lv-issue-btn {
                position: relative;
                z-index: 1;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: 100%;
                height: 46px;
                border: 0;
                border-radius: 10px;
                background: #fff;
                color: var(--danger);
                font: inherit;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                transition: background .15s, transform .1s;
            }

            .lv-issue-btn:hover {
                background: #fdf0ef;
            }

            .lv-issue-btn:active {
                transform: scale(.98);
            }


            .lv-issue-btn:disabled {
                opacity: .7;
                cursor: wait;
            }

            .lv-issue-btn:focus-visible {
                outline: 3px solid #fff;
                outline-offset: 2px;
            }

            .lv-issued {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-size: 14px;
                font-weight: 700;
                color: #fff;
            }

            .lv-issued-by {
                position: relative;
                z-index: 1;
                margin-top: -4px;
                font-size: 12px;
                color: rgba(255, 255, 255, .88);
            }

            .lv-by {
                margin-top: 3px;
                font-size: 11px;
                color: var(--muted);
                white-space: nowrap;
            }

            .lv-iss {
                display: inline-block;
                padding: 3px 10px;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 600;
                white-space: nowrap;
            }

            .lv-iss.is-done {
                background: var(--veg-bg);
                color: var(--veg-ink);
            }

            .lv-iss.is-pending {
                background: var(--brand-bg);
                color: var(--brand);
            }

            /* realtime flash when a value changes */
            .lv-flash {
                animation: lv-flash 1.8s ease-out 1 !important;
            }

            @keyframes lv-flash {
                0% {
                    box-shadow: 0 0 0 0 rgba(31, 75, 182, .55);
                    transform: scale(1.015);
                }

                100% {
                    box-shadow: 0 0 0 16px rgba(31, 75, 182, 0);
                    transform: none;
                }
            }

            @media (max-width: 991px) {

                .lv-who,
                .lv-status {
                    grid-column: span 12;
                }

                .lv-prog {
                    grid-column: span 12;
                }

                .lv-session {
                    grid-column: span 6;
                }

                .lv-extra {
                    grid-column: span 6;
                }
            }

            @media (max-width: 575px) {

                .lv-session,
                .lv-extra {
                    grid-column: span 12;
                }

                .lv-who {
                    align-items: flex-start;
                }

                .lv-avatar {
                    width: 52px;
                    height: 52px;
                    font-size: 18px;
                    border-radius: 14px;
                }
            }

            @media (prefers-reduced-motion: reduce) {

                .lv-card,
                .lv-rec,
                .lv-dot,
                .lv-flash {
                    animation: none !important;
                }
            }
        </style>
    <?php
        return ob_get_clean();
    }

    /** The live panel itself (student, programme, session, status, extra tickets, payment record). */
    function lv_render($d)
    {
        $e = function ($v) {
            return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
        };

        // initials for the avatar
        $words = preg_split('/\s+/', trim($d['name']), -1, PREG_SPLIT_NO_EMPTY);
        $sub = function_exists('mb_substr') ? 'mb_substr' : 'substr';
        $ini = '';
        if (count($words)) {
            $ini = strtoupper($sub($words[0], 0, 1));
            if (count($words) > 1) {
                $ini .= strtoupper($sub(end($words), 0, 1));
            }
        }

        $has_rec   = count($d['payments']) > 0;
        $extra     = (int)$d['extra'];
        $extra_cls = !$has_rec ? 'is-none' : ($extra > 0 ? ($d['issued_all'] ? 'is-issued' : 'has-extra') : 'is-zero');
        $sess_miss = ($d['session'] === '');
        $grad_paid = $d['grad_paid'];

        ob_start(); ?>
        <section class="lv" id="lvRoot" aria-label="Scanned student"
            data-student="<?= $e($d['student_id']) ?>"
            data-version="<?= $e($d['version']) ?>"
            data-state="<?= $e($d['state']) ?>"
            data-paid="<?= $grad_paid ? '1' : '0' ?>"
            data-rec="<?= $has_rec ? '1' : '0' ?>">

            <div class="lv-bar">
                <span class="lv-live" title="This panel refreshes automatically"><span class="lv-dot"></span><span class="lv-live-txt">Live</span></span>
                <span>QR verified <b class="lv-mono"><?= $e($d['student_id']) ?></b></span>
                <span class="lv-bar-sp"></span>
                <span>Synced <b class="lv-mono" data-live-synced><?= date('h:i:s A') ?></b></span>
            </div>

            <div class="lv-grid" aria-live="polite">

                <!-- student -->
                <div class="lv-card lv-who" style="--i:0">
                    <div class="lv-avatar" aria-hidden="true"><?= $e($ini) ?></div>
                    <div class="lv-who-main">
                        <span class="lv-k">Student</span>
                        <h2 class="lv-name"><?= $e($d['name']) ?></h2>
                        <div class="lv-chips">
                            <span class="lv-chip lv-mono"><i class="fas fa-id-badge" aria-hidden="true"></i> <?= $e($d['student_id']) ?></span>
                            <?php if ($d['in_no'] !== ''): ?>
                                <span class="lv-chip"><i class="fas fa-envelope-open-text" aria-hidden="true"></i> Invitation <b class="lv-mono"><?= $e($d['in_no']) ?></b></span>
                            <?php endif; ?>
                            <span class="lv-chip <?= $d['crs_paid'] ? 'is-ok' : 'is-warn' ?>" data-k="crs" data-v="<?= $e($d['crs_raw']) ?>">
                                <i class="fas fa-circle" aria-hidden="true" style="font-size:6px"></i>
                                Course fee: <?= $e($d['crs_paid'] ? 'Paid' : ($d['crs_raw'] !== '' ? $d['crs_raw'] : 'Not set')) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- graduation payment status (registered_students.graduation_payment_status) -->
                <div class="lv-card lv-status <?= $grad_paid ? 'is-paid' : 'is-pending' ?>" style="--i:1" data-k="grad" data-v="<?= $e($d['grad_raw']) ?>">
                    <span class="lv-k">Graduation payment</span>
                    <div class="lv-status-row">
                        <span class="lv-status-ico" aria-hidden="true"><i class="fas <?= $grad_paid ? 'fa-check' : 'fa-hourglass-half' ?>"></i></span>
                        <span class="lv-status-val"><?= $grad_paid ? 'Paid' : 'Not completed' ?></span>
                    </div>
                </div>

                <?php if (!$has_rec): ?>
                    <!-- no row in payment_records for this student -->
                    <div class="lv-alert" role="alert" style="--i:1">
                        <span class="lv-alert-ico" aria-hidden="true"><i class="fas fa-triangle-exclamation"></i></span>
                        <div>
                            <strong>Make the payment first</strong>
                            <span>No payment record was found for student <b class="lv-mono"><?= $e($d['student_id']) ?></b>. Take the graduation payment; this screen updates by itself once it is recorded.</span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- programme -->
                <div class="lv-card lv-prog" style="--i:2" data-k="program" data-v="<?= $e($d['program']) ?>">
                    <span class="lv-k">Programme</span>
                    <p class="lv-prog-val"><?= $e($d['program'] !== '' ? $d['program'] : 'Not set') ?></p>
                </div>

                <!-- session (data_tables.session) -->
                <div class="lv-card lv-session <?= $sess_miss ? 'is-missing' : '' ?>" style="--i:3" data-k="session" data-v="<?= $e($d['session']) ?>">
                    <span class="lv-k">Session</span>
                    <div class="lv-big"><?= $e($sess_miss ? 'Not assigned' : $d['session']) ?></div>
                </div>

                <!-- extra tickets (payment_records.extra_ticket_count) = highlighted -->
                <div class="lv-card lv-extra <?= $extra_cls ?>" style="--i:4" data-k="extra" data-v="<?= (int)$extra ?>|<?= $d['issued_all'] ? 'i' : 'p' ?>">
                    <span class="lv-k">Extra tickets purchased</span>
                    <div class="lv-num"><?= $has_rec ? ($extra > 0 ? '+' . $extra : '0') : '&mdash;' ?></div>
                    <div class="lv-foot">
                        <?php if ($has_rec): ?>
                            Free tickets <b class="lv-mono"><?= (int)$d['free'] ?></b>
                            &nbsp;+&nbsp; extra <b class="lv-mono"><?= $extra ?></b>
                            &nbsp;=&nbsp; <b class="lv-mono"><?= (int)$d['guests'] ?> guest ticket<?= $d['guests'] == 1 ? '' : 's' ?></b>
                        <?php else: ?>
                            Payment required first
                        <?php endif; ?>
                    </div>
                    <?php if ($d['can_issue'] && $has_rec && $extra > 0): ?>
                        <?php if ($d['issued_all']): ?>
                            <div class="lv-issued"><i class="fas fa-circle-check" aria-hidden="true"></i> Extra tickets issued</div>
                            <?php if ($d['issued_by'] !== ''): ?><div class="lv-issued-by">Issued by <b><?= $e($d['issued_by']) ?></b></div><?php endif; ?>
                        <?php else: ?>
                            <button type="button" class="lv-issue-btn" data-student="<?= $e($d['student_id']) ?>">
                                <i class="fas fa-ticket" aria-hidden="true"></i>
                                <span>Issue <?= (int)$d['pending_extra'] ?> extra ticket<?= $d['pending_extra'] == 1 ? '' : 's' ?></span>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- payment record (payment_records) -->
            <div class="lv-rec">
                <div class="lv-rec-head">
                    <h3 class="lv-rec-title"><i class="fas fa-receipt" aria-hidden="true"></i> Payment record
                        <?php if ($has_rec): ?><span class="lv-count"><?= count($d['payments']) ?> receipt<?= count($d['payments']) == 1 ? '' : 's' ?></span><?php endif; ?>
                    </h3>
                </div>

                <?php if ($has_rec): ?>
                    <div class="lv-tablewrap">
                        <table class="lv-table">
                            <thead>
                                <tr>
                                    <th>Receipt no</th>
                                    <th>Paid on</th>
                                    <th class="r">Graduation fee</th>
                                    <th class="c">Free tickets</th>
                                    <th class="c">Extra tickets</th>
                                    <th class="c">Issued</th>
                                    <th class="r">Extra ticket fee</th>
                                    <th class="r">Total (LKR)</th>
                                    <th>Collected by</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($d['payments'] as $p): ?>
                                    <tr>
                                        <td class="lv-mono lv-receipt"><?= $e($p['receipt']) ?></td>
                                        <td><?= $e(date('d M Y, h:i A', strtotime($p['date']))) ?></td>
                                        <td class="r lv-mono"><?= lv_money($p['grad']) ?></td>
                                        <td class="c lv-mono"><?= (int)$p['free'] ?></td>
                                        <td class="c"><span class="lv-xchip <?= $p['extra'] > 0 ? '' : 'is-zero' ?>"><?= $p['extra'] > 0 ? '+' . (int)$p['extra'] : '0' ?></span></td>
                                        <td class="c"><?php if ($p['extra'] <= 0): ?><span class="lv-mono">-</span><?php elseif ($p['issued']): ?><span class="lv-iss is-done">Issued</span><?php if ($p['issued_by'] !== ''): ?><div class="lv-by">by <?= $e($p['issued_by']) ?></div><?php endif; ?><?php else: ?><span class="lv-iss is-pending">Pending</span><?php endif; ?></td>
                                        <td class="r lv-mono"><?= lv_money($p['xfee']) ?></td>
                                        <td class="r lv-mono"><b><?= lv_money($p['total']) ?></b></td>
                                        <td><?= $e($p['by'] !== '' ? $p['by'] : '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">Total paid</td>
                                    <td class="c lv-mono"><?= (int)$extra ?></td>
                                    <td></td>
                                    <td></td>
                                    <td class="r lv-mono"><?= lv_money($d['paid_total']) ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="lv-empty">
                        <i class="fas fa-file-invoice" aria-hidden="true"></i>
                        <span>No graduation payment has been recorded for this student yet.</span>
                    </div>
                <?php endif; ?>
            </div>

            <?php
            $notes = [];
            if (count($d['payments']) > 1) {
                $notes[] = ['is-bad', 'fa-triangle-exclamation', count($d['payments']) . ' payment records exist for this student. Check for a duplicate payment before taking another one.'];
            }
            if ($has_rec && !$grad_paid) {
                $notes[] = ['is-info', 'fa-circle-info', 'A payment record exists, but the graduation payment status still reads "Not completed". Confirm before taking another payment.'];
            }
            if (!$has_rec && $grad_paid) {
                $notes[] = ['is-info', 'fa-circle-info', 'The status is Paid, but no payment record was found for this student.'];
            }
            if (count($notes)): ?>
                <div class="lv-notes">
                    <?php foreach ($notes as $n): ?>
                        <div class="lv-note <?= $n[0] ?>"><i class="fas <?= $n[1] ?>" aria-hidden="true"></i><span><?= $e($n[2]) ?></span></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </section>
<?php
        return ob_get_clean();
    }
}
