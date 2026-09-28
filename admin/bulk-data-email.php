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

                <h4 class="mb-4 text-danger text-center">Bulk Data Email Sending</h4>

                <div class="card w-75 mb-4 container shadow-sm">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label><strong>Select Program:</strong> <span class="text-danger">*</span></label>
                                <select name="program_name" class="form-control" id="programSelect" required>
                                    <option value="">-- Select Program --</option>
                                    <?php
                                    $query = "SELECT * FROM data_tables ORDER BY programName ASC";
                                    $result = mysqli_query($conn, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $program = htmlspecialchars($row['programName']);
                                        echo '<option value="' . $program . '">' . $program . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" id="fetchProgramData" class="btn btn-success w-100">Fetch Data</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div id="programDataResult"></div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- JS Libraries -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#programSelect').select2({
            placeholder: "-- Select Program --",
            allowClear: true,
            width: '100%'
        });

        $('#fetchProgramData').click(function() {
            var programName = $('#programSelect').val();
            if (!programName) {
                alert('Please select a program.');
                return;
            }

            $.ajax({
                url: 'fetch_program_data.php',
                type: 'POST',
                data: {
                    program_name: programName
                },
                success: function(response) {
                    $('#programDataResult').html(response);
                },
                error: function() {
                    alert('Something went wrong.');
                }
            });
        });
    });
</script>