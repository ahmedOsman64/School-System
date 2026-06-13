<?php
require_once '../lib/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete = $conn->prepare("DELETE FROM students WHERE id = ?");
    $delete->bind_param("i", $id);

    if ($delete->execute()) {
        header("Location: View_students.php?msg=deleted");
    } else {
        header("Location: View_students.php?msg=error");
    }
    $delete->close();
} else {
    header("Location: View_students.php");
}
?>
