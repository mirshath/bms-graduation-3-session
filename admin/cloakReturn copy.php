<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}

include("../database/connection.php");
include("includes/header.php");
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

    @media (max-width:991.98px) {
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

    @media (max-width:576px) {
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
</style>

<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="sidebarOverlay" class="d-md-none"></div>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>
            <div class="container-fluid mt-4">
                <button id="sidebarToggleMobile" class="btn btn-light border d-inline-flex align-items-center gap-2 d-lg-none mb-3 shadow-sm">
                    <i class="fas fa-bars"></i>
                    <span class="fw-semibold">Menu</span>
                </button>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body text-center">
                                <h1 class="mb-4 text-gray-900 archivo" style="font-size: 60px;">SCAN QR for <br> Cloak RETURN</h1>

                                <form id="studentIDForm" autocomplete="off">
                                    <div class="mb-3">
                                        <input type="text" class="form-control w-75 mx-auto p-4 fw-bolder text-center"
                                            style="font-size: 25px;" id="studentID" name="studentID" placeholder="Enter or Scan Student ID" required>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary w-50 p-2">Check Student</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- content -->
    </div> <!-- content-wrapper -->
</div> <!-- wrapper -->

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">Student Found</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="studentItemsBody">
                <h4 class="text-success fw-bold">✔ Verified Successfully!</h4>
                <div id="studentItems" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- Not Registered Modal -->
<div class="modal fade" id="notRegisteredModal" tabindex="-1" aria-labelledby="notRegisteredModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="notRegisteredModalLabel">Not Registered</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <h4 class="text-danger fw-bold">❌ Student Not Registered!</h4>
                <p>Please check the registered students table.</p>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS + jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {

        // Handle student ID form submit
        $('#studentIDForm').on('submit', function(e) {
            e.preventDefault();
            let studentID = $('#studentID').val();

            $.ajax({
                url: 'fetchStudentCloth.php',
                type: 'POST',
                data: {
                    studentID: studentID
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let html = `
                        <p><strong>ID:</strong> ${response.data.student_id}</p>
                        <p><strong>Name:</strong> ${response.data.student_name}</p>
                        <p><strong>Program:</strong> ${response.data.program_name}</p>
                    `;

                        // Collected items with return button
                        ['cloak', 'slashes', 'hats'].forEach(function(item) {
                            let collect = response.data['collect_' + item];
                            let returned = response.data['return_' + item];
                            if (collect && !returned) {
                                html += `<div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Collected ${item.charAt(0).toUpperCase() + item.slice(1)}</span>
                                <button class="btn btn-success btn-sm markReturnBtn" data-item="${item}" data-student="${response.data.student_id}">Mark as Returned</button>
                            </div>`;
                            } else if (returned) {
                                html += `<div class="mb-2"><span>Returned ${item.charAt(0).toUpperCase() + item.slice(1)} ✅</span></div>`;
                            }
                        });

                        $('#studentItems').html(html);
                        $('#successModal').modal('show');
                    } else if (response.status === 'not_registered') {
                        $('#notRegisteredModal').modal('show');
                    } else {
                        alert('Something went wrong!');
                    }
                },
                error: function() {
                    alert('AJAX error occurred');
                }
            });
        });

        // Handle mark as return button click
        $(document).on('click', '.markReturnBtn', function() {
            let studentID = $(this).data('student');
            let item = $(this).data('item');
            let btn = $(this);
            btn.prop('disabled', true).text('Processing...');

            $.ajax({
                url: 'markReturnCloth.php',
                type: 'POST',
                data: {
                    studentID: studentID,
                    item: item
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Update button to returned
                        btn.parent().html(`<span>Returned ${item.charAt(0).toUpperCase() + item.slice(1)} ✅</span>`);
                    } else {
                        alert('Failed to mark as returned!');
                        btn.prop('disabled', false).text('Mark as Returned');
                    }
                },
                error: function() {
                    alert('AJAX error occurred!');
                    btn.prop('disabled', false).text('Mark as Returned');
                }
            });
        });

    });
</script>