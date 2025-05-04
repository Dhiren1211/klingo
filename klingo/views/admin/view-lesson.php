<?php
require_once('../../includes/db.php');
require_once('../../includes/auth.php');

// Only allow admin or superadmin
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin') {
    header('Location: ../dashboard.php');
    exit();
}

// Validate and get lesson ID
$lesson_id = isset($_GET['lesson']) ? (int) $_GET['lesson'] : 0;
if ($lesson_id <= 0) {
    header('Location: manage-lessons.php');
    exit();
}

// Pagination variables
$limit = 10; // Number of words per page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count total words
$stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM words WHERE lesson_id = ?");
$stmt_count->bind_param('i', $lesson_id);
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$total_words = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_words / $limit);

// Fetch words for this page
$stmt = $conn->prepare("SELECT id, korean, english, nepali, image_url FROM words WHERE lesson_id = ? LIMIT ? OFFSET ?");
$stmt->bind_param('iii', $lesson_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Fetch lesson info
$lesson_info = $conn->query("SELECT lesson_number FROM lessons WHERE id = $lesson_id")->fetch_assoc();

?>

<?php include('../../includes/header.php'); ?>

<div class="admin-lesson-words-container">
    <h1>Lesson <?php echo htmlspecialchars($lesson_info['lesson_number'] ?? $lesson_id); ?> - Words</h1>

    <a href="manage-lessons.php" class="btn-small"  style="color: #007BFF; text-decoration: none;">Back to Lessons</a>

    <?php if ($result->num_rows > 0): ?>
        <table class="word-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Korean</th>
                    <th>English</th>
                    <th>Nepali</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($word = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $word['id']; ?></td>
                        <td><?php echo htmlspecialchars($word['korean']); ?></td>
                        <td><?php echo htmlspecialchars($word['english']); ?></td>
                        <td><?php echo htmlspecialchars($word['nepali']); ?></td>
                        <td>
                            <?php if (!empty($word['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($word['image_url']); ?>" alt="Word Image" width="50">
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?lesson=<?php echo $lesson_id; ?>&page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?lesson=<?php echo $lesson_id; ?>&page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?lesson=<?php echo $lesson_id; ?>&page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <p>No words found for this lesson.</p>
    <?php endif; ?>
</div>

<center> <?php include('../../includes/footer.php'); ?> </center>

<style>
    .word-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.word-table th, .word-table td {
    border: 1px solid #ccc;
    padding: 8px 12px;
    text-align: center;
}

.word-table th {
    background-color: #f2f2f2;
}

.admin-lesson-words-container h1 {
    margin-bottom: 20px;
}
.pagination {
    margin-top: 20px;
    text-align: center;
}

.pagination a {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 3px;
    border: 1px solid #ccc;
    text-decoration: none;
    color: #333;
}

.pagination a.active {
    background-color: #4CAF50;
    color: white;
    border-color: #4CAF50;
}

.pagination a:hover {
    background-color: #ddd;
}

</style>