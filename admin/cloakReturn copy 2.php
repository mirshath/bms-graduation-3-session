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

    /* Student Info Card Styles */
    .student-info-card {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 20px;
    }

    .student-info-item {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .student-info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .student-info-label {
        font-weight: 600;
        font-size: 13px;
        color: #6c757d;
    }

    .student-info-value {
        font-weight: 500;
        font-size: 13px;
        color: #212529;
    }

    /* Item Card Styles */
    .item-card {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .item-card:hover {
        border-color: #28a745;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15);
        transform: translateY(-2px);
    }

    .item-card.returned {
        background: #d4edda;
        border-color: #28a745;
    }

    .item-info {
        display: flex;
        align-items: center;
        flex: 1;
    }

    .item-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        margin-right: 15px;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }

    .item-card.returned .item-icon {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
    }

    .item-name {
        font-weight: 600;
        font-size: 16px;
        color: #212529;
        margin: 0;
    }

    .item-status {
        font-size: 14px;
        color: #6c757d;
        margin: 0;
    }

    .item-card.returned .item-name,
    .item-card.returned .item-status {
        color: #155724;
    }

    .markReturnBtn {
        border-radius: 8px;
        font-weight: 600;
        padding: 10px 20px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
    }

    .markReturnBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .markReturnBtn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .returned-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #28a745;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
    }

    .success-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 30px;
        color: white;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
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
            <div class="modal-body" id="studentItemsBody">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h4 class="text-success fw-bold text-center mb-4">Verified Successfully!</h4>
                <div id="studentItems"></div>
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
        let currentStudentID = '';

        // Function to reset form and focus input
        function resetForm() {
            $('#studentIDForm')[0].reset();
            $('#studentID').focus();
            currentStudentID = '';
        }

        // Function to fetch and display student data
        function fetchStudentData(studentID) {
            $.ajax({
                url: 'fetchStudentCloth.php',
                type: 'POST',
                data: {
                    studentID: studentID
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Student Info Card
                        let html = `
                            <div class="student-info-card">
                                <div class="student-info-item">
                                    <span class="student-info-label">Student ID:</span>
                                    <span class="student-info-value">${response.data.student_id}</span>
                                </div>
                                <div class="student-info-item">
                                    <span class="student-info-label">Name:</span>
                                    <span class="student-info-value">${response.data.student_name}</span>
                                </div>
                                <div class="student-info-item">
                                    <span class="student-info-label">Program:</span>
                                    <span class="student-info-value">${response.data.program_name}</span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <h5 class="mb-3 fw-bold text-gray-700">
                                 Items Status
                                </h5>
                        `;

                        // Item icons mapping
                        const itemIcons = {
                            'cloak': 'fas fa-tshirt',
                            'slashes': 'fas fa-scarf',
                            'hats': 'fas fa-hat-wizard'
                        };

                        // Item display names
                        const itemNames = {
                            'cloak': 'Cloak',
                            'slashes': 'Slashes',
                            'hats': 'Hat'
                        };

                        let hasItems = false;

                        // Collected items with return button
                        ['cloak', 'slashes', 'hats'].forEach(function(item) {
                            let collect = response.data['collect_' + item];
                            let returned = response.data['return_' + item];

                            if (collect) {
                                hasItems = true;
                                if (!returned) {
                                    // Item collected but not returned
                                    html += `
                                        <div class="item-card">
                                            <div class="item-info">
                                                <div class="item-icon">
                                                    <i class="${itemIcons[item]}"></i>
                                                </div>
                                                <div>
                                                    <p class="item-name">${itemNames[item]}</p>
                                                    <p class="item-status">Collected - Pending Return</p>
                                                </div>
                                            </div>
                                            <button class="btn btn-success markReturnBtn" data-item="${item}" data-student="${response.data.student_id}">
                                                <i class="fas fa-check-circle me-2"></i>Mark as Returned
                                            </button>
                                        </div>
                                    `;
                                } else {
                                    // Item already returned
                                    html += `
                                        <div class="item-card returned">
                                            <div class="item-info">
                                                <div class="item-icon">
                                                    <i class="${itemIcons[item]}"></i>
                                                </div>
                                                <div>
                                                    <p class="item-name">${itemNames[item]}</p>
                                                    <p class="item-status">Returned Successfully</p>
                                                </div>
                                            </div>
                                            <span class="returned-badge">
                                                <i class="fas fa-check-circle"></i> Returned
                                            </span>
                                        </div>
                                    `;
                                }
                            }
                        });

                        if (!hasItems) {
                            html += `
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No items collected for this student.
                                </div>
                            `;
                        }

                        html += `</div>`;

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
        }

        // Handle student ID form submit
        $('#studentIDForm').on('submit', function(e) {
            e.preventDefault();
            let studentID = $('#studentID').val().trim();

            if (!studentID) {
                return;
            }

            currentStudentID = studentID;
            fetchStudentData(studentID);
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
                        // Refresh student data to show updated status
                        fetchStudentData(studentID);
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

        // Reset form when modals are closed
        $('#successModal, #notRegisteredModal').on('hidden.bs.modal', function() {
            resetForm();
        });

        // Auto-focus input on page load
        $('#studentID').focus();

        // Clear input on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                if ($('#successModal').hasClass('show') || $('#notRegisteredModal').hasClass('show')) {
                    return; // Let modal handle escape
                }
                resetForm();
            }
        });
    });
</script>