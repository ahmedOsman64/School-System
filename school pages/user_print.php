<?php
require_once '../lib/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        die("User not found.");
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
    <title>Print User - <?= htmlspecialchars($user['fullname']) ?></title>
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
        .user-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .user-details th, .user-details td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: left;
            font-size: 18px;
        }
        .user-details th {
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
        <h3>User Information</h3>
    </div>

    <div class="user-details">
        <table>
            <tr>
                <th>ID</th>
                <td><?= htmlspecialchars($user['id']) ?></td>
            </tr>
            <tr>
                <th>Full Name</th>
                <td><?= htmlspecialchars($user['fullname']) ?></td>
            </tr>
            <tr>
                <th>Username</th>
                <td><?= htmlspecialchars($user['username']) ?></td>
            </tr>
            <tr>
                <th>Password</th>
                <td><?= htmlspecialchars($user['password']) ?></td>
            </tr>
            <tr>
                <th>Registration Date</th>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
