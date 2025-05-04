<?php
require_once('../includes/db.php');
require_once('../includes/auth.php');

// Require login
requireLogin();

$userId = $_SESSION['user_id'];

// Fetch all lessons
$lessons_result = $conn->query("SELECT id, lesson_number FROM lessons WHERE is_active = 1 ORDER BY lesson_number ASC");

// Fetch user progress
$stmt = $conn->prepare("SELECT lesson_id, completed FROM user_progress WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$userProgress = [];
while ($row = $result->fetch_assoc()) {
    $userProgress[(int)$row['lesson_id']] = (bool)$row['completed'];
}

// Selected lesson
$lesson_id = isset($_GET['lesson']) ? (int) $_GET['lesson'] : 0;
$words = [];

// Check if user is allowed to access the selected lesson
if ($lesson_id > 0) {
    // Get the current lesson number
    $stmtLesson = $conn->prepare("SELECT lesson_number FROM lessons WHERE id = ?");
    $stmtLesson->bind_param("i", $lesson_id);
    $stmtLesson->execute();
    $lessonData = $stmtLesson->get_result()->fetch_assoc();
    $currentLessonNumber = $lessonData['lesson_number'] ?? null;

    if ($currentLessonNumber && $currentLessonNumber > 1) {
        $prevLessonNumber = $currentLessonNumber - 1;

        // Get previous lesson ID
        $stmtPrevLesson = $conn->prepare("SELECT id FROM lessons WHERE lesson_number = ?");
        $stmtPrevLesson->bind_param("i", $prevLessonNumber);
        $stmtPrevLesson->execute();
        $prevLessonData = $stmtPrevLesson->get_result()->fetch_assoc();
        $prevLessonId = $prevLessonData['id'] ?? null;

        $isAdmin = ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super_admin');

        if (!$prevLessonId || empty($userProgress[$prevLessonId])) {
            if (!$isAdmin) {
                header('Location: quiz.php');
                exit();
            }
        }
        
    }

    // Fetch words if access allowed
    $stmt = $conn->prepare("SELECT * FROM words WHERE lesson_id = ?");
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $words = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<?php include('../includes/header.php'); ?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f2f4f8;
    margin: 0;
    padding: 0;
}

header {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    background-color: #3b4c63;
    padding: 15px;
    color: white;
    text-align: center;
}

.quiz-container {
    margin: 100px auto 30px auto;
    width: 90%;
    max-width: 900px;
    background-color: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
    height: calc(100vh - 200px);
    justify-content: center;
    align-items: center;
}

h1 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
    font-size: 2rem;
}

.lesson-selection {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 15px;
    padding: 20px;
    list-style: none;
    background-color: #e9edf3;
    border-radius: 12px;
    margin-bottom: 30px;
}

.lesson-selection li a,
.lesson-selection li span {
    display: block;
    padding: 15px;
    text-align: center;
    font-weight: 600;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    background-color: #577a9d;
    transition: background-color 0.3s ease;
}

.lesson-selection li span {
    background-color: #888;
    cursor: not-allowed;
}

.lesson-selection li a:hover {
    background-color: #3a5870;
}

.quiz-question {
    margin-bottom: 30px;
    background-color: #fefefe;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #ddd;
}

.quiz-question h3 {
    margin-bottom: 20px;
    color: #444;
}

.option {
    background-color: #fff;
    border: 1px solid #ccc;
    padding: 12px 15px;
    margin-bottom: 10px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: background-color 0.3s;
}

.option:hover {
    background-color: #f0f8ff;
    border-color: #007bff;
}

.option input[type="radio"] {
    margin-right: 12px;
}

.option.correct {
    background-color: #d4edda;
}

.option.wrong {
    background-color: #f8d7da;
}

.icon {
    margin-left: auto;
    font-size: 18px;
}

.quiz-buttons {
    text-align: center;
    margin-top: 30px;
}

.quiz-buttons button {
    background-color: #4CAF50;
    color: white;
    padding: 12px 24px;
    font-size: 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin: 5px;
}

.quiz-buttons button:hover {
    background-color: #45a049;
}

@media (max-width: 600px) {
    .quiz-container {
        padding: 15px;
    }

    .quiz-question h3 {
        font-size: 1rem;
    }

    .lesson-selection li a,
    .lesson-selection li span {
        font-size: 14px;
        padding: 12px;
    }
}


</style>

<div class="quiz-container">
<?php if (!$lesson_id): ?>
    <h1>Select a Lesson to Start Quiz</h1>
    <div class="lessons" style = " height: 80%; width:100%; display: flex; flex-wrap: wrap; flex-direction: row; justify-content: center;">
    <ul class="lesson-selection">
        <?php 
        $lessons_result->data_seek(0); // Reset pointer
        while ($lesson = $lessons_result->fetch_assoc()):
            $lessonId = $lesson['id'];
            $lessonNumber = $lesson['lesson_number'];

            $canAccess = true;
            if ($lessonNumber > 1) {
                $prevLessonNumber = $lessonNumber - 1;
                $stmtPrev = $conn->prepare("SELECT id FROM lessons WHERE lesson_number = ?");
                $stmtPrev->bind_param("i", $prevLessonNumber);
                $stmtPrev->execute();
                $prevLessonData = $stmtPrev->get_result()->fetch_assoc();
                $prevLessonId = $prevLessonData['id'] ?? null;
                $hasCompletedPrev = $prevLessonId && !empty($userProgress[$prevLessonId]);
            
                // Allow admin/super admin access even if they haven't completed previous lessons
                $canAccess = $hasCompletedPrev || ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super_admin');
            }
        ?>
            <?php if ($canAccess): ?>
                <li><a href="?lesson=<?php echo $lessonId; ?>">
                    Lesson <?php echo htmlspecialchars($lessonNumber); ?>
                </a></li>
            <?php else: ?>
                <li><span>🔒 Lesson <?php echo htmlspecialchars($lessonNumber); ?></span></li>
            <?php endif; ?>
        <?php endwhile; ?>
    </ul>
    </div>
<?php else: ?>
    <?php if (empty($words)): ?>
        <p>No words available for quiz in this lesson.</p>
    <?php else: ?>
        <h1>Quiz - Lesson <?php echo htmlspecialchars($lesson_id); ?></h1>

        <form id="quiz-form">
            <?php foreach ($words as $index => $word): ?>
                <?php
                $options = [$word['english']];
                while (count($options) < 4) {
                    $randomWord = $words[array_rand($words)];
                    if (!in_array($randomWord['english'], $options)) {
                        $options[] = $randomWord['english'];
                    }
                }
                shuffle($options);
                ?>
                <div class="quiz-question" data-correct="<?php echo htmlspecialchars($word['english']); ?>" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>">
                    <h3><?php echo ($index + 1) . ". What is the English meaning of \"" . htmlspecialchars($word['korean']) . "\"?"; ?></h3>
                    <?php foreach ($options as $option): ?>
                        <div class="option">
                            <label>
                                <input type="radio" name="question_<?php echo $index; ?>" value="<?php echo htmlspecialchars($option); ?>">
                                <?php echo htmlspecialchars($option); ?>
                                <span class="icon"></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <div class="quiz-buttons">
                <button type="button" id="next-btn">Next</button>
                <button type="submit" id="submit-btn" style="display: none;">Submit Quiz</button>
            </div>
        </form>
    <?php endif; ?>
<?php endif; ?>
</div>
<center><?php include('../includes/footer.php'); ?> </center>


<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentQuestion = 0;
    let score = 0;
    const questions = document.querySelectorAll('.quiz-question');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');

    function showQuestion(index) {
        questions.forEach((q, i) => {
            q.style.display = (i === index) ? 'block' : 'none';
        });
        if (index === questions.length - 1) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'inline-block';
        }
    }

    nextBtn.addEventListener('click', function() {
        const selected = questions[currentQuestion].querySelector('input[type="radio"]:checked');
        if (!selected) {
            alert('Please select an answer before proceeding.');
            return;
        }
        checkAnswer();
        setTimeout(() => {
            currentQuestion++;
            if (currentQuestion < questions.length) {
                showQuestion(currentQuestion);
            }
        }, 500);
    });

    document.getElementById('quiz-form').addEventListener('submit', function(e) {
        e.preventDefault();
        checkAnswer();
        setTimeout(() => {
            const lessonId = <?php echo json_encode($lesson_id); ?>;
            const totalQuestions = questions.length;
            const correctAnswers = score;

            const params = new URLSearchParams();
            params.append('lesson_id', lessonId);
            params.append('score', score);
            params.append('correct_answers', correctAnswers);
            params.append('total_questions', totalQuestions);

            fetch('../api/submit_quiz.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: params.toString()
            })
            .then(response => response.text())
            .then(data => {
                alert(`Quiz Submitted! Your Score: ${score} / ${questions.length}`);
                window.location.href = 'dashboard.php';
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }, 500);
    });

    function checkAnswer() {
        const question = questions[currentQuestion];
        const selected = question.querySelector('input[type="radio"]:checked');
        const correctAnswer = question.dataset.correct;

        question.querySelectorAll('.option').forEach(opt => {
            const input = opt.querySelector('input');
            const icon = opt.querySelector('.icon');
            icon.innerHTML = '';

            if (input.value === correctAnswer) {
                opt.classList.add('correct');
                icon.innerHTML = '✔️';
            }

            if (input.checked) {
                if (input.value === correctAnswer) {
                    score++;
                } else {
                    opt.classList.add('wrong');
                    icon.innerHTML = '❌';
                }
            }
        });

        question.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.disabled = true;
        });
    }
});
</script>
