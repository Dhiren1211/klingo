<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $name, $email, $password);
        if ($stmt->execute()) {
            header("Location: login.php?register=success");
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!-- HTML below -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | K-Lingo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Reset & basic */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
        }

        /* Center container */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        /* Card */
        .form-card {
            background: white;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .form-card h2 {
            margin-bottom: 25px;
            font-size: 28px;
            color: #333;
        }

        /* Inputs */
        .form-card input[type="text"],
        .form-card input[type="email"],
        .form-card input[type="password"] {
            width: 100%;
            padding: 14px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .form-card input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }

        /* Button */
        .form-card button {
            width: 100%;
            padding: 14px;
            background: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 15px;
        }

        .form-card button:hover {
            background: #0056b3;
        }

        /* Link */
        .form-card p {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }

        .form-card a {
            color: #007bff;
            text-decoration: none;
        }

        .form-card a:hover {
            text-decoration: underline;
        }

        /* Error Message */
        .alert-error {
            background: #ffe0e0;
            color: #c10000;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <h2>Register to K-Lingo</h2>

        <?php if (isset($error)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</div>

</body>
</html>
