<?php
require_once('../../includes/db.php');
require_once('../../includes/auth.php');

if ($_SESSION['role'] !== 'super_admin') {
    header('Location: ../dashboard.php');
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            if ($stmt->execute()) {
                header("Location: manage-admins.php?success=1");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<?php include('../../includes/header.php'); ?>

<div class="admin-page-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-title">K-Lingo Admin</div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="manage-admins.php" class="nav-link">Manage Admins</a></li>
                <li><a href="manage-users.php" class="nav-link">Manage Users</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="form-card">
            <h1>Create New Admin</h1>

            <?php if (isset($error)): ?>
                <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn-submit">➕ Create Admin</button>
            </form>

            <div class="back-link">
                <a href="manage-admins.php" style="color: #007BFF; text-decoration: none;">Back to Manage Admins</a>
            </div>
        </div>
    </main>

</div>

<?php include('../../includes/footer.php'); ?>

<!-- Redesigned Styles with Sidebar -->
<style>
/* Layout */
.admin-page-layout {
    display: flex;
    min-height: 100vh;
    background: #f4f6f9;
}

/* Sidebar */
.sidebar {
    width: 240px;
    background:rgba(44, 46, 49, 0.41);
    color: white;
    padding: 30px 20px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    margin-top: 10px;
    border-radius: 8px;
}

.sidebar-title {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 10px;
    text-align: center;
}

.sidebar-nav ul {
    display: flex;
    flex-direction: column;
    list-style: none;
    padding: 0;
    margin: 0;
   
}

.sidebar-nav li {
    margin-bottom: 5px;
    color:black;
}

.nav-link {
    color:rgb(255, 255, 255);
    text-decoration: none;
    font-size: 16px;
    transition: color 0.3s, background 0.3s;
    padding: 10px 15px;
    display: block;
    border-radius: 8px;
    font-weight: bold;
}

.nav-link:hover {
    background: #334155;
    color: white;
}

/* Main Content */
.main-content {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 50px 20px;
}

/* Form Card */
.form-card {
    background: white;
    padding: 40px 30px;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    width: 100%;
    max-width: 500px;
    text-align: center;
}

h1 {
    margin-bottom: 30px;
    font-size: 26px;
    color: #333;
}

.alert-error {
    background: #ffe0e0;
    color: #c10000;
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-weight: bold;
}

.form-group {
    text-align: left;
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #555;
}

.form-group input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    transition: border-color 0.3s;
}

.form-group input:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.btn-submit {
    width: 100%;
    padding: 14px;
    background: #007bff;
    border: none;
    color: white;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;
    cursor: pointer;
    transition: background 0.3s;
    margin-top: 10px;
}

.btn-submit:hover {
    background: #0056b3;
}

.back-link {
    margin-top: 20px;
}

.back-link a {
    color: #007bff;
    text-decoration: none;
    font-size: 14px;
}

.back-link a:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-page-layout {
        flex-direction: column;
    }
    .sidebar {
        width: 100%;
        flex-direction: row;
        justify-content: space-around;
        padding: 15px 10px;
    }
    .sidebar-title {
        display: none;
    }
    .nav-link {
        padding: 8px 12px;
        font-size: 14px;
    }
}
</style>
