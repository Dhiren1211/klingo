<?php
require_once('../../includes/db.php');
require_once('../../includes/auth.php');

// Only allow admin or superadmin
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin') {
    header('Location: ../dashboard.php');
    exit();
}

// Add new lesson if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_lesson_number'])) {
    $newLesson = intval($_POST['new_lesson_number']);
    
    if ($newLesson > 0) {
        // Check if lesson already exists
        $check = $conn->prepare("SELECT id FROM lessons WHERE lesson_number = ?");
        $check->bind_param("i", $newLesson);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO lessons (lesson_number, is_active, created_at) VALUES (?, 1, NOW())");
            $stmt->bind_param("i", $newLesson);
            if ($stmt->execute()) {
                header('Location: manage-lessons.php');
                exit();
            } else {
                $error = "Failed to add lesson.";
            }
        } else {
            $error = "Lesson already exists!";
        }
    } else {
        $error = "Invalid lesson number.";
    }
}

// Fetch all lessons
$result = $conn->query("SELECT id, lesson_number FROM lessons WHERE is_active = 1 ORDER BY lesson_number ASC");
?>

<?php include('../../includes/header.php'); ?>

<style>
.admin-lessons-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

.admin-lessons-container h1 {
    text-align: center;
    margin-bottom: 30px;
    font-size: 2.5rem;
    color: #333;
}

.lesson-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.lesson-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.lesson-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.lesson-card strong {
    font-size: 1.2rem;
    color: #555;
    display: block;
    margin-bottom: 15px;
}

.view-button, .add-button {
    display: inline-block;
    padding: 10px 18px;
    background-color: #007bff;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.view-button:hover, .add-button:hover {
    background-color: #0056b3;
}

.add-lesson-card {
    position:relative;
    background: #f9f9f9;
    border: 2px dashed #bbb;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    align-items: center;
    place-items: center;    
    justify-content: center;
}
.add-lesson-card strong{
    font-size: 1.2rem;
    color: #555;
    display: block;
    margin-top: 25px;
}

.add-lesson-card:hover {
    background: #f1f1f1;
}
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    padding-top: 120px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background: #fff;
    margin: auto;
    padding: 10px;
    border-radius: 12px;
    max-width: 400px;
    position: relative;
    text-align: center;
}

.modal-content h2 {
    margin-bottom:5px;
    font-size: 24px;
}

.modal-content input[type="number"] {
    width: 80%;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid #ccc;
    outline:none;
}

.modal-content button {
    padding: 10px 20px;
    border: none;
    background: #007bff;
    color: #fff;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.modal-content button:hover {
    background: #0056b3;
}

.modal-close {
    position: absolute;
    right: 15px;
    top: 15px;
    font-size: 20px;
    cursor: pointer;
}
.error-message {
    text-align: center;
    color: red;
    margin-bottom: 20px;
}
</style>

<div class="admin-lessons-container">
    <h1>Manage Lessons</h1>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="lesson-grid">
        <!-- Add New Lesson Card -->
        <div class="add-lesson-card" onclick="openModal()">
            <strong>➕ Add New Lesson</strong>
        </div>

        <?php while ($lesson = $result->fetch_assoc()): ?>
            <div class="lesson-card">
                <strong>Lesson <?php echo htmlspecialchars($lesson['lesson_number']); ?></strong>
                <a href="view-lesson.php?lesson=<?php echo urlencode($lesson['id']); ?>" class="view-button">View Words</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Modal for Adding New Lesson -->
<div id="lessonModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()"style = "font-size: 30px; color: red; font-weight: bold">&times; </span>
        <h2>Add New Lesson</h2>
        <form method="POST">
            <input type="number" name="new_lesson_number" min="1" placeholder="Enter Lesson Number" required>
            <br>
            <button type="submit">Add Lesson</button>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('lessonModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('lessonModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('lessonModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<center><?php include('../../includes/footer.php'); ?></center>
