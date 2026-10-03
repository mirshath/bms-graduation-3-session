<?php
// Current page (works with or without ".php" and ignores ?query=strings)
$current_url = basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', ".php");
$role = $_SESSION['role'] ?? ''; // Current user role

// ---------------------------------------------------------------------------
// Who can see what (same rules as before)
// ---------------------------------------------------------------------------
$can_front   = in_array($role, ['admin', 'registrationDesk']);                         // scan
$can_finance = in_array($role, ['admin', 'finance']);                                  // payments + extra tickets
$can_shared  = in_array($role, ['admin', 'finance', 'invitation', 'registrationDesk']); // payment success + students
$can_invite  = in_array($role, ['admin', 'invitation']);                               // invitation issue + DOB updates
$can_cloak   = in_array($role, ['admin', 'clothCollectReturn']);                       // cloak
$is_admin    = ($role === 'admin');                                                    // admin only

// ---------------------------------------------------------------------------
// Signed-in user card (bottom of the sidebar)
// ---------------------------------------------------------------------------
$role_labels = [
    'admin'             => 'Administrator',
    'finance'           => 'Finance',
    'registrationDesk'  => 'Registration desk',
    'invitation'        => 'Invitation desk',
    'clothCollectReturn' => 'Cloak desk',
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

    <!-- ============================================ -->
    <!-- COMMON: Visible to ALL ROLES -->
    <!-- ============================================ -->
    <!-- <?php $nav_label('Overview'); ?> -->
    <?php $nav_link('index', 'fa-tachometer-alt', 'Dashboard'); ?>

    <!-- ============================================ -->
    <!-- REGISTRATION DESK ROLE -->
    <!-- Roles: admin, registrationDesk -->
    <!-- ============================================ -->
    <?php if ($can_front): ?>
        <?php $nav_label('Front desk'); ?>
        <?php $nav_link('scan', 'fa-barcode', 'Scan Attendance Table'); ?>
    <?php endif; ?>

    <!-- ============================================ -->
    <!-- FINANCE ROLE -->
    <!-- Roles: admin, finance -->
    <!-- ============================================ -->
    <?php if ($can_finance): ?>
        <?php $nav_label('Payments'); ?>
        <!-- <?php $nav_link('payment_reg', 'fa-credit-card', 'Payment Registration'); ?> -->
        <!-- <?php $nav_link('payment-reports', 'fa-chart-line', 'Payment Report'); ?> -->

        <!-- Payment  Dropdown -->
        <?php $nav_menu('paymentSectionDropdown', 'collapsePaymentDropDwn', 'fa-ticket-alt', 'Payment', [
            ['payment_reg', 'fa-credit-card', 'Payment Registration'],
            ['payment-reports', 'fa-chart-line', 'Payment Report'],
        ]); ?>

        <!-- Extra Ticket Dropdown -->
        <?php $nav_menu('extraTicketDropdown', 'collapseExtraTicket', 'fa-ticket-alt', 'Extra Ticket', [
            ['extra-ticket-buying', 'fa-cart-plus', 'Buy Extra Ticket'],
            ['extra-ticket-data',   'fa-list',      'Ticket Log / Data'],
        ]); ?>

        <!-- Ticket isses & Invitaion Showing  -->
        <!-- <?php $nav_link('live_scan.php', 'fa-ticket-alt', 'Ticket & Invitation Issues'); ?> -->
        <?php $nav_link('ticket_invitation_issue', 'fa-ticket-alt', 'Ticket & Invitation Issues'); ?>

    <?php endif; ?>

    <!-- ============================================ -->
    <!-- STUDENTS: finance, invitation, registration desk (+ invitation issue, DOB) -->
    <!-- ============================================ -->
    <?php if ($can_shared || $can_invite): ?>
        <!-- <?php $nav_label('Students'); ?> -->
        <?php $nav_label(' '); ?>
    <?php endif; ?>

    <?php if ($can_shared): ?>
        <?php $nav_link('payment_success_students', 'fa-check-circle', 'Payment Success Student'); ?>

        <!-- Students Dropdown -->
        <?php $nav_menu('studentsDropdown', 'collapseStudents', 'fa-users-cog', 'Students', [
            ['oldStudentsDB',     'fa-history',       'Old Students'],
            ['registeredStudents', 'fa-user-graduate', 'Registered Students'],
        ]); ?>
    <?php endif; ?>

    <!-- INVITATION ROLE: admin, invitation -->
    <?php if ($can_invite): ?>

        <?php $nav_link('oldStudentsDOBupdate', 'fa-calendar-alt', 'DOB Updates'); ?>
    <?php endif; ?>

    <!-- ============================================ -->
    <!-- CLOTH COLLECTION/RETURN ROLE -->
    <!-- Roles: admin, clothCollectReturn -->
    <!-- ============================================ -->
    <?php if ($can_cloak): ?>
        <!-- <?php $nav_label('Cloak room'); ?> -->
        <?php $nav_menu('cloakDropdown', 'collapseCloak', 'fa-tshirt', 'Cloak', [
            ['clothCollection', 'fa-tshirt',    'Cloak Issuing'],
            ['cloakReturn',     'fa-undo',      'Cloak Collecting'],
            ['cloakReport',     'fa-chart-bar', 'Cloak Report'],
        ]); ?>
    <?php endif; ?>

    <!-- ============================================ -->
    <!-- ADMIN ONLY -->
    <!-- ============================================ -->
    <?php if ($is_admin): ?>
        <?php $nav_label('Administration'); ?>
        <?php $nav_link('user', 'fa-user-plus', 'User Create'); ?>

        <!-- Bulk Actions Dropdown -->
        <?php $nav_menu('bulkDropdown', 'collapseBulk', 'fa-tasks', 'Bulk Actions', [
            ['bulk-data',       'fa-upload',   'Bulk Upload'],
            ['bulk-data-email', 'fa-envelope', 'Bulk Email'],
            ['view_email_logs', 'fa-list-alt', 'Logs For Seat No'],
        ]); ?>

        <?php $nav_link('mark_graduate', 'fa-graduation-cap', 'Graduate Std Mark'); ?>
        <?php $nav_link('invitation-collection', 'fa-envelope-open', 'Invitation Issue'); ?>

        <?php $nav_label('Reports'); ?>
        <?php $nav_link('allocatedSeatOrder', 'fa-chair', 'Report Seat Order'); ?>
        <?php $nav_link('meals_report', 'fa-utensils', 'Report Meals'); ?>
    <?php endif; ?>

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