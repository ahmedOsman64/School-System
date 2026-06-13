<?php
require_once '../lib/db.php';

$class_id = $_GET['class_id'] ?? '';
$section_id = $_GET['section_id'] ?? '';
$exam_id = $_GET['exam_id'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <title>Exam Results - School System</title>
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
                            <h1 class="m-0">Exam Results</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Exam Results</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Filter Results</h3>
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
                                <button type="submit" class="btn btn-primary">View Results</button>
                            </div>
                        </form>
                    </div>

                    <?php if($class_id && $section_id && $exam_id): ?>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Results Sheet</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <?php
                                            // Get subjects for this class
                                            $subjects = [];
                                            $sub_res = $conn->query("SELECT s.id, s.subject_name 
                                                                    FROM subjects s 
                                                                    JOIN class_subjects cs ON s.id = cs.subject_id 
                                                                    WHERE cs.class_id = $class_id");
                                            while($sub = $sub_res->fetch_assoc()) {
                                                $subjects[] = $sub;
                                                echo "<th>{$sub['subject_name']}</th>";
                                            }
                                            ?>
                                            <th>Total</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $students = $conn->query("SELECT id, full_name FROM students WHERE class_id = $class_id AND section_id = $section_id ORDER BY full_name");
                                        while($st = $students->fetch_assoc()) {
                                            echo "<tr><td>{$st['full_name']}</td>";
                                            $total = 0;
                                            $count = 0;
                                            $max_total = 0;
                                            foreach($subjects as $sub) {
                                                $mark_res = $conn->query("SELECT marks_obtained, max_marks FROM exam_marks WHERE exam_id = $exam_id AND student_id = {$st['id']} AND subject_id = {$sub['id']}");
                                                if($mark_res->num_rows > 0) {
                                                    $mdata = $mark_res->fetch_assoc();
                                                    $mark = $mdata['marks_obtained'];
                                                    $max = $mdata['max_marks'];
                                                    echo "<td>$mark / $max</td>";
                                                    $total += $mark;
                                                    $max_total += $max;
                                                } else {
                                                    echo "<td>-</td>";
                                                }
                                                $count++;
                                            }
                                            $percentage = ($max_total > 0) ? round(($total / $max_total) * 100, 2) : 0;
                                            echo "<td>$total / $max_total</td>";
                                            echo "<td>$percentage%</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
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
