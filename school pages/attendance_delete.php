<?php
require_once '../lib/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete = $conn->prepare("DELETE FROM student_attendance WHERE id = ?");
    $delete->bind_param("i", $id);

    if ($delete->execute()) {
        header("Location: View_attendance.php?msg=deleted");
    } else {
        header("Location: View_attendance.php?msg=error");
    }
    $delete->close();
} else {
    header("Location: View_attendance.php");
}
?>
