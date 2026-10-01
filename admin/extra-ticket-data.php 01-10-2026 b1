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

                <h4 class="mb-4 text-danger text-center">🎟️ Extra Ticket Log </h4>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="extraTicketTable" class="table table-bordered table-striped table-hover">
                                <thead class="bg-primary text-white text-center">
                                    <tr>
                                        <th>ID</th>
                                        <!-- <th>Student ID</th> -->
                                        <!-- <th>Student Name</th> -->
                                        <th>Added Tickets</th>
                                        <th>Ticket Price</th>
                                        <th>Total Added</th>
                                        <th>Added By (Admin)</th>
                                        <th>Added On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // ✅ Fixed join based on actual likely column names
                                    $query = "
                                        SELECT 
                                            e.id,
                                            e.student_id,
                                            r.name_in_full AS student_name,
                                            e.added_tickets,
                                            e.ticket_price,
                                            e.total_added,
                                            a.admin_name AS added_by_name,
                                            e.added_on
                                        FROM extra_ticket_log e
                                        LEFT JOIN registered_students r ON e.student_id = r.student_id
                                        LEFT JOIN admin a ON e.added_by = a.id
                                        ORDER BY e.id DESC
                                    ";

                                    $result = mysqli_query($conn, $query);

                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr class='text-center'>";
                                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                            // echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                                            // echo "<td>" . htmlspecialchars($row['student_name'] ?? '—') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['added_tickets']) . "</td>";
                                            echo "<td>" . htmlspecialchars(number_format($row['ticket_price'], 2)) . "</td>";
                                            echo "<td><strong>" . htmlspecialchars(number_format($row['total_added'], 2)) . "</strong></td>";
                                            echo "<td>" . htmlspecialchars($row['added_by_name'] ?? '—') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['added_on']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center text-muted'>No records found</td></tr>";
                                    }
                                    ?>
                                </tbody>
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
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#extraTicketTable').DataTable({
            "pageLength": 10,
            "order": [
                [0, "desc"]
            ],
            "responsive": true
        });
    });
</script>