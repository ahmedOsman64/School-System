<?php
require_once '../lib/db.php';

$edit_id = '';
$edit_name = '';
$edit_amount = '';
$edit_desc = '';
$edit_class = '';


// Handle Add/Update Fee Type
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_fee'])) {
    $fee_name = $_POST['fee_name'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $class_id = $_POST['class_id'] ?: null; // Handle empty class selection
    $id = $_POST['fee_id'];

    if ($id) {
        $stmt = $conn->prepare("UPDATE fee_types SET fee_name = ?, amount = ?, description = ?, class_id = ? WHERE id = ?");
        $stmt->bind_param("sdsii", $fee_name, $amount, $description, $class_id, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO fee_types (fee_name, amount, description, class_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $fee_name, $amount, $description, $class_id);
    }
    
    $stmt->execute();
    header("Location: fee_types.php");
    exit();
}

// Prepare Edit
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM fee_types WHERE id = $edit_id");
    if ($row = $res->fetch_assoc()) {
        $edit_name = $row['fee_name'];
        $edit_amount = $row['amount'];
        $edit_desc = $row['description'];
        $edit_class = $row['class_id'];
    }
}

// Handle Delete Fee Type
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM fee_types WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: fee_types.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <title>Fee Types - School System</title>
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
                            <h1 class="m-0">Fee Types</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Fee Types</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title"><?= $edit_id ? 'Edit Fee Type' : 'Add New Fee Type' ?></h3>
                                </div>
                                <form method="POST">
                                    <input type="hidden" name="fee_id" value="<?= $edit_id ?>">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Fee Name</label>
                                            <input type="text" name="fee_name" class="form-control" required placeholder="e.g. Tuition Fee" value="<?= htmlspecialchars($edit_name) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <input type="number" step="0.01" name="amount" class="form-control" required placeholder="0.00" value="<?= htmlspecialchars($edit_amount) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control" rows="2" placeholder="Optional description"><?= htmlspecialchars($edit_desc) ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Select Class (Optional)</label>
                                            <select name="class_id" class="form-control select2">
                                                <option value="">-- All Classes / None --</option>
                                                <?php
                                                $classes = $conn->query("SELECT * FROM classes ORDER BY class_name ASC");
                                                while($c = $classes->fetch_assoc()){
                                                    $sel = ($c['id'] == $edit_class) ? 'selected' : '';
                                                    echo "<option value='{$c['id']}' $sel>{$c['class_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="save_fee" class="btn btn-primary btn-block"><?= $edit_id ? 'Update Fee' : 'Add Fee' ?></button>
                                        <?php if($edit_id): ?>
                                            <a href="fee_types.php" class="btn btn-default btn-block">Cancel</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Fee Types List</h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Fee Name</th>
                                                <th>Amount</th>
                                                <th>Class</th>
                                                <th>Description</th>

                                                <th style="width: 150px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT f.*, c.class_name 
                                                    FROM fee_types f 
                                                    LEFT JOIN classes c ON f.class_id = c.id 
                                                    ORDER BY f.id DESC";
                                            $result = $conn->query($sql);
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $class_display = $row['class_name'] ?: '<span class="badge badge-secondary">Universal</span>';
                                                    echo "<tr>
                                                        <td>{$row['id']}</td>
                                                        <td>{$row['fee_name']}</td>
                                                        <td>\${$row['amount']}</td>
                                                        <td>$class_display</td>
                                                        <td>{$row['description']}</td>

                                                        <td>
                                                            <div class='btn-group'>
                                                                <a href='fee_types.php?edit={$row['id']}' class='btn btn-success btn-xs'><i class='fas fa-edit'></i> Edit</a>
                                                                <a href='fee_types.php?delete={$row['id']}' class='btn btn-danger btn-xs' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i> Del</a>
                                                            </div>
                                                        </td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='5' class='text-center'>No fee types found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
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
