<?php
require_once '../lib/db.php';

$success = '';
$error   = '';
$teacher = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id   = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $teacher = $result->fetch_assoc();
    } else {
        die("Teacher not found.");
    }
    $stmt->close();
} else {
    die("Invalid request.");
}

if (isset($_POST['btnupdate'])) {
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
        $update = $conn->prepare(
            "UPDATE teachers SET employee_no=?, full_name=?, gender=?, phone=?, email=?, hire_date=?, qualification=?, salary=? WHERE id=?"
        );
        $update->bind_param("sssssssdi", $employee_no, $full_name, $gender, $phone, $email, $hire_date, $qualification, $salary, $id);

        if ($update->execute()) {
            $success = "Teacher updated successfully!";
            $teacher = array_merge($teacher, [
                'employee_no'   => $employee_no,
                'full_name'     => $full_name,
                'gender'        => $gender,
                'phone'         => $phone,
                'email'         => $email,
                'hire_date'     => $hire_date,
                'qualification' => $qualification,
                'salary'        => $salary,
            ]);
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
    <?php include '../lib/head.php'; ?>
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
                            <h1 class="m-0">Update Teacher</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="View_teachers.php">View Teachers</a></li>
                                <li class="breadcrumb-item active">Update Teacher</li>
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

                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Update Teacher Form</h3>
                                </div>
                                <form action="" method="post">
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="employee_no">Employee No</label>
                                                    <input type="text" class="form-control" id="employee_no" name="employee_no"
                                                        value="<?= htmlspecialchars($teacher['employee_no'] ?? '') ?>" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="full_name">Full Name</label>
                                                    <input type="text" class="form-control" id="full_name" name="full_name"
                                                        value="<?= htmlspecialchars($teacher['full_name'] ?? '') ?>" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="gender">Gender</label>
                                                    <select class="form-control" id="gender" name="gender">
                                                        <option value="">-- Select Gender --</option>
                                                        <option value="Male"   <?= ($teacher['gender'] ?? '') === 'Male'   ? 'selected' : '' ?>>Male</option>
                                                        <option value="Female" <?= ($teacher['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">Phone</label>
                                                    <input type="text" class="form-control" id="phone" name="phone"
                                                        value="<?= htmlspecialchars($teacher['phone'] ?? '') ?>" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        value="<?= htmlspecialchars($teacher['email'] ?? '') ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="hire_date">Hire Date</label>
                                                    <input type="date" class="form-control" id="hire_date" name="hire_date"
                                                        value="<?= htmlspecialchars($teacher['hire_date'] ?? '') ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="qualification">Qualification</label>
                                                    <select class="form-control" id="qualification" name="qualification">
                                                        <option value="">-- Select Qualification --</option>
                                                        <option value="Dugsi Hoose" <?= (htmlspecialchars($teacher['qualification'] ?? '') === 'Dugsi Hoose') ? 'selected' : '' ?>>Dugsi Hoose</option>
                                                        <option value="Dugsi Dhexe" <?= (htmlspecialchars($teacher['qualification'] ?? '') === 'Dugsi Dhexe') ? 'selected' : '' ?>>Dugsi Dhexe</option>
                                                        <option value="Dugsi Sare"  <?= (htmlspecialchars($teacher['qualification'] ?? '') === 'Dugsi Sare') ? 'selected' : '' ?>>Dugsi Sare</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="salary">Salary</label>
                                                    <input type="number" step="0.01" class="form-control" id="salary" name="salary"
                                                        value="<?= htmlspecialchars($teacher['salary'] ?? '0.00') ?>">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-info" name="btnupdate">
                                            <i class="fas fa-save mr-1"></i> Save Changes
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

        <?php include '../lib/footer.php'; ?>
        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>
    <?php include '../lib/script.php'; ?>
</body>
</html>
