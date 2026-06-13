<?php
require_once '../lib/db.php';

$edit_id = '';
$edit_class_id = '';
$edit_name = '';

// Handle Add/Update Section
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_section'])) {
    $class_id = $_POST['class_id'];
    $section_name = $_POST['section_name'];
    $id = $_POST['section_id'];

    if ($id) {
        $stmt = $conn->prepare("UPDATE sections SET class_id = ?, section_name = ? WHERE id = ?");
        $stmt->bind_param("isi", $class_id, $section_name, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO sections (class_id, section_name) VALUES (?, ?)");
        $stmt->bind_param("is", $class_id, $section_name);
    }
    
    $stmt->execute();
    header("Location: sections.php");
    exit();
}

// Prepare Edit
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM sections WHERE id = $edit_id");
    if ($row = $res->fetch_assoc()) {
        $edit_class_id = $row['class_id'];
        $edit_name = $row['section_name'];
    }
}

// Handle Delete Section
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM sections WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: sections.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <title>Sections - School System</title>
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
                            <h1 class="m-0">Sections</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Sections</li>
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
                                    <h3 class="card-title"><?= $edit_id ? 'Edit Section' : 'Add New Section' ?></h3>
                                </div>
                                <form method="POST">
                                    <input type="hidden" name="section_id" value="<?= $edit_id ?>">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Class</label>
                                            <select name="class_id" class="form-control" required>
                                                <option value="">Select Class</option>
                                                <?php
                                                $classes = $conn->query("SELECT * FROM classes ORDER BY class_name ASC");
                                                while ($c = $classes->fetch_assoc()) {
                                                    $sel = ($c['id'] == $edit_class_id) ? 'selected' : '';
                                                    echo "<option value='{$c['id']}' $sel>{$c['class_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Section Name</label>
                                            <input type="text" name="section_name" class="form-control" required placeholder="e.g. A" value="<?= htmlspecialchars($edit_name) ?>">
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="save_section" class="btn btn-primary btn-block"><?= $edit_id ? 'Update Section' : 'Add Section' ?></button>
                                        <?php if($edit_id): ?>
                                            <a href="sections.php" class="btn btn-default btn-block">Cancel</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Section List</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Class</th>
                                                <th>Section</th>
                                                <th style="width: 150px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $result = $conn->query("SELECT s.*, c.class_name FROM sections s JOIN classes c ON s.class_id = c.id ORDER BY c.class_name ASC, s.section_name ASC");
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>
                                                        <td>{$row['id']}</td>
                                                        <td>{$row['class_name']}</td>
                                                        <td>{$row['section_name']}</td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <a href='sections.php?edit={$row['id']}' class='btn btn-success btn-xs'><i class='fas fa-edit'></i> Edit</a>
                                                                <a href='sections.php?delete={$row['id']}' class='btn btn-danger btn-xs' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i> Del</a>
                                                            </div>
                                                        </td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='4' class='text-center'>No sections found</td></tr>";
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
