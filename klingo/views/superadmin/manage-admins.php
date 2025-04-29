<?php
require_once('../../includes/db.php');
require_once('../../includes/auth.php');

// Only allow superadmin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../dashboard.php');
    exit();
}

// Fetch Admins
$admins = [];
$adminQuery = "SELECT id, name, email FROM users WHERE role = 'admin'";
$result = $conn->query($adminQuery);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
} else {
    die("Admin Query Failed: " . $conn->error);
}

// Fetch total lessons
$totalLessonsQuery = "SELECT COUNT(*) as total_lessons FROM lessons"; 
$totalLessonsResult = $conn->query($totalLessonsQuery);

$totalLessons = 0;
if ($totalLessonsResult) {
    $row = $totalLessonsResult->fetch_assoc();
    $totalLessons = (int)$row['total_lessons'];
} else {
    die("Total Lessons Query Failed: " . $conn->error);
}

// Fetch Users and their Progress
$users = [];
$userQuery = "
    SELECT u.id, u.name, u.email, u.role, 
           SUM(CASE WHEN p.completed = 1 THEN 1 ELSE 0 END) AS completed_lessons
    FROM users u
    LEFT JOIN user_progress p ON u.id = p.user_id
    WHERE u.role = 'user'
    GROUP BY u.id
";

$userResult = $conn->query($userQuery);

if ($userResult) {
    while ($row = $userResult->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    die("User Query Failed: " . $conn->error);
}
?>

<?php include('../../includes/header.php'); ?>

<div class="superadmin-container">
    <h1>👑 Super Admin Dashboard</h1>

    <!-- Admin List Card -->
    <div class="card">
        <div class="card-header">
            <h2>🛡️ Admins</h2>
            <a href="create-admin.php" class="btn-primary">➕ Add New Admin</a>
        </div>

        <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($admins) > 0): ?>
                    <?php foreach ($admins as $index => $admin): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($admin['name']); ?></td>
                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td>
                                <form action="delete-admin.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                    <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                    <button type="submit" class="btn-danger"> Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No admins found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

    <!-- User Progress List Card -->
    <div class="card">
        <div class="card-header">
            <h2>📈 User Progress</h2>
        </div>

        <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $index => $user): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <?php 
                                    $progress = 0;
                                    if ($totalLessons > 0) {
                                        $progress = round(($user['completed_lessons'] / $totalLessons) * 100);
                                    }
                                ?>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $progress; ?>%;">
                                        <?php echo $progress; ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>

<!-- Redesigned Styles -->
<style>
.superadmin-container {
    padding: 40px;
    background: #f2f6fa;
    min-height: 90vh;
}
h1 {
    font-size: 32px;
    margin-bottom: 30px;
    color: #222;
}
.card {
    background: #fff;
    padding: 25px 30px;
    margin-bottom: 30px;
    border-radius: 14px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
}
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
h2 {
    font-size: 24px;
    color: #333;
}
.btn-primary {
    background: #007bff;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 8px;
    font-size: 15px;
    transition: background 0.3s ease;
}
.btn-primary:hover {
    background: #0056b3;
}
.btn-danger {
    background: #e3342f;
    color: white;
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s ease;
}
.btn-danger:hover {
    background: #cc1f1a;
}
.table-wrapper {
    overflow-x: auto;
}
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
}
th, td {
    padding: 14px 12px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}
thead {
    background: #f9fafc;
}
th {
    color: #555;
}
.progress-bar {
    background: #e0e0e0;
    border-radius: 30px;
    overflow: hidden;
    height: 20px;
    width: 100%;
    position: relative;
}
.progress-fill {
    background: linear-gradient(90deg, #00c6ff, #0072ff);
    height: 100%;
    color: #fff;
    font-size: 13px;
    font-weight: bold;
    text-align: center;
    line-height: 20px;
}
@media (max-width: 768px) {
    .card {
        padding: 20px;
    }
    h1 {
        font-size: 26px;
    }
    table {
        font-size: 14px;
    }
}
</style>
