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

        if (!$prevLessonId || empty($userProgress[$prevLessonId])) {
            // User has not completed previous lesson
            header('Location: quiz.php');
            exit();
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
.quiz-container {
    max-width: 900px;
    margin: 50px auto;
    padding: 20px;
}
.quiz-container h1 {
    text-align: center;
    font-size: 2.2rem;
    color: #333;
    margin-bottom: 30px;
}
.lesson-selection {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    list-style: none;
    padding: 0;
}
.lesson-selection li a, .lesson-selection li span {
    display: block;
    text-align: center;
    background: #007bff;
    color: #fff;
    padding: 15px;
    border-radius: 10px;
    font-weight: bold;
    transition: background 0.3s;
    text-decoration: none;
}
.lesson-selection li span {
    background: #6c757d;
    cursor: not-allowed;
}
.lesson-selection li a:hover {
    background: #0056b3;
}
.quiz-question {
    background: #f9f9f9;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.quiz-question h3 {
    margin-bottom: 20px;
    font-size: 1.3rem;
    color: #444;
}
.option {
    background: #fff;
    padding: 12px;
    border: 1px solid #ddd;
    margin-bottom: 10px;
    border-radius: 8px;
    transition: all 0.3s;
    cursor: pointer;
    display: flex;
    align-items: center;
}
.option input[type="radio"] {
    margin-right: 15px;
}
.option:hover {
    background: #f0f8ff;
    border-color: #007bff;
}
.option.correct {
    background: #d4edda !important;
}
.option.wrong {
    background: #f8d7da !important;
}
.icon {
    margin-left: auto;
    font-size: 20px;
}
.quiz-buttons {
    text-align: center;
    margin-top: 30px;
}
.quiz-buttons button {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 12px 25px;
    font-size: 1rem;
    border-radius: 8px;
    margin: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.quiz-buttons button:hover {
    background-color: #218838;
}
</style>

<div class="quiz-container">
<?php if (!$lesson_id): ?>
    <h1>Select a Lesson to Start Quiz</h1>
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
                $canAccess = $prevLessonId && !empty($userProgress[$prevLessonId]);
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
