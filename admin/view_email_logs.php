<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");
?>

<div id="wrapper">
    <?php include("nav.php"); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <div class="container-fluid mt-4">

                <h2 class="mb-4"><i class="fas fa-envelope-open-text text-primary"></i> Email Logs</h2>
                <p class="text-muted">View all sent and failed graduation emails</p>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <?php
                    $stats_query = "SELECT 
                        COUNT(*) as total_emails,
                        SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_count,
                        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
                        COUNT(DISTINCT student_id) as unique_students
                        FROM email_log";
                    $stats_result = $conn->query($stats_query);
                    $stats = $stats_result->fetch_assoc();
                    ?>

                    <div class="col-md-3 mb-2">
                        <div class="card shadow-sm stats-card border-left-primary">
                            <div class="card-body">
                                <h6 class="text-muted">Total Emails</h6>
                                <h4><i class="fas fa-envelope text-primary"></i> <?= $stats['total_emails'] ?></h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-2">
                        <div class="card shadow-sm stats-card border-left-success">
                            <div class="card-body">
                                <h6 class="text-muted">Successfully Sent</h6>
                                <h4><i class="fas fa-check-circle text-success"></i> <?= $stats['sent_count'] ?></h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-2">
                        <div class="card shadow-sm stats-card border-left-danger">
                            <div class="card-body">
                                <h6 class="text-muted">Failed</h6>
                                <h4><i class="fas fa-times-circle text-danger"></i> <?= $stats['failed_count'] ?></h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-2">
                        <div class="card shadow-sm stats-card border-left-info">
                            <div class="card-body">
                                <h6 class="text-muted">Unique Students</h6>
                                <h4><i class="fas fa-users text-info"></i> <?= $stats['unique_students'] ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4 shadow-sm w-100">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Program</label>
                                <select id="filterProgram" class="form-select">
                                    <option value="">All Programs</option>
                                    <?php
                                    $programs_query = "SELECT DISTINCT program_name FROM email_log ORDER BY program_name";
                                    $programs_result = $conn->query($programs_query);
                                    while ($program = $programs_result->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($program['program_name']) . '">'
                                            . htmlspecialchars($program['program_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select id="filterStatus" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="sent">Sent</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Email Type</label>
                                <select id="filterType" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="single">Single</option>
                                    <option value="bulk">Bulk</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button id="resetFilters" class="btn btn-secondary w-100">
                                    <i class="fas fa-redo"></i> Reset Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Logs Table -->
                <div class="card w-100 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="emailLogsTable" class="table table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Program</th>
                                        <th>Seat No</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Sent At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT el.*, a.admin_name as sent_by_name
                                              FROM email_log el
                                              LEFT JOIN admin a ON el.sent_by = a.id
                                              ORDER BY el.sent_at DESC";
                                    $result = $conn->query($query);
                                    while ($row = $result->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['student_id']) ?></td>
                                            <td><?= htmlspecialchars($row['name_in_full']) ?></td>
                                            <td><?= htmlspecialchars($row['email_address']) ?></td>
                                            <td><?= htmlspecialchars($row['program_name']) ?></td>
                                            <td><?= htmlspecialchars($row['seat_no']) ?></td>
                                            <td>
                                                <span class="badge bg-info text-white">
                                                    <?= ucfirst($row['email_type']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?= $row['status'] === 'sent' ? 'bg-success' : 'bg-danger' ?>">
                                                    <?= ucfirst($row['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y h:i A', strtotime($row['sent_at'])) ?></td>
                                            <td>
                                                <?php if ($row['status'] === 'failed'): ?>
                                                    <button class="btn btn-sm btn-danger viewError"
                                                        data-error="<?= htmlspecialchars($row['error_message']) ?>">
                                                        <i class="fas fa-exclamation-circle"></i> View Error
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-info viewDetails"
                                                        data-id="<?= $row['id'] ?>"
                                                        data-student="<?= htmlspecialchars($row['student_id']) ?>"
                                                        data-name="<?= htmlspecialchars($row['name_in_full']) ?>"
                                                        data-email="<?= htmlspecialchars($row['email_address']) ?>"
                                                        data-program="<?= htmlspecialchars($row['program_name']) ?>"
                                                        data-seat="<?= htmlspecialchars($row['seat_no']) ?>"
                                                        data-session="<?= htmlspecialchars($row['session_time']) ?>"
                                                        data-sent-by="<?= htmlspecialchars($row['sent_by_name']) ?>">
                                                        <i class="fas fa-eye"></i> Details
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div> <!-- /container-fluid -->
        </div> <!-- /content -->
    </div> <!-- /content-wrapper -->
</div> <!-- /wrapper -->

<!-- Modals -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-info-circle"></i> Email Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailsContent"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="errorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Error Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="errorContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- JS & CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#emailLogsTable').DataTable({
        pageLength: 50,
        order: [[0, 'desc']],
        lengthMenu: [[25, 50, 100, 200], [25, 50, 100, 200]]
    });

    $('#filterProgram, #filterStatus, #filterType').on('change', function() {
        table.columns(4).search($('#filterProgram').val()).draw();
        table.columns(7).search($('#filterStatus').val()).draw();
        table.columns(6).search($('#filterType').val()).draw();
    });

    $('#resetFilters').on('click', function() {
        $('#filterProgram, #filterStatus, #filterType').val('');
        table.search('').columns().search('').draw();
    });

    $(document).on('click', '.viewDetails', function() {
        var data = $(this).data();
        var html = `
            <table class="table table-bordered">
                <tr><th>Student ID</th><td>${data.student}</td></tr>
                <tr><th>Name</th><td>${data.name}</td></tr>
                <tr><th>Email</th><td>${data.email}</td></tr>
                <tr><th>Program</th><td>${data.program}</td></tr>
                <tr><th>Seat No</th><td>${data.seat}</td></tr>
                <tr><th>Session</th><td>${data.session}</td></tr>
                <tr><th>Sent By</th><td>${data.sentBy}</td></tr>
            </table>
        `;
        $('#detailsContent').html(html);
        $('#detailsModal').modal('show');
    });

    $(document).on('click', '.viewError', function() {
        var error = $(this).data('error');
        $('#errorContent').text(error || 'No error details available');
        $('#errorModal').modal('show');
    });
});
</script>
