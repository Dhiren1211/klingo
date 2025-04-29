<?php
session_start();

// If user already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: views/dashboard.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to K-Lingo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container" style="text-align: center; margin-top: 100px;">
    <h1>Welcome to <span style="color: #4caf50;">K-Lingo</span> 🎓</h1>
    <p>Learn Korean easily and have fun!</p>

    <div style="margin-top: 30px;">
        <a href="views/login.php" class="btn">Login</a>
        <a href="views/register.php" class="btn" style="background-color: #2196f3;">Register</a>
    </div>
</div>
</body>
</html>
