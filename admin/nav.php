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

<style>
    /* =====================================================================
       Sidebar theme  (change the colors here, everything follows)
       ===================================================================== */
    #accordionSidebar {
        --nav-bg-top: #182a51;
        --nav-bg-mid: #0f2e71;
        --nav-bg-bot: #03143d;
        --nav-line: rgba(255, 255, 255, .09);
        --nav-ink: #c6d0e3;
        --nav-mute: #7d8db0;
        --nav-accent: #a70000;
        --nav-accent-soft: #7f9df0;
        --nav-pop: #1d2d4d;

        font-family: 'Public Sans', system-ui, sans-serif;
        background: linear-gradient(180deg, var(--nav-bg-top) 0%, var(--nav-bg-mid) 38%, var(--nav-bg-bot) 100%) !important;
        position: sticky;
        top: 0;
        align-self: flex-start;
        height: 100vh;
        overflow-x: hidden;
        overflow-y: auto;
        padding-bottom: 14px;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, .18) transparent;
        -webkit-font-smoothing: antialiased;
    }

    #accordionSidebar::-webkit-scrollbar {
        width: 6px;
    }

    #accordionSidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, .16);
        border-radius: 6px;
    }

    /* ---------------- brand ---------------- */
    #accordionSidebar .sidebar-brand {
        height: auto !important;
        padding: 22px 18px 14px !important;
        margin: 0 !important;
        display: flex;
        justify-content: center;
    }

    #accordionSidebar .sidebar-brand-text {
        margin: 0 !important;
        width: 100%;
        padding: 12px 14px;
        border: 1px solid var(--nav-line);
        border-radius: 14px;
        background: rgba(255, 255, 255, .05);
    }

    #accordionSidebar .sidebar-brand-text img {
        display: block;
        width: 100%;
        max-width: 100%;
        max-height: 60px;
        height: auto;
        margin: 0;
        object-fit: contain;
        box-shadow: none !important;
    }

    #accordionSidebar .sidebar-brand-icon {
        display: none;
    }

    /* ---------------- group labels ---------------- */
    #accordionSidebar .nav-group {
        list-style: none;
        padding: 20px 26px 6px;
    }

    #accordionSidebar .nav-group span {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--nav-mute);
    }

    /* ---------------- links ---------------- */
    #accordionSidebar .nav-item {
        margin: 0;
    }

    #accordionSidebar .nav-item .nav-link {
        display: flex !important;
        align-items: center;
        gap: 12px;
        width: auto !important;
        margin: 2px 12px;
        padding: 10px 12px !important;
        border: 0;
        border-radius: 10px;
        color: var(--nav-ink) !important;
        font-size: 14px;
        font-weight: 500;
        text-align: left;
        line-height: 1.25;
        transition: background-color .2s ease, color .2s ease, box-shadow .2s ease;
    }

    #accordionSidebar .nav-item .nav-link i {
        flex: 0 0 auto;
        width: 20px;
        margin: 0 !important;
        font-size: 15px !important;
        text-align: center;
        color: var(--nav-mute);
        transition: color .2s ease;
    }

    #accordionSidebar .nav-item .nav-link span {
        font-size: 14px !important;
        min-width: 0;
    }

    #accordionSidebar .nav-item .nav-link:hover,
    #accordionSidebar .nav-item:hover>.nav-link {
        background: rgba(255, 255, 255, .07);
        color: #fff !important;
        font-weight: 500;
        border: 0;
    }

    #accordionSidebar .nav-item .nav-link:hover i {
        color: #fff;
    }

    /* active page */
    #accordionSidebar .nav-item.active>.nav-link {
        background: var(--nav-accent);
        color: #fff !important;
        font-weight: 600;
        border: 0;
        box-shadow: 0 8px 18px rgba(31, 75, 182, .38);
    }

    #accordionSidebar .nav-item.active>.nav-link i {
        color: #fff;
    }

    /* a dropdown that contains the active page: calmer, the sub item carries the accent */
    #accordionSidebar .nav-item--menu.active>.nav-link {
        background: rgba(255, 255, 255, .09);
        box-shadow: none;
    }

    #accordionSidebar .nav-item--menu.active>.nav-link i {
        color: var(--nav-accent-soft);
    }

    #accordionSidebar .nav-link:focus-visible,
    #accordionSidebar .collapse-item:focus-visible {
        outline: 2px solid var(--nav-accent-soft);
        outline-offset: 2px;
    }

    /* dropdown chevron */
    #accordionSidebar .nav-link[data-toggle="collapse"]::after {
        content: '\f107';
        font-family: 'Font Awesome 5 Free', 'Font Awesome 6 Free', 'FontAwesome';
        font-weight: 900;
        margin-left: auto;
        float: none;
        width: auto;
        border: 0;
        font-size: 12px;
        color: var(--nav-mute);
        transition: transform .25s ease;
    }

    #accordionSidebar .nav-link[data-toggle="collapse"].collapsed::after {
        transform: rotate(-90deg);
    }

    /* ---------------- sub menu ---------------- */
    #accordionSidebar .collapse-inner {
        min-width: 0 !important;
        margin: 2px 2px 3px 9px !important;
        padding: 2px 0 2px 12px !important;
        border-left: 1px solid var(--nav-line);
        border-radius: 0 !important;
        background: transparent !important;
    }

    #accordionSidebar .collapse-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 1px 0 !important;
        padding: 8px 12px !important;
        border-radius: 8px !important;
        color: var(--nav-ink) !important;
        font-size: 13px;
        font-weight: 500;
        white-space: normal;
        transition: background-color .2s ease, color .2s ease;
    }

    #accordionSidebar .collapse-item i {
        width: 16px;
        margin: 0 !important;
        font-size: 12px;
        color: var(--nav-mute);
        text-align: center;
    }

    #accordionSidebar .collapse-item:hover {
        background: rgba(255, 255, 255, .07) !important;
        color: #fff !important;
        font-weight: 500;
    }

    #accordionSidebar .collapse-item:hover i {
        color: #fff;
    }

    #accordionSidebar .collapse-item.active {
        background: var(--nav-accent) !important;
        color: #fff !important;
        font-weight: 600;
        border-radius: 8px !important;
    }

    #accordionSidebar .collapse-item.active i {
        color: #fff;
    }

    /* ---------------- user card ---------------- */
    #accordionSidebar .nav-profile {
        list-style: none;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: auto 12px 0;
        padding: 12px;
        border: 1px solid var(--nav-line);
        border-radius: 14px;
        background: rgba(255, 255, 255, .05);
    }

    #accordionSidebar .nav-profile-avatar {
        position: relative;
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 38px;
        height: 38px;
        border-radius: 11px;
        background: var(--nav-accent);
        color: #fff;
        font-size: 15px;
        font-weight: 700;
    }

    #accordionSidebar .nav-profile-avatar::after {
        content: "";
        position: absolute;
        right: -3px;
        bottom: -3px;
        width: 11px;
        height: 11px;
        border-radius: 50%;
        background: #2f9e66;
        border: 2px solid #17233d;
    }

    #accordionSidebar .nav-profile-txt {
        min-width: 0;
        line-height: 1.25;
    }

    #accordionSidebar .nav-profile-name {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 13px;
        font-weight: 600;
        color: #fff;
    }

    #accordionSidebar .nav-profile-role {
        display: block;
        font-size: 12px;
        color: var(--nav-mute);
    }

    /* =====================================================================
       Collapsed (icon rail) mode - the SB Admin "toggle" button
       ===================================================================== */
    #accordionSidebar.toggled {
        overflow: visible;
    }

    #accordionSidebar.toggled .sidebar-brand {
        padding: 22px 0 12px !important;
    }

    #accordionSidebar.toggled .sidebar-brand-text {
        display: none !important;
    }

    #accordionSidebar.toggled .sidebar-brand-icon {
        display: grid !important;
        place-items: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--nav-accent);
        color: #fff;
        font-size: 19px;
        transform: none;
    }

    #accordionSidebar.toggled .nav-group {
        height: 1px;
        margin: 10px 22px;
        padding: 0;
        background: var(--nav-line);
    }

    #accordionSidebar.toggled .nav-group span,
    #accordionSidebar.toggled .nav-item .nav-link span,
    #accordionSidebar.toggled .nav-link[data-toggle="collapse"]::after,
    #accordionSidebar.toggled .nav-profile-txt {
        display: none !important;
    }

    #accordionSidebar.toggled .nav-item .nav-link {
        justify-content: center;
        margin: 2px 10px;
        padding: 12px 0 !important;
    }

    #accordionSidebar.toggled .nav-item .nav-link i {
        font-size: 17px !important;
    }

    #accordionSidebar.toggled .nav-profile {
        justify-content: center;
        margin: auto 10px 0;
        padding: 10px 0;
    }

    /* pop-out sub menu next to the rail */
    #accordionSidebar.toggled .nav-item .collapse .collapse-inner {
        min-width: 13rem !important;
        margin: 0 !important;
        padding: 8px !important;
        border: 1px solid var(--nav-line);
        border-radius: 14px !important;
        background: var(--nav-pop) !important;
        box-shadow: 0 16px 36px rgba(0, 0, 0, .38);
    }

    @media (prefers-reduced-motion: reduce) {

        #accordionSidebar .nav-link,
        #accordionSidebar .collapse-item,
        #accordionSidebar .nav-link::after {
            transition: none;
        }
    }
</style>

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