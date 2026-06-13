<?php
require_once '../lib/db.php';

$success = '';
$error   = '';

if (isset($_POST['btnregister'])) {
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

    // Photo upload handling
    $photo = '';
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
        $insert = $conn->prepare(
            "INSERT INTO students (admission_no, full_name, gender, dob, phone, address, class_id, section_id, parent_id, photo, admission_date, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $insert->bind_param("ssssssiiissi", $admission_no, $full_name, $gender, $dob, $phone, $address, $class_id, $section_id, $parent_id, $photo, $admission_date, $status);

        if ($insert->execute()) {
            $success = "Student <strong>$full_name</strong> registered successfully!";
        } else {
            $error = 'Registration failed: ' . $conn->error;
        }
        $insert->close();
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
          <div class="col-sm-6"><h1 class="m-0">Student Registration</h1></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Student Registration</li>
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

            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Student Registration Form</h3>
              </div>
              <form action="" method="post" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="row">

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="admission_no">Admission No</label>
                        <input type="text" class="form-control" id="admission_no" name="admission_no"
                               placeholder="e.g. ADM-2023-001"
                               value="<?= htmlspecialchars($_POST['admission_no'] ?? '') ?>" required>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name"
                               placeholder="Enter Full Name"
                               value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender">
                          <option value="">-- Select Gender --</option>
                          <option value="Male"   <?= (($_POST['gender'] ?? '') === 'Male')   ? 'selected' : '' ?>>Male</option>
                          <option value="Female" <?= (($_POST['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob"
                               value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                               placeholder="Enter Phone Number"
                               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="admission_date">Admission Date</label>
                        <input type="date" class="form-control" id="admission_date" name="admission_date"
                               value="<?= htmlspecialchars($_POST['admission_date'] ?? date('Y-m-d')) ?>">
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
                            echo "<option value='{$c['id']}'>{$c['class_name']}</option>";
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
                          <!-- Will be populated by JS -->
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
                            echo "<option value='{$p['id']}'>{$p['father_name']} / {$p['mother_name']}</option>";
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter Address"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="photo">Student Photo</label>
                        <div class="input-group">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="photo" name="photo">
                            <label class="custom-file-label" for="photo">Choose file</label>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-2">
                       <div class="form-group mt-4 pt-2">
                        <div class="custom-control custom-checkbox">
                          <input class="custom-control-input" type="checkbox" id="status" name="status" checked>
                          <label for="status" class="custom-control-label">Active Status</label>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" name="btnregister">
                    <i class="fas fa-user-graduate mr-1"></i> Register Student
                  </button>
                  <a href="View_students.php" class="btn btn-secondary ml-2">
                    <i class="fas fa-list mr-1"></i> View Students
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
