<?php
require_once '../lib/db.php';

// Initialize variables
$school_name = '';
$phone = '';
$email = '';
$address = '';
$logo = '';
$id = 1; // We assume single school record with ID 1

// Fetch existing school info
$res = $conn->query("SELECT * FROM school_info WHERE id = $id");
if ($row = $res->fetch_assoc()) {
    $school_name = $row['school_name'];
    $phone = $row['phone'];
    $email = $row['email'];
    $address = $row['address'];
    $logo = $row['logo'];
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_school'])) {
    $school_name = $_POST['school_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    
    // Handle Logo Upload
    $new_logo = $logo;
    if (!empty($_FILES['logo']['name'])) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION);
        $new_logo = "logo_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_logo;
        
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
            // Delete old logo if exist
            if ($logo && file_exists($target_dir . $logo)) {
                @unlink($target_dir . $logo);
            }
        } else {
            $new_logo = $logo;
        }
    }

    if ($row) {
        $stmt = $conn->prepare("UPDATE school_info SET school_name = ?, phone = ?, email = ?, address = ?, logo = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $school_name, $phone, $email, $address, $new_logo, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO school_info (id, school_name, phone, email, address, logo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $id, $school_name, $phone, $email, $address, $new_logo);
    }
    
    if ($stmt->execute()) {
        header("Location: school_info.php?success=1");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <title>School Information - School System</title>
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
                            <h1 class="m-0">School Information</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">School Info</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <?php if(isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-check"></i> Success!</h5>
                            School information has been updated successfully.
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Manage School Profile</h3>
                                </div>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>School Name</label>
                                                    <input type="text" name="school_name" class="form-control" required value="<?= htmlspecialchars($school_name) ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Phone Number</label>
                                                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Email Address</label>
                                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Address</label>
                                                    <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($address) ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>School Logo</label>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" name="logo" class="custom-file-input">
                                                            <label class="custom-file-label">Choose file</label>
                                                        </div>
                                                    </div>
                                                    <?php if($logo): ?>
                                                        <div class="mt-2">
                                                            <img src="../uploads/<?= $logo ?>" alt="Logo" style="max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-right">
                                        <button type="submit" name="update_school" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include '../lib/footer.php'; ?>
    </div>
    <?php include '../lib/script.php'; ?>
</body>
</html>
