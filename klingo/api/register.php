<?php
require_once('../includes/db.php');
session_start();

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$name || !$email || !$password) {
    header('Location: ../views/register.php?error=empty');
    exit();
}

// Check if email already exists
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->rowCount() > 0) {
    header('Location: ../views/register.php?error=exists');
    exit();
}

// Insert user
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
$stmt->execute([$name, $email, $hashed_password]);

// Auto-login after registration
$_SESSION['user_id'] = $conn->lastInsertId();
$_SESSION['name'] = $name;
$_SESSION['role'] = 'user';

header('Location: ../views/dashboard.php');
exit();
