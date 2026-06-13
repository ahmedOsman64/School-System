<?php
require_once '../lib/db.php';

$success = '';
$error   = '';

if (!isset($_GET['id'])) {
    header("Location: View_parents.php");
    exit();
}

$id = $_GET['id'];
$res = $conn->query("SELECT * FROM parents WHERE id = $id");
$parent = $res->fetch_assoc();

if (!$parent) {
    header("Location: View_parents.php");
    exit();
}

if (isset($_POST['btnupdate'])) {
    $father_name = trim($_POST['father_name']);
    $mother_name = trim($_POST['mother_name']);
    $phone       = trim($_POST['phone']);
    $email       = trim($_POST['email']);
    $address     = trim($_POST['address']);

    if (empty($father_name) || empty($phone)) {
        $error = 'Father Name and Phone are required.';
    } else {
        $update = $conn->prepare("UPDATE parents SET father_name=?, mother_name=?, phone=?, email=?, address=? WHERE id=?");
        $update->bind_param("sssssi", $father_name, $mother_name, $phone, $email, $address, $id);

        if ($update->execute()) {
            $success = "Parent <strong>$father_name</strong> updated successfully!";
            // Re-fetch data
            $res = $conn->query("SELECT * FROM parents WHERE id = $id");
            $parent = $res->fetch_assoc();
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
          <div class="col-sm-6"><h1 class="m-0">Edit Parent</h1></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item"><a href="View_parents.php">View Parents</a></li>
              <li class="breadcrumb-item active">Edit Parent</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-8">

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
                <h3 class="card-title">Edit Parent Form</h3>
              </div>
              <form action="" method="post">
                <div class="card-body">
                  <div class="form-group">
                    <label for="father_name">Father Name</label>
                    <input type="text" class="form-control" id="father_name" name="father_name" value="<?= htmlspecialchars($parent['father_name']) ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="mother_name">Mother Name</label>
                    <input type="text" class="form-control" id="mother_name" name="mother_name" value="<?= htmlspecialchars($parent['mother_name']) ?>">
                  </div>
                  <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($parent['phone']) ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($parent['email']) ?>">
                  </div>
                  <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($parent['address']) ?></textarea>
                  </div>
                </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-success" name="btnupdate">
                    <i class="fas fa-save mr-1"></i> Update Parent
                  </button>
                  <a href="View_parents.php" class="btn btn-secondary ml-2">
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
