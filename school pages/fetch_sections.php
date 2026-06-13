<?php
require_once '../lib/db.php';

if (isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];
    $sql = "SELECT id, section_name FROM sections WHERE class_id = ? ORDER BY section_name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">-- Select Section --</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['section_name']) . '</option>';
    }
    $stmt->close();
}
?>
