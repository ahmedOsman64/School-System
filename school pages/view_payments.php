<?php
require_once '../lib/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <title>Fee Payments - School System</title>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../lib/nav.php'; ?>
        <?php require '../lib/sidebar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Fee Payments List</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Fee Payments</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Recent Payments</h3>
                                    <div class="card-tools">
                                        <a href="fee_payments.php" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus mr-1"></i> Collect New Fee
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="paymentsTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Student Name</th>
                                                <th>Admission No</th>
                                                <th>Fee Type</th>
                                                <th>Amount Paid</th>
                                                <th>Date</th>
                                                <th>Method</th>
                                                <th>Reference No</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT p.*, s.full_name, s.admission_no, f.fee_name 
                                                    FROM fee_payments p 
                                                    JOIN students s ON p.student_id = s.id 
                                                    JOIN fee_types f ON p.fee_type_id = f.id 
                                                    ORDER BY p.payment_date DESC";
                                            $result = $conn->query($sql);
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>
                                                        <td>{$row['id']}</td>
                                                        <td>{$row['full_name']}</td>
                                                        <td>{$row['admission_no']}</td>
                                                        <td>{$row['fee_name']}</td>
                                                        <td>\${$row['amount_paid']}</td>
                                                        <td>" . date('d-m-Y', strtotime($row['payment_date'])) . "</td>
                                                        <td>{$row['payment_method']}</td>
                                                        <td>{$row['reference_no']}</td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='8' class='text-center'>No payments found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include '../lib/footer.php'; ?>
    </div>
    <?php include '../lib/script.php'; ?>
    <script>
    $(function () {
        $("#paymentsTable").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#paymentsTable_wrapper .col-md-6:eq(0)');
    });
    </script>
</body>
</html>
