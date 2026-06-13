<?php
require_once '../lib/db.php';

$edit_id = '';
$edit_name = '';
$edit_class_id = '';

// Handle Add/Update Subject
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_subject'])) {
    $subject_name = $_POST['subject_name'];
    $class_id = $_POST['class_id'];
    $id = $_POST['subject_id'];

    if ($id) {
        $stmt = $conn->prepare("UPDATE subjects SET subject_name = ?, class_id = ? WHERE id = ?");
        $stmt->bind_param("sii", $subject_name, $class_id, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO subjects (subject_name, class_id) VALUES (?, ?)");
        $stmt->bind_param("si", $subject_name, $class_id);
    }
    
    $stmt->execute();
    header("Location: subjects.php");
    exit();
}

// Prepare Edit
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM subjects WHERE id = $edit_id");
    if ($row = $res->fetch_assoc()) {
        $edit_name = $row['subject_name'];
        $edit_class_id = $row['class_id'];
    }
}

// Handle CSV Import
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_import'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        
        // Skip header
        fgetcsv($handle);
        
        $count = 0;
        $errors = [];
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $sub_name = trim($data[0]);
            $cls_name = trim($data[1]);

            if (!empty($sub_name) && !empty($cls_name)) {
                // Find class ID by name
                $res_cls = $conn->query("SELECT id FROM classes WHERE class_name = '" . $conn->real_escape_string($cls_name) . "'");
                if ($res_cls && $res_cls->num_rows > 0) {
                    $cls_id = $res_cls->fetch_assoc()['id'];
                    // Insert subject
                    $stmt = $conn->prepare("INSERT INTO subjects (subject_name, class_id) VALUES (?, ?)");
                    $stmt->bind_param("si", $sub_name, $cls_id);
                    $stmt->execute();
                    $count++;
                } else {
                    $errors[] = "Class '$cls_name' not found for subject '$sub_name'.";
                }
            }
        }
        fclose($handle);
        $import_success = "Successfully imported $count subjects!";
        if (!empty($errors)) {
            $import_error = implode("<br>", $errors);
        }
    }
}

// Handle Template Download
if (isset($_GET['download_template'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=subjects_template.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Subject Name', 'Class Name']);
    // Optional: Add examples
    fputcsv($output, ['Mathematics', 'Grade 1']);
    fputcsv($output, ['Physics', 'Grade 2']);
    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <title>Subjects - School System</title>
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
                            <h1 class="m-0">Subjects</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Subjects</li>
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
                                    <h3 class="card-title"><?= $edit_id ? 'Edit Subject' : 'Add New Subject' ?></h3>
                                </div>
                                <form method="POST">
                                    <input type="hidden" name="subject_id" value="<?= $edit_id ?>">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Subject Name</label>
                                            <input type="text" name="subject_name" class="form-control" required placeholder="e.g. Mathematics" value="<?= htmlspecialchars($edit_name) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Grade (Class)</label>
                                            <select name="class_id" class="form-control" required>
                                                <option value="">Select Grade</option>
                                                <?php
                                                $classes = $conn->query("SELECT * FROM classes ORDER BY class_name ASC");
                                                while ($c = $classes->fetch_assoc()) {
                                                    $sel = ($c['id'] == $edit_class_id) ? 'selected' : '';
                                                    echo "<option value='{$c['id']}' $sel>{$c['class_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="save_subject" class="btn btn-primary btn-block"><?= $edit_id ? 'Update Subject' : 'Add Subject' ?></button>
                                        <?php if($edit_id): ?>
                                            <a href="subjects.php" class="btn btn-default btn-block">Cancel</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>

                            <div class="card card-success mt-3">
                                <div class="card-header">
                                    <h3 class="card-title">Import Subjects (Excel/CSV)</h3>
                                </div>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <?php if(isset($import_success)): ?>
                                            <div class="alert alert-success alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                <?= $import_success ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if(isset($import_error)): ?>
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                <?= $import_error ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="form-group">
                                            <label>Select CSV File</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="csv_file" class="custom-file-input" accept=".csv" required id="csv_file">
                                                    <label class="custom-file-label" for="csv_file">Choose file</label>
                                                </div>
                                            </div>
                                            <small class="text-muted">Columns: Subject Name, Class Name. <a href="subjects.php?download_template=1">Download Template</a></small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="btn_import" class="btn btn-success btn-block">Import Subjects</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Subject List</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Subject Name</th>
                                                <th>Grade</th>
                                                <th style="width: 150px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $result = $conn->query("SELECT s.*, c.class_name FROM subjects s LEFT JOIN classes c ON s.class_id = c.id ORDER BY c.class_name ASC, s.subject_name ASC");
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>
                                                        <td>{$row['id']}</td>
                                                        <td>" . htmlspecialchars($row['subject_name']) . "</td>
                                                        <td>" . htmlspecialchars($row['class_name'] ?? 'N/A') . "</td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <a href='subjects.php?edit={$row['id']}' class='btn btn-success btn-xs'><i class='fas fa-edit'></i> Edit</a>
                                                                <a href='subjects.php?delete={$row['id']}' class='btn btn-danger btn-xs' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i> Del</a>
                                                            </div>
                                                        </td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='4' class='text-center'>No subjects found</td></tr>";
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
        $(document).ready(function () {
            bsCustomFileInput.init();
        });
    </script>
</body>
</html>
