<?php
// ─── DB connection ───────────────────────
require_once '../lib/db.php';

$success = '';
$error = '';
$user = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        die("User not found.");
    }
    $stmt->close();
} else {
    die("Invalid request.");
}

if (isset($_POST['btnupdate'])) {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role     = trim($_POST['role']);
    $status   = trim($_POST['status']);

    // Basic validation
    if (empty($fullname) || empty($username)) {
        $error = 'Fullname and Username are required.';
    } else {
        // Check if username already exists for OTHER users
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username <strong>$username</strong> is already taken by another user.";
        } else {
            if (!empty($password)) {
                $update = $conn->prepare("UPDATE users SET fullname = ?, username = ?, password = ?, role = ?, status = ? WHERE id = ?");
                $update->bind_param("sssssi", $fullname, $username, $password, $role, $status, $id);
            } else {
                $update = $conn->prepare("UPDATE users SET fullname = ?, username = ?, role = ?, status = ? WHERE id = ?");
                $update->bind_param("ssssi", $fullname, $username, $role, $status, $id);
            }

            if ($update->execute()) {
                $success = "User updated successfully!";
                $user['fullname'] = $fullname;
                $user['username'] = $username;
                $user['role']     = $role;
                $user['status']   = $status;
            } else {
                $error = 'Update failed. Please try again.';
            }
            $update->close();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include '../lib/nav.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php require '../lib/sidebar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Update User</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="Users_view.php">Users View</a></li>
                                <li class="breadcrumb-item active">Update User</li>
                            </ol>
                        </div>
                    </div>
                </div>
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
                            <?php if ($success): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= $success ?>
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                </div>
                            <?php endif; ?>

                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Update User Form</h3>
                                </div>
                                <form action="" method="post">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="fullname">Full Name</label>
                                            <input type="text" class="form-control" id="fullname" name="fullname"
                                                value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="username">User Name</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password (Leave blank to keep current)</label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                placeholder="Enter new password if you want to change it">
                                        </div>
                                        <div class="form-group">
                                            <label for="role">Role</label>
                                            <select class="form-control" id="role" name="role" required>
                                                <option value="super_admin" <?= ($user['role'] ?? '') === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                                                <option value="admin"       <?= ($user['role'] ?? '') === 'admin'       ? 'selected' : '' ?>>Admin</option>
                                                <option value="staff"       <?= ($user['role'] ?? 'staff') === 'staff'  ? 'selected' : '' ?>>Staff</option>
                                                <option value="finance"     <?= ($user['role'] ?? '') === 'finance'     ? 'selected' : '' ?>>Finance</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="active"   <?= ($user['status'] ?? 'active') === 'active'   ? 'selected' : '' ?>>Active</option>
                                                <option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-info" name="btnupdate">
                                            <i class="fas fa-save mr-1"></i> Save Changes
                                        </button>
                                        <a href="Users_view.php" class="btn btn-default float-right">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include '../lib/footer.php'; ?>
        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>
    <?php include '../lib/script.php'; ?>
</body>
</html>
