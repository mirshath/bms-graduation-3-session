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
                <!-- Page Title -->
                <h4 class="mb-4 text-danger text-center fs-5 fs-md-4">Graduated Student Mark</h4>

                <!-- Session Selector -->
                <div class="card mb-4 shadow-sm p-3 mx-auto" style="max-width: 700px;">
                    <h5 class="mb-3 text-primary">Select Session</h5>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <select id="sessionSelect" class="form-control select2" required>
                                <option value="">-- Select Session --</option>
                                <?php
                                $sessions = $conn->query("SELECT DISTINCT session_time FROM bulk_data_table ORDER BY session_time ASC");
                                while ($row = $sessions->fetch_assoc()) {
                                    echo "<option value='{$row['session_time']}'>{$row['session_time']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="card w-100 mx-auto shadow-sm mt-4 mb-5">
                    <div class="card-body">
                        <h5 class="mb-3 text-primary">Graduated Student Mark List</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="studentTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID</th>
                                        <th class="d-none d-md-table-cell">Student Calling Name</th>
                                        <th class="d-none d-md-table-cell">Program</th>

                                        <th class="d-none d-md-table-cell">Seat No</th>
                                        <th>Graduated</th>
                                    </tr>
                                </thead>
                                <tbody id="studentData"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> <!-- /container-fluid -->
        </div> <!-- /content -->
    </div> <!-- /content-wrapper -->
</div> <!-- /wrapper -->

<!-- ========== JS & CSS ========== -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    body.mark-graduate-page {
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
        body.mark-graduate-page #accordionSidebar {
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

        body.mark-graduate-page.sidebar-open #accordionSidebar {
            transform: translateX(0);
        }

        body.mark-graduate-page #content-wrapper {
            margin-left: 0 !important;
        }
    }

    /* Graduate button full width on mobile */
    @media (max-width: 576px) {
        .graduateBtn {
            width: 100%;
            margin-bottom: 5px;
        }

        #studentTable thead {
            display: none;
        }

        #studentTable tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 0.75rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        #studentTable tbody tr td {
            display: flex;
            justify-content: space-between;
            padding: 0.35rem 0;
            font-size: 0.95rem;
        }

        #studentTable tbody tr td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #495057;
            margin-right: 0.5rem;
        }

        #studentTable .d-none.d-md-table-cell {
            display: flex !important;
        }
    }

    /* Responsive card spacing */
    .card {
        border-radius: 12px;
    }
</style>

<script>
    $(document).ready(function() {
        $('body').addClass('mark-graduate-page');

        const sidebarOverlay = $('#sidebarOverlay');

        $('#sidebarToggleMobile').on('click', function() {
            $('body').toggleClass('sidebar-open');
        });

        sidebarOverlay.on('click', function() {
            $('body').removeClass('sidebar-open');
        });

        $('.select2').select2({
            width: '100%'
        });

        let table = $('#studentTable').DataTable({
            pageLength: 500,
            ordering: true,
            destroy: true
        });

        // Fetch session students
        $('#sessionSelect').change(function() {
            const session = $(this).val();
            if (session === "") {
                table.clear().draw();
                return;
            }

            $.ajax({
                url: "fetch_session_students.php",
                type: "POST",
                data: {
                    session_time: session
                },
                dataType: "json",
                success: function(response) {
                    table.clear();
                    if (response.status === 'success') {
                        $.each(response.data, function(index, row) {
                            let gradBtnClass = (row.graduated_status === 'Yes') ? 'btn-success' : 'btn-secondary';
                            let gradBtnText = (row.graduated_status === 'Yes') ? 'Graduated' : 'Graduate';
                            let graduateBtn = `<button class="btn ${gradBtnClass} btn-sm graduateBtn" data-student="${row.student_id}">${gradBtnText}</button>`;

                            let rowHtml = `
                                <tr>
                                    <td data-label="#">${index + 1}</td>
                                    <td data-label="Student ID">${row.student_id}</td>
                                    <td data-label="Name" class="d-none d-md-table-cell">${row.calling_name ?? ''}</td>
                                    <td data-label="Program" class="d-none d-md-table-cell">${row.program_name ?? ''}</td>
                                
                                    <td data-label="Seat No" class="d-none d-md-table-cell">${row.seat_no ?? ''}</td>
                                    <td data-label="Graduated">${graduateBtn}</td>
                                </tr>
                            `;
                            table.row.add($(rowHtml));
                        });
                    } else {
                        alert(response.message || "No records found.");
                    }
                    table.draw();
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                    console.log("Response Text:", xhr.responseText);
                    alert("Error fetching data. Check console for details.");
                }
            });
        });

        // Graduate button click
        $('#studentTable tbody').on('click', '.graduateBtn', function() {
            let btn = $(this);
            let studentId = btn.data('student');

            if (btn.hasClass('btn-success')) return; // already graduated

            $.ajax({
                url: 'mark_graduate_action.php',
                type: 'POST',
                data: {
                    student_id: studentId
                },
                success: function(response) {
                    try {
                        let res = JSON.parse(response);
                        if (res.status === 'success') {
                            btn.removeClass('btn-secondary').addClass('btn-success').text('Graduated');
                        } else {
                            alert(res.message || "Failed to mark as graduated.");
                        }
                    } catch (e) {
                        console.error("Error parsing response:", response);
                        alert("Something went wrong.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                    alert("Error updating status.");
                }
            });
        });
    });
</script>