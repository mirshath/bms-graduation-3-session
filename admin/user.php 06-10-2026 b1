<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");

$successMsg = $errorMsg = '';

/* ------------------------------------------------------------------
 * Handle new admin registration
 * (done BEFORE header.php so a clean redirect is possible afterwards)
 * ------------------------------------------------------------------ */
if (isset($_POST['AdminRegisterBtn'])) {
    $adminName       = trim($_POST['adminName'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $role            = $_POST['role'] ?? '';

    if ($adminName === '' || $email === '' || $password === '' || $confirmPassword === '' || $role === '') {
        $errorMsg = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $errorMsg = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirmPassword) {
        $errorMsg = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errorMsg = "Email already exists.";
            $stmt->close();
        } else {
            $stmt->close();
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insert = $conn->prepare("INSERT INTO admin (admin_name, email, password, role) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssss", $adminName, $email, $hashedPassword, $role);
            if ($insert->execute()) {
                $insert->close();
                // Post/Redirect/Get: refreshing the page will not re-submit the form
                header("Location: " . basename($_SERVER['PHP_SELF']) . "?success=1");
                exit();
            } else {
                $errorMsg = "Database error. Please try again.";
                $insert->close();
            }
        }
    }
}

if (isset($_GET['success'])) {
    $successMsg = "Admin registered successfully!";
}

include("includes/header.php");

/* ------------------------------------------------------------------
 * Data for the page
 * ------------------------------------------------------------------ */
$roleMap = [
    'admin'              => ['Admin',                  'fa-user-shield',       'um-r-admin'],
    'finance'            => ['Finance',                'fa-coins',             'um-r-finance'],
    'invitation'         => ['Invitation',             'fa-envelope-open-text', 'um-r-invite'],
    'registrationdesk'   => ['Registration Desk',      'fa-clipboard-list',    'um-r-reg'],
    'clothcollectreturn' => ['Cloth Collection/Return', 'fa-shirt',             'um-r-cloth'],
];
function um_role($roleMap, $raw)
{
    $k = strtolower(trim((string)$raw));
    return $roleMap[$k] ?? [ucfirst((string)$raw) !== '' ? ucfirst((string)$raw) : 'Unknown', 'fa-user', 'um-r-other'];
}
function um_initials($name)
{
    $parts = preg_split('/\s+/', trim((string)$name), -1, PREG_SPLIT_NO_EMPTY);
    if (!$parts) return '?';
    $f = function_exists('mb_substr') ? 'mb_substr' : 'substr';
    $ini = strtoupper($f($parts[0], 0, 1));
    if (count($parts) > 1) $ini .= strtoupper($f(end($parts), 0, 1));
    return $ini;
}

$admins = [];
$roleCount = [];
$q = $conn->query("SELECT id, admin_name, email, role, created_at FROM admin ORDER BY id DESC");
if ($q) {
    while ($r = $q->fetch_assoc()) {
        $admins[] = $r;
        $k = strtolower(trim((string)$r['role']));
        $roleCount[$k] = ($roleCount[$k] ?? 0) + 1;
    }
}
$totalAdmins = count($admins);
?>

<style>
    .um {
        --um-brand: #3b5bdb;
        --um-brand-bg: #eef2ff;
        --um-ink: #1b2437;
        --um-muted: #6b7690;
        --um-line: #e6eaf2;
        --um-bg: #f6f8fc;
        --um-radius: 14px;
        color: var(--um-ink);
    }

    .um * {
        box-sizing: border-box;
    }

    /* page head */
    .um-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 18px;
    }

    .um-title {
        margin: 0;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .um-sub {
        margin: 4px 0 0;
        font-size: 14px;
        color: var(--um-muted);
    }

    /* stat tiles */
    .um-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .um-stat {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: #fff;
        border: 1px solid var(--um-line);
        border-radius: var(--um-radius);
    }

    .um-stat-ico {
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        width: 40px;
        height: 40px;
        border-radius: 11px;
        font-size: 16px;
    }

    .um-stat-n {
        font-size: 22px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.02em;
    }

    .um-stat-l {
        margin-top: 3px;
        font-size: 12px;
        color: var(--um-muted);
        white-space: nowrap;
    }

    .um-stat.is-total {
        background: var(--um-brand);
        border-color: var(--um-brand);
        color: #fff;
    }

    .um-stat.is-total .um-stat-ico {
        background: rgba(255, 255, 255, .18);
        color: #fff;
    }

    .um-stat.is-total .um-stat-l {
        color: rgba(255, 255, 255, .85);
    }

    /* role colours */
    .um-r-admin {
        background: #eef2ff;
        color: #3b5bdb;
    }

    .um-r-finance {
        background: #e6f6ee;
        color: #1f8a4c;
    }

    .um-r-invite {
        background: #fff4e0;
        color: #b86e00;
    }

    .um-r-reg {
        background: #e8f4fd;
        color: #1670a8;
    }

    .um-r-cloth {
        background: #f3ebfd;
        color: #7a3fc4;
    }

    .um-r-other {
        background: #eef0f4;
        color: #5a6478;
    }

    /* cards */
    .um-card {
        background: #fff;
        border: 1px solid var(--um-line);
        border-radius: var(--um-radius);
        box-shadow: 0 1px 2px rgba(20, 30, 60, .04);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .um-card-h {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 22px;
        border-bottom: 1px solid var(--um-line);
        background: #fff;
    }

    .um-card-ico {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--um-brand-bg);
        color: var(--um-brand);
    }

    .um-card-t {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
    }

    .um-card-s {
        margin: 1px 0 0;
        font-size: 12.5px;
        color: var(--um-muted);
    }

    .um-card-b {
        padding: 22px;
    }

    /* form */
    .um-label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 600;
        color: var(--um-ink);
    }

    .um-field {
        position: relative;
    }

    .um-field>i.um-fi {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        color: #97a1b8;
        pointer-events: none;
    }

    .um-field .form-control,
    .um-field .form-select {
        height: 44px;
        padding-left: 40px;
        border: 1px solid #d9dfeb;
        border-radius: 10px;
        font-size: 14px;
        background-color: #fbfcfe;
        transition: border-color .15s, box-shadow .15s, background-color .15s;
    }

    .um-field .form-control:focus,
    .um-field .form-select:focus {
        border-color: var(--um-brand);
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(59, 91, 219, .12);
    }

    .um-field .um-eye {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        color: #8a94ab;
        cursor: pointer;
    }

    .um-field .um-eye:hover {
        background: #eef1f7;
        color: var(--um-ink);
    }

    .um-field.has-eye .form-control {
        padding-right: 44px;
    }

    .um-hint {
        margin-top: 6px;
        font-size: 12px;
        color: var(--um-muted);
        min-height: 16px;
    }

    .um-meter {
        display: flex;
        gap: 4px;
        margin-top: 8px;
    }

    .um-meter span {
        flex: 1;
        height: 4px;
        border-radius: 4px;
        background: #e6eaf2;
        transition: background .2s;
    }

    .um-meter[data-s="1"] span:nth-child(-n+1) {
        background: #e5484d;
    }

    .um-meter[data-s="2"] span:nth-child(-n+2) {
        background: #f5a524;
    }

    .um-meter[data-s="3"] span:nth-child(-n+3) {
        background: #3b9ae1;
    }

    .um-meter[data-s="4"] span {
        background: #2fa66a;
    }

    .um-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 44px;
        padding: 0 22px;
        border: 0;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: transform .1s, box-shadow .15s, background .15s;
    }

    .um-btn:active {
        transform: translateY(1px);
    }

    .um-btn-primary {
        background: var(--um-brand);
        color: #fff;
        box-shadow: 0 6px 16px rgba(59, 91, 219, .28);
    }

    .um-btn-primary:hover {
        background: #2f4ac0;
        color: #fff;
    }

    .um-btn-ghost {
        background: #eef1f7;
        color: var(--um-ink);
    }

    .um-btn-ghost:hover {
        background: #e2e7f1;
    }

    .um-btn[disabled] {
        opacity: .65;
        cursor: wait;
    }

    .um-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 6px;
    }

    /* alerts */
    .um-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        margin-bottom: 18px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
    }

    .um-alert.is-ok {
        background: #e6f6ee;
        color: #176b3c;
        border: 1px solid #c4e8d3;
    }

    .um-alert.is-bad {
        background: #fdecec;
        color: #a52a2e;
        border: 1px solid #f6caca;
    }

    .um-alert button {
        margin-left: auto;
        border: 0;
        background: transparent;
        color: inherit;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        opacity: .7;
    }

    /* table */
    .um-table-wrap {
        padding: 18px 22px 8px;
    }

    #adminTable {
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
    }

    #adminTable thead th {
        padding: 12px 14px;
        background: var(--um-bg);
        border: 0;
        border-top: 1px solid var(--um-line);
        border-bottom: 1px solid var(--um-line);
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--um-muted);
        white-space: nowrap;
    }

    #adminTable thead th:first-child {
        border-left: 1px solid var(--um-line);
        border-radius: 10px 0 0 10px;
    }

    #adminTable thead th:last-child {
        border-right: 1px solid var(--um-line);
        border-radius: 0 10px 10px 0;
    }

    #adminTable tbody td {
        padding: 14px;
        border: 0;
        border-bottom: 1px solid var(--um-line);
        vertical-align: middle;
        font-size: 14px;
    }

    #adminTable tbody tr:hover td {
        background: #fafbfe;
    }

    .um-idx {
        font-variant-numeric: tabular-nums;
        color: var(--um-muted);
        font-weight: 600;
    }

    .um-user {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .um-av {
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: var(--um-brand-bg);
        color: var(--um-brand);
        font-size: 14px;
        font-weight: 700;
    }

    .um-user-n {
        font-weight: 700;
        line-height: 1.2;
    }

    .um-user-e {
        font-size: 12.5px;
        color: var(--um-muted);
        overflow-wrap: anywhere;
    }

    .um-role {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 11px;
        border-radius: 999px;
        font-size: 12.5px;
        font-weight: 600;
        white-space: nowrap;
    }

    .um-date {
        color: var(--um-muted);
        font-size: 13px;
        white-space: nowrap;
    }

    .um-ibtn {
        display: inline-grid;
        place-items: center;
        width: 34px;
        height: 34px;
        margin-right: 6px;
        border: 0;
        border-radius: 9px;
        font-size: 13px;
        cursor: pointer;
        transition: background .15s, transform .1s;
    }

    .um-ibtn:active {
        transform: scale(.95);
    }

    .um-ibtn.is-edit {
        background: #fff4e0;
        color: #b86e00;
    }

    .um-ibtn.is-edit:hover {
        background: #ffe8bf;
    }

    .um-ibtn.is-del {
        background: #fdecec;
        color: #c0353a;
        margin-right: 0;
    }

    .um-ibtn.is-del:hover {
        background: #f9d5d6;
    }

    /* DataTables polish */
    .um .dataTables_wrapper .dataTables_length,
    .um .dataTables_wrapper .dataTables_filter {
        margin-bottom: 14px;
        font-size: 13px;
        color: var(--um-muted);
    }

    .um .dataTables_wrapper .dataTables_filter input {
        height: 38px;
        min-width: 240px;
        margin-left: 8px;
        padding: 0 12px;
        border: 1px solid #d9dfeb;
        border-radius: 10px;
        background: #fbfcfe;
        outline: none;
    }

    .um .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--um-brand);
        box-shadow: 0 0 0 4px rgba(59, 91, 219, .12);
    }

    .um .dataTables_wrapper .dataTables_length select {
        height: 34px;
        border: 1px solid #d9dfeb;
        border-radius: 8px;
        padding: 0 6px;
    }

    .um .dataTables_wrapper .dataTables_info,
    .um .dataTables_wrapper .dataTables_paginate {
        padding: 14px 0;
        font-size: 13px;
        color: var(--um-muted);
    }

    .um .page-item.active .page-link {
        background: var(--um-brand);
        border-color: var(--um-brand);
        color: #fff;
    }

    .um .page-link {
        border-radius: 8px;
        margin: 0 2px;
        color: var(--um-ink);
        border-color: var(--um-line);
    }

    /* modal */
    #editAdminModal .modal-content {
        border: 0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(20, 30, 60, .25);
    }

    #editAdminModal .modal-header {
        align-items: center;
        gap: 12px;
        padding: 18px 22px;
        border-bottom: 1px solid var(--um-line);
    }

    #editAdminModal .modal-title {
        font-size: 17px;
        font-weight: 700;
    }

    #editAdminModal .modal-body {
        padding: 22px;
    }

    #editAdminModal .modal-footer {
        padding: 14px 22px;
        border-top: 1px solid var(--um-line);
        background: var(--um-bg);
    }

    @media (max-width: 575px) {
        .um-card-b {
            padding: 16px;
        }

        .um-table-wrap {
            padding: 14px 12px 4px;
        }

        .um-actions {
            flex-direction: column-reverse;
        }

        .um-btn {
            width: 100%;
        }

        .um .dataTables_wrapper .dataTables_filter input {
            min-width: 0;
            width: 100%;
            margin-left: 0;
        }
    }
</style>

<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>
            <div class="container-fluid mt-4 um">

                <!-- page head -->
                <div class="um-head">
                    <div>
                        <h1 class="um-title">Admin Management</h1>
                        <p class="um-sub">Create admin accounts, assign roles and manage who can access the system.</p>
                    </div>
                </div>

                <!-- stats -->
                <div class="um-stats">
                    <div class="um-stat is-total">
                        <span class="um-stat-ico"><i class="fas fa-users"></i></span>
                        <div>
                            <div class="um-stat-n"><?= (int)$totalAdmins ?></div>
                            <div class="um-stat-l">Total admins</div>
                        </div>
                    </div>
                    <?php foreach ($roleMap as $key => $meta): ?>
                        <div class="um-stat">
                            <span class="um-stat-ico <?= $meta[2] ?>"><i class="fas <?= $meta[1] ?>"></i></span>
                            <div>
                                <div class="um-stat-n"><?= (int)($roleCount[$key] ?? 0) ?></div>
                                <div class="um-stat-l"><?= htmlspecialchars($meta[0]) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- registration form -->
                <div class="um-card">
                    <div class="um-card-h">
                        <span class="um-card-ico"><i class="fas fa-user-plus"></i></span>
                        <div>
                            <h2 class="um-card-t">Register new admin</h2>
                            <p class="um-card-s">All fields are required. Passwords need at least 6 characters.</p>
                        </div>
                    </div>
                    <div class="um-card-b">
                        <?php if ($successMsg): ?>
                            <div class="um-alert is-ok" role="alert">
                                <i class="fas fa-circle-check"></i>
                                <span><?= htmlspecialchars($successMsg) ?></span>
                                <button type="button" aria-label="Close" onclick="this.parentNode.remove()">&times;</button>
                            </div>
                        <?php endif; ?>
                        <?php if ($errorMsg): ?>
                            <div class="um-alert is-bad" role="alert">
                                <i class="fas fa-circle-exclamation"></i>
                                <span><?= htmlspecialchars($errorMsg) ?></span>
                                <button type="button" aria-label="Close" onclick="this.parentNode.remove()">&times;</button>
                            </div>
                        <?php endif; ?>

                        <form method="post" id="registerForm" autocomplete="off">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="um-label" for="adminName">Admin name</label>
                                    <div class="um-field">
                                        <i class="fas fa-user um-fi"></i>
                                        <input type="text" name="adminName" id="adminName" class="form-control"
                                            placeholder="Enter full name"
                                            value="<?= htmlspecialchars($_POST['adminName'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="um-label" for="email">Email</label>
                                    <div class="um-field">
                                        <i class="fas fa-envelope um-fi"></i>
                                        <input type="email" name="email" id="email" class="form-control"
                                            placeholder="example@gmail.com"
                                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="um-label" for="role">Role</label>
                                    <div class="um-field">
                                        <i class="fas fa-user-tag um-fi"></i>
                                        <select name="role" id="role" class="form-select" required>
                                            <option value="" disabled <?= empty($_POST['role']) ? 'selected' : '' ?>>Select a role</option>
                                            <option value="admin">Admin</option>
                                            <option value="finance">Finance</option>
                                            <!-- <option value="invitation">Invitation</option> -->
                                            <option value="registrationDesk">Registration Desk</option>
                                            <option value="clothCollectReturn">Cloth Collection/Return</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="um-label" for="password">Password</label>
                                    <div class="um-field has-eye">
                                        <i class="fas fa-lock um-fi"></i>
                                        <input type="password" name="password" id="password" class="form-control"
                                            placeholder="Enter password" minlength="6" required>
                                        <span class="um-eye toggle-password" data-target="password" title="Show / hide">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    <div class="um-meter" id="pwMeter" data-s="0"><span></span><span></span><span></span><span></span></div>
                                    <div class="um-hint" id="pwHint">Minimum 6 characters</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="um-label" for="confirmPassword">Confirm password</label>
                                    <div class="um-field has-eye">
                                        <i class="fas fa-lock um-fi"></i>
                                        <input type="password" name="confirmPassword" id="confirmPassword"
                                            class="form-control" placeholder="Confirm password" minlength="6" required>
                                        <span class="um-eye toggle-password" data-target="confirmPassword" title="Show / hide">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    <div class="um-hint" id="cpHint"></div>
                                </div>

                                <div class="col-12">
                                    <div class="um-actions">
                                        <button type="reset" class="um-btn um-btn-ghost">Clear</button>
                                        <button type="submit" name="AdminRegisterBtn" class="um-btn um-btn-primary">
                                            <i class="fas fa-user-plus"></i> Register admin
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <!-- admin list -->
                <div class="um-card">
                    <div class="um-card-h">
                        <span class="um-card-ico"><i class="fas fa-users-gear"></i></span>
                        <div>
                            <h2 class="um-card-t">All registered admins</h2>
                            <p class="um-card-s"><?= (int)$totalAdmins ?> account<?= $totalAdmins == 1 ? '' : 's' ?> in the system</p>
                        </div>
                    </div>
                    <div class="um-table-wrap">
                        <div class="table-responsive">
                            <table class="table" id="adminTable">
                                <thead>
                                    <tr>
                                        <th style="width:56px">#</th>
                                        <th>Admin</th>
                                        <th>Role</th>
                                        <th>Created at</th>
                                        <th class="text-end" style="width:110px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $counter = 1;
                                    foreach ($admins as $row):
                                        $rm = um_role($roleMap, $row['role']);
                                        $ts = strtotime((string)$row['created_at']);
                                    ?>
                                        <tr>
                                            <td class="um-idx"><?= $counter++ ?></td>
                                            <td>
                                                <div class="um-user">
                                                    <span class="um-av"><?= htmlspecialchars(um_initials($row['admin_name'])) ?></span>
                                                    <div>
                                                        <div class="um-user-n"><?= htmlspecialchars($row['admin_name']) ?></div>
                                                        <div class="um-user-e"><?= htmlspecialchars($row['email']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-order="<?= htmlspecialchars($rm[0]) ?>">
                                                <span class="um-role <?= $rm[2] ?>"><i class="fas <?= $rm[1] ?>"></i> <?= htmlspecialchars($rm[0]) ?></span>
                                            </td>
                                            <td class="um-date" data-order="<?= (int)$ts ?>">
                                                <?= $ts ? htmlspecialchars(date('d M Y, h:i A', $ts)) : htmlspecialchars((string)$row['created_at']) ?>
                                            </td>
                                            <td class="text-end" data-order="0">
                                                <button type="button" class="um-ibtn is-edit" title="Edit admin"
                                                    onclick="editAdmin(<?= (int)$row['id'] ?>)">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <button type="button" class="um-ibtn is-del" title="Delete admin"
                                                    onclick="deleteAdmin(<?= (int)$row['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal fade um" id="editAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editAdminForm" autocomplete="off">
                <div class="modal-header">
                    <span class="um-card-ico"><i class="fas fa-user-pen"></i></span>
                    <h5 class="modal-title">Edit admin</h5>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="admin_id" id="editAdminId">

                    <div class="mb-3">
                        <label class="um-label" for="editAdminName">Admin name</label>
                        <div class="um-field">
                            <i class="fas fa-user um-fi"></i>
                            <input type="text" name="admin_name" id="editAdminName" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="um-label" for="editAdminEmail">Email</label>
                        <div class="um-field">
                            <i class="fas fa-envelope um-fi"></i>
                            <input type="email" name="email" id="editAdminEmail" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="um-label" for="editAdminRole">Role</label>
                        <div class="um-field">
                            <i class="fas fa-user-tag um-fi"></i>
                            <select name="role" id="editAdminRole" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="finance">Finance</option>
                                <!-- <option value="invitation">Invitation</option> -->
                                <option value="registrationDesk">Registration Desk</option>
                                <option value="clothCollectReturn">Cloth Collection/Return</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="um-label" for="editAdminPassword">New password <span class="fw-normal text-muted">(leave blank to keep current)</span></label>
                        <div class="um-field has-eye">
                            <i class="fas fa-lock um-fi"></i>
                            <input type="password" name="password" id="editAdminPassword" class="form-control"
                                placeholder="Enter new password">
                            <span class="um-eye toggle-password" data-target="editAdminPassword" title="Show / hide">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="um-hint">Minimum 6 characters if changing</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="um-btn um-btn-ghost" id="cancelEditBtn">Cancel</button>
                    <button type="submit" class="um-btn um-btn-primary" id="saveEditBtn">
                        <i class="fas fa-floppy-disk"></i> Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Font Awesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">

<!-- Scripts: only load a library if header.php / nav.php has not loaded it already.
     Loading jQuery or Bootstrap a second time wipes the handlers the sidebar and top menu
     registered, which is what stopped the nav clicks from working on this page. -->
<script>
    window.jQuery || document.write('<script src="vendor/jquery/jquery.min.js"><\/script>');
</script>
<script>
    (window.jQuery && jQuery.fn.modal && jQuery.fn.collapse) || document.write('<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"><\/script>');
</script>
<script>
    (window.jQuery && jQuery.fn.DataTable) || document.write('<script src="vendor/datatables/jquery.dataTables.min.js"><\/script><script src="vendor/datatables/dataTables.bootstrap4.min.js"><\/script>');
</script>

<script>
    $(document).ready(function() {

        // Admin table: keep the server order (newest first), # and Actions are not sortable
        $('#adminTable').DataTable({
            order: [],
            pageLength: 10,
            columnDefs: [{
                orderable: false,
                targets: [0, 4]
            }],
            language: {
                search: '',
                searchPlaceholder: 'Search admins...',
                emptyTable: 'No admins found',
                zeroRecords: 'No matching admins found'
            }
        });

        // Password show / hide (registration + edit modal)
        $(document).on('click', '.toggle-password', function() {
            var input = $('#' + $(this).data('target'));
            var icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Password strength meter
        $('#password').on('input', function() {
            var v = $(this).val(),
                s = 0;
            if (v.length >= 6) s++;
            if (v.length >= 10) s++;
            if (/[A-Z]/.test(v) && /[a-z]/.test(v)) s++;
            if (/\d/.test(v) && /[^A-Za-z0-9]/.test(v)) s++;
            if (v.length === 0) s = 0;
            $('#pwMeter').attr('data-s', s);
            var t = ['Minimum 6 characters', 'Weak', 'Fair', 'Good', 'Strong'];
            $('#pwHint').text(v.length ? t[s] : t[0]);
            $('#confirmPassword').trigger('input');
        });

        // Confirm password live check
        $('#confirmPassword').on('input', function() {
            var c = $(this).val();
            var h = $('#cpHint');
            if (!c.length) {
                h.text('').css('color', '');
                return;
            }
            if (c === $('#password').val()) h.text('Passwords match').css('color', '#1f8a4c');
            else h.text('Passwords do not match').css('color', '#c0353a');
        });

        $('#registerForm').on('reset', function() {
            $('#pwMeter').attr('data-s', 0);
            $('#pwHint').text('Minimum 6 characters');
            $('#cpHint').text('');
        });

        // Client-side validation before the form is sent
        $('#registerForm').on('submit', function(e) {
            var password = $('#password').val();
            var confirmPassword = $('#confirmPassword').val();
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });

        // Modal close buttons
        $('#cancelEditBtn, #editAdminModal .btn-close').on('click', function() {
            $('#editAdminModal').modal('hide');
        });
    });

    // Edit admin: load data then open modal
    function editAdmin(adminId) {
        $.ajax({
            url: 'admin_fetch.php',
            type: 'POST',
            data: {
                admin_id: adminId
            },
            dataType: 'json',
            success: function(admin) {
                $('#editAdminId').val(admin.id);
                $('#editAdminName').val(admin.admin_name);
                $('#editAdminEmail').val(admin.email);
                $('#editAdminRole').val(admin.role);
                $('#editAdminPassword').val('');
                $('#editAdminModal').modal('show');
            },
            error: function(xhr, status, error) {
                alert('Error fetching admin data: ' + error);
                console.error(xhr.responseText);
            }
        });
    }

    // Edit admin: save
    $('#editAdminForm').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#saveEditBtn').prop('disabled', true);
        $.ajax({
            url: 'admin_update.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert(response);
                location.reload();
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                alert('Error updating admin: ' + error);
                console.error(xhr.responseText);
            }
        });
    });

    // Delete admin
    function deleteAdmin(adminId) {
        if (confirm('Are you sure you want to delete this admin?')) {
            $.ajax({
                url: 'admin_delete.php',
                type: 'POST',
                data: {
                    admin_id: adminId
                },
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Error deleting admin: ' + error);
                    console.error(xhr.responseText);
                }
            });
        }
    }
</script>