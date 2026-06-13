<?php
// ─── DB connection ───────────────────────
require_once '../lib/db.php';

$success = '';
$error   = '';

if (isset($_POST['btnregister'])) {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role     = trim($_POST['role']);
    $status   = trim($_POST['status']);
    $date     = trim($_POST['date']);

    // Basic validation
    if (empty($fullname) || empty($username) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username <strong>$username</strong> is already taken. Please choose another.";
        } else {
            $insert = $conn->prepare(
                "INSERT INTO users (fullname, username, password, role, status) VALUES (?, ?, ?, ?, ?)"
            );
            $insert->bind_param("sssss", $fullname, $username, $password, $role, $status);

            if ($insert->execute()) {
                $success = "User <strong>$username</strong> registered successfully!";
            } else {
                $error = 'Registration failed. Please try again.';
            }
            $insert->close();
        }
        $stmt->close();
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
  <!-- Navbar -->
  <?php include '../lib/nav.php';?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php require '../lib/sidebar.php';?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Users</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">User Registration</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-8">

            <?php if ($error): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
              </div>
            <?php endif; ?>

            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">User Registration Form</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form action="" method="post">
                <div class="card-body">

                  <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" class="form-control" id="fullname" name="fullname"
                           placeholder="Enter Fullname"
                           value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>" required>
                  </div>

                  <div class="form-group">
                    <label for="username">User Name</label>
                    <input type="text" class="form-control" id="username" name="username"
                           placeholder="Enter username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                  </div>

                  <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Password" required>
                  </div>

                  <div class="form-group">
                    <label for="role">Role</label>
                    <select class="form-control" id="role" name="role" required>
                      <option value="super_admin" <?= (($_POST['role'] ?? '') === 'super_admin') ? 'selected' : '' ?>>Super Admin</option>
                      <option value="admin"       <?= (($_POST['role'] ?? '') === 'admin')       ? 'selected' : '' ?>>Admin</option>
                      <option value="staff"       <?= (($_POST['role'] ?? 'staff') === 'staff')  ? 'selected' : '' ?>>Staff</option>
                      <option value="finance"     <?= (($_POST['role'] ?? '') === 'finance')     ? 'selected' : '' ?>>Finance</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status" required>
                      <option value="active"   <?= (($_POST['status'] ?? 'active') === 'active')   ? 'selected' : '' ?>>Active</option>
                      <option value="inactive" <?= (($_POST['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date"
                           value="<?= htmlspecialchars($_POST['date'] ?? date('Y-m-d')) ?>">
                  </div>

                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" name="btnregister">
                    <i class="fas fa-user-plus mr-1"></i> Register
                  </button>
                  <a href="Users_view.php" class="btn btn-secondary ml-2">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                  </a>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 <?php include '../lib/footer.php';?>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
<?php include '../lib/script.php';?>

</body>
</html>
