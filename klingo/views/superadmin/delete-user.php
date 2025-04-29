<?php
require_once('../../includes/db.php');
require_once('../../includes/auth.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);

    // Prevent deleting superadmin
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    header('Location: manage-user.php');
    exit();
}
?>
