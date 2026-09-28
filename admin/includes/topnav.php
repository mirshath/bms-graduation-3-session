<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <!-- <form
        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form> -->
    <div class="ml-5 text-muted fw-bolder">2025 BMS Graduations</div>
    <div class="topbar-divider d-none d-sm-block"></div>
    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small"
                            placeholder="Search for..." aria-label="Search"
                            aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>


        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow mr-5">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <b> <?= $_SESSION['admin_name'] ?> - <?= $_SESSION['role'] ?> </b></span>
                <img src="data:image/png;base64,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" alt="Admin Image" class="rounded-circle" style="width: 30px; height: 30px;">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>



    </ul>

</nav>