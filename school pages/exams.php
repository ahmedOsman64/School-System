<?php
require_once '../lib/db.php';

$edit_id = '';
$edit_name = '';
$edit_year = '';
$edit_date = '';
$edit_desc = '';

// Handle Add/Update Exam
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_exam'])) {
    $exam_name = $_POST['exam_name'];
    $academic_year_id = $_POST['academic_year_id'];
    $exam_date = $_POST['exam_date'];
    $description = $_POST['description'];
    $id = $_POST['exam_id'];

    if ($id) {
        $stmt = $conn->prepare("UPDATE exams SET exam_name = ?, academic_year_id = ?, exam_date = ?, description = ? WHERE id = ?");
        $stmt->bind_param("sissi", $exam_name, $academic_year_id, $exam_date, $description, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO exams (exam_name, academic_year_id, exam_date, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $exam_name, $academic_year_id, $exam_date, $description);
    }
    
    $stmt->execute();
    header("Location: exams.php");
    exit();
}

// Prepare Edit
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM exams WHERE id = $edit_id");
    if ($row = $res->fetch_assoc()) {
        $edit_name = $row['exam_name'];
        $edit_year = $row['academic_year_id'];
        $edit_date = $row['exam_date'];
        $edit_desc = $row['description'];
    }
}

// Handle Delete Exam
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM exams WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: exams.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <title>Exams - School System</title>
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
                            <h1 class="m-0">Exams</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Exams</li>
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
                                    <h3 class="card-title"><?= $edit_id ? 'Edit Exam' : 'Add New Exam' ?></h3>
                                </div>
                                <form method="POST">
                                    <input type="hidden" name="exam_id" value="<?= $edit_id ?>">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Exam Name</label>
                                            <input type="text" name="exam_name" class="form-control" required placeholder="e.g. First Midterm" value="<?= htmlspecialchars($edit_name) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Academic Year</label>
                                            <select name="academic_year_id" class="form-control" required>
                                                <option value="">-- Select Year --</option>
                                                <?php
                                                $years = $conn->query("SELECT * FROM academic_years ORDER BY year_name DESC");
                                                while($y = $years->fetch_assoc()) {
                                                    $selected = ($y['id'] == $edit_year) ? 'selected' : '';
                                                    echo "<option value='{$y['id']}' $selected>{$y['year_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Exam Date</label>
                                            <input type="date" name="exam_date" class="form-control" value="<?= htmlspecialchars($edit_date) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($edit_desc) ?></textarea>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="save_exam" class="btn btn-primary btn-block"><?= $edit_id ? 'Update Exam' : 'Add Exam' ?></button>
                                        <?php if($edit_id): ?>
                                            <a href="exams.php" class="btn btn-default btn-block">Cancel</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Exam List</h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Exam Name</th>
                                                <th>Year</th>
                                                <th>Date</th>
                                                <th style="width: 150px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $result = $conn->query("SELECT e.*, y.year_name FROM exams e JOIN academic_years y ON e.academic_year_id = y.id ORDER BY e.id DESC");
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>
                                                        <td>{$row['id']}</td>
                                                        <td>{$row['exam_name']}</td>
                                                        <td>{$row['year_name']}</td>
                                                        <td>" . ($row['exam_date'] ? date('d-m-Y', strtotime($row['exam_date'])) : 'N/A') . "</td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <a href='exams.php?edit={$row['id']}' class='btn btn-success btn-xs'><i class='fas fa-edit'></i> Edit</a>
                                                                <a href='exams.php?delete={$row['id']}' class='btn btn-danger btn-xs' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i> Del</a>
                                                            </div>
                                                        </td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='5' class='text-center'>No exams found</td></tr>";
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
