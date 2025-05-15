<?php
require_once '../includes/db.php';

// Fetch all grammar lessons from database
try {
    $stmt = $conn->query("SELECT * FROM grammar_lessons ORDER BY created_date DESC");
    $lessons = $stmt->fetch_all(MYSQLI_ASSOC);
} catch (PDOException $e) {
    die("Error fetching grammar lessons: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kalam:wght@300;400;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Playwrite+DK+Loopet:wght@100..400&family=Share+Tech&display=swap" rel="stylesheet">

    <title>Grammar Lessons</title>
    <style>
        body {
             font-family: "Playwrite DK Loopet", serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(145deg, #f0f2f5, #e3e6ea);
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }

        h1 {
            font-size: 36px;
            text-align: center;
            color: #2c3e50;
            margin-bottom: 40px;
            position: relative;
        }

        h1::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: #3498db;
            margin: 10px auto 0;
            border-radius: 2px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .lesson-card {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            padding: 24px;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .lesson-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
        }

        .lesson-icon {
            font-size: 30px;
            color: #3498db;
            margin-bottom: 12px;
        }

        .lesson-title {
            text-transform: uppercase;
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 6px;
        }

        .lesson-meta {
            font-size: 14px;
            color: #777;
            margin-bottom: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 30px;
            margin-bottom: 10px;
        }

        .status-active {
            background-color: #eafaf1;
            color: #27ae60;
        }

        .status-inactive {
            background-color: #f4f4f4;
            color: #888;
        }

        .status-draft {
            background-color: #fff4e5;
            color: #e67e22;
        }

        .file-actions {
            margin-top: 15px;
        }

        .file-link, .download-link {
            display: inline-block;
            margin-right: 12px;
            margin-top: 8px;
            font-size: 14px;
            color: #2980b9;
            text-decoration: none;
            padding: 6px 10px;
            border: 1px solid #2980b9;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .file-link:hover,
        .download-link:hover {
            background-color: #2980b9;
            color: white;
        }

        .no-lessons {
            text-align: center;
            font-size: 20px;
            color: #666;
            margin-top: 60px;
        }
    </style>
</head>
<body>
<div class="container">
    <a href="dashboard.php" class="btn-back">Back to dashboard</a>
    <h1>📘 Grammar Lessons</h1>

    <?php if (count($lessons) > 0): ?>
        <div class="card-grid">
            <?php foreach ($lessons as $lesson): ?>
                <div class="lesson-card">
                    <div class="lesson-icon">📄</div>
                    <div class="lesson-title"><?= htmlspecialchars($lesson['lesson_name']) ?></div>
                    <div class="status-badge status-<?= htmlspecialchars($lesson['status']) ?>">
                        <?= ucfirst(htmlspecialchars($lesson['status'])) ?>
                    </div>
                    <div class="lesson-meta">
                        📅 <?= date('M d, Y H:i', strtotime($lesson['created_date'])) ?>
                    </div>

                    <div class="file-actions">
                        <a class="file-link" href="<?= '../uploads/' . basename($lesson['file_url']) ?>" target="_blank">🔍 View File</a>
                        <a class="download-link" href="<?= '../uploads/' . basename($lesson['file_url']) ?>" download>⬇️ Download</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-lessons">No grammar lessons found.</div>
    <?php endif; ?>
</div>
</body>
</html>
