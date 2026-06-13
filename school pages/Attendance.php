<?php
require_once '../lib/db.php';

$class_id = $_GET['class_id'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');
$msg = '';

if (isset($_POST['btnsave_attendance'])) {
    $class_id = $_POST['class_id'];
    $date = $_POST['attendance_date'];
    $statuses = $_POST['status'] ?? []; // student_id => status

    foreach ($statuses as $student_id => $status) {
        // Check if already exists for this date
        $check = $conn->prepare("SELECT id FROM student_attendance WHERE student_id = ? AND attendance_date = ?");
        $check->bind_param("is", $student_id, $date);
        $check->execute();
        $res = $check->get_result();
        
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $update = $conn->prepare("UPDATE student_attendance SET status = ?, class_id = ? WHERE id = ?");
            $update->bind_param("sii", $status, $class_id, $row['id']);
            $update->execute();
            $update->close();
        } else {
            $insert = $conn->prepare("INSERT INTO student_attendance (student_id, class_id, attendance_date, status) VALUES (?, ?, ?, ?)");
            $insert->bind_param("iiss", $student_id, $class_id, $date, $status);
            $insert->execute();
            $insert->close();
        }
        $check->close();
    }
    $msg = '<div class="alert alert-success">Attendance saved successfully!</div>';
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
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0">Student Attendance</h1></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Attendance</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <?= $msg ?>
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Filter Selection</h3>
          </div>
          <form method="get" action="">
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Class</label>
                    <select name="class_id" class="form-control" required>
                      <option value="">-- Select Class --</option>
                      <?php
                      $classes = $conn->query("SELECT * FROM classes ORDER BY class_name");
                      while($c = $classes->fetch_assoc()){
                        $sel = ($c['id'] == $class_id) ? 'selected' : '';
                        echo "<option value='{$c['id']}' $sel>{$c['class_name']}</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" value="<?= $date ?>" required>
                  </div>
                </div>
                <div class="col-md-4">
                   <div class="form-group mt-4 pt-2">
                     <button type="submit" class="btn btn-primary">Fetch Students</button>
                   </div>
                </div>
              </div>
            </div>
          </form>
        </div>

        <?php if ($class_id): ?>
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Attendance List</h3>
          </div>
          <form method="post" action="">
            <input type="hidden" name="class_id" value="<?= $class_id ?>">
            <input type="hidden" name="attendance_date" value="<?= $date ?>">
            <div class="card-body p-0">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Adm No</th>
                    <th>Name</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $students = $conn->query("SELECT * FROM students WHERE class_id = $class_id AND status = 1 ORDER BY full_name");
                  $i = 1;
                  while($s = $students->fetch_assoc()){
                    // Get current status if exists
                    $st_res = $conn->query("SELECT status FROM student_attendance WHERE student_id = {$s['id']} AND attendance_date = '$date'");
                    $curr_status = ($st_res && $st_res->num_rows > 0) ? $st_res->fetch_assoc()['status'] : 'Present';
                    
                    echo "<tr>
                      <td>$i</td>
                      <td>{$s['admission_no']}</td>
                      <td>{$s['full_name']}</td>
                      <td>
                        <div class='btn-group btn-group-toggle' data-toggle='buttons'>
                          <label class='btn btn-outline-success btn-sm " . ($curr_status == 'Present' ? 'active' : '') . "'>
                            <input type='radio' name='status[{$s['id']}]' value='Present' " . ($curr_status == 'Present' ? 'checked' : '') . "> Present
                          </label>
                          <label class='btn btn-outline-danger btn-sm " . ($curr_status == 'Absent' ? 'active' : '') . "'>
                            <input type='radio' name='status[{$s['id']}]' value='Absent' " . ($curr_status == 'Absent' ? 'checked' : '') . "> Absent
                          </label>
                          <label class='btn btn-outline-warning btn-sm " . ($curr_status == 'Late' ? 'active' : '') . "'>
                            <input type='radio' name='status[{$s['id']}]' value='Late' " . ($curr_status == 'Late' ? 'checked' : '') . "> Late
                          </label>
                        </div>
                      </td>
                    </tr>";
                    $i++;
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div class="card-footer">
              <button type="submit" name="btnsave_attendance" class="btn btn-success float-right">Save Attendance</button>
            </div>
          </form>
        </div>
        <?php endif; ?>
      </div>
    </section>
  </div>

  <?php include '../lib/footer.php';?>
</div>
<?php include '../lib/script.php';?>
</body>
</html>
