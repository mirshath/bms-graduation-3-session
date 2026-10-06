<?php
session_start();

/* ------------------------------------------------------------------
 * Access: logged-in ADMIN only
 * ------------------------------------------------------------------ */
if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_name'] ?? '') === 'Unknown Admin') {
    header("Location: login");
    exit();
}
if (($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: index");
    exit();
}

include("../database/connection.php");

/* ------------------------------------------------------------------
 * Create the table automatically (safe to run every time)
 * ------------------------------------------------------------------ */
$conn->query("
    CREATE TABLE IF NOT EXISTS `role_permissions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `role` varchar(100) NOT NULL,
      `page_slug` varchar(100) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `uniq_role_page` (`role`, `page_slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
");

/* ------------------------------------------------------------------
 * Page catalog  (same pages / groups / icons as nav.php)
 * group => [ slug => [label, icon] ]
 * ------------------------------------------------------------------ */
$pageCatalog = [
    'Overview' => [
        'index' => ['Dashboard', 'fa-tachometer-alt'],
    ],
    'Front desk' => [
        'scan' => ['Scan Attendance Table', 'fa-barcode'],
    ],
    'Finance' => [
        'payment_reg'              => ['Payment Registration', 'fa-credit-card'],
        'payment-reports'          => ['Payment Report', 'fa-chart-line'],
        'payment_success_students' => ['Payment Success Student', 'fa-check-circle'],
        'extra-ticket-buying'      => ['Buy Extra Ticket', 'fa-cart-plus'],
        'extra-ticket-data'        => ['Ticket Log / Data', 'fa-list'],
        'ticket_invitation_issue'  => ['Ticket & Invitation Issues', 'fa-ticket-alt'],
    ],
    'Students' => [
        'oldStudentsDB'        => ['Old Students', 'fa-history'],
        'registeredStudents'   => ['Registered Students', 'fa-user-graduate'],
        'upload_student_db'    => ['Upload Student', 'fa-user-graduate'],
        'oldStudentsDOBupdate' => ['Edit Student', 'fa-calendar-alt'],
    ],
    'Cloak room' => [
        'clothCollection' => ['Cloak Issuing', 'fa-tshirt'],
        'cloakReturn'     => ['Cloak Collecting', 'fa-undo'],
        'cloakReport'     => ['Cloak Report', 'fa-chart-bar'],
    ],
    'Administration' => [
        'user'                  => ['User Create', 'fa-user-plus'],
        'permission'            => ['Role Permissions', 'fa-user-lock'],
        'bulk-data'             => ['Bulk Upload', 'fa-upload'],
        'bulk-data-email'       => ['Bulk Email', 'fa-envelope'],
        'view_email_logs'       => ['Logs For Seat No', 'fa-list-alt'],
        'mark_graduate'         => ['Graduate Std Mark', 'fa-graduation-cap'],
        'invitation-collection' => ['Invitation Issue', 'fa-envelope-open'],
    ],
    'Reports' => [
        'allocatedSeatOrder' => ['Report Seat Order', 'fa-chair'],
        'meals_report'       => ['Report Meals', 'fa-utensils'],
    ],
];

// flat list of valid slugs (used to validate what is posted)
$validSlugs = [];
foreach ($pageCatalog as $items) {
    foreach ($items as $slug => $meta) {
        $validSlugs[$slug] = true;
    }
}

/* ------------------------------------------------------------------
 * Fetch ALL roles  (known roles + any other role found in `admin`)
 * ------------------------------------------------------------------ */
$roles = [
    'admin'              => 'Admin',
    'finance'            => 'Finance',
    'invitation'         => 'Invitation',
    'registrationDesk'   => 'Registration Desk',
    'clothCollectReturn' => 'Cloth Collection/Return',
    'coordinator'        => 'Coordinator',
];
$rq = $conn->query("SELECT DISTINCT role FROM admin WHERE role IS NOT NULL AND role <> ''");
if ($rq) {
    while ($r = $rq->fetch_assoc()) {
        $val = trim((string)$r['role']);
        if ($val !== '' && !isset($roles[$val])) {
            $roles[$val] = ucfirst($val);
        }
    }
}

/* ------------------------------------------------------------------
 * CSRF token
 * ------------------------------------------------------------------ */
if (empty($_SESSION['perm_csrf'])) {
    $_SESSION['perm_csrf'] = bin2hex(random_bytes(32));
}

$errorMsg = '';

/* ------------------------------------------------------------------
 * SAVE  (done BEFORE header.php so the redirect works)
 * ------------------------------------------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['savePermissions'])) {
    $postRole  = (string)($_POST['role'] ?? '');
    $postToken = (string)($_POST['csrf'] ?? '');

    if (!hash_equals($_SESSION['perm_csrf'], $postToken)) {
        $errorMsg = "Invalid request. Please refresh the page and try again.";
    } elseif (!isset($roles[$postRole])) {
        $errorMsg = "Please select a valid role.";
    } elseif ($postRole === 'admin') {
        $errorMsg = "Admin always has full access and cannot be changed.";
    } else {
        // keep only slugs that really exist in the catalog
        $selected = [];
        foreach ((array)($_POST['pages'] ?? []) as $slug) {
            $slug = (string)$slug;
            if (isset($validSlugs[$slug])) {
                $selected[$slug] = true;
            }
        }
        $selected = array_keys($selected);

        $conn->begin_transaction();
        try {
            // 1) remove the old permissions of this role
            $del = $conn->prepare("DELETE FROM role_permissions WHERE role = ?");
            $del->bind_param("s", $postRole);
            if (!$del->execute()) {
                throw new Exception($del->error);
            }
            $del->close();

            // 2) insert the checked pages
            if ($selected) {
                $ins = $conn->prepare("INSERT INTO role_permissions (role, page_slug) VALUES (?, ?)");
                foreach ($selected as $slug) {
                    $ins->bind_param("ss", $postRole, $slug);
                    if (!$ins->execute()) {
                        throw new Exception($ins->error);
                    }
                }
                $ins->close();
            }

            $conn->commit();
            header("Location: " . basename($_SERVER['PHP_SELF']) . "?role=" . urlencode($postRole) . "&saved=" . count($selected));
            exit();
        } catch (Throwable $e) {
            $conn->rollback();
            $errorMsg = "Database error. Permissions were not saved.";
        }
    }
}

/* ------------------------------------------------------------------
 * Selected role + its saved pages
 * ------------------------------------------------------------------ */
$selectedRole = (string)($_POST['role'] ?? $_GET['role'] ?? '');
if (!isset($roles[$selectedRole])) {
    $selectedRole = '';
}

/* what is saved in the database for this role */
$saved = [];
if ($selectedRole !== '') {
    $st = $conn->prepare("SELECT page_slug FROM role_permissions WHERE role = ?");
    $st->bind_param("s", $selectedRole);
    $st->execute();
    $st->bind_result($slugOut);
    while ($st->fetch()) {
        if (isset($validSlugs[$slugOut])) {
            $saved[$slugOut] = true;
        }
    }
    $st->close();
}

/* what is ticked on screen (after a failed save, keep what the user ticked) */
$checked = $saved;
if ($selectedRole !== '' && $errorMsg !== '' && isset($_POST['pages'])) {
    $checked = [];
    foreach ((array)$_POST['pages'] as $s) {
        $s = (string)$s;
        if (isset($validSlugs[$s])) {
            $checked[$s] = true;
        }
    }
}

/* number of allowed pages per role (for the role list) */
$totalPages = count($validSlugs);
$roleCounts = [];
$cq = $conn->query("SELECT role, COUNT(*) AS c FROM role_permissions GROUP BY role");
if ($cq) {
    while ($r = $cq->fetch_assoc()) {
        $roleCounts[$r['role']] = (int)$r['c'];
    }
}
$roleCounts['admin'] = $totalPages; // admin always has everything

$roleIcons = [
    'admin'              => 'fa-user-shield',
    'finance'            => 'fa-coins',
    'invitation'         => 'fa-envelope-open-text',
    'registrationDesk'   => 'fa-clipboard-list',
    'clothCollectReturn' => 'fa-tshirt',
    'coordinator'        => 'fa-user-tie',
];

$successMsg = '';
if (isset($_GET['saved'])) {
    $successMsg = "Permissions saved. This role can now open " . (int)$_GET['saved'] . " page(s).";
}

$isAdminRole   = ($selectedRole === 'admin');
$selectedCount = $isAdminRole ? $totalPages : count($checked);
$self          = basename($_SERVER['PHP_SELF']);

include("includes/header.php");
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600&family=Geist+Mono:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    .pm {
        --ink: #18181b;
        --ink-2: #3f3f46;
        --muted: #71717a;
        --faint: #a1a1aa;
        --line: #e4e4e7;
        --line-soft: #efeff1;
        --surface: #ffffff;
        --wash: #fafafa;
        --accent: #0f766e;
        --accent-ink: #115e59;
        --accent-soft: #eaf5f3;
        --warn: #b45309;
        --warn-soft: #fdf6e7;
        --bad: #b42318;
        --bad-soft: #fdf0ee;
        --ease: cubic-bezier(0.16, 1, 0.3, 1);
        --mono: 'Geist Mono', ui-monospace, SFMono-Regular, Menlo, monospace;
        --pm-zoom: 0.90;
        /* page zoom: 1 = normal, 0.85 = 85%, 0.8 = 80% */
        zoom: var(--pm-zoom);
        font-family: 'Geist', system-ui, -apple-system, 'Segoe UI', sans-serif;
        color: var(--ink);
        -webkit-font-smoothing: antialiased;
        padding-bottom: 120px;
    }

    .pm *,
    .pm *::before,
    .pm *::after {
        box-sizing: border-box;
    }

    .pm label {
        margin: 0;
    }

    .pm [hidden] {
        display: none !important;
    }

    .pm-shell {
        max-width: 1500px;
        margin: 0 auto;
    }

    .pm-mono {
        font-family: var(--mono);
        font-feature-settings: 'zero' 1;
    }

    /* keep normal size on phones / small tablets */
    @media (max-width: 767px) {
        .pm {
            --pm-zoom: 1;
        }
    }

    /* ---------- header ---------- */
    .pm-head {
        padding: 8px 0 28px;
        max-width: 640px;
    }

    .pm-eyebrow {
        margin: 0 0 10px;
        font-family: var(--mono);
        font-size: 12px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .pm-title {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
        letter-spacing: -0.025em;
        line-height: 1.15;
    }

    .pm-lede {
        margin: 10px 0 0;
        font-size: 15px;
        line-height: 1.6;
        color: var(--muted);
        max-width: 60ch;
    }

    /* ---------- notices ---------- */
    .pm-note {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 16px;
        margin-bottom: 20px;
        border-radius: 10px;
        font-size: 14px;
        line-height: 1.5;
        border: 1px solid transparent;
        animation: pm-rise 0.5s var(--ease) both;
    }

    .pm-note i {
        margin-top: 3px;
        font-size: 14px;
    }

    .pm-note.ok {
        background: var(--accent-soft);
        border-color: #cfe6e2;
        color: var(--accent-ink);
    }

    .pm-note.bad {
        background: var(--bad-soft);
        border-color: #f4d4cf;
        color: var(--bad);
    }

    .pm-note.info {
        background: var(--warn-soft);
        border-color: #f2e2bd;
        color: var(--warn);
    }

    /* ---------- layout: narrow role rail + wide workspace ---------- */
    .pm-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 28px;
        align-items: start;
    }

    @media (min-width: 992px) {
        .pm-layout {
            grid-template-columns: minmax(250px, 1fr) minmax(0, 3fr);
            gap: 48px;
        }
    }

    /* ---------- role rail ---------- */
    .pm-rail {
        min-width: 0;
    }

    @media (min-width: 992px) {
        .pm-rail {
            position: sticky;
            top: 24px;
        }
    }

    .pm-kicker {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 0 0 8px;
        padding: 0 12px;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .pm-roles {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 6px;
        scrollbar-width: thin;
    }

    @media (min-width: 992px) {
        .pm-roles {
            flex-direction: column;
            overflow: visible;
            gap: 2px;
            padding-bottom: 0;
        }
    }

    .pm-role {
        position: relative;
        display: grid;
        grid-template-columns: 32px minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        color: var(--ink-2);
        text-decoration: none;
        white-space: nowrap;
        border: 1px solid transparent;
        transition: background-color 0.25s var(--ease), border-color 0.25s var(--ease), transform 0.25s var(--ease);
        animation: pm-rise 0.55s var(--ease) both;
        animation-delay: calc(var(--i) * 45ms);
    }

    .pm-role:hover {
        background: var(--wash);
        color: var(--ink);
        text-decoration: none;
    }

    .pm-role:active {
        transform: translateY(1px);
    }

    .pm-role:focus-visible {
        outline: 2px solid var(--accent);
        outline-offset: 2px;
    }

    .pm-role.is-active {
        background: var(--surface);
        border-color: var(--line);
        color: var(--ink);
        box-shadow: 0 8px 24px -12px rgba(24, 24, 27, 0.14);
    }

    .pm-role-ico {
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--wash);
        border: 1px solid var(--line-soft);
        color: var(--muted);
        font-size: 13px;
        transition: background-color 0.25s var(--ease), color 0.25s var(--ease);
    }

    .pm-role.is-active .pm-role-ico {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
    }

    .pm-role-name {
        display: block;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.2;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pm-role-key {
        display: block;
        margin-top: 2px;
        font-family: var(--mono);
        font-size: 11px;
        color: var(--faint);
    }

    .pm-role-count {
        font-family: var(--mono);
        font-size: 12px;
        color: var(--muted);
    }

    .pm-role-count b {
        font-weight: 600;
        color: var(--ink);
    }

    /* ---------- workspace ---------- */
    .pm-work {
        min-width: 0;
    }

    .pm-bar-top {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--line);
        animation: pm-rise 0.55s var(--ease) both;
    }

    @media (min-width: 768px) {
        .pm-bar-top {
            grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
            align-items: end;
            gap: 32px;
        }
    }

    .pm-who {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 6px;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .pm-dot {
        position: relative;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--accent);
    }

    .pm-dot::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: var(--accent);
        animation: pm-ping 2.2s var(--ease) infinite;
    }

    .pm-work-title {
        margin: 0;
        font-size: 22px;
        font-weight: 600;
        letter-spacing: -0.02em;
    }

    .pm-meter {
        margin-top: 14px;
    }

    .pm-meter-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
        color: var(--muted);
    }

    .pm-meter-row b {
        font-family: var(--mono);
        font-weight: 600;
        font-size: 15px;
        color: var(--ink);
    }

    .pm-track {
        height: 4px;
        border-radius: 99px;
        background: var(--line-soft);
        overflow: hidden;
    }

    .pm-fill {
        height: 100%;
        width: 100%;
        border-radius: 99px;
        background: var(--accent);
        transform-origin: left center;
        transform: scaleX(0);
        transition: transform 0.7s var(--ease);
    }

    /* form controls: label above, as per spec */
    .pm-field {
        display: grid;
        gap: 8px;
    }

    .pm-label {
        font-size: 13px;
        font-weight: 500;
        color: var(--ink-2);
    }

    .pm-input-wrap {
        position: relative;
    }

    .pm-input-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        color: var(--faint);
        pointer-events: none;
    }

    .pm-input {
        width: 100%;
        height: 40px;
        padding: 0 12px 0 34px;
        border: 1px solid var(--line);
        border-radius: 10px;
        background: var(--surface);
        font: inherit;
        font-size: 14px;
        color: var(--ink);
        transition: border-color 0.2s var(--ease), box-shadow 0.2s var(--ease);
    }

    .pm-input::placeholder {
        color: var(--faint);
    }

    .pm-input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.14);
    }

    .pm-tools {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 16px 0 4px;
    }

    .pm-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 36px;
        padding: 0 14px;
        border: 1px solid var(--line);
        border-radius: 9px;
        background: var(--surface);
        font: inherit;
        font-size: 13px;
        font-weight: 500;
        color: var(--ink-2);
        cursor: pointer;
        transition: background-color 0.2s var(--ease), border-color 0.2s var(--ease), transform 0.2s var(--ease);
    }

    .pm-btn:hover {
        background: var(--wash);
        border-color: #d4d4d8;
        color: var(--ink);
    }

    .pm-btn:active {
        transform: translateY(1px) scale(0.98);
    }

    .pm-btn:focus-visible {
        outline: 2px solid var(--accent);
        outline-offset: 2px;
    }

    .pm-btn i {
        font-size: 12px;
        color: var(--muted);
    }

    .pm-btn.is-primary {
        background: var(--ink);
        border-color: var(--ink);
        color: #fff;
        height: 38px;
        padding: 0 18px;
    }

    .pm-btn.is-primary:hover {
        background: #27272a;
    }

    .pm-btn.is-primary i {
        color: #fff;
    }

    .pm-btn.is-ghost {
        border-color: transparent;
        background: transparent;
    }

    .pm-btn.is-loading {
        pointer-events: none;
        background: linear-gradient(100deg, #27272a 30%, #52525b 50%, #27272a 70%);
        background-size: 220% 100%;
        animation: pm-shimmer 1.2s linear infinite;
    }

    /* ---------- groups (no boxed cards, just hairlines) ---------- */
    .pm-groups {
        margin-top: 8px;
    }

    .pm-group {
        padding: 26px 0 6px;
        border-bottom: 1px solid var(--line);
        animation: pm-rise 0.6s var(--ease) both;
        animation-delay: calc(var(--i) * 55ms + 120ms);
    }

    .pm-group:last-of-type {
        border-bottom: 0;
    }

    .pm-group-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 6px;
    }

    .pm-group-name {
        margin: 0;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: var(--ink-2);
    }

    .pm-group-count {
        font-family: var(--mono);
        font-size: 12px;
        color: var(--muted);
        padding: 2px 8px;
        border-radius: 99px;
        background: var(--wash);
        border: 1px solid var(--line-soft);
    }

    .pm-group-toggle {
        margin-left: auto;
        border: 0;
        background: transparent;
        padding: 4px 8px;
        border-radius: 6px;
        font: inherit;
        font-size: 12px;
        font-weight: 500;
        color: var(--accent-ink);
        cursor: pointer;
        transition: background-color 0.2s var(--ease);
    }

    .pm-group-toggle:hover {
        background: var(--accent-soft);
    }

    .pm-group-toggle:focus-visible {
        outline: 2px solid var(--accent);
    }

    .pm-rows {
        display: grid;
        grid-template-columns: 1fr;
        column-gap: 40px;
    }

    @media (min-width: 1200px) {
        .pm-rows {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .pm-row {
        position: relative;
        display: grid;
        grid-template-columns: 28px minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;
        padding: 13px 0;
        border-bottom: 1px solid var(--line-soft);
        cursor: pointer;
        transition: transform 0.25s var(--ease);
    }

    .pm-row:active {
        transform: translateY(1px);
    }

    .pm-row-ico {
        display: grid;
        place-items: center;
        width: 28px;
        height: 28px;
        border-radius: 7px;
        font-size: 12px;
        color: var(--faint);
        background: var(--wash);
        transition: background-color 0.25s var(--ease), color 0.25s var(--ease);
    }

    .pm-row.is-on .pm-row-ico {
        background: var(--accent-soft);
        color: var(--accent);
    }

    .pm-row-name {
        display: block;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.25;
        color: var(--ink);
    }

    .pm-row-slug {
        display: block;
        margin-top: 2px;
        font-family: var(--mono);
        font-size: 11px;
        color: var(--faint);
    }

    /* pending change marker */
    .pm-row.is-changed::before {
        content: '';
        position: absolute;
        left: -14px;
        top: 50%;
        width: 6px;
        height: 6px;
        margin-top: -3px;
        border-radius: 50%;
        background: var(--warn);
    }

    /* switch */
    .pm-switch {
        -webkit-appearance: none;
        appearance: none;
        position: relative;
        width: 36px;
        height: 22px;
        margin: 0;
        border-radius: 99px;
        background: #d4d4d8;
        cursor: pointer;
        transition: background-color 0.3s var(--ease);
        flex-shrink: 0;
    }

    .pm-switch::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 2px rgba(24, 24, 27, 0.25);
        transition: transform 0.35s var(--ease);
    }

    .pm-switch:checked {
        background: var(--accent);
    }

    .pm-switch:checked::after {
        transform: translateX(14px);
    }

    .pm-switch:focus-visible {
        outline: 2px solid var(--accent);
        outline-offset: 2px;
    }

    .pm-switch:disabled {
        cursor: not-allowed;
        opacity: 0.55;
    }

    /* ---------- empty states ---------- */
    .pm-empty {
        padding: 56px 8px;
        max-width: 440px;
        animation: pm-rise 0.6s var(--ease) both;
    }

    .pm-empty-ico {
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        margin-bottom: 18px;
        border-radius: 12px;
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--muted);
        box-shadow: 0 8px 24px -12px rgba(24, 24, 27, 0.14);
    }

    .pm-empty h2 {
        margin: 0 0 6px;
        font-size: 17px;
        font-weight: 600;
        letter-spacing: -0.01em;
    }

    .pm-empty p {
        margin: 0;
        font-size: 14px;
        line-height: 1.6;
        color: var(--muted);
    }

    /* ---------- floating save bar ---------- */
    .pm-savebar {
        position: fixed;
        left: 0;
        right: 0;
        bottom: calc(20px + env(safe-area-inset-bottom, 0px));
        width: max-content;
        max-width: calc(100% - 32px);
        margin: 0 auto;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 10px 10px 10px 18px;
        border-radius: 14px;
        background: rgba(24, 24, 27, 0.94);
        color: #fafafa;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08), 0 24px 48px -16px rgba(24, 24, 27, 0.45);
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        opacity: 0;
        visibility: hidden;
        transform: translateY(24px);
        pointer-events: none;
        transition: transform 0.5s var(--ease), opacity 0.3s var(--ease), visibility 0s linear 0.5s;
        z-index: 1040;
    }

    .pm-savebar.is-visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        pointer-events: auto;
        transition: transform 0.5s var(--ease), opacity 0.3s var(--ease), visibility 0s;
    }

    .pm-savebar-text {
        font-size: 13px;
        color: #d4d4d8;
        white-space: nowrap;
    }

    .pm-savebar-text b {
        font-family: var(--mono);
        font-weight: 600;
        color: #fff;
    }

    .pm-savebar .pm-btn.is-ghost {
        color: #d4d4d8;
    }

    .pm-savebar .pm-btn.is-ghost:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
    }

    .pm-savebar .pm-btn.is-primary {
        background: #fafafa;
        border-color: #fafafa;
        color: var(--ink);
    }

    .pm-savebar .pm-btn.is-primary:hover {
        background: #fff;
    }

    .pm-savebar .pm-btn.is-primary i {
        color: var(--ink);
    }

    .pm-savebar .pm-btn.is-primary.is-loading {
        color: #fff;
        background: linear-gradient(100deg, #52525b 30%, #71717a 50%, #52525b 70%);
        background-size: 220% 100%;
    }

    @media (max-width: 575px) {
        .pm-savebar {
            gap: 8px;
            padding-left: 14px;
        }

        .pm-savebar-text {
            font-size: 12px;
        }
    }

    /* ---------- motion ---------- */
    @keyframes pm-rise {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pm-ping {
        0% {
            transform: scale(1);
            opacity: 0.45;
        }

        70%,
        100% {
            transform: scale(2.6);
            opacity: 0;
        }
    }

    @keyframes pm-shimmer {
        from {
            background-position: 120% 0;
        }

        to {
            background-position: -120% 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .pm *,
        .pm *::before,
        .pm *::after {
            animation: none !important;
            transition: none !important;
        }
    }
</style>

<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>
            <div class="container-fluid mt-4 pm">
                <div class="pm-shell">

                    <header class="pm-head">
                        <p class="pm-eyebrow">Administration / Access control</p>
                        <h1 class="pm-title">Role permissions</h1>
                        <p class="pm-lede">Choose which pages each role can open. The sidebar updates the next time a person with that role loads a page.</p>
                    </header>

                    <?php if ($successMsg !== ''): ?>
                        <div class="pm-note ok" role="status"><i class="fas fa-check-circle"></i><span><?= htmlspecialchars($successMsg) ?></span></div>
                    <?php endif; ?>
                    <?php if ($errorMsg !== ''): ?>
                        <div class="pm-note bad" role="alert"><i class="fas fa-exclamation-circle"></i><span><?= htmlspecialchars($errorMsg) ?></span></div>
                    <?php endif; ?>

                    <div class="pm-layout">

                        <!-- ============ role rail ============ -->
                        <aside class="pm-rail" aria-label="Roles">
                            <p class="pm-kicker"><span>Roles</span><span class="pm-mono"><?= count($roles) ?></span></p>
                            <ul class="pm-roles">
                                <?php $ri = 0;
                                foreach ($roles as $val => $label):
                                    $isActive = ($selectedRole === $val);
                                    $cnt = (int)($roleCounts[$val] ?? 0);
                                    $ico = $roleIcons[$val] ?? 'fa-user';
                                ?>
                                    <li>
                                        <a class="pm-role <?= $isActive ? 'is-active' : '' ?>"
                                            style="--i: <?= $ri++ ?>"
                                            href="<?= htmlspecialchars($self) ?>?role=<?= urlencode($val) ?>"
                                            <?= $isActive ? 'aria-current="page"' : '' ?>>
                                            <span class="pm-role-ico"><i class="fas <?= htmlspecialchars($ico) ?>"></i></span>
                                            <span>
                                                <span class="pm-role-name"><?= htmlspecialchars($label) ?></span>
                                                <span class="pm-role-key"><?= htmlspecialchars($val) ?></span>
                                            </span>
                                            <span class="pm-role-count"><b><?= $cnt ?></b>/<?= $totalPages ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </aside>

                        <!-- ============ workspace ============ -->
                        <main class="pm-work">

                            <?php if ($selectedRole === ''): ?>

                                <div class="pm-empty">
                                    <div class="pm-empty-ico"><i class="fas fa-user-lock"></i></div>
                                    <h2>Select a role to begin</h2>
                                    <p>Pick a role from the list. You will see every page in the system, with the ones that role can already open switched on.</p>
                                </div>

                            <?php else: ?>

                                <form method="post" action="<?= htmlspecialchars($self) ?>" id="permForm"
                                    data-locked="<?= $isAdminRole ? '1' : '0' ?>" data-total="<?= (int)$totalPages ?>">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['perm_csrf']) ?>">
                                    <input type="hidden" name="role" value="<?= htmlspecialchars($selectedRole) ?>">
                                    <input type="hidden" name="savePermissions" value="1">

                                    <?php if ($isAdminRole): ?>
                                        <div class="pm-note info" role="note">
                                            <i class="fas fa-lock"></i>
                                            <span>Administrator always has access to every page, so this role is read-only. This prevents anyone from locking themselves out.</span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="pm-bar-top">
                                        <div>
                                            <p class="pm-who"><span class="pm-dot"></span> Editing role</p>
                                            <h2 class="pm-work-title"><?= htmlspecialchars($roles[$selectedRole]) ?></h2>
                                            <div class="pm-meter">
                                                <div class="pm-meter-row">
                                                    <span>Pages allowed</span>
                                                    <span><b id="pmSel"><?= (int)$selectedCount ?></b> <span class="pm-mono">/ <?= (int)$totalPages ?></span></span>
                                                </div>
                                                <div class="pm-track">
                                                    <div class="pm-fill" id="pmFill"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pm-field">
                                            <label class="pm-label" for="pmFilter">Filter pages</label>
                                            <div class="pm-input-wrap">
                                                <i class="fas fa-search"></i>
                                                <input type="text" id="pmFilter" class="pm-input" placeholder="Name or page address" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (!$isAdminRole): ?>
                                        <div class="pm-tools">
                                            <button type="button" class="pm-btn" id="checkAll"><i class="fas fa-check-double"></i> Select all</button>
                                            <button type="button" class="pm-btn" id="uncheckAll"><i class="fas fa-times"></i> Clear all</button>
                                        </div>
                                    <?php endif; ?>

                                    <div class="pm-groups">
                                        <?php $gi = 0;
                                        foreach ($pageCatalog as $groupName => $items):
                                            $gTotal = count($items);
                                            $gOn = 0;
                                            foreach ($items as $slug => $meta) {
                                                if ($isAdminRole || isset($checked[$slug])) $gOn++;
                                            }
                                        ?>
                                            <section class="pm-group" style="--i: <?= $gi++ ?>">
                                                <div class="pm-group-head">
                                                    <h3 class="pm-group-name"><?= htmlspecialchars($groupName) ?></h3>
                                                    <span class="pm-group-count"><span data-g-on><?= $gOn ?></span>/<?= $gTotal ?></span>
                                                    <?php if (!$isAdminRole): ?>
                                                        <button type="button" class="pm-group-toggle" data-group-toggle>Toggle group</button>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="pm-rows">
                                                    <?php foreach ($items as $slug => $meta):
                                                        $on = ($isAdminRole || isset($checked[$slug]));
                                                        $wasSaved = ($isAdminRole || isset($saved[$slug]));
                                                    ?>
                                                        <label class="pm-row <?= $on ? 'is-on' : '' ?>" data-search="<?= htmlspecialchars(strtolower($meta[0] . ' ' . $slug)) ?>">
                                                            <span class="pm-row-ico"><i class="fas <?= htmlspecialchars($meta[1]) ?>"></i></span>
                                                            <span>
                                                                <span class="pm-row-name"><?= htmlspecialchars($meta[0]) ?></span>
                                                                <span class="pm-row-slug"><?= htmlspecialchars($slug) ?></span>
                                                            </span>
                                                            <input type="checkbox" class="pm-switch" name="pages[]"
                                                                value="<?= htmlspecialchars($slug) ?>"
                                                                data-saved="<?= $wasSaved ? '1' : '0' ?>"
                                                                aria-label="<?= htmlspecialchars($meta[0]) ?>"
                                                                <?= $on ? 'checked' : '' ?>
                                                                <?= $isAdminRole ? 'disabled' : '' ?>>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </section>
                                        <?php endforeach; ?>

                                        <div class="pm-empty" id="pmNoMatch" hidden>
                                            <div class="pm-empty-ico"><i class="fas fa-search"></i></div>
                                            <h2>No pages match</h2>
                                            <p>Nothing matches that filter. Try a shorter word, or clear the field to see every page again.</p>
                                        </div>
                                    </div>

                                    <?php if (!$isAdminRole): ?>
                                        <div class="pm-savebar" id="pmBar" role="region" aria-label="Unsaved changes">
                                            <span class="pm-savebar-text" id="pmBarText"></span>
                                            <button type="button" class="pm-btn is-ghost" id="pmDiscard">Discard</button>
                                            <button type="submit" class="pm-btn is-primary" id="pmSave"><i class="fas fa-save"></i> <span>Save changes</span></button>
                                        </div>
                                    <?php endif; ?>
                                </form>

                            <?php endif; ?>
                        </main>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        var form = document.getElementById('permForm');
        if (!form) return;

        var locked = form.getAttribute('data-locked') === '1';
        var total = parseInt(form.getAttribute('data-total'), 10) || 1;
        var checks = [].slice.call(form.querySelectorAll('.pm-switch'));
        var groups = [].slice.call(form.querySelectorAll('.pm-group'));
        var selEl = document.getElementById('pmSel');
        var fillEl = document.getElementById('pmFill');
        var bar = document.getElementById('pmBar');
        var barText = document.getElementById('pmBarText');
        var saveBtn = document.getElementById('pmSave');
        var filter = document.getElementById('pmFilter');
        var noMatch = document.getElementById('pmNoMatch');
        var submitting = false;

        function isDirty() {
            return checks.some(function(c) {
                return (c.checked ? '1' : '0') !== c.getAttribute('data-saved');
            });
        }

        function refresh() {
            var on = 0,
                added = 0,
                removed = 0;

            checks.forEach(function(c) {
                var was = c.getAttribute('data-saved') === '1';
                var row = c.closest('.pm-row');
                if (c.checked) on++;
                if (c.checked && !was) added++;
                if (!c.checked && was) removed++;
                row.classList.toggle('is-on', c.checked);
                row.classList.toggle('is-changed', c.checked !== was);
            });

            if (selEl) selEl.textContent = on;
            if (fillEl) fillEl.style.transform = 'scaleX(' + (on / total) + ')';

            groups.forEach(function(g) {
                var boxes = g.querySelectorAll('.pm-switch');
                var n = 0;
                [].forEach.call(boxes, function(b) {
                    if (b.checked) n++;
                });
                g.querySelector('[data-g-on]').textContent = n;
                var t = g.querySelector('[data-group-toggle]');
                if (t) t.textContent = (n === boxes.length) ? 'Clear group' : 'Select group';
            });

            if (!locked && bar) {
                var changes = added + removed;
                bar.classList.toggle('is-visible', changes > 0);
                if (changes > 0) {
                    var parts = [];
                    if (added) parts.push('<b>' + added + '</b> added');
                    if (removed) parts.push('<b>' + removed + '</b> removed');
                    barText.innerHTML = 'Unsaved: ' + parts.join(', ');
                }
            }
        }

        function setVisible(state) {
            checks.forEach(function(c) {
                if (c.disabled) return;
                if (c.closest('.pm-row').hidden) return;
                c.checked = state;
            });
            refresh();
        }

        var all = document.getElementById('checkAll');
        var none = document.getElementById('uncheckAll');
        if (all) all.addEventListener('click', function() {
            setVisible(true);
        });
        if (none) none.addEventListener('click', function() {
            setVisible(false);
        });

        [].forEach.call(form.querySelectorAll('[data-group-toggle]'), function(btn) {
            btn.addEventListener('click', function() {
                var boxes = [].slice.call(btn.closest('.pm-group').querySelectorAll('.pm-switch'));
                var allOn = boxes.every(function(b) {
                    return b.checked;
                });
                boxes.forEach(function(b) {
                    if (!b.disabled) b.checked = !allOn;
                });
                refresh();
            });
        });

        checks.forEach(function(c) {
            c.addEventListener('change', refresh);
        });

        /* filter */
        if (filter) {
            filter.addEventListener('input', function() {
                var q = filter.value.trim().toLowerCase();
                var anyVisible = false;
                groups.forEach(function(g) {
                    var shown = 0;
                    [].forEach.call(g.querySelectorAll('.pm-row'), function(r) {
                        var match = q === '' || r.getAttribute('data-search').indexOf(q) !== -1;
                        r.hidden = !match;
                        if (match) shown++;
                    });
                    g.hidden = shown === 0;
                    if (shown) anyVisible = true;
                });
                if (noMatch) noMatch.hidden = anyVisible;
            });
        }

        /* discard */
        var discard = document.getElementById('pmDiscard');
        if (discard) discard.addEventListener('click', function() {
            checks.forEach(function(c) {
                c.checked = c.getAttribute('data-saved') === '1';
            });
            refresh();
        });

        /* saving state (prevents double submit) */
        form.addEventListener('submit', function(e) {
            if (submitting) {
                e.preventDefault();
                return;
            }
            submitting = true;
            if (saveBtn) {
                saveBtn.classList.add('is-loading');
                saveBtn.querySelector('span').textContent = 'Saving';
            }
        });

        /* warn before switching role with unsaved changes */
        [].forEach.call(document.querySelectorAll('.pm-role'), function(a) {
            a.addEventListener('click', function(e) {
                if (!locked && isDirty() && !submitting) {
                    if (!confirm('You have unsaved changes. Discard them and switch role?')) {
                        e.preventDefault();
                    }
                }
            });
        });

        refresh();
    })();
</script>

</body>

</html>