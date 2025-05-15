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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-gray: #f8f9fa;
            --dark-gray: #6c757d;
            --border-radius: 10px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-gray);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #333;
            line-height: 1.6;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            background: #ffffff;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
            align-items: start;
        }

        h1 {
            font-size: clamp(1.5rem, 2.5vw, 2rem);
            margin-bottom: 0.5rem;
            color: #333;
        }

        .user-role {
            color: var(--dark-gray);
            font-size: clamp(0.875rem, 1.5vw, 1.125rem);
            margin-bottom: 1.5rem;
        }

        .progress-section {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .progress-circle {
            position: relative;
            width: clamp(100px, 20vw, 150px);
            height: clamp(100px, 20vw, 150px);
            margin: 1.5rem auto;
        }

        .progress-circle svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .progress-circle circle {
            fill: none;
            stroke-width: 15;
        }

        .progress-circle .bg {
            stroke: #eee;
        }

        .progress-circle .progress {
            stroke: var(--success-color);
            stroke-linecap: round;
            stroke-dasharray: 534;
            stroke-dashoffset: 534;
            transition: var(--transition);
        }

        .progress-circle .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: clamp(1.25rem, 2.5vw, 1.75rem);
            font-weight: bold;
            color: var(--success-color);
        }

        .progress-circle .progress-text span {
            font-size: clamp(0.75rem, 1.5vw, 1rem);
            display: block;
        }

        .lesson-section {
            padding: 1rem;
        }

        .lesson-section h2 {
            font-size: clamp(1.25rem, 2vw, 1.5rem);
            margin-bottom: 1rem;
            color: #333;
        }

        .lesson-tree {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
        }

        .lesson-tree a, .lesson-tree span {
            display: block;
            background: #f1f1f1;
            padding: 1rem;
            border-radius: var(--border-radius);
            text-align: center;
            font-size: clamp(0.875rem, 1.5vw, 1rem);
            color: #333;
            text-decoration: none;
            transition: var(--transition);
        }

        .lesson-tree a:hover {
            background: #e0e0e0;
            transform: translateY(-3px);
            box-shadow: var(--box-shadow);
        }

        .completed {
            background-color: #d4edda !important;
            color: #155724 !important;
        }

        .locked {
            background-color: #f8d7da !important;
            color: #721c24 !important;
            cursor: not-allowed;
        }

        .btn-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
            width: 100%;
        }

        .btn {
            padding: 0.75rem 1rem;
            text-decoration: none;
            font-size: clamp(0.75rem, 1.25vw, 0.875rem);
            border-radius: var(--border-radius);
            color: #fff;
            background-color: var(--primary-color);
            text-align: center;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: var(--box-shadow);
        }

        .btn-secondary {
            background-color: var(--success-color);
        }

        .btn-secondary:hover {
            background-color: #1e7e34;
        }

        .btn-danger {
            background-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: #bd2130;
        }

        footer {
            text-align: center;
            margin: 2rem 0 1rem;
            color: var(--dark-gray);
            font-size: clamp(0.75rem, 1.25vw, 0.875rem);
            padding: 0 1rem;
        }

        /* Responsive Breakpoints */
        @media (max-width: 992px) {
            .dashboard-container {
                grid-template-columns: 1fr;
                padding: 1.5rem;
                gap: 1.5rem;
            }

            .progress-section {
                order: -1;
                border-bottom: 1px solid #eee;
                padding-bottom: 1.5rem;
                margin-bottom: 1rem;
            }

            .btn-group {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
                margin: 1rem;
            }

            .lesson-tree {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }

            .btn, .lesson-tree a, .lesson-tree span {
                padding: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .lesson-tree {
                grid-template-columns: 1fr 1fr;
            }

            .btn-group {
                grid-template-columns: 1fr;
            }

            .progress-circle {
                width: 100px;
                height: 100px;
            }
        }

        @media (max-width: 400px) {
            .lesson-tree {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="progress-section">
        <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?> 👋</h1>
        <p class="user-role">Role: <?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>

        <div class="progress-circle">
            <svg viewBox="0 0 200 200">
                <circle class="bg" cx="100" cy="100" r="85"></circle>
                <circle class="progress" cx="100" cy="100" r="85"
                    style="stroke-dashoffset: <?php echo 534 - (534 * $progressPercent / 100); ?>;">
                </circle>
            </svg>
            <div class="progress-text">
                <?php echo $progressPercent; ?>%
                <span>Completed</span>
            </div>
        </div>

        <div class="btn-group">
            <?php if ($user['role'] === 'super_admin'): ?>
                <a href="./superadmin/manage-admins.php" class="btn">Manage Admins</a>
            <?php endif; ?>
            <?php if ($user['role'] === 'admin'): ?>
                <a href="admin/manage-lessons.php" class="btn">Manage Lessons</a>
                <a href="admin/upload.php" class="btn">Upload Words</a>
                <a href="admin/managegrammar.php" class="btn">Manage Grammar</a>
            <?php endif; ?>
            <a href="learn.php" class="btn btn-secondary">📚 Start Learning</a>
            <a href="grammar.php" class="btn btn-secondary">📕 Grammar</a>
            <a href="quiz.php" class="btn btn-secondary">📝 Quiz</a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
