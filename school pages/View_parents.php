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
                        <div class="col-sm-6"><h1 class="m-0">View Parents</h1></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">View Parents</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">All Parents</h3>
                            <div class="card-tools">
                                <a href="Parents.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Parent</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Father Name</th>
                                        <th>Mother Name</th>
                                        <th>Phone</th>
                                        <th>Students</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT p.*, GROUP_CONCAT(s.full_name SEPARATOR ', ') as students 
                                            FROM parents p 
                                            LEFT JOIN students s ON s.parent_id = p.id 
                                            GROUP BY p.id 
                                            ORDER BY p.id DESC";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        $students = !empty($row['students']) ? $row['students'] : '<span class="text-muted">No students</span>';
                                        echo "<tr>
                                            <td>{$row['id']}</td>
                                            <td>" . htmlspecialchars($row['father_name']) . "</td>
                                            <td>" . htmlspecialchars($row['mother_name']) . "</td>
                                            <td>" . htmlspecialchars($row['phone']) . "</td>
                                            <td>" . $students . "</td>
                                            <td>" . htmlspecialchars($row['email']) . "</td>
                                            <td>
                                                <a href='parent_edit.php?id={$row['id']}' class='btn btn-xs btn-success'><i class='fas fa-edit'></i></a>
                                                <a href='parent_delete.php?id={$row['id']}' class='btn btn-xs btn-danger' onclick='return confirm(\"Are you sure?\");'><i class='fas fa-trash'></i></a>
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
            $("#example1").DataTable();
        });
    </script>
</body>
</html>
