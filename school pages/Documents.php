<?php
require_once '../lib/db.php';

$student_id = $_GET['student_id'] ?? '';
$success = '';
$error   = '';

if (isset($_POST['btnupload'])) {
    $student_id = $_POST['student_id'];
    $doc_name   = trim($_POST['file_name']);

    if (empty($student_id) || empty($doc_name) || !isset($_FILES['student_file'])) {
        $error = 'All fields are required.';
    } else {
        $target_dir = "../uploads/documents/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_ext = pathinfo($_FILES["student_file"]["name"], PATHINFO_EXTENSION);
        $file_path = "doc_" . $student_id . "_" . time() . "." . $file_ext;

        if (move_uploaded_file($_FILES["student_file"]["tmp_name"], $target_dir . $file_path)) {
            $insert = $conn->prepare("INSERT INTO student_documents (student_id, file_name, file_path) VALUES (?, ?, ?)");
            $insert->bind_param("iss", $student_id, $doc_name, $file_path);
            if ($insert->execute()) {
                $success = "Document uploaded successfully!";
            } else {
                $error = "Database error: " . $conn->error;
            }
            $insert->close();
        } else {
            $error = "Failed to upload file.";
        }
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
      <div class="container-fluid"><h1 class="m-0">Student Documents</h1></div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
        <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

        <div class="card card-primary">
          <div class="card-header"><h3 class="card-title">Upload Document</h3></div>
          <form method="post" action="" enctype="multipart/form-data">
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <label>Select Student</label>
                  <select name="student_id" class="form-control" required>
                    <option value="">-- Select Student --</option>
                    <?php
                    $sts = $conn->query("SELECT id, full_name, admission_no FROM students ORDER BY full_name");
                    while($s = $sts->fetch_assoc()){
                      $sel = ($s['id'] == $student_id) ? 'selected' : '';
                      echo "<option value='{$s['id']}' $sel>{$s['full_name']} ({$s['admission_no']})</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label>Document Title</label>
                  <input type="text" name="file_name" class="form-control" placeholder="e.g. Birth Certificate" required>
                </div>
                <div class="col-md-4">
                  <label>File</label>
                  <input type="file" name="student_file" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" name="btnupload" class="btn btn-primary">Upload</button>
            </div>
          </form>
        </div>

        <div class="card">
          <div class="card-header"><h3 class="card-title">Recent Documents</h3></div>
          <div class="card-body p-0">
             <table class="table table-striped">
               <thead>
                 <tr>
                   <th>Student</th>
                   <th>Document Name</th>
                   <th>Uploaded At</th>
                   <th>Action</th>
                 </tr>
               </thead>
               <tbody>
                 <?php
                 $docs = $conn->query("SELECT d.*, s.full_name FROM student_documents d JOIN students s ON d.student_id = s.id ORDER BY d.id DESC LIMIT 20");
                 while($d = $docs->fetch_assoc()){
                   echo "<tr>
                     <td>{$d['full_name']}</td>
                     <td>" . htmlspecialchars($d['file_name']) . "</td>
                     <td>{$d['uploaded_at']}</td>
                     <td><a href='../uploads/documents/{$d['file_path']}' target='_blank' class='btn btn-xs btn-info'>View</a></td>
                   </tr>";
                 }
                 ?>
               </tbody>
             </table>
          </div>
        </div>
      </div>
    </section>
  </div>
  <?php include '../lib/footer.php';?>
</div>
<?php include '../lib/script.php';?>
</body>
</html>
