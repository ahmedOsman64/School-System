<?php
require_once '../lib/db.php';

$edit_id = '';
$edit_name = '';
$edit_start = '';
$edit_end = '';
$edit_status = 0;

// Handle Add/Update Academic Year
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_year'])) {
    $year_name = $_POST['year_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = isset($_POST['status']) ? 1 : 0;
    $id = $_POST['year_id'];

    if ($status == 1) {
        // Deactivate all other years if this one is active
        $conn->query("UPDATE academic_years SET status = 0");
    }

    if ($id) {
        $stmt = $conn->prepare("UPDATE academic_years SET year_name = ?, start_date = ?, end_date = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssii", $year_name, $start_date, $end_date, $status, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO academic_years (year_name, start_date, end_date, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $year_name, $start_date, $end_date, $status);
    }
    
    $stmt->execute();
    header("Location: academic_years.php");
    exit();
}

// Prepare Edit
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM academic_years WHERE id = $edit_id");
    if ($row = $res->fetch_assoc()) {
        $edit_name = $row['year_name'];
        $edit_start = $row['start_date'];
        $edit_end = $row['end_date'];
        $edit_status = $row['status'];
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM academic_years WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: academic_years.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <title>Academic Years - School System</title>
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
                            <h1 class="m-0">Academic Years</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Academic Years</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title"><?= $edit_id ? 'Edit Academic Year' : 'Add New Year' ?></h3>
                                </div>
                                <form method="POST">
                                    <input type="hidden" name="year_id" value="<?= $edit_id ?>">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Year Name</label>
                                            <input type="text" name="year_name" class="form-control" required placeholder="e.g. 2023-2024" value="<?= htmlspecialchars($edit_name) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="date" name="start_date" class="form-control" required value="<?= $edit_start ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="date" name="end_date" class="form-control" required value="<?= $edit_end ?>">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" name="status" class="custom-control-input" id="statusSwitch" <?= $edit_status ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="statusSwitch">Set as Active Year</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="save_year" class="btn btn-primary btn-block"><?= $edit_id ? 'Update Year' : 'Add Year' ?></button>
                                        <?php if($edit_id): ?>
                                            <a href="academic_years.php" class="btn btn-default btn-block">Cancel</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Academic Year List</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Year Name</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Status</th>
                                                <th style="width: 150px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $result = $conn->query("SELECT * FROM academic_years ORDER BY start_date DESC");
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $status_badge = $row['status'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>';
                                                    echo "<tr>
                                                        <td>{$row['id']}</td>
                                                        <td>" . htmlspecialchars($row['year_name']) . "</td>
                                                        <td>{$row['start_date']}</td>
                                                        <td>{$row['end_date']}</td>
                                                        <td>$status_badge</td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <a href='academic_years.php?edit={$row['id']}' class='btn btn-success btn-xs'><i class='fas fa-edit'></i> Edit</a>
                                                                <a href='academic_years.php?delete={$row['id']}' class='btn btn-danger btn-xs' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i> Del</a>
                                                            </div>
                                                        </td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
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
</body>
</html>
