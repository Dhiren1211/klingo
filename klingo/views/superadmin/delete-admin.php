<?php
require_once('../../includes/db.php');
require_once('../../includes/auth.php');

if ($_SESSION['role'] !== 'super_admin') {
    header('Location: ../dashboard.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_id'])) {
    $admin_id = intval($_POST['admin_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
    $stmt->bind_param("i", $admin_id);

    if ($stmt->execute()) {
        header('Location: manage-admins.php');
        exit();
    } else {
        echo "Failed to delete admin.";
    }
} else {
    echo "Invalid Request.";
}
?>
