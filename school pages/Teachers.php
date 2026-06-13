<?php
require_once '../lib/db.php';

$success = '';
$error   = '';

if (isset($_POST['btnregister'])) {
    $employee_no   = trim($_POST['employee_no']);
    $full_name     = trim($_POST['full_name']);
    $gender        = trim($_POST['gender']);
    $phone         = trim($_POST['phone']);
    $email         = trim($_POST['email']);
    $hire_date     = trim($_POST['hire_date']);
    $qualification = trim($_POST['qualification']);
    $salary        = trim($_POST['salary']);

    if (empty($employee_no) || empty($full_name) || empty($phone)) {
        $error = 'Employee No, Full Name, and Phone are required.';
    } else {
        $insert = $conn->prepare(
            "INSERT INTO teachers (employee_no, full_name, gender, phone, email, hire_date, qualification, salary)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $insert->bind_param("sssssssd", $employee_no, $full_name, $gender, $phone, $email, $hire_date, $qualification, $salary);

        if ($insert->execute()) {
            $success = "Teacher <strong>$full_name</strong> registered successfully!";
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
          <div class="col-sm-6"><h1 class="m-0">Teachers</h1></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Teacher Registration</li>
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
                <h3 class="card-title">Teacher Registration Form</h3>
              </div>
              <form action="" method="post">
                <div class="card-body">
                  <div class="row">

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="employee_no">Employee No</label>
                        <input type="text" class="form-control" id="employee_no" name="employee_no"
                               placeholder="e.g. EMP-001"
                               value="<?= htmlspecialchars($_POST['employee_no'] ?? '') ?>" required>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name"
                               placeholder="Enter Full Name"
                               value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender">
                          <option value="">-- Select Gender --</option>
                          <option value="Male"   <?= (($_POST['gender'] ?? '') === 'Male')   ? 'selected' : '' ?>>Male</option>
                          <option value="Female" <?= (($_POST['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                               placeholder="Enter Phone Number"
                               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="Enter Email"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="hire_date">Hire Date</label>
                        <input type="date" class="form-control" id="hire_date" name="hire_date"
                               value="<?= htmlspecialchars($_POST['hire_date'] ?? date('Y-m-d')) ?>">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="qualification">Qualification</label>
                        <select class="form-control" id="qualification" name="qualification">
                            <option value="">-- Select Qualification --</option>
                            <option value="Dugsi Hoose" <?= (($_POST['qualification'] ?? '') === 'Dugsi Hoose') ? 'selected' : '' ?>>Dugsi Hoose</option>
                            <option value="Dugsi Dhexe" <?= (($_POST['qualification'] ?? '') === 'Dugsi Dhexe') ? 'selected' : '' ?>>Dugsi Dhexe</option>
                            <option value="Dugsi Sare"  <?= (($_POST['qualification'] ?? '') === 'Dugsi Sare') ? 'selected' : '' ?>>Dugsi Sare</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="salary">Salary</label>
                        <input type="number" step="0.01" class="form-control" id="salary" name="salary"
                               placeholder="0.00"
                               value="<?= htmlspecialchars($_POST['salary'] ?? '') ?>">
                      </div>
                    </div>

                  </div>
                </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" name="btnregister">
                    <i class="fas fa-chalkboard-teacher mr-1"></i> Register Teacher
                  </button>
                  <a href="View_teachers.php" class="btn btn-secondary ml-2">
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
</body>
</html>
