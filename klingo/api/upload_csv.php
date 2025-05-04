<?php
require_once('../includes/db.php');
require_once('../vendor/autoload.php'); // Include PhpSpreadsheet
session_start();

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    http_response_code(403);
    echo "Access denied!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    $fileType = $_FILES['csv_file']['type'];

    if ($fileType === 'text/csv' || pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION) === 'csv') {
        // Handle CSV normally
        if (($handle = fopen($file, "r")) !== FALSE) {
            $stmt = $conn->prepare("INSERT INTO words (lesson_id, korean, english, nepali, image_url, created_at) VALUES (?, ?, ?, ?, ?, NOW())");

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) < 4) continue;

                $lesson_id = intval($data[0]);
                $korean = trim($data[1]);
                $english = trim($data[2]);
                $nepali = trim($data[3]);
                $image_url = isset($data[4]) ? trim($data[4]) : null;

                $stmt->bind_param("issss", $lesson_id, $korean, $english, $nepali, $image_url);
                $stmt->execute();
            }
            fclose($handle);
        }
    } else {
        // Handle Excel file (.xlsx)
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $stmt = $conn->prepare("INSERT INTO words (lesson_id, korean, english, nepali, image_url, created_at) VALUES (?, ?, ?, ?, ?, NOW())");

        foreach ($rows as $data) {
            if (count($data) < 4) continue;

            $lesson_id = intval($data[0]);
            $korean = trim($data[1]);
            $english = trim($data[2]);
            $nepali = trim($data[3]);
            $image_url = isset($data[4]) ? trim($data[4]) : null;

            $stmt->bind_param("issss", $lesson_id, $korean, $english, $nepali, $image_url);
            $stmt->execute();
        }
    }

    echo "Upload successful!";
} else {
    echo "No file uploaded.";
}
?>
