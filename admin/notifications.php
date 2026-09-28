<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");

// Handle bulk update via POST (non-AJAX fallback)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_read'])) {
    $selected = $_POST['notification_ids'] ?? [];
    if (!empty($selected)) {
        $ids = implode(",", array_map('intval', $selected));
        $conn->query("UPDATE notifications SET status = 'read' WHERE id IN ($ids)");
    }
    header("Location: notifications.php");
    exit();
}

// Fetch all notifications
$result = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");
$notifications = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}
?>

<div id="wrapper">
    <?php include("nav.php"); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <div class="container-fluid mt-4">
                <h3 class="mb-4 text-primary fw-bold">System Notifications</h3>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">All Notifications</h5>
                        <div class="d-flex align-items-center">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label fw-bold text-white" for="selectAll" style="cursor: pointer;">
                                    Select All Unread
                                </label>
                            </div>
                            <button id="markReadBtn" class="btn btn-light btn-sm fw-bold">Mark Selected as Read</button>
                        </div>
                    </div>

                    <div class="card-body">
                        <?php if (count($notifications) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle" id="notificationsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th><!-- checkbox for row selection --></th>
                                            <th>#</th>
                                            <th>Table</th>
                                            <th>Student ID</th>
                                            <th>Action</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($notifications as $index => $note): ?>
                                            <?php $isRead = ($note['status'] === 'read'); ?>
                                            <tr class="<?php echo $isRead ? 'table-success' : ''; ?>">
                                                <td>
                                                    <input type="checkbox" class="notificationCheckbox" value="<?php echo $note['id']; ?>">
                                                </td>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($note['table_name']); ?></span></td>
                                                <td><?php echo htmlspecialchars($note['student_id']); ?></td>
                                                <td>
                                                    <?php
                                                    $action = htmlspecialchars($note['action_type']);
                                                    $badgeClass = ($action == 'INSERT') ? 'bg-success' : (($action == 'UPDATE') ? 'bg-warning text-dark' : 'bg-secondary');
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $action; ?></span>
                                                </td>
                                                <td><?php echo htmlspecialchars($note['description']); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $isRead ? 'bg-secondary' : 'bg-danger'; ?>">
                                                        <?php echo $isRead ? 'Read' : 'Unread'; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($note['created_at']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center mb-0">No notifications available.</p>
                        <?php endif; ?>

                        <!-- DataTables CSS and JS -->
                        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
                        <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                        <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                        <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Script -->
<script>
    $(document).ready(function() {
        // Initialize DataTable with page length 300
        const table = $('#notificationsTable').DataTable({
            "pageLength": 300,
            "lengthMenu": [10, 25, 50, 100, 300, 500],
            "order": [
                [7, 'desc']
            ], // Sort by date column (index 7) descending
            "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                } // Disable sorting for checkbox column
            ]
        });

        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.notificationCheckbox');
        const markReadBtn = document.getElementById('markReadBtn');

        // Filter for checkboxes of unread notifications
        const getUnreadCheckboxes = () => {
            return Array.from(document.querySelectorAll('.notificationCheckbox')).filter(cb => {
                const row = cb.closest('tr');
                return row && !row.classList.contains('table-success');
            });
        };

        // Disable "Select All" if there are no unread notifications
        const updateSelectAllState = () => {
            const unreadCheckboxes = getUnreadCheckboxes();
            if (unreadCheckboxes.length === 0) {
                selectAll.disabled = true;
            } else {
                selectAll.disabled = false;
            }
        };

        updateSelectAllState();

        // ✅ Select All toggle for unread notifications
        selectAll.addEventListener('change', function() {
            const unreadCheckboxes = getUnreadCheckboxes();
            unreadCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });

        // ✅ Sync "Select All" checkbox with individual unread checkboxes
        const syncSelectAll = () => {
            const unreadCheckboxes = getUnreadCheckboxes();
            if (unreadCheckboxes.length > 0) {
                selectAll.checked = unreadCheckboxes.every(cb => cb.checked);
            }
        };

        // Add event listeners to checkboxes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('notificationCheckbox')) {
                syncSelectAll();
            }
        });

        // Initial sync on page load
        syncSelectAll();

        // ✅ Mark selected as read (AJAX)
        markReadBtn.addEventListener('click', function() {
            const selected = Array.from(document.querySelectorAll('.notificationCheckbox:checked'))
                .map(cb => cb.value);

            if (selected.length === 0) {
                alert("Please select at least one notification.");
                return;
            }

            if (!confirm("Mark selected notifications as read?")) return;

            fetch('update_notifications.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ids: selected
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Selected notifications marked as read!");
                        location.reload();
                    } else {
                        alert("Error updating notifications.");
                    }
                })
                .catch(err => alert("Something went wrong!"));
        });
    });
</script>