<?php
require_once('../../includes/db.php');
require_once('../../includes/auth.php');

// Only allow admins
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header('Location: ../dashboard.php');
    exit();
}

// Load PhpSpreadsheet for Excel reading
require '../../vendor/autoload.php'; // make sure you have PhpSpreadsheet installed via Composer

use PhpOffice\PhpSpreadsheet\IOFactory;

$message = '';
$message_type = ''; // success or error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        $fileType = mime_content_type($file);

        $stmt = $conn->prepare("INSERT INTO words (lesson_id, korean, english, nepali, image_url, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $rowCount = 0;
        $duplicateCount = 0;

        if (in_array($fileType, ['text/plain', 'text/csv', 'application/vnd.ms-excel'])) {
            // Process CSV file
            if (($handle = fopen($file, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (count($data) < 4) continue;
                    processRow($data, $conn, $stmt, $rowCount, $duplicateCount);
                }
                fclose($handle);
            } else {
                $message = "Failed to open CSV file.";
                $message_type = 'error';
            }
        } else {
            // Process Excel file
            try {
                $spreadsheet = IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                foreach ($sheet->getRowIterator() as $row) {
                    $cells = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $cells[] = $cell->getValue();
                    }
                    if (count($cells) < 4) continue;
                    processRow($cells, $conn, $stmt, $rowCount, $duplicateCount);
                }
            } catch (Exception $e) {
                $message = "Failed to read Excel file: " . $e->getMessage();
                $message_type = 'error';
            }
        }

        if ($rowCount > 0) {
            $message = "$rowCount words uploaded successfully.";
            if ($duplicateCount > 0) {
                $message .= " ($duplicateCount duplicates skipped.)";
            }
            $message_type = 'success';
        } else {
            $message = "No new words uploaded. Check file or duplicates.";
            $message_type = 'error';
        }
    } else {
        $message = "Please select a valid CSV or Excel file.";
        $message_type = 'error';
    }
}

function processRow($data, $conn, $stmt, &$rowCount, &$duplicateCount) {
    $lesson_id = intval($data[0]);
    $korean = trim($data[1]);
    $english = trim($data[2]);
    $nepali = trim($data[3]);
    $image_url = isset($data[4]) ? trim($data[4]) : null;

    // Check for duplicates
    $checkStmt = $conn->prepare("SELECT id FROM words WHERE lesson_id = ? AND korean = ?");
    $checkStmt->bind_param("is", $lesson_id, $korean);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $duplicateCount++;
        return; // Duplicate found
    }

    // Insert if not duplicate
    $stmt->bind_param("issss", $lesson_id, $korean, $english, $nepali, $image_url);
    if ($stmt->execute()) {
        $rowCount++;
    }
}
?>

<!-- Your existing HTML code for the form remains the same -->
<?php include('../../includes/header.php'); ?>

<div class="upload-page-container">
    <div class="upload-card">
        <h1>📤 Upload Words CSV/Excel</h1>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="upload-form">
            <input type="file" name="csv_file" accept=".csv, .xlsx, .xls" required class="file-input">
            <button type="submit" class="upload-btn">Upload File</button>
        </form>
    </div>
</div>

<center><?php include('../../includes/footer.php'); ?></center>
<style>
    *{
        font-family: times;
    }
.upload-page-container {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f2f5f7;
    padding: 20px;
}

.upload-card {
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    max-width: 450px;
    width: 100%;
    text-align: center;
}

.upload-card h1 {
    margin-bottom: 20px;
    font-size: 26px;
    color: #333;
}

.alert {
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-size: 16px;
}

.alert.success {
    background-color: #d4edda;
    color: #155724;
}

.alert.error {
    background-color: #f8d7da;
    color: #721c24;
}

.upload-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.file-input {
    padding: 10px;
    border-radius: 5px;
    outline: 2px dashed #bbb;
    height: 90px;
    text-align:center;
    place-content:center;
    font-size:20px; 
    cursor:pointer;
}

.upload-btn {
    background: #007bff;
    color: #fff;
    border: none;
    padding: 12px;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.upload-btn:hover {
    background: #0056b3;
}
</style>
