<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT name, role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch all lessons
$lessonsResult = $conn->query("SELECT id, lesson_number FROM lessons WHERE is_active = 1 ORDER BY lesson_number ASC");
$lessons = $lessonsResult->fetch_all(MYSQLI_ASSOC);

// Fetch user progress
$progressStmt = $conn->prepare("SELECT lesson_id, quiz_score, completed FROM user_progress WHERE user_id = ?");
$progressStmt->bind_param("i", $userId);
$progressStmt->execute();
$progressResult = $progressStmt->get_result();

$userProgress = [];
$completedLessons = [];

while ($row = $progressResult->fetch_assoc()) {
    $lessonId = $row['lesson_id'];
    $userProgress[$lessonId] = [
        'score' => (int) $row['quiz_score'],
        'completed' => (bool) $row['completed'],
    ];

    if ($row['completed']) {
        $completedLessons[] = $lessonId;
    }
}

// Progress calculation
$totalLessons = count($lessons);
$completedCount = count($completedLessons);
$progressPercent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

// Helper function to check if lesson is unlocked
function isLessonUnlocked($lessonNumber, $completedLessons, $lessons) {
    if ($lessonNumber == 1) return true; // Always unlock first lesson

    foreach ($lessons as $lesson) {
        if ($lesson['lesson_number'] == $lessonNumber - 1) {
            return in_array($lesson['id'], $completedLessons);
        }
    }
 
    return false;
}

// Fetch quiz results per lesson for this user
$quizStmt = $conn->prepare("SELECT lesson_id, correct_answers, total_questions FROM quiz_results WHERE user_id = ?");
$quizStmt->bind_param("i", $userId);
$quizStmt->execute();
$quizResult = $quizStmt->get_result();

$userQuizResults = [];
while ($row = $quizResult->fetch_assoc()) {
    $userQuizResults[$row['lesson_id']] = [
        'correct' => (int) $row['correct_answers'],
        'total' => (int) $row['total_questions'],
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | K-Lingo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* (Your same style as before, kept exactly) */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; min-height: 100vh; display: flex; flex-direction: column; }
        .dashboard-container { max-width: 1200px; margin: 50px auto; background: #ffffff; padding: 50px; border-radius: 20px; box-shadow: 0px 4px 25px rgba(0,0,0,0.1); display: grid; grid-template-columns: 1fr 2fr; gap: 50px; align-items: start; }
        h1 { font-size: 32px; margin-bottom: 10px; color: #333; }
        .user-role { color: #777; font-size: 18px; margin-bottom: 30px; }
        .progress-section { text-align: center; }
        .progress-circle { position: relative; width: 200px; height: 200px; margin: 30px auto; }
        .progress-circle svg { transform: rotate(-90deg); }
        .progress-circle circle { fill: none; stroke-width: 15; }
        .progress-circle .bg { stroke: #eee; }
        .progress-circle .progress { stroke: #28a745; stroke-linecap: round; stroke-dasharray: 534; stroke-dashoffset: 534; transition: stroke-dashoffset 0.5s ease; }
        .progress-circle .progress-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 28px; font-weight: bold; color: #28a745; }
        .lesson-section { padding: 20px; }
        .lesson-section h2 { font-size: 26px; margin-bottom: 20px; color: #333; }
        .lesson-tree { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 15px; }
        .lesson-tree a, .lesson-tree span { display: block; background: #f1f1f1; padding: 14px; border-radius: 10px; text-align: center; font-size: 18px; color: #333; text-decoration: none; transition: background 0.3s, transform 0.3s; }
        .lesson-tree a:hover { background: #e0e0e0; transform: translateY(-3px); }
        .completed { background-color: #d4edda !important; color: #155724 !important; }
        .locked { background-color: #f8d7da !important; color: #721c24 !important; cursor: not-allowed; }
        .btn-group { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 40px; }
        .btn { flex: 1; min-width: 160px; padding: 14px 20px; text-decoration: none; font-size: 16px; border-radius: 10px; color: #fff; background-color: #007bff; text-align: center; transition: background-color 0.25s ease, transform 0.25s ease; }
        .btn:hover { background-color: #0056b3; transform: translateY(-2px); }
        .btn-secondary { background-color: #28a745; }
        .btn-secondary:hover { background-color: #1e7e34; }
        .btn-danger { background-color: #dc3545; }
        .btn-danger:hover { background-color: #bd2130; }
        footer { text-align: center; margin: 40px 0 20px; color: #999; font-size: 14px; }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="progress-section">
        <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?> 👋</h1>
        <p class="user-role">Role: <?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>

        <div class="progress-circle">
            <svg width="200" height="200">
                <circle class="bg" cx="100" cy="100" r="85"></circle>
                <circle class="progress" cx="100" cy="100" r="85"
                    style="stroke-dashoffset: <?php echo 534 - (534 * $progressPercent / 100); ?>;">
                </circle>
            </svg>
            <div class="progress-text"><?php echo $progressPercent; ?>% <span style="font-size: 18px;">Completed</span></div>
            <div></div>
        </div>

        <div class="btn-group">
            <?php if ($user['role'] === 'super_admin'): ?>
                <a href="./superadmin/manage-admins.php" class="btn">Manage Admins</a>
            <?php endif; ?>
            <?php if ($user['role'] === 'admin'): ?>
                <a href="admin/manage-lessons.php" class="btn">Manage Lessons</a>
                <a href="admin/upload.php" class="btn">Upload Words</a>
            <?php endif; ?>
            <a href="learn.php" class="btn btn-secondary">📚 Start Learning</a>
            <a href="quiz.php" class="btn btn-secondary">📝 Take a Quiz</a>
            <a href="../api/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <div class="lesson-section">
    <h2>📚 Lessons List</h2>
    <div class="lesson-tree">
        <?php 
        foreach ($lessons as $lesson): 
            $lessonId = $lesson['id'];
            $lessonNumber = $lesson['lesson_number'];
            $progress = $userProgress[$lessonId]['score'] ?? 0;
            $isCompleted = $userProgress[$lessonId]['completed'] ?? false;
            $isUnlocked = isLessonUnlocked($lessonNumber, $completedLessons, $lessons) || $user['role'] === 'admin' || $user['role'] === 'super_admin';
        ?>
            <?php if ($isUnlocked): ?>
                <a href="quiz.php?lesson=<?php echo $lessonId; ?>" class="<?php echo $isCompleted ? 'completed' : ''; ?>">
                    <?php echo $isCompleted ? '✅ ' : ''; ?>Lesson <?php echo $lessonNumber; ?>
                    <br>
                    <small><?php echo $progress; ?>/<?php echo $userQuizResults[$lessonId]['total'] ?? 0; ?> Correct</small>

                </a>
            <?php else: ?>
                <span class="locked">🔒 Lesson <?php echo $lessonNumber; ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

</div>

<footer>
    &copy; <?php echo date('Y'); ?> K-Lingo. All rights reserved.
</footer>

</body>
</html>
