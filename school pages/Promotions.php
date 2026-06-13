<?php
require_once '../lib/db.php';

$from_class_id = $_GET['from_class'] ?? '';
$to_class_id = $_GET['to_class'] ?? '';
$msg = '';

if (isset($_POST['btnpromote'])) {
    $from_class = $_POST['from_class_id'];
    $to_class   = $_POST['to_class_id'];
    $selected_students = $_POST['promote_students'] ?? [];

    if (empty($to_class)) {
        $msg = '<div class="alert alert-danger">Please select a target class.</div>';
    } elseif (empty($selected_students)) {
        $msg = '<div class="alert alert-warning">No students selected for promotion.</div>';
    } else {
        $conn->begin_transaction();
        try {
            foreach ($selected_students as $student_id) {
                // Insert into promotions log
                $log = $conn->prepare("INSERT INTO student_promotions (student_id, from_class, to_class, promoted_date) VALUES (?, ?, ?, CURDATE())");
                $log->bind_param("iii", $student_id, $from_class, $to_class);
                $log->execute();
                $log->close();

                // Update student's current class
                $update = $conn->prepare("UPDATE students SET class_id = ? WHERE id = ?");
                $update->bind_param("ii", $to_class, $student_id);
                $update->execute();
                $update->close();
            }
            $conn->commit();
            $msg = '<div class="alert alert-success">Promoted ' . count($selected_students) . ' students successfully!</div>';
        } catch (Exception $e) {
            $conn->rollback();
            $msg = '<div class="alert alert-danger">Promotion failed: ' . $e->getMessage() . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include '../lib/head.php';?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php include '../lib/nav.php';?>
  <?php require '../lib/sidebar.php';?>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0">Student Promotions</h1>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <?= $msg ?>
        <div class="card card-default">
          <div class="card-header"><h3 class="card-title">Promote Students</h3></div>
          <form method="get" action="">
            <div class="card-body">
              <div class="row">
                <div class="col-md-5">
                  <label>From Class</label>
                  <select name="from_class" class="form-control" required onchange="this.form.submit()">
                    <option value="">-- Select Source Class --</option>
                    <?php
                    $classes = $conn->query("SELECT * FROM classes ORDER BY class_name");
                    while($c = $classes->fetch_assoc()){
                      $sel = ($c['id'] == $from_class_id) ? 'selected' : '';
                      echo "<option value='{$c['id']}' $sel>{$c['class_name']}</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-5">
                   <label>To Class (Target)</label>
                   <select name="to_class" class="form-control">
                    <option value="">-- Select Target Class --</option>
                    <?php
                    $classes = $conn->query("SELECT * FROM classes ORDER BY class_name");
                    while($c = $classes->fetch_assoc()){
                      $sel = ($c['id'] == $to_class_id) ? 'selected' : '';
                      echo "<option value='{$c['id']}' $sel>{$c['class_name']}</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>
          </form>
        </div>

        <?php if ($from_class_id): ?>
        <form method="post" action="">
          <input type="hidden" name="from_class_id" value="<?= $from_class_id ?>">
          <input type="hidden" name="to_class_id" value="<?= $to_class_id ?>">
          <div class="card card-primary">
            <div class="card-body p-0">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Adm No</th>
                    <th>Name</th>
                    <th>Gender</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $students = $conn->query("SELECT * FROM students WHERE class_id = $from_class_id AND status = 1 ORDER BY full_name");
                  while($s = $students->fetch_assoc()){
                    echo "<tr>
                      <td><input type='checkbox' name='promote_students[]' value='{$s['id']}' class='student-checkbox'></td>
                      <td>{$s['admission_no']}</td>
                      <td>{$s['full_name']}</td>
                      <td>{$s['gender']}</td>
                    </tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div class="card-footer">
               <button type="submit" name="btnpromote" class="btn btn-warning float-right">Promote Selected Students</button>
            </div>
          </div>
        </form>
        <?php endif; ?>
      </div>
    </section>
  </div>

  <?php include '../lib/footer.php';?>
</div>
<?php include '../lib/script.php';?>
<script>
$('#select-all').click(function() {
    $('.student-checkbox').prop('checked', this.checked);
});
</script>
</body>
</html>
