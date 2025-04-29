<?php
require_once('../../includes/db.php');
require_once('../../includes/auth.php');

// Only allow superadmin
if ($_SESSION['role'] !== 'super_admin') {
    header('Location: ../dashboard.php');
    exit();
}

$userResult = $conn->query("
    SELECT u.id, u.name, u.email, u.role, 
           COUNT(p.id) AS total_completed_lessons
    FROM users u
    LEFT JOIN user_progress p ON u.id = p.user_id AND p.completed = 1
    WHERE u.role = 'user'
    GROUP BY u.id
");

$users = [];
if ($userResult) {
    while ($row = $userResult->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<?php include('../../includes/header.php'); ?>

<div class="superadmin-container">
    <h1>Manage Users</h1>

    <div class="card">
        <h2>User List</h2>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Lessons Completed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['total_completed_lessons']); ?> lesson(s)</td>
                        <td>
                            <form action="delete-user.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>

<style>
.superadmin-container {
    padding: 40px;
    background: #f7f9fc;
    min-height: 90vh;
}
.card {
    background: #fff;
    padding: 25px 30px;
    margin-bottom: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}
h1, h2 {
    margin-bottom: 20px;
    color: #333;
}
.btn-danger {
    background: #dc3545;
    color: white;
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
}
.btn-danger:hover {
    background: #c82333;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
thead {
    background: #f0f2f5;
}
</style>
