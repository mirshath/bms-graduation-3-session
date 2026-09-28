<?php
$current_url = basename($_SERVER['REQUEST_URI'], ".php");
$role = $_SESSION['role'] ?? ''; // Current user role
?>

<style>
    .collapse-item.active {
        background: linear-gradient(90deg, rgb(151, 30, 11) 0%, rgb(128, 20, 4) 100%);
        border-radius: 6px;
        font-weight: 600;
    }

    .collapse-item:hover {
        /* color: #fff !important; */
        background: linear-gradient(90deg, rgb(151, 30, 11) 0%, rgb(128, 20, 4) 100%);
        border-radius: 6px;
        font-weight: 600;
    }

    /* .sidebar .nav-item .collapse .collapse-inner,
    .sidebar .nav-item .collapsing .collapse-inner {
        min-width: 13rem;
    } */


    .nav-item.active .nav-link {
        color: white !important;
        background: rgb(146, 22, 2);
        background: linear-gradient(90deg, rgb(151, 30, 11) 0%, rgb(128, 20, 4) 100%);
        font-weight: 600;

        border: 1px solid white;
    }

    .nav-hover:hover .nav-link {
        color: white !important;
        background: rgb(146, 22, 2);
        background: linear-gradient(90deg, rgb(151, 30, 11) 0%, rgb(128, 20, 4) 100%);
        transition: background-color 0.3s ease;
        font-weight: 600;

        border: 1px solid white;
    }

    .img-fluid {
        max-width: 126%;
        margin-left: -25px;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center mt-4 mb-4" href="index">
        <div class="sidebar-brand-icon rotate-n-15"></div>
        <div class="sidebar-brand-text mx-3"><img src="img/logo4.png" class="img-fluid shadow"></div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard: Visible to all roles -->
    <li class="nav-item <?= ($current_url == 'index') ? 'active' : '' ?> nav-hover">
        <a class="nav-link" href="index">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Scan: Admin & Finance -->
    <?php if ($role == 'admin'): ?>
        <!-- ✅✅✅ updated into host ✅✅✅ -->

        <li class="nav-item <?= ($current_url == 'scan') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="scan">
                <i class="fas fa-fw fa-barcode"></i>
                <span>Scan Attandance Table</span>
            </a>
        </li>
    <?php endif; ?>



    <!-- ------------------------------------------------admin, finance ------------------------------------------------ -->
    <!-- Payment Report: Invitation only -->
    <?php if (in_array($role, ['admin', 'finance'])): ?>

        <!-- ✅✅✅ updated into host ✅✅✅ -->
        <!-- ✅✅✅ updated into host ✅✅✅ -->

        <li class="nav-item <?= ($current_url == 'payment_reg') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="payment_reg">
                <i class="fas fa-fw fa-users"></i>
                <span>Payment Registration</span>
            </a>
        </li>

        <li class="nav-item <?= ($current_url == 'payment-reports') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="payment-reports">
                <i class="fas fa-fw fa-users"></i>
                <span>Payment Report</span>
            </a>
        </li>

        <!-- ✅✅✅ updated into host ✅✅✅ -->
        <!-- <li class="nav-item <?= ($current_url == 'extra-ticket-buying') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="extra-ticket-buying">
                <i class="fas fa-fw fa-users"></i>
                <span>Extra Ticket</span>
            </a>
        </li>

        <li class="nav-item <?= ($current_url == 'extra-ticket-data') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="extra-ticket-data">
                <i class="fas fa-fw fa-users"></i>
                <span>Extra Ticket Datas</span>
            </a>
        </li> -->


        <!-- Dropdown for Extra Ticket -->
        <li class="nav-item dropdown <?= in_array($current_url, ['extra-ticket-buying', 'extra-ticket-data']) ? 'active show' : '' ?> nav-hover">
            <a class="nav-link dropdown-toggle <?= in_array($current_url, ['extra-ticket-buying', 'extra-ticket-data']) ? '' : 'collapsed' ?>"
                href="#"
                id="extraTicketDropdown"
                role="button"
                data-toggle="collapse"
                data-target="#collapseExtraTicket"
                aria-expanded="<?= in_array($current_url, ['extra-ticket-buying', 'extra-ticket-data']) ? 'true' : 'false' ?>"
                aria-controls="collapseExtraTicket">
                <i class="fas fa-fw fa-ticket-alt"></i>
                <span>Extra Ticket</span>
            </a>

            <div id="collapseExtraTicket"
                class="collapse <?= in_array($current_url, ['extra-ticket-buying', 'extra-ticket-data']) ? 'show' : '' ?>"
                aria-labelledby="extraTicketDropdown"
                data-parent="#accordionSidebar">

                <div class="bg-dark py-2 collapse-inner rounded">
                    <a class="collapse-item text-white <?= ($current_url == 'extra-ticket-buying') ? 'active' : '' ?>" href="extra-ticket-buying">
                        <i class="fas fa-fw fa-cart-plus mr-2"></i> Buy Extra Ticket
                    </a>
                    <a class="collapse-item text-white <?= ($current_url == 'extra-ticket-data') ? 'active' : '' ?>" href="extra-ticket-data">
                        <i class="fas fa-fw fa-list mr-2"></i> Ticket Log / Data
                    </a>
                </div>
            </div>
        </li>


    <?php endif; ?>


    <!-- ------------------------------------------------ admin, finance , invitation  ------------------------------------------------ -->


    <!-- Payment Success Student: Admin & Finance -->
    <?php if (in_array($role, ['admin', 'finance', 'invitation'])): ?>
        <!-- ✅✅✅ updated into host ✅✅✅ -->

        <li class="nav-item <?= ($current_url == 'payment_success_students') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="payment_success_students">
                <i class="fas fa-fw fa-users"></i>
                <span>Payment Success Student</span>
            </a>
        </li>

        <!-- ✅✅✅ updated into host ✅✅✅ -->

        <!-- <li class="nav-item <?= ($current_url == 'oldStudentsDB') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="oldStudentsDB">
                <i class="fas fa-fw fa-users"></i>
                <span>Old Students</span>
            </a>
        </li> -->

        <!-- ✅✅✅ updated into host ✅✅✅ -->

        <!-- <li class="nav-item <?= ($current_url == 'registeredStudents') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="registeredStudents">
                <i class="fas fa-fw fa-user-graduate"></i>
                <span>Reg Students</span>
            </a>
        </li> -->



        <!-- Dropdown for Student Records -->
        <li class="nav-item dropdown <?= in_array($current_url, ['oldStudentsDB', 'registeredStudents']) ? 'active show' : '' ?> nav-hover">
            <a class="nav-link dropdown-toggle <?= in_array($current_url, ['oldStudentsDB', 'registeredStudents']) ? '' : 'collapsed' ?>"
                href="#"
                id="studentsDropdown"
                role="button"
                data-toggle="collapse"
                data-target="#collapseStudents"
                aria-expanded="<?= in_array($current_url, ['oldStudentsDB', 'registeredStudents']) ? 'true' : 'false' ?>"
                aria-controls="collapseStudents">
                <i class="fas fa-fw fa-users-cog"></i>
                <span>Students</span>
            </a>

            <div id="collapseStudents"
                class="collapse <?= in_array($current_url, ['oldStudentsDB', 'registeredStudents']) ? 'show' : '' ?>"
                aria-labelledby="studentsDropdown"
                data-parent="#accordionSidebar">

                <div class="bg-dark py-2 collapse-inner rounded">
                    <a class="collapse-item text-white <?= ($current_url == 'oldStudentsDB') ? 'active' : '' ?>" href="oldStudentsDB">
                        <i class="fas fa-fw fa-history mr-2"></i> Old Students
                    </a>
                    <a class="collapse-item text-white <?= ($current_url == 'registeredStudents') ? 'active' : '' ?>" href="registeredStudents">
                        <i class="fas fa-fw fa-user-graduate mr-2"></i> Registered Students
                    </a>
                </div>
            </div>
        </li>


    <?php endif; ?>



    <!-- ------------------------------------------------  admin, invitations ------------------------------------------------ -->
    <?php if (in_array($role, ['admin', 'invitation'])): ?>
        <!-- ✅✅✅ updated into host ✅✅✅ -->
        <li class="nav-item <?= ($current_url == 'invitation-collection') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="invitation-collection">
                <i class="fas fa-fw fa-users"></i> <!-- Users icon -->
                <span> Invitation Collect</span>
            </a>
        </li>
    <?php endif; ?>


    <!-- ------------------------------------------------ only admin ------------------------------------------------ -->

    <!-- User Create: Admin only -->
    <?php if ($role == 'admin'): ?>
        <!-- ✅✅✅ updated into host ✅✅✅ -->

        <li class="nav-item <?= ($current_url == 'user') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="user">
                <i class="fas fa-fw fa-users"></i>
                <span>User Create</span>
            </a>
        </li>

        <!-- <li class="nav-item <?= ($current_url == 'bulk-data') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="bulk-data">
                <i class="fas fa-fw fa-users"></i>
                <span>Bulk upload</span>
            </a>
        </li>

        <li class="nav-item <?= ($current_url == 'bulk-data-email') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="bulk-data-email">
                <i class="fas fa-fw fa-users"></i>
                <span>Bulk Email</span>
            </a>
        </li> -->



        <!-- Dropdown for Bulk Actions -->
        <!-- Bulk Actions Dropdown -->
        <li class="nav-item dropdown <?= (in_array($current_url, ['bulk-data', 'bulk-data-email', 'view_email_logs'])) ? 'active show' : '' ?> nav-hover">
            <a class="nav-link dropdown-toggle <?= (in_array($current_url, ['bulk-data', 'bulk-data-email', 'view_email_logs'])) ? '' : 'collapsed' ?>"
                href="#" id="bulkDropdown" role="button"
                data-toggle="collapse"
                data-target="#collapseBulk"
                aria-expanded="<?= (in_array($current_url, ['bulk-data', 'bulk-data-email', 'view_email_logs'])) ? 'true' : 'false' ?>"
                aria-controls="collapseBulk">
                <i class="fas fa-fw fa-users"></i>
                <span>Bulk Actions</span>
            </a>

            <div id="collapseBulk"
                class="collapse <?= (in_array($current_url, ['bulk-data', 'bulk-data-email', 'view_email_logs'])) ? 'show' : '' ?>"
                aria-labelledby="bulkDropdown"
                data-parent="#accordionSidebar">

                <div class="bg-dark py-2 collapse-inner rounded">
                    <a class="collapse-item text-white <?= ($current_url == 'bulk-data') ? 'active' : '' ?>" href="bulk-data">
                        <i class="fas fa-fw fa-upload mr-2"></i> Bulk Upload
                    </a>
                    <a class="collapse-item text-white <?= ($current_url == 'bulk-data-email') ? 'active' : '' ?>" href="bulk-data-email">
                        <i class="fas fa-fw fa-envelope mr-2"></i> Bulk Email
                    </a>
                    <a class="collapse-item text-white <?= ($current_url == 'view_email_logs') ? 'active' : '' ?>" href="view_email_logs">
                        <i class="fas fa-fw fa-list-alt mr-2"></i> View Email Logs
                    </a>
                </div>
            </div>
        </li>


        <!-- ✅✅✅ updated into host ✅✅✅ -->

        <li class="nav-item <?= ($current_url == 'oldStudentsDOBupdate') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="oldStudentsDOBupdate">
                <i class="fas fa-fw fa-users"></i>
                <span>DOB Updates</span>
            </a>
        </li>


        <li class="nav-item <?= ($current_url == 'mark_graduate') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="mark_graduate">
                <i class="fas fa-fw fa-users"></i>
                <span>Graduete Std Mark</span>
            </a>
        </li>




        <!-- --------------- new report to allocated seat order --------------------  -->
        <li class="nav-item <?= ($current_url == 'allocatedSeatOrder') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="allocatedSeatOrder">
                <i class="fas fa-fw fa-users"></i>
                <span>Report Seat Order</span>
            </a>
        </li>
        <!-- --------------- new report to allocated seat order --------------------  -->
        <li class="nav-item <?= ($current_url == 'clothCollection') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="clothCollection">
                <i class="fas fa-fw fa-users"></i>
                <span>Cloth Collection</span>
            </a>
        </li>
        <li class="nav-item <?= ($current_url == 'cloakReturn') ? 'active' : '' ?> nav-hover">
            <a class="nav-link" href="cloakReturn">
                <i class="fas fa-fw fa-users"></i>
                <span>Cloth Return</span>
            </a>
        </li>

    <?php endif; ?>

</ul>
<!-- End of Sidebar -->