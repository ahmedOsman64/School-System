<?php
require_once '../lib/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../lib/head.php'; ?>
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
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
                            <h1 class="m-0">View Students</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">View Students</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">All Students</h3>
                                    <div class="card-tools">
                                        <a href="Students.php" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> New Student
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Photo</th>
                                                <th>Adm No</th>
                                                <th>Full Name</th>
                                                <th>Class</th>
                                                <th>Section</th>
                                                <th>Parent</th>
                                                <th>Gender</th>
                                                <th>Phone</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT s.*, c.class_name, sc.section_name, p.father_name 
                                                    FROM students s
                                                    LEFT JOIN classes c ON s.class_id = c.id
                                                    LEFT JOIN sections sc ON s.section_id = sc.id
                                                    LEFT JOIN parents p ON s.parent_id = p.id
                                                    ORDER BY s.id DESC";
                                            $result = $conn->query($sql);
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $photo = !empty($row['photo']) ? "../uploads/students/" . $row['photo'] : "../dist/img/avatar.png";
                                                    $status_badge = $row['status'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
                                                    
                                                    echo "<tr>";
                                                    echo "<td>" . $row['id'] . "</td>";
                                                    echo "<td><img src='$photo' class='img-circle elevation-2' width='30' alt='User Image'></td>";
                                                    echo "<td style='white-space: nowrap;'>" . htmlspecialchars($row['admission_no']) . "</td>";
                                                    echo "<td style='white-space: nowrap;'>" . htmlspecialchars($row['full_name']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['class_name'] ?? 'N/A') . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['section_name'] ?? 'N/A') . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['father_name'] ?? 'N/A') . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                                    echo "<td>" . $status_badge . "</td>";
                                                    echo "<td>
                                                        <div class='d-flex' style='gap: 5px; white-space: nowrap;'>
                                                            <a href='student_edit.php?id=" . $row['id'] . "' class='btn btn-sm btn-success'><i class='fas fa-edit'></i> Edit</a>
                                                            <a href='student_delete.php?id=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\");'><i class='fas fa-trash'></i> Del</a>
                                                            <a href='student_print.php?id=" . $row['id'] . "' target='_blank' class='btn btn-sm btn-info'><i class='fas fa-print'></i> Print</a>
                                                        </div>
                                                    </td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='11' class='text-center'>No students found</td></tr>";
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
        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>

    <?php include '../lib/script.php'; ?>
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../plugins/jszip/jszip.min.js"></script>
    <script src="../plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script>
        $(function () {
            $("#example1").DataTable({
                "responsive": false,
                "scrollX": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>
</body>

</html>
