<?php
// ---- values for the top bar (escaped, with safe fallbacks) ----
$gl_name = trim((string)($_SESSION['admin_name'] ?? ''));
if ($gl_name === '') {
    $gl_name = 'Admin';
}
$gl_role_raw = strtolower(trim((string)($_SESSION['role'] ?? '')));
$gl_role_map = [
    'admin'              => 'Admin',
    'finance'            => 'Finance',
    'invitation'         => 'Invitation',
    'registrationdesk'   => 'Registration Desk',
    'clothcollectreturn' => 'Cloth Collection/Return',
];
$gl_role = $gl_role_map[$gl_role_raw] ?? ($gl_role_raw !== '' ? ucfirst($gl_role_raw) : 'Staff');

$gl_parts = preg_split('/\s+/', $gl_name, -1, PREG_SPLIT_NO_EMPTY);
$gl_sub   = function_exists('mb_substr') ? 'mb_substr' : 'substr';
$gl_ini   = strtoupper($gl_sub($gl_parts[0] ?? 'A', 0, 1));
if (count($gl_parts) > 1) {
    $gl_ini .= strtoupper($gl_sub(end($gl_parts), 0, 1));
}
?>

<!---------------- Custom styles for this template---------------------------->
<link href="css/new_style_css/topnav.css" rel="stylesheet">

<nav class="gl-topbar" aria-label="Top bar">

    <!-- Sidebar toggle (mobile). The id is used by the sidebar script. -->
    <button id="sidebarToggleTop" type="button" class="gl-burger d-md-none" aria-label="Toggle menu">
        <i class="fa fa-bars"></i>
    </button>

    <div class="gl-event">
        <span class="gl-event-ico"><i class="fas fa-graduation-cap"></i></span>
        <span class="gl-event-t">BMS Graduation - 2026 Nov</span>
    </div>

    <div class="gl-grow"></div>

    <div class="gl-clock" id="glClock" title="Local date and time">
        <i class="far fa-clock"></i><span id="glClockTxt"></span>
    </div>

    <div class="gl-sep"></div>

    <div class="gl-user" id="glUser">
        <button type="button" class="gl-user-btn" id="glUserBtn" aria-haspopup="true" aria-expanded="false">
            <span class="gl-av" aria-hidden="true"><?= htmlspecialchars($gl_ini) ?></span>
            <span class="gl-user-meta">
                <span class="gl-user-n"><?= htmlspecialchars($gl_name) ?></span>
                <span class="gl-user-r"><?= htmlspecialchars($gl_role) ?></span>
            </span>
            <i class="fas fa-chevron-down gl-chev" aria-hidden="true"></i>
        </button>

        <div class="gl-menu" id="glMenu" role="menu">
            <div class="gl-menu-h">
                <span class="gl-av" aria-hidden="true"><?= htmlspecialchars($gl_ini) ?></span>
                <div>
                    <div class="gl-menu-n"><?= htmlspecialchars($gl_name) ?></div>
                    <span class="gl-menu-r"><?= htmlspecialchars($gl_role) ?></span>
                </div>
            </div>
            <a class="gl-item is-out" href="logout" role="menuitem">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</nav>

<script>
    /* Self-contained: works on every page, with or without Bootstrap 4/5 loaded. */
    (function() {
        if (window.__glTopnav) return;
        window.__glTopnav = true;

        var box = document.getElementById('glUser');
        var btn = document.getElementById('glUserBtn');
        if (!box || !btn) return;

        function setOpen(open) {
            box.classList.toggle('is-open', open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            setOpen(!box.classList.contains('is-open'));
        });
        document.addEventListener('click', function(e) {
            if (!box.contains(e.target)) setOpen(false);
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') setOpen(false);
        });

        // live date / time
        var txt = document.getElementById('glClockTxt');

        function tick() {
            if (!txt) return;
            var d = new Date();
            txt.textContent =
                d.toLocaleDateString('en-GB', {
                    weekday: 'short',
                    day: '2-digit',
                    month: 'short'
                }) + ' · ' +
                d.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        }
        tick();
        setInterval(tick, 15000);
    })();
</script>