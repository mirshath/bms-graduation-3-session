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

                <h4 class="mb-4 text-danger text-center">Session Wise Student List after seat allocated</h4>

                <div class="card w-75 mb-4 container shadow-sm">
                    <div class="card-body">
                        <div class="form-group">
                            <label><b>Select Session</b></label>
                            <select id="sessionSelect" class="form-control select2">
                                <option value="">-- Select Session --</option>
                                <option value="MORNING">Morning</option>
                                <option value="EVENING">Evening</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Display Session Data -->
                <div class="card w-100 container shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3 text-primary">Student Details</h5>
                        <div id="sessionData"></div>
                    </div>
                </div>

            </div> <!-- /container-fluid -->
        </div> <!-- /content -->
    </div> <!-- /content-wrapper -->
</div> <!-- /wrapper -->

<!-- ========== CSS ========== -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">

<!-- ========== JS ========== -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>




<script>
    // $(document).ready(function() {
    //     $('.select2').select2({
    //         placeholder: "-- Select Session --",
    //         width: '100%'
    //     });

    //     $('#sessionSelect').on('change', function() {
    //         let session = $(this).val();

    //         if (session === "") {
    //             $('#sessionData').html("");
    //             return;
    //         }

    //         $.ajax({
    //             url: "load_session_data.php",
    //             type: "POST",
    //             data: {
    //                 session: session
    //             },
    //             beforeSend: function() {
    //                 $('#sessionData').html("<p class='text-info'>Loading...</p>");
    //             },
    //             success: function(response) {
    //                 $('#sessionData').html(response);

    //                 $('#sessionTable').DataTable({
    //                     dom: 'Bfrtip',
    //                     buttons: [
    //                         {
    //                             extend: 'copy',
    //                             className: 'btn btn-sm btn-primary me-1'
    //                         },
    //                         {
    //                             extend: 'csv',
    //                             className: 'btn btn-sm btn-success me-1'
    //                         },
    //                         {
    //                             extend: 'excel',
    //                             className: 'btn btn-sm btn-success me-1'
    //                         },
    //                         {
    //                             extend: 'pdf',
    //                             className: 'btn btn-sm btn-danger me-1'
    //                         },
    //                         {
    //                             extend: 'print',
    //                             className: 'btn btn-sm btn-info'
    //                         }
    //                     ],
    //                     // ordering: true,
    //                     // order: [
    //                     //     [4, 'asc']
    //                     // ], 
    //                     // pageLength: 500, 
    //                     // lengthMenu: [[500, -1, 10, 25, 50, 100], [500, "All", 10, 25, 50, 100]]
                        
    //                      pageLength: 500, // Show 500 rows by default
    //                     lengthMenu: [
    //                         [500, -1, 10, 25, 50, 100],
    //                         [500, "All", 10, 25, 50, 100]
    //                     ],
    //                     columnDefs: [{
    //                         targets: 4, // Seat No column index
    //                         type: 'seat-sort'
    //                     }]
                        
    //                 });
    //             }
    //         });
    //     });
    // });


  $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Select Session --",
            width: '100%'
        });

        $('#sessionSelect').on('change', function() {
            let session = $(this).val();

            if (session === "") {
                $('#sessionData').html("");
                return;
            }

            $.ajax({
                url: "load_session_data.php",
                type: "POST",
                data: {
                    session: session
                },
                beforeSend: function() {
                    $('#sessionData').html("<p class='text-info'>Loading...</p>");
                },
                success: function(response) {
                    $('#sessionData').html(response);

                    // 🔥 Custom Seat Sorting Function
                    $.fn.dataTable.ext.type.order['seat-sort-pre'] = function(data) {
                        let parts = data.trim().split(" ");

                        if (parts.length === 2) {
                            let prefix = parts[0];
                            let number = parseInt(parts[1], 10);
                            return prefix.charCodeAt(0) * 100000 + number;
                        }
                        return data;
                    };

                    // Datatable Init
                    $('#sessionTable').DataTable({
                        dom: 'Bfrtip',
                        buttons: [{
                                extend: 'copy',
                                className: 'btn btn-sm btn-primary me-1'
                            },
                            {
                                extend: 'csv',
                                className: 'btn btn-sm btn-success me-1'
                            },
                            {
                                extend: 'excel',
                                className: 'btn btn-sm btn-success me-1'
                            },
                            {
                                extend: 'pdf',
                                className: 'btn btn-sm btn-danger me-1'
                            },
                            {
                                extend: 'print',
                                className: 'btn btn-sm btn-info'
                            }
                        ],
                        pageLength: 500,
                        lengthMenu: [
                            [500, -1, 10, 25, 50, 100],
                            [500, "All", 10, 25, 50, 100]
                        ],
                        columnDefs: [{
                            targets: 3, // Seat No column index
                            // type: 'seat-sort'
                             type: 'num-sort' // Sort as integer
                        }]
                    });
                }
            });
        });
    });


</script>