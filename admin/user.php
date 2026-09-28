<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");

$successMsg = $errorMsg = '';

// Handle new admin registration
if (isset($_POST['AdminRegisterBtn'])) {
    $adminName = trim($_POST['adminName']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $role = $_POST['role'] ?? 'admin';

    if (empty($adminName) || empty($email) || empty($password) || empty($confirmPassword) || empty($role)) {
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
                $successMsg = "Admin registered successfully!";
                $insert->close();
                // Clear form by redirecting
                // header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
                // echo '<script>window.location.href = "' . $_SERVER['HTTP_REFERER'] . '";</script>';
                echo '<script>alert("Admin registered successfully!s"); window.location.href = "' . $_SERVER['HTTP_REFERER'] . '";</script>';
                exit();
            } else {
                $errorMsg = "Database error. Please try again.";
                $insert->close();
            }
        }
    }
}

// Show success message from redirect
if (isset($_GET['success'])) {
    $successMsg = "Admin registered successfully!";
}

// Fetch all admins
$adminsQuery = $conn->query("SELECT id, admin_name, email, role, created_at FROM admin ORDER BY id DESC");


?>

<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>
            <div class="container-fluid mt-4">

                <h4 class="mb-4">Admin Management</h4>

                <!-- Registration Form -->
                <div class="card w-75 mb-4 shadow-sm">
                    <div class="card-body">
                        <?php if ($successMsg): ?>
                            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <?= htmlspecialchars($successMsg) ?>
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                            </div>
                        <?php endif; ?>
                        <?php if ($errorMsg): ?>
                            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                <?= htmlspecialchars($errorMsg) ?>
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                            </div>
                        <?php endif; ?>

                        <!-- <form method="post" id="registerForm">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label">Admin Name</label>
                                    <input type="text" name="adminName" class="form-control"
                                        placeholder="Enter full name" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="example@gmail.com" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option value="admin">Admin</option>
                                        <option value="finance">Finance</option>
                                        <option value="invitation">Invitation</option>
                                        <option value="registrationDesk">Registration Desk</option>
                                        <option value="clothCollectReturn">Cloth Collection/Return</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" class="form-control"
                                            placeholder="Enter password" minlength="6" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Minimum 6 characters</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" name="confirmPassword" id="confirmPassword"
                                            class="form-control" placeholder="Confirm password" minlength="6" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="toggleConfirmPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-6 offset-md-6 d-flex align-items-end">
                                    <button type="submit" name="AdminRegisterBtn" class="btn btn-primary w-100">Register
                                        Admin</button>
                                </div>
                            </div>
                        </form> -->


                        <form method="post" id="registerForm">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Admin Name</label>
                                    <input type="text" name="adminName" class="form-control"
                                        placeholder="Enter full name" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="example@gmail.com" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option value="" disabled selected>Select a role</option>
                                        <option value="admin">Admin</option>
                                        <option value="finance">Finance</option>
                                        <option value="invitation">Invitation</option>
                                        <option value="registrationDesk">Registration Desk</option>
                                        <option value="clothCollectReturn">Cloth Collection/Return</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" class="form-control"
                                            placeholder="Enter password" minlength="6" required>
                                        <span class="input-group-text bg-white border-start-0 toggle-password"
                                            data-target="password" style="cursor:pointer;">
                                            <i class="fas fa-eye text-muted"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted">Minimum 6 characters</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" name="confirmPassword" id="confirmPassword"
                                            class="form-control" placeholder="Confirm password" minlength="6" required>
                                        <span class="input-group-text bg-white border-start-0 toggle-password"
                                            data-target="confirmPassword" style="cursor:pointer;">
                                            <i class="fas fa-eye text-muted"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-6 offset-md-6 d-flex align-items-end">
                                    <button type="submit" name="AdminRegisterBtn" class="btn btn-primary w-100">
                                        <i class="fas fa-user-plus me-2"></i> Register Admin
                                    </button>
                                </div>

                            </div>
                        </form>

                        <script>
                            // Password toggle (works for both fields)
                            $(document).on('click', '.toggle-password', function () {
                                let targetId = $(this).data('target');
                                let input = $('#' + targetId);
                                let icon = $(this).find('i');

                                if (input.attr('type') === 'password') {
                                    input.attr('type', 'text');
                                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                                } else {
                                    input.attr('type', 'password');
                                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                                }
                            });
                        </script>

                    </div>
                </div>

                <!-- Admin List Table -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">All Registered Admins</h5>
                        <div class="table-responsive">
                            <table class="table table-striped" id="adminTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($adminsQuery && $adminsQuery->num_rows > 0): ?>
                                        <?php $counter = 1;
                                        while ($row = $adminsQuery->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $counter++ ?></td>
                                                <td><?= htmlspecialchars($row['admin_name']) ?></td>
                                                <td><?= htmlspecialchars($row['email']) ?></td>
                                                <td><span
                                                        class="badge bg-primary"><?= htmlspecialchars(ucfirst($row['role'])) ?></span>
                                                </td>
                                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1"
                                                        onclick="editAdmin(<?= $row['id'] ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="deleteAdmin(<?= $row['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No admins found</td>
                                        </tr>
                                    <?php endif; ?>
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
<!-- <div class="modal fade" id="editAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editAdminForm">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="admin_id" id="editAdminId">
                    <div class="mb-3">
                        <label class="form-label">Admin Name</label>
                        <input type="text" name="admin_name" id="editAdminName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="editAdminEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="editAdminRole" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="finance">Finance</option>
                            <option value="invitation">Invitation</option>
                            <option value="registrationDesk">Registration Desk</option>
                            <option value="cloakCollectReturn">Cloak Collection/Return</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div> -->




<!-- Edit Admin Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editAdminForm">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Admin</h5>
                    <!-- Bootstrap 5 -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="admin_id" id="editAdminId">
                    <div class="mb-3">
                        <label class="form-label">Admin Name</label>
                        <input type="text" name="admin_name" id="editAdminName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="editAdminEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="editAdminRole" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="finance">Finance</option>
                            <option value="invitation">Invitation</option>
                            <option value="registrationDesk">Registration Desk</option>
                            <option value="clothCollectReturn">Cloth Collection/Return</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password (leave blank to keep current)</label>
                        <div class="input-group">
                            <input type="password" name="password" id="editAdminPassword" class="form-control"
                                placeholder="Enter new password">
                            <span class="input-group-text bg-white border-start-0 toggle-password"
                                data-target="editAdminPassword" style="cursor:pointer;">
                                <i class="fas fa-eye text-muted"></i>
                            </span>
                        </div>
                        <small class="text-muted">Minimum 6 characters if changing</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <!-- Fix Cancel button to manually trigger close -->
                    <button type="button" class="btn btn-secondary" id="cancelEditBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Font Awesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        $('#adminTable').DataTable({
            order: [
                [0, 'desc']
            ],
            pageLength: 10,
            language: {
                emptyTable: "No admins found"
            }
        });
        // Manual modal close buttons
        $('#cancelEditBtn').on('click', function () {
            $('#editAdminModal').modal('hide');
        });

        // Also fix the X button just in case
        $('#editAdminModal .btn-close').on('click', function () {
            $('#editAdminModal').modal('hide');
        });
        // Password toggle for registration form
        // $(document).on('click', '#togglePassword', function () {
        //     let input = $('#password');
        //     let icon = $(this).find('i');
        //     if (input.attr('type') === 'password') {
        //         input.attr('type', 'text');
        //         icon.removeClass('fa-eye').addClass('fa-eye-slash');
        //     } else {
        //         input.attr('type', 'password');
        //         icon.removeClass('fa-eye-slash').addClass('fa-eye');
        //     }
        // });

        // $(document).on('click', '#toggleConfirmPassword', function () {
        //     let input = $('#confirmPassword');
        //     let icon = $(this).find('i');
        //     if (input.attr('type') === 'password') {
        //         input.attr('type', 'text');
        //         icon.removeClass('fa-eye').addClass('fa-eye-slash');
        //     } else {
        //         input.attr('type', 'password');
        //         icon.removeClass('fa-eye-slash').addClass('fa-eye');
        //     }
        // });




        // Client-side password validation
        $('#registerForm').submit(function (e) {
            let password = $('#password').val();
            let confirmPassword = $('#confirmPassword').val();

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
    });

    // Edit admin function
    function editAdmin(adminId) {
        $.ajax({
            url: "admin_fetch.php",
            type: "POST",
            data: {
                admin_id: adminId
            },
            dataType: "json",
            success: function (admin) {
                $('#editAdminId').val(admin.id);
                $('#editAdminName').val(admin.admin_name);
                $('#editAdminEmail').val(admin.email);
                $('#editAdminRole').val(admin.role);
                $('#editAdminModal').modal('show');
            },
            error: function (xhr, status, error) {
                alert('Error fetching admin data: ' + error);
                console.error(xhr.responseText);
            }
        });
    }

    // Edit admin form submission
    $('#editAdminForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: 'admin_update.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                alert(response);
                location.reload();
            },
            error: function (xhr, status, error) {
                alert('Error updating admin: ' + error);
                console.error(xhr.responseText);
            }
        });
    });

    // Delete admin function
    function deleteAdmin(adminId) {
        if (confirm("Are you sure you want to delete this admin?")) {
            $.ajax({
                url: 'admin_delete.php',
                type: 'POST',
                data: {
                    admin_id: adminId
                },
                success: function (response) {
                    alert(response);
                    location.reload();
                },
                error: function (xhr, status, error) {
                    alert('Error deleting admin: ' + error);
                    console.error(xhr.responseText);
                }
            });
        }
    }
</script>

<?php include("includes/footer.php"); ?>