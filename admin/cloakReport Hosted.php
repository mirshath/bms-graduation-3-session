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

                <h4 class="mb-4 text-danger text-center">Cloak Issued Report and Return Report</h4>


                <div class="card w-100 container shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3 text-primary"><i class="fas fa-user-tag"></i> Clothing Collections</h5>
                        <?php
                        // Filters
                        $sessionFilter = isset($_GET['session']) ? strtoupper(trim($_GET['session'])) : '';
                        $returnFilter  = isset($_GET['return_status']) ? trim($_GET['return_status']) : '';
                        $programFilter = isset($_GET['program']) ? trim($_GET['program']) : '';

                        $allowedSessions = ['MORNING', 'EVENING'];
                        $allowedReturn   = ['issued', 'returned', 'not_returned', 'fully_returned', 'partial_returned'];

                        if (!in_array($sessionFilter, $allowedSessions)) {
                            $sessionFilter = '';
                        }
                        if (!in_array($returnFilter, $allowedReturn)) {
                            $returnFilter = '';
                        }

                        // Build WHERE clause
                        $where = [];
                        if ($sessionFilter !== '') {
                            $where[] = "dt.session = '" . mysqli_real_escape_string($conn, $sessionFilter) . "'";
                        }
                        if ($programFilter !== '') {
                            $where[] = "cc.program_name = '" . mysqli_real_escape_string($conn, $programFilter) . "'";
                        }
                        $col_cnt = "(CASE WHEN COALESCE(cc.collect_cloak,'')!='' THEN 1 ELSE 0 END + CASE WHEN COALESCE(cc.collect_slashes,'')!='' THEN 1 ELSE 0 END + CASE WHEN COALESCE(cc.collect_hats,'')!='' THEN 1 ELSE 0 END)";
                        $ret_cnt = "(CASE WHEN COALESCE(cc.return_cloak,'')!='' THEN 1 ELSE 0 END + CASE WHEN COALESCE(cc.return_slashes,'')!='' THEN 1 ELSE 0 END + CASE WHEN COALESCE(cc.return_hats,'')!='' THEN 1 ELSE 0 END)";
                        if ($returnFilter === 'issued') {
                            $where[] = "($col_cnt > 0)";
                        }
                        if ($returnFilter === 'returned') {
                            $where[] = "($ret_cnt > 0)";
                        }
                        if ($returnFilter === 'not_returned') {
                            $where[] = "($col_cnt > 0 AND $ret_cnt = 0)";
                        }
                        if ($returnFilter === 'fully_returned') {
                            $where[] = "($col_cnt > 0 AND $ret_cnt > 0 AND $col_cnt = $ret_cnt)";
                        }
                        if ($returnFilter === 'partial_returned') {
                            $where[] = "($col_cnt > 0 AND $ret_cnt > 0 AND $ret_cnt < $col_cnt)";
                        }
                        $where_sql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

                        // Main query
                        $query = "
                            SELECT cc.id, cc.student_id, cc.student_name, cc.program_name,
                                   cc.collect_cloak, cc.collect_slashes, cc.collect_hats, cc.collected_at,
                                   cc.return_cloak, cc.return_slashes, cc.return_hats,
                                   dt.session,
                                   rs.phone_no
                            FROM clothing_collections cc
                            LEFT JOIN data_tables dt ON cc.program_name = dt.programName
                            LEFT JOIN registered_students rs ON rs.student_id = cc.student_id
                            $where_sql
                            ORDER BY cc.collected_at DESC
                        ";
                        $collections = mysqli_query($conn, $query);

                        // Preload programs for filter
                        $programs = [];
                        $progRes = mysqli_query($conn, "SELECT DISTINCT programName FROM data_tables ORDER BY programName");
                        if ($progRes) {
                            while ($row = mysqli_fetch_assoc($progRes)) {
                                if (!empty($row['programName'])) {
                                    $programs[] = $row['programName'];
                                }
                            }
                        }

                        // Counters
                        $totalRecords      = 0;
                        $totalIssued       = 0;
                        $totalReturned     = 0;
                        $totalNotReturned  = 0;
                        $rows = [];

                        if ($collections && mysqli_num_rows($collections) > 0) {
                            while ($c = mysqli_fetch_assoc($collections)) {
                                $totalRecords++;

                                $collected_items = [];
                                if (!empty($c['collect_cloak'])) {
                                    $collected_items[] = 'Cloak';
                                }
                                if (!empty($c['collect_slashes'])) {
                                    $collected_items[] = 'Slashes';
                                }
                                if (!empty($c['collect_hats'])) {
                                    $collected_items[] = 'Hats';
                                }

                                $returned_items = [];
                                if (!empty($c['return_cloak'])) {
                                    $returned_items[] = 'Cloak';
                                }
                                if (!empty($c['return_slashes'])) {
                                    $returned_items[] = 'Slashes';
                                }
                                if (!empty($c['return_hats'])) {
                                    $returned_items[] = 'Hats';
                                }

                                $collected_count = count($collected_items);
                                $returned_count  = count($returned_items);

                                if ($collected_count > 0) {
                                    $totalIssued++;
                                }
                                if ($returned_count > 0) {
                                    $totalReturned++;
                                }
                                if ($collected_count > 0 && $returned_count === 0) {
                                    $totalNotReturned++;
                                }

                                $cloak_status   = !empty($c['collect_cloak']) ? (!empty($c['return_cloak']) ? 'Returned' : 'Issued') : 'Not Issued';
                                $slashes_status = !empty($c['collect_slashes']) ? (!empty($c['return_slashes']) ? 'Returned' : 'Issued') : 'Not Issued';
                                $hats_status    = !empty($c['collect_hats']) ? (!empty($c['return_hats']) ? 'Returned' : 'Issued') : 'Not Issued';

                                $overall_status = 'N/A';
                                if ($collected_count > 0) {
                                    if ($returned_count === 0) {
                                        $overall_status = 'Issued';
                                    } elseif ($returned_count >= $collected_count) {
                                        $overall_status = 'Fully Returned';
                                    } else {
                                        $overall_status = 'Partial Returned';
                                    }
                                }

                                $cloak_class   = $cloak_status === 'Returned' ? 'badge-success' : ($cloak_status === 'Issued' ? 'badge-warning' : 'badge-secondary');
                                $slashes_class = $slashes_status === 'Returned' ? 'badge-success' : ($slashes_status === 'Issued' ? 'badge-warning' : 'badge-secondary');
                                $hats_class    = $hats_status === 'Returned' ? 'badge-success' : ($hats_status === 'Issued' ? 'badge-warning' : 'badge-secondary');
                                $overall_class = $overall_status === 'Fully Returned' ? 'badge-success' : ($overall_status === 'Partial Returned' ? 'badge-info' : ($overall_status === 'Issued' ? 'badge-warning' : 'badge-secondary'));

                                $rows[] = [
                                    'raw'             => $c,
                                    'collected_items' => $collected_items,
                                    'returned_items'  => $returned_items,
                                    'cloak_status'    => $cloak_status,
                                    'slashes_status'  => $slashes_status,
                                    'hats_status'     => $hats_status,
                                    'overall_status'  => $overall_status,
                                    'cloak_class'     => $cloak_class,
                                    'slashes_class'   => $slashes_class,
                                    'hats_class'      => $hats_class,
                                    'overall_class'   => $overall_class,
                                ];
                            }
                        }
                        ?>

                        <!-- Filter section (designed card) -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light py-3">
                                <h6 class="mb-0 text-dark"><i class="fas fa-filter me-2"></i>Filters</h6>
                            </div>
                            <div class="card-body">
                                <form id="filterForm" method="get">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-3 col-sm-6">
                                            <label for="session" class="form-label small text-muted mb-1">Session</label>
                                            <select name="session" id="session" class="form-control form-control-sm select2-filter w-100">
                                                <option value="">All Sessions</option>
                                                <option value="MORNING" <?= $sessionFilter === 'MORNING' ? 'selected' : '' ?>>Morning</option>
                                                <option value="EVENING" <?= $sessionFilter === 'EVENING' ? 'selected' : '' ?>>Evening</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <label for="program" class="form-label small text-muted mb-1">Program</label>
                                            <select name="program" id="program" class="form-control form-control-sm select2-filter w-100">
                                                <option value="">All Programs</option>
                                                <?php foreach ($programs as $prog): ?>
                                                    <option value="<?= htmlspecialchars($prog) ?>" <?= $programFilter === $prog ? 'selected' : '' ?>><?= htmlspecialchars($prog) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <label for="return_status" class="form-label small text-muted mb-1">Return Status</label>
                                            <select name="return_status" id="return_status" class="form-control form-control-sm select2-filter w-100">
                                                <option value="">All Status</option>
                                                <option value="issued" <?= $returnFilter === 'issued' ? 'selected' : '' ?>>Issued</option>
                                                <option value="not_returned" <?= $returnFilter === 'not_returned' ? 'selected' : '' ?>>Not Returned</option>
                                                <option value="partial_returned" <?= $returnFilter === 'partial_returned' ? 'selected' : '' ?>>Partial Returned</option>
                                                <option value="fully_returned" <?= $returnFilter === 'fully_returned' ? 'selected' : '' ?>>Fully Returned</option>
                                                <option value="returned" <?= $returnFilter === 'returned' ? 'selected' : '' ?>>Returned (any)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-sm-6 d-flex flex-wrap gap-2 align-items-end">
                                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i> Apply</button>
                                            <a href="cloakReport.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-undo me-1"></i> Reset</a>
                                            <?php
                                            $exportQuery = http_build_query(array_filter([
                                                'session' => $sessionFilter,
                                                'program' => $programFilter,
                                                'return_status' => $returnFilter
                                            ]));
                                            ?>
                                            <a href="cloakReportExport.php?<?= $exportQuery ?>" class="btn btn-success btn-sm" target="_blank"><i class="fas fa-file-export me-1"></i> Export</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Summary cards -->
                        <div class="row mb-3">
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="card border-left-primary h-100">
                                    <div class="card-body py-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Records</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalRecords ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="card border-left-warning h-100">
                                    <div class="card-body py-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Issued</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalIssued ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="card border-left-success h-100">
                                    <div class="card-body py-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Returned</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalReturned ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="card border-left-danger h-100">
                                    <div class="card-body py-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Not Returned</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalNotReturned ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table id="collectionsTable" class="table table-bordered table-striped" style="font-size: 13px;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Program</th>
                                        <th>Session</th>
                                        <th>Issued Items</th>
                                        <th>Issued At</th>
                                        <th>Returned Items</th>
                                        <th>Cloak</th>
                                        <th>Slashes</th>
                                        <th>Hats</th>
                                        <th>Return Status</th>
                                        <th>Phone</th>
                                    </tr>
                                </thead>
                                <!--<tbody>-->
                                <tbody style="font-size: 12px;">
                                    <?php if (!empty($rows)) : ?>
                                        <?php foreach ($rows as $row) :
                                            $c               = $row['raw'];
                                            $collected_items = $row['collected_items'];
                                            $returned_items  = $row['returned_items'];
                                            $cloak_status    = $row['cloak_status'];
                                            $slashes_status  = $row['slashes_status'];
                                            $hats_status     = $row['hats_status'];
                                            $overall_status  = $row['overall_status'];
                                            $cloak_class     = $row['cloak_class'];
                                            $slashes_class   = $row['slashes_class'];
                                            $hats_class      = $row['hats_class'];
                                            $overall_class   = $row['overall_class'];
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($c['student_id']) ?></td>
                                                <td><?= htmlspecialchars($c['student_name']) ?></td>
                                                <td><?= htmlspecialchars($c['program_name']) ?></td>
                                                <td><?= htmlspecialchars($c['session'] ?? '') ?></td>
                                                <td>
                                                    <?php foreach ($collected_items as $ci) : ?>
                                                        <span class="badge badge-info mr-1"><?= htmlspecialchars($ci) ?></span>
                                                    <?php endforeach; ?>
                                                </td>
                                                <td><?= htmlspecialchars($c['collected_at']) ?></td>
                                                <td>
                                                    <?php foreach ($returned_items as $ri) : ?>
                                                        <span class="badge badge-success mr-1"><?= htmlspecialchars($ri) ?></span>
                                                    <?php endforeach; ?>
                                                </td>
                                                <td><span class="badge <?= $cloak_class ?>"><?= htmlspecialchars($cloak_status) ?></span></td>
                                                <td><span class="badge <?= $slashes_class ?>"><?= htmlspecialchars($slashes_status) ?></span></td>
                                                <td><span class="badge <?= $hats_class ?>"><?= htmlspecialchars($hats_status) ?></span></td>
                                                <td><span class="badge <?= $overall_class ?>"><?= htmlspecialchars($overall_status) ?></span></td>
                                                <td><?= htmlspecialchars($c['phone_no'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="vendor/jquery/jquery.min.js"></script>
<!-- <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(function() {
        $('#collectionsTable').DataTable({
            pageLength: 500,
            lengthMenu: [
                [50, 100, 250, 500, -1],
                [50, 100, 250, 500, "All"]
            ],
            order: [
                [5, 'desc']
            ]
        });
        $('.select2, .select2-filter').select2({
            width: '100%'
        });
        // Auto-submit filters on any change
        $('#session, #program, #return_status').on('change', function() {
            $('#filterForm')[0].submit();
        });
    });
</script>
