<?php
require_once('../includes/db.php');
session_start();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    header('Location: ../views/login.php?error=empty');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    header('Location: ../views/dashboard.php');
    exit();
} else {
    header('Location: ../views/login.php?error=invalid');
    exit();
}
