<?php
require_once('../includes/db.php');

$lesson_id = $_GET['lesson_id'] ?? 1;

// Fetch words for a lesson
$stmt = $conn->prepare("SELECT * FROM words WHERE lesson_id = ?");
$stmt->execute([$lesson_id]);
$words = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($words);
