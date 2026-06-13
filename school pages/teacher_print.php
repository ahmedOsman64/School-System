<?php
require_once '../lib/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Teacher - <?= htmlspecialchars($teacher['fullname']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .teacher-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .teacher-details th, .teacher-details td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: left;
            font-size: 18px;
        }
        .teacher-details th {
            background-color: #f8f9fa;
            width: 30%;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Print</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Close</button>
    </div>

    <div class="header">
        <h2>School System</h2>
        <h3>Teacher Information</h3>
    </div>

    <div class="teacher-details">
        <table>
            <tr>
                <th>ID</th>
                <td><?= htmlspecialchars($teacher['id']) ?></td>
            </tr>
            <tr>
                <th>Full Name</th>
                <td><?= htmlspecialchars($teacher['fullname']) ?></td>
            </tr>
            <tr>
                <th>Subject</th>
                <td><?= htmlspecialchars($teacher['subject']) ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?= htmlspecialchars($teacher['phone']) ?></td>
            </tr>
            <tr>
                <th>Registration Date</th>
                <td><?= htmlspecialchars($teacher['created_at']) ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
