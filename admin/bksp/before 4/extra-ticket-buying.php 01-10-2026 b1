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

                <h4 class="mb-4 text-danger text-center">Extra Ticket Buying</h4>

                <div class="card w-75 mb-4 container shadow-sm">
                    <div class="card-body">
                        <h5>Add More Extra Tickets</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Number of Tickets</label>
                                <select id="additionalTickets" class="form-control">
                                    <?php for ($i = 1; $i <= 3; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Price Per Ticket</label>
                                <input type="number" id="ticketPriceInput" class="form-control" min="0" step="0.01" value="2500">
                            </div>
                            <div class="col-md-4">
                                <label>Total Additional</label>
                                <p class="form-control-plaintext">Rs. <span id="totalAdded">0.00</span></p>
                            </div>
                        </div>
                        <button id="addExtraTicketBtn" class="btn btn-success mt-3 w-100">
                            <i class="fas fa-plus-circle"></i> Add Extra Ticket(s)
                        </button>
                    </div>
                </div>

                <!-- Today's Added Tickets -->
                <div class="card w-75 container shadow-sm">
                    <div class="card-header bg-info text-white mt-3">
                        <h6 class="m-0">Today's Added Tickets</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $todayRows = [];
                        $totalTicketsToday = 0;
                        $totalAmountToday = 0.0;
                        $resToday = $conn->query("
                            SELECT 
                                e.id,
                                e.added_tickets,
                                e.ticket_price,
                                e.total_added,
                                e.added_on,
                                COALESCE(a.admin_name, e.added_by) AS added_by_name
                            FROM extra_ticket_log e
                            LEFT JOIN admin a ON e.added_by = a.id
                            WHERE DATE(e.added_on) = CURDATE()
                            ORDER BY e.added_on DESC
                        ");
                        if ($resToday) {
                            while ($r = $resToday->fetch_assoc()) {
                                $todayRows[] = $r;
                                $totalTicketsToday += (int)$r['added_tickets'];
                                $totalAmountToday += (float)$r['total_added'];
                            }
                        }
                        ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="alert alert-secondary mb-0">
                                    Total Extra Tickets Today: <strong><?= (int)$totalTicketsToday ?></strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-secondary mb-0 text-right">
                                    Total Amount Today (Extra Ticket): <strong>Rs. <?= number_format($totalAmountToday, 2) ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="todayExtraTickets">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Time</th>
                                        <th>Tickets</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Admin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($todayRows)) { ?>
                                        <tr><td colspan="6" class="text-center text-muted">No tickets added today</td></tr>
                                    <?php } else { 
                                        $i = 1;
                                        foreach ($todayRows as $row) { ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td data-order="<?= strtotime($row['added_on']) ?>"><?= date('h:i A', strtotime($row['added_on'])) ?></td>
                                                <td><?= (int)$row['added_tickets'] ?></td>
                                                <td>Rs. <?= number_format($row['ticket_price'], 2) ?></td>
                                                <td><strong>Rs. <?= number_format($row['total_added'], 2) ?></strong></td>
                                                <td><?= htmlspecialchars($row['added_by_name']) ?></td>
                                            </tr>
                                    <?php } } ?>
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
        function recalc() {
            const count = parseInt($('#additionalTickets').val()) || 0;
            const price = parseFloat($('#ticketPriceInput').val()) || 0;
            $('#totalAdded').text((count * price).toFixed(2));
        }

        $('#ticketPriceInput').val('2500');
        $('#additionalTickets').val('0');
        recalc();

        $('#additionalTickets').on('change', function() {
            recalc();
        });
        $('#ticketPriceInput').on('input', recalc);

        $('#addExtraTicketBtn').click(function() {
            const addTickets = parseInt($('#additionalTickets').val());
            const ticketPrice = parseFloat($('#ticketPriceInput').val());

            if (!addTickets || addTickets <= 0) {
                alert('Please enter a valid number of tickets.');
                return;
            }
            if (!ticketPrice || ticketPrice <= 0) {
                alert('Please enter a valid ticket price.');
                return;
            }

            $.ajax({
                url: 'update_extra_ticket.php',
                type: 'POST',
                data: {
                    addTickets: addTickets,
                    ticketPrice: ticketPrice
                },
                success: function(response) {
                    alert(response);
                    location.reload();
                }
            });
        });

        if ($('#todayExtraTickets').length) {
            $('#todayExtraTickets').DataTable({
                order: [[1, 'desc']],
                pageLength: 100,
                lengthMenu: [10, 25, 50, 100]
            });
        }
    });
</script>
