<?php
require_once '../lib/db.php';

$edit_id = '';
$edit_name = '';

// Handle Add/Update Class
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_class'])) {
    $class_name = $_POST['class_name'];
    $id = $_POST['class_id'];

    if ($id) {
        $stmt = $conn->prepare("UPDATE classes SET class_name = ? WHERE id = ?");
        $stmt->bind_param("si", $class_name, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO classes (class_name) VALUES (?)");
        $stmt->bind_param("s", $class_name);
    }
    
    $stmt->execute();
    header("Location: classes.php");
    exit();
}

// Prepare Edit
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM classes WHERE id = $edit_id");
    if ($row = $res->fetch_assoc()) {
        $edit_name = $row['class_name'];
    }
}

// Handle Delete Class
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM classes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: classes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <title>Classes - School System</title>
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
                            <h1 class="m-0">Classes</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Classes</li>
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
                                    <h3 class="card-title"><?= $edit_id ? 'Edit Class' : 'Add New Class' ?></h3>
                                </div>
                                <form method="POST">
                                    <input type="hidden" name="class_id" value="<?= $edit_id ?>">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Class Name</label>
                                            <input type="text" name="class_name" class="form-control" required placeholder="e.g. Grade 1" value="<?= htmlspecialchars($edit_name) ?>">
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="save_class" class="btn btn-primary btn-block"><?= $edit_id ? 'Update Class' : 'Add Class' ?></button>
                                        <?php if($edit_id): ?>
                                            <a href="classes.php" class="btn btn-default btn-block">Cancel</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Class List</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Class Name</th>
                                                <th style="width: 150px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $result = $conn->query("SELECT * FROM classes ORDER BY id DESC");
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>
                                                        <td>{$row['id']}</td>
                                                        <td>{$row['class_name']}</td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <a href='classes.php?edit={$row['id']}' class='btn btn-success btn-xs'><i class='fas fa-edit'></i> Edit</a>
                                                                <a href='classes.php?delete={$row['id']}' class='btn btn-danger btn-xs' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i> Del</a>
                                                            </div>
                                                        </td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='3' class='text-center'>No classes found</td></tr>";
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
