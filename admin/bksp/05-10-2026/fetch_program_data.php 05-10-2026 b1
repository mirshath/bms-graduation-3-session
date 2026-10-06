<?php
include('../database/connection.php');


if (isset($_POST['program_name'])) {
    $program_name = mysqli_real_escape_string($conn, $_POST['program_name']);

    $query = "SELECT 
    b.student_id, 
    b.seat_no, 
    b.program_name, 
    b.session_time, 
    b.calling_name,
    r.given_email_add, 
    r.email_address, 
    r.name_in_full,
    -- Get latest email sent info
    (SELECT el.sent_at 
        FROM email_log el 
        WHERE el.student_id = b.student_id 
        ORDER BY el.sent_at DESC 
        LIMIT 1) AS last_sent_date,
    (SELECT el.status 
        FROM email_log el 
        WHERE el.student_id = b.student_id 
        ORDER BY el.sent_at DESC 
        LIMIT 1) AS last_sent_status
  FROM bulk_data_table b
  LEFT JOIN registered_students r 
    ON b.student_id = r.student_id
  WHERE b.program_name = ?
  ORDER BY b.student_id ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $program_name);
    $stmt->execute();
    $result = $stmt->get_result();
?>

    <?php if ($result->num_rows > 0): ?>
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <span id="emailStatus" class="text-muted"></span>
            <button id="sendToAllBtn" class="btn btn-success" data-program="<?= htmlspecialchars($program_name) ?>">
                <i class="fas fa-envelope"></i> Send Email to All Students
            </button>
        </div>

        <table id="programDataTable" style="font-size: 13px;" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Student ID</th>
                    <th>Calling Name</th>
                    <th>Seat No</th>
                    <th>Program</th>
                    <th>Session</th>
                    <th>Email</th>
                    <th>Given Email</th>
                    <th>Last Email Sent</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()):
                    $email = $row['email_address'] ?? '';
                    $given_email_add = $row['given_email_add'] ?? '';
                    $last_sent = $row['last_sent_date']
                        ? date("Y-m-d h:i A", strtotime($row['last_sent_date']))
                        : '<span class="text-muted">Never</span>';
                    $last_status = $row['last_sent_status']
                        ? htmlspecialchars(ucfirst($row['last_sent_status']))
                        : '<span class="text-muted">N/A</span>';
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['student_id']) ?></td>
                        <!-- <td><?= htmlspecialchars($row['name_in_full']) ?></td> -->
                        <td><?= htmlspecialchars($row['calling_name']) ?></td>
                        <td><?= htmlspecialchars($row['seat_no']) ?></td>
                        <td><?= htmlspecialchars($row['program_name']) ?></td>
                        <td><?= htmlspecialchars($row['session_time']) ?></td>
                        <td><?= $email ? htmlspecialchars($email) : '<span class="text-danger">Missing</span>' ?></td>
                        <td><?= $given_email_add ? htmlspecialchars($given_email_add) : '<span class="text-danger">Missing</span>' ?></td>
                        <td><?= $last_sent ?></td>
                        <!-- <td><?= $last_status ?></td> -->
                        <td>
                            <?php
                            // Normalize last sent status
                            $clean_status = strtolower(strip_tags($row['last_sent_status'] ?? ''));
                            if ($clean_status === 'success' || $clean_status === 'sent') {
                                echo '<span class="badge bg-success">Sent</span>';
                            } elseif ($clean_status) {
                                echo '<span class="badge bg-danger">' . htmlspecialchars(ucfirst($clean_status)) . '</span>';
                            } else {
                                echo '<span class="badge bg-secondary">N/A</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($email || $given_email_add): ?>
                                <?php
                                // Show "Resend Email" if any status (sent, error, etc.), else "Send Email"
                                if (
                                    $clean_status === 'success' ||
                                    $clean_status === 'sent' ||
                                    $clean_status === 'error' ||
                                    $clean_status // any other non-empty status string
                                ) {
                                ?>
                                    <button class="btn btn-primary btn-sm sendEmailBtn"
                                        data-email="<?= htmlspecialchars($email) ?>"
                                        data-given-email="<?= htmlspecialchars($given_email_add) ?>"
                                        data-calling-name="<?= htmlspecialchars($row['calling_name'] ?? '') ?>">
                                        Resend Email
                                    </button>
                                <?php
                                } else {
                                ?>
                                    <button class="btn btn-primary btn-sm sendEmailBtn"
                                        data-email="<?= htmlspecialchars($email) ?>"
                                        data-given-email="<?= htmlspecialchars($given_email_add) ?>"
                                        data-calling-name="<?= htmlspecialchars($row['calling_name'] ?? '') ?>">
                                        Send Email
                                    </button>
                                <?php
                                }
                                ?>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>Email Missing</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php else: ?>
        <div class="alert alert-warning">No data found for this program.</div>
    <?php endif; ?>
<?php } ?>




<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#programDataTable')) {
            $('#programDataTable').DataTable().destroy();
        }
        $('#programDataTable').DataTable({
            pageLength: 200,
            lengthMenu: [50, 100, 200, 500],
            order: [
                [0, 'asc']
            ]
        });

        // Send Email to All Button
        $('#sendToAllBtn').on('click', function() {
            var btn = $(this);
            var programName = btn.data('program');

            if (!confirm('Are you sure you want to send emails to ALL students in this program?')) {
                return;
            }

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
            $('#emailStatus').text('Processing...');

            $.ajax({
                url: 'send_to_all.php',
                type: 'POST',
                data: {
                    program_name: programName
                },
                success: function(response) {
                    alert(response);
                    btn.prop('disabled', false).html('<i class="fas fa-envelope"></i> Send Email to All Students');
                    $('#emailStatus').text('');
                },
                error: function() {
                    alert('Error sending emails. Please try again.');
                    btn.prop('disabled', false).html('<i class="fas fa-envelope"></i> Send Email to All Students');
                    $('#emailStatus').text('');
                }
            });
        });

        // Single Email Button
        $(document).on('click', '.sendEmailBtn', function() {
            var email = $(this).data('email');
            var given_email = $(this).data('given-email');
            var calling_name = $(this).data('calling-name');
            var btn = $(this);

            btn.prop('disabled', true).text('Sending...');

            $.ajax({
                url: 'single_send_email.php',
                type: 'POST',
                data: {
                    email: email,
                    given_email_add: given_email,
                    student_id: btn.closest('tr').find('td:eq(0)').text().trim(),
                    name_in_full: btn.closest('tr').find('td:eq(1)').text().trim(),
                    seat_no: btn.closest('tr').find('td:eq(2)').text().trim(),
                    program_name: btn.closest('tr').find('td:eq(3)').text().trim(),
                    session_time: btn.closest('tr').find('td:eq(4)').text().trim(),
                    calling_name: calling_name
                },
                success: function(response) {
                    alert(response);
                    btn.prop('disabled', false).text('Send Email');
                },
                error: function() {
                    alert('Error sending email.');
                    btn.prop('disabled', false).text('Send Email');
                }
            });
        });
    });
</script>