<?php
// Current page (works with or without ".php" and ignores ?query=strings)
$current_url = basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', ".php");
$role = $_SESSION['role'] ?? ''; // Current user role
$is_admin = ($role === 'admin');  // admin always sees everything

// ---------------------------------------------------------------------------
// Database connection (normally already included by the page)
// ---------------------------------------------------------------------------
if (!isset($conn) || !($conn instanceof mysqli)) {
    include_once(__DIR__ . "/../database/connection.php");
}

// ---------------------------------------------------------------------------
// Load the pages this role is allowed to see (table: role_permissions)
// ---------------------------------------------------------------------------
$allowed_pages = [];
if (!$is_admin && $role !== '' && isset($conn) && $conn instanceof mysqli) {
    try {
        $perm_stmt = $conn->prepare("SELECT page_slug FROM role_permissions WHERE role = ?");
        if ($perm_stmt) {
            $perm_stmt->bind_param("s", $role);
            $perm_stmt->execute();
            $perm_stmt->bind_result($perm_slug);
            while ($perm_stmt->fetch()) {
                $allowed_pages[$perm_slug] = true;
            }
            $perm_stmt->close();
        }
    } catch (Throwable $e) {
        $allowed_pages = []; // table missing / DB error -> show nothing
    }
}

// true when the current role may see this page
$nav_can = function ($slug) use ($is_admin, $allowed_pages) {
    return $is_admin || isset($allowed_pages[$slug]);
};

// ---------------------------------------------------------------------------
// Signed-in user card (bottom of the sidebar)
// ---------------------------------------------------------------------------
$role_labels = [
    'admin'              => 'Administrator',
    'finance'            => 'Finance',
    'registrationDesk'   => 'Registration desk',
    'invitation'         => 'Invitation desk',
    'clothCollectReturn' => 'Cloak desk',
    'coordinator'        => 'Coordinator',
];
$nav_user = trim((string)($_SESSION['admin_name'] ?? ''));
if ($nav_user === 'Unknown Admin') {
    $nav_user = '';
}
$nav_role_label = $role_labels[$role] ?? ($role !== '' ? ucfirst($role) : 'Staff');
$nav_initial = $nav_user !== '' ? strtoupper(substr($nav_user, 0, 1)) : strtoupper(substr($nav_role_label, 0, 1));

// ---------------------------------------------------------------------------
// Small render helpers (closures, so including this file twice is safe)
// ---------------------------------------------------------------------------
$nav_label = function ($text) {
    echo '<li class="nav-group" role="presentation"><span>' . htmlspecialchars($text) . '</span></li>';
};

$nav_link = function ($slug, $icon, $label) use ($current_url) {
    $active = ($current_url === $slug);
?>
    <li class="nav-item <?= $active ? 'active' : '' ?> nav-hover">
        <a class="nav-link" href="<?= $slug ?>" title="<?= htmlspecialchars($label) ?>" <?= $active ? 'aria-current="page"' : '' ?>>
            <i class="fas fa-fw <?= $icon ?>"></i>
            <span><?= htmlspecialchars($label) ?></span>
        </a>
    </li>
<?php
};

// $items = [ [slug, icon, label], ... ]  (slug is also the page link)
$nav_menu = function ($toggle_id, $collapse_id, $icon, $label, $items) use ($current_url) {
    $slugs  = array_column($items, 0);
    $active = in_array($current_url, $slugs);
?>
    <li class="nav-item nav-item--menu dropdown <?= $active ? 'active show' : '' ?> nav-hover">
        <a class="nav-link dropdown-toggle <?= $active ? '' : 'collapsed' ?>"
            href="#"
            id="<?= $toggle_id ?>"
            role="button"
            title="<?= htmlspecialchars($label) ?>"
            data-toggle="collapse"
            data-target="#<?= $collapse_id ?>"
            aria-expanded="<?= $active ? 'true' : 'false' ?>"
            aria-controls="<?= $collapse_id ?>">
            <i class="fas fa-fw <?= $icon ?>"></i>
            <span><?= htmlspecialchars($label) ?></span>
        </a>
        <div id="<?= $collapse_id ?>"
            class="collapse <?= $active ? 'show' : '' ?>"
            aria-labelledby="<?= $toggle_id ?>"
            data-parent="#accordionSidebar">
            <div class="collapse-inner">
                <?php foreach ($items as $it): ?>
                    <a class="collapse-item <?= ($current_url === $it[0]) ? 'active' : '' ?>" href="<?= $it[0] ?>" <?= ($current_url === $it[0]) ? 'aria-current="page"' : '' ?>>
                        <i class="fas fa-fw <?= $it[1] ?>"></i> <span><?= htmlspecialchars($it[2]) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </li>
<?php
};

// ---------------------------------------------------------------------------
// Full menu definition. Anything not allowed for the role is filtered out.
// link : ['type'=>'link', 'slug', 'icon', 'label']
// menu : ['type'=>'menu', 'toggle', 'collapse', 'icon', 'label', 'items'=>[[slug, icon, label], ...]]
// ---------------------------------------------------------------------------
$nav_structure = [
    [
        'label' => null,
        'entries' => [
            ['type' => 'link', 'slug' => 'index', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
        ],
    ],
    [
        'label' => 'Front desk',
        'entries' => [
            ['type' => 'link', 'slug' => 'scan', 'icon' => 'fa-barcode', 'label' => 'Scan Attendance Table'],
        ],
    ],
    [
        'label' => 'Finance',
        'entries' => [
            ['type' => 'menu', 'toggle' => 'paymentSectionDropdown', 'collapse' => 'collapsePaymentDropDwn', 'icon' => 'fa-ticket-alt', 'label' => 'Finance', 'items' => [
                ['payment_reg', 'fa-credit-card', 'Payment Registration'],
                ['payment-reports', 'fa-chart-line', 'Payment Report'],
                ['payment_success_students', 'fa-check-circle', 'Payment Success Student'],
            ]],
            ['type' => 'menu', 'toggle' => 'extraTicketDropdown', 'collapse' => 'collapseExtraTicket', 'icon' => 'fa-ticket-alt', 'label' => 'Extra Ticket', 'items' => [
                ['extra-ticket-buying', 'fa-cart-plus', 'Buy Extra Ticket'],
                ['extra-ticket-data', 'fa-list', 'Ticket Log / Data'],
            ]],
            ['type' => 'link', 'slug' => 'ticket_invitation_issue', 'icon' => 'fa-ticket-alt', 'label' => 'Ticket & Invitation Issues'],
        ],
    ],
    [
        'label' => ' ',
        'entries' => [
            ['type' => 'menu', 'toggle' => 'studentsDropdown', 'collapse' => 'collapseStudents', 'icon' => 'fa-users-cog', 'label' => 'Students', 'items' => [
                ['oldStudentsDB', 'fa-history', 'Old Students'],
                ['registeredStudents', 'fa-user-graduate', 'Registered Students'],
                ['upload_student_db', 'fa-user-graduate', 'Upload Student'],
                ['oldStudentsDOBupdate', 'fa-calendar-alt', 'Edit Student'],
            ]],
        ],
    ],
    [
        'label' => null,
        'entries' => [
            ['type' => 'menu', 'toggle' => 'cloakDropdown', 'collapse' => 'collapseCloak', 'icon' => 'fa-tshirt', 'label' => 'Cloak', 'items' => [
                ['clothCollection', 'fa-tshirt', 'Cloak Issuing'],
                ['cloakReturn', 'fa-undo', 'Cloak Collecting'],
                ['cloakReport', 'fa-chart-bar', 'Cloak Report'],
            ]],
        ],
    ],
    [
        'label' => 'Administration',
        'entries' => [
            ['type' => 'link', 'slug' => 'user', 'icon' => 'fa-user-plus', 'label' => 'User Create'],
            ['type' => 'link', 'slug' => 'permission', 'icon' => 'fa-user-lock', 'label' => 'Role Permissions'],
            ['type' => 'link', 'slug' => 'portal_active', 'icon' => 'fa-user-lock', 'label' => 'Portal Active'],
            ['type' => 'menu', 'toggle' => 'bulkDropdown', 'collapse' => 'collapseBulk', 'icon' => 'fa-tasks', 'label' => 'Bulk Actions', 'items' => [
                ['bulk-data', 'fa-upload', 'Bulk Upload'],
                ['bulk-data-email', 'fa-envelope', 'Bulk Email'],
                ['view_email_logs', 'fa-list-alt', 'Logs For Seat No'],
            ]],
            ['type' => 'link', 'slug' => 'mark_graduate', 'icon' => 'fa-graduation-cap', 'label' => 'Graduate Std Mark'],
            ['type' => 'link', 'slug' => 'invitation-collection', 'icon' => 'fa-envelope-open', 'label' => 'Invitation Issue'],
        ],
    ],
    [
        'label' => 'Reports',
        'entries' => [
            ['type' => 'link', 'slug' => 'allocatedSeatOrder', 'icon' => 'fa-chair', 'label' => 'Report Seat Order'],
            ['type' => 'link', 'slug' => 'meals_report', 'icon' => 'fa-utensils', 'label' => 'Report Meals'],
        ],
    ],
];
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<!---------------- Custom styles for this template---------------------------->
<link href="css/new_style_css/nav.css" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index">
        <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-graduation-cap"></i></div>
        <div class="sidebar-brand-text mx-3"><img src="img/logo4.png" class="img-fluid" alt="Logo"></div>
    </a>

    <?php
    // Render ONLY what this role has in the role_permissions table
    foreach ($nav_structure as $section) {
        $visible = [];
        foreach ($section['entries'] as $entry) {
            if ($entry['type'] === 'link') {
                if ($nav_can($entry['slug'])) {
                    $visible[] = $entry;
                }
            } else {
                $items = [];
                foreach ($entry['items'] as $it) {
                    if ($nav_can($it[0])) {
                        $items[] = $it;
                    }
                }
                if ($items) {
                    $entry['items'] = $items;
                    $visible[] = $entry;
                }
            }
        }

        if (!$visible) {
            continue; // nothing allowed here -> hide the section and its label
        }

        if ($section['label'] !== null) {
            $nav_label($section['label']);
        }

        foreach ($visible as $entry) {
            if ($entry['type'] === 'link') {
                $nav_link($entry['slug'], $entry['icon'], $entry['label']);
            } else {
                $nav_menu($entry['toggle'], $entry['collapse'], $entry['icon'], $entry['label'], $entry['items']);
            }
        }
    }
    ?>

    <!-- Signed-in user -->
    <li class="nav-profile" title="<?= htmlspecialchars(($nav_user !== '' ? $nav_user . ' - ' : '') . $nav_role_label) ?>">
        <span class="nav-profile-avatar" aria-hidden="true"><?= htmlspecialchars($nav_initial) ?></span>
        <span class="nav-profile-txt">
            <span class="nav-profile-name"><?= htmlspecialchars($nav_user !== '' ? $nav_user : $nav_role_label) ?></span>
            <span class="nav-profile-role"><?= htmlspecialchars($nav_role_label) ?></span>
        </span>
    </li>

</ul>
<!-- End of Sidebar -->