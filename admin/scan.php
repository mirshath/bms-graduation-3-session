<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");



// Fetch program-wise and session-wise summary
$query = "
SELECT rs.program_name, bd.session_time,
       COUNT(*) AS total_registered,
       SUM(CASE WHEN rs.attend='attended' THEN 1 ELSE 0 END) AS total_attended
FROM registered_students rs
LEFT JOIN bulk_data_table bd ON rs.student_id = bd.student_id
GROUP BY rs.program_name, bd.session_time
ORDER BY rs.program_name, bd.session_time
";

$result = $conn->query($query);
$summaries = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $summaries[] = $row;
    }
}

// -------------------------
// Session Stats (Morning & Evening) - Real-time
// -------------------------
$morningPaidStudents = 0;
$morningAttendedStudents = 0;
$morningNotAttendedStudents = 0;
$eveningPaidStudents = 0;
$eveningAttendedStudents = 0;
$eveningNotAttendedStudents = 0;

// Morning session students who paid (unique students only)
$sql = "
    SELECT COUNT(DISTINCT rs.student_id) AS total_paid
    FROM registered_students rs
    INNER JOIN data_tables dt ON rs.program_name = dt.programName
    INNER JOIN payment_records pr ON rs.student_id = pr.student_id
    WHERE dt.session = 'MORNING'
";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $morningPaidStudents = $row['total_paid'] ?? 0;
}

// Morning session students who attended (unique students only)
$sql = "
    SELECT COUNT(DISTINCT rs.student_id) AS total_attended
    FROM registered_students rs
    INNER JOIN data_tables dt ON rs.program_name = dt.programName
    WHERE dt.session = 'MORNING' AND rs.attend = 'attended'
";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $morningAttendedStudents = $row['total_attended'] ?? 0;
}

// Morning session students who paid but not attended (unique students only)
$sql = "
    SELECT COUNT(DISTINCT rs.student_id) AS total_not_attended
    FROM registered_students rs
    INNER JOIN data_tables dt ON rs.program_name = dt.programName
    INNER JOIN payment_records pr ON rs.student_id = pr.student_id
    WHERE dt.session = 'MORNING' AND (rs.attend IS NULL OR rs.attend != 'attended')
";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $morningNotAttendedStudents = $row['total_not_attended'] ?? 0;
}

// Evening session students who paid (unique students only)
$sql = "
    SELECT COUNT(DISTINCT rs.student_id) AS total_paid
    FROM registered_students rs
    INNER JOIN data_tables dt ON rs.program_name = dt.programName
    INNER JOIN payment_records pr ON rs.student_id = pr.student_id
    WHERE dt.session = 'EVENING'
";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $eveningPaidStudents = $row['total_paid'] ?? 0;
}

// Evening session students who attended (unique students only)
$sql = "
    SELECT COUNT(DISTINCT rs.student_id) AS total_attended
    FROM registered_students rs
    INNER JOIN data_tables dt ON rs.program_name = dt.programName
    WHERE dt.session = 'EVENING' AND rs.attend = 'attended'
";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $eveningAttendedStudents = $row['total_attended'] ?? 0;
}

// Evening session students who paid but not attended (unique students only)
$sql = "
    SELECT COUNT(DISTINCT rs.student_id) AS total_not_attended
    FROM registered_students rs
    INNER JOIN data_tables dt ON rs.program_name = dt.programName
    INNER JOIN payment_records pr ON rs.student_id = pr.student_id
    WHERE dt.session = 'EVENING' AND (rs.attend IS NULL OR rs.attend != 'attended')
";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $eveningNotAttendedStudents = $row['total_not_attended'] ?? 0;
}

?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

<style>
    .archivo {
        font-family: "Archivo", sans-serif;
    }

    body.scan-page {
        overflow-x: hidden;
    }

    #sidebarToggleMobile {
        border-radius: 12px;
        font-weight: 600;
    }

    #sidebarOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease;
        z-index: 1039;
    }

    body.sidebar-open #sidebarOverlay {
        opacity: 1;
        visibility: visible;
    }

    @media (max-width: 991.98px) {
        body.scan-page #accordionSidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1040;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        body.scan-page.sidebar-open #accordionSidebar {
            transform: translateX(0);
        }

        body.scan-page #content-wrapper {
            margin-left: 0 !important;
        }
    }

    @media (max-width: 576px) {
        h1.archivo {
            font-size: 2.2rem !important;
        }

        #studentID {
            width: 100% !important;
            font-size: 1.3rem !important;
        }

        #studentTable thead {
            display: none;
        }

        .card {
            border-radius: 12px;
        }
    }

    /* Border-left styles for session stats cards */
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }

    .border-left-warning {
        border-left: 4px solid #f6c23e !important;
    }

    /* Animation for stat updates */
    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    .stat-updating {
        animation: pulse 0.5s ease-in-out;
    }
</style>

<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="sidebarOverlay" class="d-md-none"></div>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>






            <div class="container-fluid mt-4">
                <button id="sidebarToggleMobile"
                    class="btn btn-light border d-inline-flex align-items-center gap-2 d-lg-none mb-3 shadow-sm">
                    <i class="fas fa-bars"></i>
                    <span class="fw-semibold">Menu</span>
                </button>
                
                 <div class="text-end mt-5 mb-3">
                            <a href="all_attended_student" class="btn btn-secondary">
                                View All Attended Students
                            </a>
                        </div>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class=" ">
                            <div class="card-body text-center">
                                <h1 class="mb-4 text-gray-900 archivo" style="font-size: 60px;">SCAN QR for Attendance</h1>

                                <form id="studentIDForm" autocomplete="off">
                                    <div class="mb-3">
                                        <input type="text" class="form-control w-75 mx-auto p-4 fw-bolder text-center"
                                            style="font-size: 25px;" id="studentID" name="studentID" placeholder="Enter or Scan Student ID" required>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary w-50 p-2">Check Student ID</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Session Stats Cards - Real-time -->
            <div class="container-fluid mt-4 mb-4">
                <div class="row text-center">
                    <!-- Morning Session Stats -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow border-left-primary py-3">
                            <div class="card-body">
                                <h5 class="text-primary fw-bold mb-4 archivo">Morning Session</h5>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h6 class="text-success fw-bold">Paid Students</h6>
                                        <h2 class="fw-bold text-dark" id="morningPaid"><?php echo $morningPaidStudents; ?></h2>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="text-info fw-bold">Attended Students</h6>
                                        <h2 class="fw-bold text-dark" id="morningAttended"><?php echo $morningAttendedStudents; ?></h2>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="text-danger fw-bold">Remaining</h6>
                                        <h2 class="fw-bold text-dark" id="morningRemaining"><?php echo $morningNotAttendedStudents; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Evening Session Stats -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow border-left-warning py-3">
                            <div class="card-body">
                                <h5 class="text-warning fw-bold mb-4 archivo">Evening Session</h5>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h6 class="text-success fw-bold">Paid Students</h6>
                                        <h2 class="fw-bold text-dark" id="eveningPaid"><?php echo $eveningPaidStudents; ?></h2>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="text-info fw-bold">Attended Students</h6>
                                        <h2 class="fw-bold text-dark" id="eveningAttended"><?php echo $eveningAttendedStudents; ?></h2>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="text-danger fw-bold">Remaining</h6>
                                        <h2 class="fw-bold text-dark" id="eveningRemaining"><?php echo $eveningNotAttendedStudents; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container shadow mb-4">

                <div class="row mt-4 mb-4 justify-content-center">
                    <div class="col-md-6">
                        <div class="text-center mb-2">
                            <h4 class="fw-bold archivo">Morning Session</h4>
                        </div>
                        <?php foreach ($summaries as $sum): ?>
                            <?php if (stripos($sum['session_time'], 'morning') !== false): ?>
                                <div class="mb-3">
                                    <div class="card border-primary shadow-sm">
                                        <div class="card-body text-center">
                                            <h5 class="card-title fw-bold archivo"><?php echo $sum['program_name']; ?></h5>
                                            <h6 class="card-subtitle mb-2 text-muted"><?php echo $sum['session_time']; ?> Session</h6>
                                            <p class="mb-1"><strong>Registered:</strong> <?php echo $sum['total_registered']; ?></p>
                                            <p class="mb-0"><strong>Attended:</strong> <?php echo $sum['total_attended']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center mb-2">
                            <h4 class="fw-bold archivo">Evening Session</h4>
                        </div>
                        <?php foreach ($summaries as $sum): ?>
                            <?php if (stripos($sum['session_time'], 'evening') !== false): ?>
                                <div class="mb-3">
                                    <div class="card border-primary shadow-sm">
                                        <div class="card-body text-center">
                                            <h5 class="card-title fw-bold archivo"><?php echo $sum['program_name']; ?></h5>
                                            <h6 class="card-subtitle mb-2 text-muted"><?php echo $sum['session_time']; ?> Session</h6>
                                            <p class="mb-1"><strong>Registered:</strong> <?php echo $sum['total_registered']; ?></p>
                                            <p class="mb-0"><strong>Attended:</strong> <?php echo $sum['total_attended']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                       
                    </div>
                </div>
            </div>
        </div> <!-- content -->
    </div> <!-- content-wrapper -->
</div> <!-- wrapper -->

<!-- ✅ Success Modal -->

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">Student Found</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <h4 class="text-success fw-bold">✔ Verified Successfully!</h4>
                <p id="studentInfo" class="mt-3"></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" id="attendedBtn" class="btn btn-success">Mark as Attended</button>
            </div>
        </div>
    </div>
</div>

<!-- ❌ Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <h4 class="text-danger fw-bold">❌ Student Not Found!</h4>
                <p>Please check the ID and try again.</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attendedModal" tabindex="-1" aria-labelledby="attendedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-warning">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="attendedModalLabel">Already Attended</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <h4 class="text-warning fw-bold">⚠ Student Already Marked as Attended!</h4>
                <p id="attendedInfo" class="mt-3"></p>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS + jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        $('body').addClass('scan-page');

        const sidebarOverlay = $('#sidebarOverlay');

        $('#sidebarToggleMobile').on('click', function() {
            $('body').toggleClass('sidebar-open');
        });

        sidebarOverlay.on('click', function() {
            $('body').removeClass('sidebar-open');
        });

        const $studentID = $('#studentID');
        const $studentForm = $('#studentIDForm');

        // Focus input on page load
        $studentID.focus();

        // Reset input and focus when any modal is hidden
        $('#successModal, #errorModal, #attendedModal').on('hidden.bs.modal', function() {
            $studentID.val('').focus();
        });

        // Handle form submission (manual input or Enter key)
        $studentForm.on('submit', function(e) {
            e.preventDefault();

            const studentID = $studentID.val().trim();
            if (!studentID) return;

            $.ajax({
                url: 'check_student.php',
                type: 'POST',
                data: {
                    studentID
                },

                success: function(response) {
                    let res = JSON.parse(response);

                    if (res.status === 'success') {
                        // Store student data for receipt printing
                        window.lastStudentCheck = res.data;
                        currentStudentData = res.data;

                        $('#studentInfo').html(`
                        <p><strong>Student ID:</strong> ${res.data.student_id}</p>
                        <p><strong>Name:</strong> ${res.data.full_name}</p>
                        <p><strong>Program:</strong> ${res.data.program_name}</p>
                        <h2 class="text-primary my-3" style="font-weight: bold;">SEAT NO: ${res.data.seat_no}</h2>
                        <h5 class="text-primary"><strong>SESSION:</strong> ${res.data.session}</h5>
                        <p><strong>Graduation Payment:</strong> ${res.data.graduation_payment_status}</p>
                    `);
                        $('#successModal').modal('show');

                    } else if (res.status === 'attended') {
                        $('#attendedInfo').html(`
                        <p><strong>Name:</strong> ${res.data.full_name}</p>
                        <p><strong>Program:</strong> ${res.data.program_name}</p>
                        <h5><strong>Student ID:</strong> ${res.data.student_id}</h5>
                        <h2 class="text-primary my-3" style="font-weight: bold;">SEAT NO: ${res.data.seat_no}</h2>
                        <h5 class="text-primary"><strong>SESSION:</strong> ${res.data.session}</h5>
                        <p><strong>Graduation Payment:</strong> ${res.data.graduation_payment_status}</p>
                    `);
                        $('#attendedModal').modal('show');

                    } else {
                        $('#errorModal').modal('show');
                    }
                },

                error: function() {
                    alert('Server error! Please try again.');
                }
            });
        });

        // Trigger form submit on Enter key
        $studentID.on('keypress', function(e) {
            if (e.which === 13) $studentForm.submit();
        });

        // Store student data for receipt printing
        let currentStudentData = null;

        // Function to update session stats in real-time
        function updateSessionStats() {
            $.ajax({
                url: 'fetch_session_stats.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success') {
                        // Update Morning Session
                        $('#morningPaid').text(data.morning.paid || 0);
                        $('#morningAttended').text(data.morning.attended || 0);
                        $('#morningRemaining').text(data.morning.remaining || 0);

                        // Update Evening Session
                        $('#eveningPaid').text(data.evening.paid || 0);
                        $('#eveningAttended').text(data.evening.attended || 0);
                        $('#eveningRemaining').text(data.evening.remaining || 0);
                    }
                },
                error: function() {
                    console.log('Error updating session stats');
                }
            });
        }

        // Auto-refresh stats every 5 seconds
        setInterval(updateSessionStats, 5000);

       
        
        // Handle "Mark as Attended" button click
        $(document).on('click', '#attendedBtn', function() {
            const studentID = $('#studentID').val().trim();
            if (!studentID) return;

            console.log("Mark as Attended clicked. Student ID:", studentID);

            $.ajax({
                url: 'mark_attended.php',
                type: 'POST',
                data: {
                    studentID
                },
                success: function(response) {
                    let res = JSON.parse(response);

                    if (res.status === 'success') {
                        $('#successModal').modal('hide');

                        // Update stats immediately after marking attendance
                        setTimeout(function() {
                            updateSessionStats();
                        }, 500);

                        console.log("Student marked as attended:", studentID);

                    } else {
                        alert("❌ Error: " + res.message);
                    }
                },
                error: function() {
                    alert("Server error! Please try again.");
                }
            });
        });
        
        
        

        // Print receipt function for 72mm thermal printer - Direct print without preview
        function printReceipt(studentData) {
            // Create receipt content
            const receiptContent = `
                <div class="receipt-container">
                    <div class="receipt-header">
                        <h2>BMS GRADUATION 2026</h2>
                        <hr>
                    </div>
                    <div class="receipt-body">
                        <p><strong>Student ID:</strong> ${studentData.student_id}</p>
                        <p><strong>Name:</strong> ${studentData.full_name}</p>
                        <hr>
                        <h1 class="seat-number">SEAT NO: ${studentData.seat_no}</h1>
                        <h3 class="in-number"><strong>IN NO:</strong> ${studentData.in_no || 'N/A'}</h3>
                        <p><strong>Session:</strong> ${studentData.session}</p>
                        <hr>
                        <p class="timestamp">Date: ${new Date().toLocaleString()}</p>
                        <p class="footer">Thank you for attending!</p>
                    </div>
                </div>
            `;

            // Create hidden iframe for printing
            const iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            iframe.style.opacity = '0';
            iframe.style.pointerEvents = 'none';
            document.body.appendChild(iframe);

            const iframeDoc = iframe.contentWindow.document;
            iframeDoc.open();
            iframeDoc.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Print Receipt</title>
                    <style>
                        @media print {
                            @page {
                                size: 72mm auto;
                                margin: 0;
                            }
                            * {
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            body {
                                margin: 0;
                                padding: 5mm 3mm;
                                font-family: 'Courier New', monospace;
                                font-size: 11px;
                                width: 72mm;
                                line-height: 1.3;
                            }
                        }
                        body {
                            margin: 0;
                            padding: 5mm 3mm;
                            font-family: 'Courier New', monospace;
                            font-size: 11px;
                            width: 72mm;
                            line-height: 1.3;
                        }
                        .receipt-container {
                            width: 100%;
                            text-align: center;
                        }
                        .receipt-header h2 {
                            font-size: 14px;
                            font-weight: bold;
                            margin: 4px 0;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        }
                        .receipt-header p {
                            font-size: 10px;
                            margin: 2px 0;
                        }
                        .receipt-body {
                            text-align: left;
                            margin-top: 8px;
                        }
                        .receipt-body p {
                            margin: 4px 0;
                            font-size: 10px;
                            word-wrap: break-word;
                        }
                        .receipt-body strong {
                            font-weight: bold;
                        }
                        .seat-number {
                            text-align: center;
                            font-size: 25px;
                            font-weight: bold;
                            margin: 8px 0;
                            color: #000;
                            text-transform: uppercase;
                        }
                        .in-number {
                            text-align: center;
                            font-size: 16px;
                            font-weight: bold;
                            margin: 8px 0;
                            color: #000;
                        }
                        .in-number strong {
                            font-weight: bold;
                        }
                        hr {
                            border: none;
                            border-top: 1px dashed #000;
                            margin: 6px 0;
                        }
                        .timestamp {
                            text-align: center;
                            font-size: 9px;
                            margin-top: 8px;
                        }
                        .footer {
                            text-align: center;
                            font-size: 10px;
                            font-weight: bold;
                            margin-top: 8px;
                        }
                    </style>
                </head>
                <body>
                    ${receiptContent}
                </body>
                </html>
            `);
            iframeDoc.close();

            // Wait for content to load, then print directly
            setTimeout(function() {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();

                    // Remove iframe after printing (with delay to ensure print completes)
                    setTimeout(function() {
                        document.body.removeChild(iframe);
                    }, 1000);
                } catch (e) {
                    console.error('Print error:', e);
                    // Fallback: remove iframe if print fails
                    document.body.removeChild(iframe);
                }
            }, 300);
        }

    });
</script>

</body>

</html>