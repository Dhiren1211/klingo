<?php
require_once('../includes/db.php');
require_once('../includes/auth.php');

// Require login
requireLogin();

// Get POST data safely
$lesson_id = (int) ($_POST['lesson_id'] ?? 0);
$score = (int) ($_POST['score'] ?? 0);
$correct_answers = (int) ($_POST['correct_answers'] ?? 0);
$total_questions = (int) ($_POST['total_questions'] ?? 0);
$user_id = $_SESSION['user_id']; // Assuming user_id stored in session

// Check if data is valid
if ($lesson_id > 0 && $score >= 0 && $correct_answers >= 0 && $total_questions > 0) {
    
    // Save to quiz_results table
    $stmt = $conn->prepare("
        INSERT INTO quiz_results (user_id, lesson_id, correct_answers, total_questions, score, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("iiiii", $user_id, $lesson_id, $correct_answers, $total_questions, $score);

    if ($stmt->execute()) {
        // ✅ Successfully saved quiz result

        // Now check if lesson is completed (full marks = correct_answers == total_questions)
        $completed = ($correct_answers === $total_questions) ? 1 : 0;
        $completed_at = $completed ? date('Y-m-d H:i:s') : NULL;

        // Save or update into user_progress
        $progressStmt = $conn->prepare("
            INSERT INTO user_progress (user_id, lesson_id, quiz_score, completed, completed_at)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                quiz_score = VALUES(quiz_score),
                completed = VALUES(completed),
                completed_at = VALUES(completed_at)
        ");

        if ($progressStmt === false) {
            die('Prepare failed for user_progress: ' . htmlspecialchars($conn->error));
        }

        $progressStmt->bind_param("iiiss", $user_id, $lesson_id, $score, $completed, $completed_at);
        $progressStmt->execute();

        echo "Score saved successfully!";

    } else {
        echo "Error saving score: " . htmlspecialchars($stmt->error);
    }

} else {
    echo "Invalid data provided.";
}
?>
