<?php
include('../database/connection.php');

function fp_h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function fp_initials(string $name): string
{
    $w = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);
    if (!$w) return '?';
    $first = mb_substr($w[0], 0, 1);
    $last  = count($w) > 1 ? mb_substr($w[count($w) - 1], 0, 1) : '';
    return mb_strtoupper($first . $last);
}

if (!isset($_POST['program_name']) || trim($_POST['program_name']) === '') {
    exit;
}

// Raw value is bound below, so no manual escaping (it would double-escape names with quotes)
$program_name = trim($_POST['program_name']);
// The session picked on the page (each program belongs to one session in data_tables)
$session_label = trim($_POST['session'] ?? '');

$rows = [];
$failed = false;

try {
    $query = "SELECT
            b.student_id,
            b.seat_no,
            b.program_name,
            b.session_time,
            b.calling_name,
            r.given_email_add,
            r.email_address,
            r.name_in_full,
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
    if (!$stmt) throw new Exception($conn->error);
    $stmt->bind_param("s", $program_name);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($r = $result->fetch_assoc()) $rows[] = $r;
} catch (Exception $e) {
    $failed = true;
    error_log("fetch_program_data error: " . $e->getMessage());
}

if ($failed): ?>
    <div class="be-empty">
        <i class="fas fa-exclamation-triangle be-bad-ic" aria-hidden="true"></i>
        <strong>Could not load students</strong>
        <span>Try again, or contact the administrator if it keeps happening.</span>
    </div>
<?php exit;
endif;

if (!$rows): ?>
    <div class="be-empty">
        <i class="fas fa-user-slash" aria-hidden="true"></i>
        <strong>No students found</strong>
        <span>No seat data has been uploaded for <?= fp_h($program_name) ?> yet.</span>
    </div>
<?php exit;
endif;

// Summary numbers
$total = count($rows);
$sent = 0;
$noEmail = 0;
foreach ($rows as $r) {
    $st = strtolower(trim((string)($r['last_sent_status'] ?? '')));
    if ($st === 'sent' || $st === 'success') $sent++;
    if (empty($r['email_address']) && empty($r['given_email_add'])) $noEmail++;
}
$notSent = $total - $sent;
if ($session_label === '') $session_label = trim((string)($rows[0]['session_time'] ?? ''));
?>

<div class="be-top">
    <div class="be-top-txt">
        <h2><?= fp_h($program_name) ?></h2>
        <?php if ($session_label !== ''): ?>
            <span class="be-pill type"><i class="fas fa-calendar-alt" aria-hidden="true"></i> <?= fp_h($session_label) ?></span>
        <?php endif; ?>
    </div>
    <div class="be-top-act">
        <span id="emailStatus" class="be-status" role="status"></span>
        <button type="button" id="sendToAllBtn" class="be-btn"
            data-program="<?= fp_h($program_name) ?>"
            data-count="<?= $total ?>">
            <i class="fas fa-envelope" aria-hidden="true"></i> Send email to all students
        </button>
    </div>
</div>

<div class="be-kpis">
    <div class="be-kpi"><span class="k">Students</span><strong><?= $total ?></strong></div>
    <div class="be-kpi ok"><span class="k">Email sent</span><strong><?= $sent ?></strong></div>
    <div class="be-kpi warn"><span class="k">Not sent yet</span><strong><?= $notSent ?></strong></div>
    <div class="be-kpi bad"><span class="k">No email address</span><strong><?= $noEmail ?></strong></div>
</div>

<div id="actionNotice" class="be-notice" role="status" aria-live="polite"></div>

<table id="programDataTable" class="table" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th>Student ID</th>
            <th>Calling Name</th>
            <th>Seat No</th>
            <th>Session</th>
            <th>Email</th>
            <th>Given Email</th>
            <th>Last Email Sent</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row):
            $email = trim((string)($row['email_address'] ?? ''));
            $given = trim((string)($row['given_email_add'] ?? ''));
            $calling = trim((string)($row['calling_name'] ?? ''));
            $ts = !empty($row['last_sent_date']) ? strtotime($row['last_sent_date']) : 0;
            $status = strtolower(strip_tags((string)($row['last_sent_status'] ?? '')));
            $isSent = ($status === 'sent' || $status === 'success');
        ?>
            <tr>
                <td><span class="be-mono"><?= fp_h($row['student_id']) ?></span></td>
                <td>
                    <div class="be-person">
                        <span class="be-ini" aria-hidden="true"><?= fp_h(fp_initials($calling)) ?></span>
                        <span class="nm"><?= $calling !== '' ? fp_h($calling) : '<span class="be-na">N/A</span>' ?></span>
                    </div>
                </td>
                <td><span class="be-mono"><?= fp_h($row['seat_no']) ?></span></td>
                <td><span class="be-mono"><?= trim((string)$row['session_time']) !== '' ? fp_h($row['session_time']) : '<span class="be-na">N/A</span>' ?></span></td>
                <td><?= $email !== '' ? '<span class="be-em">' . fp_h($email) . '</span>' : '<span class="be-pill bad">Missing</span>' ?></td>
                <td><?= $given !== '' ? '<span class="be-em">' . fp_h($given) . '</span>' : '<span class="be-pill bad">Missing</span>' ?></td>
                <td data-order="<?= (int)$ts ?>"><?= $ts ? '<span class="be-mono">' . date("Y-m-d h:i A", $ts) . '</span>' : '<span class="be-na">Never</span>' ?></td>
                <td>
                    <?php if ($isSent): ?>
                        <span class="be-pill ok"><i class="fas fa-check-circle" aria-hidden="true"></i> Sent</span>
                    <?php elseif ($status !== ''): ?>
                        <span class="be-pill bad"><i class="fas fa-times-circle" aria-hidden="true"></i> <?= fp_h(ucfirst($status)) ?></span>
                    <?php else: ?>
                        <span class="be-pill">N/A</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($email !== '' || $given !== ''): ?>
                        <button type="button" class="be-act sendEmailBtn"
                            data-email="<?= fp_h($email) ?>"
                            data-given-email="<?= fp_h($given) ?>"
                            data-student-id="<?= fp_h($row['student_id']) ?>"
                            data-seat-no="<?= fp_h($row['seat_no']) ?>"
                            data-program="<?= fp_h($row['program_name']) ?>"
                            data-session="<?= fp_h($row['session_time']) ?>"
                            data-calling-name="<?= fp_h($calling) ?>">
                            <i class="fas <?= $status !== '' ? 'fa-redo' : 'fa-paper-plane' ?>" aria-hidden="true"></i>
                            <?= $status !== '' ? 'Resend email' : 'Send email' ?>
                        </button>
                    <?php else: ?>
                        <button type="button" class="be-act" disabled>Email missing</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    (function($) {
        if ($.fn.DataTable.isDataTable('#programDataTable')) {
            $('#programDataTable').DataTable().destroy();
        }

        $('#programDataTable').DataTable({
            autoWidth: false,
            pageLength: 200,
            lengthMenu: [50, 100, 200, 500],
            order: [
                [0, 'asc']
            ],
            columnDefs: [{
                orderable: false,
                targets: 8
            }],
            dom: "<'be-tbar'<'be-len'l><'be-search'f>><'be-scroll'rt><'be-tfoot'<'be-info'i><'be-pg'p>>",
            language: {
                search: '',
                searchPlaceholder: 'Search students…',
                lengthMenu: 'Show _MENU_',
                zeroRecords: 'No matching students found',
                info: 'Showing _START_ to _END_ of _TOTAL_ students',
                infoEmpty: 'Showing 0 to 0 of 0 students',
                infoFiltered: '(filtered from _MAX_ total students)'
            }
        });

        function notify(msg, isError) {
            $('#actionNotice')
                .removeClass('err')
                .toggleClass('err', !!isError)
                .text(msg)
                .addClass('show');
        }

        // Send email to all students in this program
        $('#sendToAllBtn').on('click', function() {
            var btn = $(this),
                idle = btn.html(),
                programName = btn.attr('data-program'),
                count = btn.attr('data-count');

            if (!confirm('Send emails to all ' + count + ' students in this program?')) {
                return;
            }

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Sending…');
            $('#emailStatus').text('Processing…');

            $.ajax({
                url: 'send_to_all.php',
                type: 'POST',
                data: {
                    program_name: programName
                },
                success: function(response) {
                    notify(response, false);
                },
                error: function() {
                    notify('Emails could not be sent. Try again.', true);
                },
                complete: function() {
                    btn.prop('disabled', false).html(idle);
                    $('#emailStatus').text('');
                }
            });
        });

        // Single email. Namespaced and unbound first so re-fetching a program
        // never stacks handlers (which would send the same email several times).
        $(document).off('click.beSend', '.sendEmailBtn').on('click.beSend', '.sendEmailBtn', function() {
            var btn = $(this);

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Sending…');

            $.ajax({
                url: 'single_send_email.php',
                type: 'POST',
                data: {
                    email: btn.attr('data-email'),
                    given_email_add: btn.attr('data-given-email'),
                    student_id: btn.attr('data-student-id'),
                    name_in_full: btn.attr('data-calling-name'),
                    seat_no: btn.attr('data-seat-no'),
                    program_name: btn.attr('data-program'),
                    session_time: btn.attr('data-session'),
                    calling_name: btn.attr('data-calling-name')
                },
                success: function(response) {
                    notify(response, false);
                },
                error: function() {
                    notify('Email could not be sent. Try again.', true);
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-redo" aria-hidden="true"></i> Resend email');
                }
            });
        });
    })(jQuery);
</script>