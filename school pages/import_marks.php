<?php
require_once '../lib/db.php';

$success = '';
$error = '';

// Handle Template Download
if (isset($_GET['download'])) {
    $class_id = $_GET['class_id'];
    $section_id = $_GET['section_id'];
    $exam_id = $_GET['exam_id'];

    if (!$class_id || !$section_id || !$exam_id) {
        die("Missing parameters for template.");
    }

    // Get Info for headers
    $class_name = $conn->query("SELECT class_name FROM classes WHERE id = $class_id")->fetch_assoc()['class_name'];
    
    // Get Subjects linked to this class
    $subjects = [];
    $sub_res = $conn->query("SELECT s.id, s.subject_name FROM subjects s WHERE s.class_id = $class_id");
    while($s = $sub_res->fetch_assoc()) {
        $subjects[] = $s;
    }

    $filename = "Marks_Template_{$class_name}.csv";
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    
    $output = fopen('php://output', 'w');
    
    // Header Row
    $header = ['Student ID', 'Admission No', 'Student Name', 'Class'];
    foreach($subjects as $sub) {
        $header[] = $sub['subject_name'];
    }
    fputcsv($output, $header);
    
    // Get Students
    $students = $conn->query("SELECT id, admission_no, full_name FROM students WHERE class_id = $class_id AND section_id = $section_id ORDER BY full_name");
    while ($row = $students->fetch_assoc()) {
        $student_row = [$row['id'], $row['admission_no'], $row['full_name'], $class_name];
        // Empty values for subjects
        foreach($subjects as $sub) {
            $student_row[] = ''; 
        }
        fputcsv($output, $student_row);
    }
    fclose($output);
    exit();
}

// Handle Import
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnimport'])) {
    $exam_id = $_POST['exam_id'];
    $class_id = $_POST['class_id'];
    
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file_name = $_FILES['csv_file']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        
        if (strtolower($file_ext) !== 'csv') {
            $error = "Faylka aad soo dirtay ma ahan <strong>.CSV</strong>. Fadlan Excel-ka dhexdiis kaga dhig 'Save As CSV' ka dibna soo upload gareey.";
        } else {
            $file = $_FILES['csv_file']['tmp_name'];
            $handle = fopen($file, "r");
            
            // Read header row
            $header = fgetcsv($handle);
            
            // Map subject names to IDs
            $subject_map = []; // column_index => subject_id
            for ($i = 4; $i < count($header); $i++) {
                $sub_name = trim($header[$i]);
                $res = $conn->query("SELECT id FROM subjects WHERE subject_name = '" . $conn->real_escape_string($sub_name) . "' AND class_id = $class_id");
                if ($res && $res->num_rows > 0) {
                    $subject_map[$i] = $res->fetch_assoc()['id'];
                }
            }
            
            $student_count = 0;
            $mark_count = 0;
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $student_id = $data[0];
                if (!$student_id) continue;
                
                $student_count++;
                
                foreach ($subject_map as $col_idx => $subject_id) {
                    $mark_value = trim($data[$col_idx]);
                    if ($mark_value !== '') {
                        $max_marks = 100; // Default max marks
                        
                        // Check if exists
                        $check = $conn->query("SELECT id FROM exam_marks WHERE exam_id = $exam_id AND student_id = $student_id AND subject_id = $subject_id");
                        if ($check->num_rows > 0) {
                            $stmt = $conn->prepare("UPDATE exam_marks SET marks_obtained = ?, max_marks = ? WHERE exam_id = ? AND student_id = ? AND subject_id = ?");
                            $stmt->bind_param("ddiii", $mark_value, $max_marks, $exam_id, $student_id, $subject_id);
                        } else {
                            $stmt = $conn->prepare("INSERT INTO exam_marks (exam_id, student_id, subject_id, marks_obtained, max_marks) VALUES (?, ?, ?, ?, ?)");
                            $stmt->bind_param("iiidd", $exam_id, $student_id, $subject_id, $mark_value, $max_marks);
                        }
                        $stmt->execute();
                        $mark_count++;
                    }
                }
            }
            fclose($handle);
            $success = "Successfully imported marks for $student_count students ($mark_count total marks recorded).";
        }
    } else {
        $error = "Please upload a valid CSV file.";
    }
}

$class_id = $_GET['class_id'] ?? '';
$section_id = $_GET['section_id'] ?? '';
$exam_id = $_GET['exam_id'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <title>Multi-Subject Import - School System</title>
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
                            <h1 class="m-0">Multi-Subject Import (Excel)</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Import Marks</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">1. Select Information & Download Template</h3>
                        </div>
                        <form method="GET">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Class</label>
                                        <select name="class_id" class="form-control" required onchange="fetchSections(this.value)">
                                            <option value="">-- Select Class --</option>
                                            <?php
                                            $classes = $conn->query("SELECT * FROM classes ORDER BY class_name");
                                            while($c = $classes->fetch_assoc()) {
                                                $sel = ($c['id'] == $class_id) ? 'selected' : '';
                                                echo "<option value='{$c['id']}' $sel>{$c['class_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Section</label>
                                        <select name="section_id" id="section_id" class="form-control" required>
                                            <option value="">-- Select Section --</option>
                                            <?php
                                            if($class_id) {
                                                $sections = $conn->query("SELECT * FROM sections WHERE class_id = $class_id");
                                                while($s = $sections->fetch_assoc()) {
                                                    $sel = ($s['id'] == $section_id) ? 'selected' : '';
                                                    echo "<option value='{$s['id']}' $sel>{$s['section_name']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Exam</label>
                                        <select name="exam_id" class="form-control" required>
                                            <option value="">-- Select Exam --</option>
                                            <?php
                                            $exams = $conn->query("SELECT * FROM exams ORDER BY id DESC");
                                            while($e = $exams->fetch_assoc()) {
                                                $sel = ($e['id'] == $exam_id) ? 'selected' : '';
                                                echo "<option value='{$e['id']}' $sel>{$e['exam_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Refresh Filters</button>
                                <?php if($class_id && $section_id && $exam_id): ?>
                                    <a href="import_marks.php?download=1&class_id=<?= $class_id ?>&section_id=<?= $section_id ?>&exam_id=<?= $exam_id ?>" class="btn btn-success ml-2">
                                        <i class="fas fa-download mr-1"></i> Download CSV Template
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <?php if($class_id && $section_id && $exam_id): ?>
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">2. Upload Filled Template</h3>
                            </div>
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="exam_id" value="<?= $exam_id ?>">
                                <input type="hidden" name="class_id" value="<?= $class_id ?>">
                                <div class="card-body">
                                    <?php if($success): ?>
                                        <div class="alert alert-success"><?= $success ?></div>
                                    <?php endif; ?>
                                    <?php if($error): ?>
                                        <div class="alert alert-danger"><?= $error ?></div>
                                    <?php endif; ?>

                                    <div class="form-group">
                                        <label for="csv_file">Select CSV File (Naga Save As CSV Excel-ka)</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="csv_file" name="csv_file" accept=".csv" required>
                                                <label class="custom-file-label" for="csv_file">Choose CSV file</label>
                                            </div>
                                        </div>
                                        <p class="text-danger mt-2">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> <strong>Fiiro gaar ah:</strong> Nidaamku wuxuu akhrinayaa kaliya faylka <strong>.CSV</strong>. Haddii faylkaagu yahay <strong>.xlsx</strong>, fadlan Excel-ka dhexdiisa ku guji <strong>File > Save As</strong> ka dibna u dooro <strong>CSV (Comma delimited) (*.csv)</strong>.
                                        </p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" name="btnimport" class="btn btn-success float-right">
                                        <i class="fas fa-file-import mr-1"></i> Finalize Import
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
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

    function fetchSections(classId) {
        if (!classId) {
            $('#section_id').html('<option value="">-- Select Section --</option>');
            return;
        }
        $.ajax({
            url: 'fetch_sections.php',
            method: 'POST',
            data: {class_id: classId},
            success: function(data) {
                $('#section_id').html(data);
            }
        });
    }
    </script>
</body>
</html>
