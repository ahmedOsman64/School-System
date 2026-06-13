<?php
require_once '../lib/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../lib/nav.php'; ?>
        <?php require '../lib/sidebar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6"><h1 class="m-0">Attendance Report</h1></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Attendance Report</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Attendance History</h3>
                            <div class="card-tools">
                                <a href="Attendance.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Take Attendance</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Adm No</th>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT a.*, s.full_name, s.admission_no, c.class_name 
                                            FROM student_attendance a
                                            JOIN students s ON a.student_id = s.id
                                            JOIN classes c ON a.class_id = c.id
                                            ORDER BY a.attendance_date DESC, s.full_name ASC";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        $badge = 'secondary';
                                        if($row['status'] == 'Present') $badge = 'success';
                                        elseif($row['status'] == 'Absent') $badge = 'danger';
                                        elseif($row['status'] == 'Late') $badge = 'warning';

                                        echo "<tr>
                                            <td>{$row['attendance_date']}</td>
                                            <td>" . htmlspecialchars($row['admission_no']) . "</td>
                                            <td>" . htmlspecialchars($row['full_name']) . "</td>
                                            <td>" . htmlspecialchars($row['class_name']) . "</td>
                                            <td><span class='badge badge-$badge'>{$row['status']}</span></td>
                                            <td>
                                                <a href='attendance_delete.php?id={$row['id']}' class='btn btn-xs btn-danger' onclick='return confirm(\"Are you sure?\");'><i class='fas fa-trash'></i></a>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../lib/footer.php'; ?>
    </div>
    <?php include '../lib/script.php'; ?>
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(function () {
            $("#example1").DataTable({
                "order": [[ 0, "desc" ]]
            });
        });
    </script>
</body>
</html>
