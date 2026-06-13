<?php
require_once '../lib/db.php';

$success = '';
$error = '';

$class_id = $_GET['class_id'] ?? '';
$section_id = $_GET['section_id'] ?? '';
$subject_id = $_GET['subject_id'] ?? '';
$exam_id = $_GET['exam_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_marks'])) {
    $marks = $_POST['marks']; // array student_id => mark
    $exam_id = $_POST['exam_id'];
    $subject_id = $_POST['subject_id'];
    $max_marks = $_POST['max_marks'];

    foreach ($marks as $student_id => $mark_value) {
        if ($mark_value !== '') {
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
        }
    }
    $success = "Marks saved successfully!";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <title>Marks Entry - School System</title>
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
                            <h1 class="m-0">Marks Entry</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Marks Entry</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Filter Selection</h3>
                        </div>
                        <form method="GET">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
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
                                    <div class="col-md-3">
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
                                    <div class="col-md-3">
                                        <label>Subject</label>
                                        <select name="subject_id" class="form-control" required>
                                            <option value="">-- Select Subject --</option>
                                            <?php
                                            $subjects = $conn->query("SELECT * FROM subjects ORDER BY subject_name");
                                            while($s = $subjects->fetch_assoc()) {
                                                $sel = ($s['id'] == $subject_id) ? 'selected' : '';
                                                echo "<option value='{$s['id']}' $sel>{$s['subject_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
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
                                <button type="submit" class="btn btn-primary">Filter Students</button>
                            </div>
                        </form>
                    </div>

                    <?php if($class_id && $section_id && $subject_id && $exam_id): ?>
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">Enter Marks</h3>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="exam_id" value="<?= $exam_id ?>">
                                <input type="hidden" name="subject_id" value="<?= $subject_id ?>">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <label>Max Marks</label>
                                            <input type="number" name="max_marks" class="form-control" value="100" required>
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Admission No</th>
                                                <th>Student Name</th>
                                                <th>Marks Obtained</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $students = $conn->query("SELECT id, full_name, admission_no FROM students WHERE class_id = $class_id AND section_id = $section_id ORDER BY full_name");
                                            while($st = $students->fetch_assoc()) {
                                                // Get existing mark if any
                                                $mark_res = $conn->query("SELECT marks_obtained FROM exam_marks WHERE exam_id = $exam_id AND student_id = {$st['id']} AND subject_id = $subject_id");
                                                $existing_mark = ($mark_res->num_rows > 0) ? $mark_res->fetch_assoc()['marks_obtained'] : '';
                                                echo "<tr>
                                                    <td>{$st['admission_no']}</td>
                                                    <td>{$st['full_name']}</td>
                                                    <td>
                                                        <input type='number' step='0.01' name='marks[{$st['id']}]' class='form-control' value='$existing_mark' placeholder='Enter Marks'>
                                                    </td>
                                                </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" name="save_marks" class="btn btn-success float-right">Save All Marks</button>
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
