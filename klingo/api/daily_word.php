<?php
require_once('../includes/db.php');

// Pick a random word
$stmt = $conn->query("SELECT * FROM words ORDER BY RAND() LIMIT 1");
$word = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($word);
