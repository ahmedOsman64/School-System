<?php
require_once '../lib/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Check if parent has students
    $check = $conn->query("SELECT id FROM students WHERE parent_id = $id");
    if ($check->num_rows > 0) {
        header("Location: View_parents.php?msg=has_students");
        exit();
    }

    $delete = $conn->prepare("DELETE FROM parents WHERE id = ?");
    $delete->bind_param("i", $id);

    if ($delete->execute()) {
        header("Location: View_parents.php?msg=deleted");
    } else {
        header("Location: View_parents.php?msg=error");
    }
    $delete->close();
} else {
    header("Location: View_parents.php");
}
?>
