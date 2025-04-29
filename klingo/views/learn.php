<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lesson selection
$currentLesson = isset($_GET['lesson']) ? (int)$_GET['lesson'] : 1;
if ($currentLesson < 1) $currentLesson = 1;

// Fetch all lessons for dropdown
$lessonsResult = $conn->query("SELECT DISTINCT lesson_id FROM words ORDER BY lesson_id ASC");
$allLessons = $lessonsResult->fetch_all(MYSQLI_ASSOC);

// Pagination
$wordsPerPage = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $wordsPerPage;

// Total words count for this lesson
$countStmt = $conn->prepare("SELECT COUNT(*) as total FROM words WHERE lesson_id = ?");
$countStmt->bind_param("i", $currentLesson);
$countStmt->execute();
$countResult = $countStmt->get_result()->fetch_assoc();
$totalWords = $countResult['total'];
$totalPages = ceil($totalWords / $wordsPerPage);

// Fetch words for current page
$stmt = $conn->prepare("SELECT korean, english, nepali FROM words WHERE lesson_id = ? LIMIT ? OFFSET ?");
$stmt->bind_param("iii", $currentLesson, $wordsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learn Korean | K-Lingo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .lesson-select {
            text-align: center;
            margin-bottom: 20px;
        }
        .lesson-select select {
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 16px;
        }
        .word-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .word-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .word-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        .korean-word {
            font-size: 24px;
            font-weight: bold;
            color: #0077cc;
            margin-bottom: 10px;
            text-align: center;
        }
        .english-meaning {
            font-size: 18px;
            margin-bottom: 8px;
            color: #555;
            text-align: center;
        }
        .nepali-meaning {
            font-size: 16px;
            color: #888;
            text-align: center;
        }
        .pagination {
            text-align: center;
            margin-top: 30px;
        }
        .pagination a {
            display: inline-block;
            padding: 10px 15px;
            margin: 0 5px;
            background: #0077cc;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .pagination a:hover {
            background: #005fa3;
        }
        .btn-back {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background: #0077cc;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .btn-back:hover {
            background: #005fa3;
        }
    </style>
</head>
<body>

<div class="container">
    
    <div class="lesson-select">
        <form method="get">
            <label for="lesson" style="font-size: 18px; margin-right: 10px;">Select Lesson:</label>
            <select name="lesson" id="lesson" onchange="this.form.submit()">
                <?php foreach ($allLessons as $lesson): ?>
                    <option value="<?php echo $lesson['lesson_id']; ?>" <?php if ($lesson['lesson_id'] == $currentLesson) echo 'selected'; ?>>
                        Lesson <?php echo $lesson['lesson_id']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <h1>Lesson <?php echo $currentLesson; ?> Vocabulary</h1>

    <div class="word-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="word-card">
                <div class="korean-word"><?php echo htmlspecialchars($row['korean']); ?></div>
                <div class="english-meaning"><?php echo htmlspecialchars($row['english']); ?></div>
                <div class="nepali-meaning"><?php echo htmlspecialchars($row['nepali']); ?></div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?lesson=<?php echo $currentLesson; ?>&page=<?php echo $page - 1; ?>">&laquo; Prev</a>
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?lesson=<?php echo $currentLesson; ?>&page=<?php echo $page + 1; ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>

    <div style="text-align: center;">
        <a href="dashboard.php" class="btn-back">⬅️ Back to Dashboard</a>
    </div>

</div>

</body>
</html>
