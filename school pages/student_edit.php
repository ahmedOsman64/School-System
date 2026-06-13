<?php
require_once '../lib/db.php';

$success = '';
$error   = '';

if (!isset($_GET['id'])) {
    header("Location: View_students.php");
    exit();
}

$id = $_GET['id'];
$res = $conn->query("SELECT * FROM students WHERE id = $id");
$student = $res->fetch_assoc();

if (!$student) {
    header("Location: View_students.php");
    exit();
}

if (isset($_POST['btnupdate'])) {
    $admission_no   = trim($_POST['admission_no']);
    $full_name      = trim($_POST['full_name']);
    $gender         = trim($_POST['gender']);
    $dob            = trim($_POST['dob']);
    $phone          = trim($_POST['phone']);
    $address        = trim($_POST['address']);
    $class_id       = trim($_POST['class_id']);
    $section_id     = trim($_POST['section_id']);
    $parent_id      = trim($_POST['parent_id']);
    $admission_date = trim($_POST['admission_date']);
    $status         = isset($_POST['status']) ? 1 : 0;

    // Photo update handling
    $photo = $student['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "../uploads/students/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
        $photo = "student_" . time() . "." . $file_ext;
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $photo);
    }

    if (empty($admission_no) || empty($full_name) || empty($class_id) || empty($section_id)) {
        $error = 'Admission No, Full Name, Class, and Section are required.';
    } else {
        $update = $conn->prepare(
            "UPDATE students SET admission_no=?, full_name=?, gender=?, dob=?, phone=?, address=?, class_id=?, section_id=?, parent_id=?, photo=?, admission_date=?, status=? WHERE id=?"
        );
        $update->bind_param("ssssssiiissii", $admission_no, $full_name, $gender, $dob, $phone, $address, $class_id, $section_id, $parent_id, $photo, $admission_date, $status, $id);

        if ($update->execute()) {
            $success = "Student <strong>$full_name</strong> updated successfully!";
            // Re-fetch student data
            $res = $conn->query("SELECT * FROM students WHERE id = $id");
            $student = $res->fetch_assoc();
        } else {
            $error = 'Update failed: ' . $conn->error;
        }
        $update->close();
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
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0">Edit Student</h1></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item"><a href="View_students.php">View Students</a></li>
              <li class="breadcrumb-item active">Edit Student</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-10">

            <?php if ($error): ?>
              <div class="alert alert-danger alert-dismissible fade show">
                <?= $error ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
              </div>
            <?php endif; ?>
            <?php if ($success): ?>
              <div class="alert alert-success alert-dismissible fade show">
                <?= $success ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
              </div>
            <?php endif; ?>

            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">Edit Student Form</h3>
              </div>
              <form action="" method="post" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="row">

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="admission_no">Admission No</label>
                        <input type="text" class="form-control" id="admission_no" name="admission_no"
                               value="<?= htmlspecialchars($student['admission_no']) ?>" required>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name"
                               value="<?= htmlspecialchars($student['full_name']) ?>" required>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender">
                          <option value="">-- Select Gender --</option>
                          <option value="Male"   <?= ($student['gender'] === 'Male')   ? 'selected' : '' ?>>Male</option>
                          <option value="Female" <?= ($student['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob"
                               value="<?= $student['dob'] ?>">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                               value="<?= htmlspecialchars($student['phone']) ?>">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="admission_date">Admission Date</label>
                        <input type="date" class="form-control" id="admission_date" name="admission_date"
                               value="<?= $student['admission_date'] ?>">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="class_id">Class</label>
                        <select class="form-control" id="class_id" name="class_id" required onchange="fetchSections(this.value)">
                          <option value="">-- Select Class --</option>
                          <?php
                          $classes = $conn->query("SELECT * FROM classes ORDER BY class_name");
                          while($c = $classes->fetch_assoc()) {
                            $sel = ($c['id'] == $student['class_id']) ? 'selected' : '';
                            echo "<option value='{$c['id']}' $sel>{$c['class_name']}</option>";
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="section_id">Section</label>
                        <select class="form-control" id="section_id" name="section_id" required>
                          <option value="">-- Select Section --</option>
                          <?php
                          $sections = $conn->query("SELECT * FROM sections WHERE class_id = {$student['class_id']} ORDER BY section_name");
                          while($s = $sections->fetch_assoc()) {
                            $sel = ($s['id'] == $student['section_id']) ? 'selected' : '';
                            echo "<option value='{$s['id']}' $sel>{$s['section_name']}</option>";
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="parent_id">Parent/Guardian</label>
                        <select class="form-control" id="parent_id" name="parent_id">
                          <option value="">-- Select Parent --</option>
                          <?php
                          $parents = $conn->query("SELECT * FROM parents ORDER BY father_name");
                          while($p = $parents->fetch_assoc()) {
                            $sel = ($p['id'] == $student['parent_id']) ? 'selected' : '';
                            echo "<option value='{$p['id']}' $sel>{$p['father_name']} / {$p['mother_name']}</option>";
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($student['address']) ?></textarea>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="photo">Update Photo (Leave blank to keep current)</label>
                        <div class="input-group">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="photo" name="photo">
                            <label class="custom-file-label" for="photo">Choose file</label>
                          </div>
                        </div>
                        <?php if($student['photo']): ?>
                            <small>Current: <a href="../uploads/students/<?= $student['photo'] ?>" target="_blank"><?= $student['photo'] ?></a></small>
                        <?php endif; ?>
                      </div>
                    </div>

                    <div class="col-md-2">
                       <div class="form-group mt-4 pt-2">
                        <div class="custom-control custom-checkbox">
                          <input class="custom-control-input" type="checkbox" id="status" name="status" <?= $student['status'] ? 'checked' : '' ?>>
                          <label for="status" class="custom-control-label">Active Status</label>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-success" name="btnupdate">
                    <i class="fas fa-save mr-1"></i> Update Student
                  </button>
                  <a href="View_students.php" class="btn btn-secondary ml-2">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                  </a>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include '../lib/footer.php';?>
  <aside class="control-sidebar control-sidebar-dark"></aside>
</div>
<?php include '../lib/script.php';?>
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
