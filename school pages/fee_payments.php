<?php
require_once '../lib/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['collect_fee'])) {
    $student_id = $_POST['student_id'];
    $fee_type_id = $_POST['fee_type_id'];
    $amount_paid = $_POST['amount_paid'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    $reference_no = $_POST['reference_no'];
    $note = $_POST['note'];

    if (empty($student_id) || empty($fee_type_id) || empty($amount_paid) || empty($payment_date)) {
        $error = "Please fill all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO fee_payments (student_id, fee_type_id, amount_paid, payment_date, payment_method, reference_no, note) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iidssss", $student_id, $fee_type_id, $amount_paid, $payment_date, $payment_method, $reference_no, $note);
        
        if ($stmt->execute()) {
            $success = "Fee collected successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <title>Collect Fees - School System</title>
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
                            <h1 class="m-0">Collect Fees</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Collect Fees</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <?php if($success): ?>
                                <div class="alert alert-success"><?= $success ?></div>
                            <?php endif; ?>
                            <?php if($error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Fee Collection Form</h3>
                                </div>
                                <form method="POST">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Select Student</label>
                                                    <select name="student_id" class="form-control select2" required>
                                                        <option value="">-- Select Student --</option>
                                                        <?php
                                                        $students = $conn->query("SELECT id, full_name, admission_no FROM students ORDER BY full_name");
                                                        while($s = $students->fetch_assoc()) {
                                                            echo "<option value='{$s['id']}'>{$s['full_name']} ({$s['admission_no']})</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Fee Type</label>
                                                    <select name="fee_type_id" class="form-control" required id="fee_type_id" onchange="updateAmount(this)">
                                                        <option value="">-- Select Fee Type --</option>
                                                        <?php
                                                        $fees = $conn->query("SELECT * FROM fee_types ORDER BY fee_name");
                                                        while($f = $fees->fetch_assoc()) {
                                                            echo "<option value='{$f['id']}' data-amount='{$f['amount']}'>{$f['fee_name']} (\${$f['amount']})</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Amount Paid</label>
                                                    <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control" required placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Payment Date</label>
                                                    <input type="date" name="payment_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Payment Method</label>
                                                    <select name="payment_method" class="form-control">
                                                        <option value="Cash">Cash</option>
                                                        <option value="Bank Transfer">Bank Transfer</option>
                                                        <option value="Check">Check</option>
                                                        <option value="Mobile Money">Mobile Money</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Reference No (Optional)</label>
                                                    <input type="text" name="reference_no" class="form-control" placeholder="Transaction ID, Check No, etc.">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Note</label>
                                            <textarea name="note" class="form-control" rows="2" placeholder="Any additional info"></textarea>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="collect_fee" class="btn btn-primary float-right">
                                            <i class="fas fa-save mr-1"></i> Save Payment
                                        </button>
                                        <a href="view_payments.php" class="btn btn-default">View All Payments</a>
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
    <script>
    function updateAmount(select) {
        var amount = select.options[select.selectedIndex].getAttribute('data-amount');
        if (amount) {
            document.getElementById('amount_paid').value = amount;
        }
    }
    </script>
</body>
</html>
