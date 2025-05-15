<?php
require_once '../../includes/db.php';

session_start();

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        // First get the file URL to delete the physical file
        $stmt = $conn->prepare("SELECT file_url FROM grammar_lessons WHERE grammar_ID = ?");
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $lesson = $result->fetch_assoc();
        
        if ($lesson) {
            // Delete the physical file
            if (file_exists($lesson['file_url'])) {
                unlink($lesson['file_url']);
            }
            
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM grammar_lessons WHERE grammar_ID = ?");
            $stmt->bind_param("i", $_GET['id']);
            $stmt->execute();
            
            $_SESSION['message'] = "Grammar lesson deleted successfully!";
            $_SESSION['message_type'] = "success";
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "Error deleting lesson: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
    header('Location: managegrammar.php');
    exit;
}

// Handle form submission for add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $grammar_ID = $_POST['grammar_ID'] ?? null;
    $lesson_name = trim($_POST['lesson_name']);
    $status = $_POST['status'];
    
    try {
        // File upload handling
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = '../../uploads/';
            $fileName = uniqid() . '_' . basename($_FILES['pdf_file']['name']);
            $targetPath = $uploadDir . $fileName;
            
            // Check if file is a PDF
            $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
            if ($fileType != 'pdf') {
                throw new Exception("Only PDF files are allowed.");
            }
            
            if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $targetPath)) {
                $file_url = $targetPath;
            } else {
                throw new Exception("Error uploading file.");
            }
        }
        
        if ($grammar_ID) {
            // Update existing record
            if (isset($file_url)) {
                // Get old file to delete it
                $stmt = $conn->prepare("SELECT file_url FROM grammar_lessons WHERE grammar_ID = ?");
                $stmt->bind_param("i", $grammar_ID);
                $stmt->execute();
                $result = $stmt->get_result();
                $oldLesson = $result->fetch_assoc();
                
                $stmt = $conn->prepare("UPDATE grammar_lessons SET lesson_name = ?, file_url = ?, status = ? WHERE grammar_ID = ?");
                $stmt->bind_param("sssi", $lesson_name, $file_url, $status, $grammar_ID);
                $stmt->execute();
                
                if ($oldLesson && file_exists($oldLesson['file_url'])) {
                    unlink($oldLesson['file_url']);
                }
            } else {
                $stmt = $conn->prepare("UPDATE grammar_lessons SET lesson_name = ?, status = ? WHERE grammar_ID = ?");
                $stmt->bind_param("ssi", $lesson_name, $status, $grammar_ID);
                $stmt->execute();
            }
            
            $_SESSION['message'] = "Grammar lesson updated successfully!";
        } else {
            // Insert new record
            if (!isset($file_url)) {
                throw new Exception("PDF file is required for new lessons.");
            }
            
            $stmt = $conn->prepare("INSERT INTO grammar_lessons (lesson_name, file_url, status) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $lesson_name, $file_url, $status);
            $stmt->execute();
            
            $_SESSION['message'] = "Grammar lesson added successfully!";
        }
        
        $_SESSION['message_type'] = "success";
        header('Location: managegrammar.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}

// Fetch all grammar lessons
try {
    $result = $conn->query("SELECT * FROM grammar_lessons ORDER BY created_date DESC");
    $lessons = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Error fetching grammar lessons: " . $e->getMessage());
}

// For edit mode, fetch the lesson to edit
$editLesson = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("SELECT * FROM grammar_lessons WHERE grammar_ID = ?");
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $editLesson = $result->fetch_assoc();
    } catch (Exception $e) {
        $_SESSION['message'] = "Error fetching lesson: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Grammar Lessons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
    .file-preview {
        border: 1px dashed #ccc;
        padding: 10px;
        margin-top: 10px;
    }

    .file-preview a {
        display: block;
        margin-top: 5px;
    }
    .pdf-modal .modal-dialog {
        max-width: 80%;
    }

    .pdf-modal iframe {
        width: 100%;
        height: 80vh;
        border: none;
    }
    </style>
</head>

<body>
    <div class="container mt-4">
         <a href="../dashboard.php" class="btn btn-secondary float-end">Back to Dashboard</a>
        <h1 class="mb-4">Manage Grammar Lessons</h1>
       
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?>">
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                <?= $editLesson ? 'Edit Grammar Lesson' : 'Add New Grammar Lesson' ?>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="grammar_ID" value="<?= $editLesson ? $editLesson['grammar_ID'] : '' ?>">

                    <div class="mb-3">
                        <label for="lesson_name" class="form-label">Lesson Name</label>
                        <input type="text" class="form-control" id="lesson_name" name="lesson_name"
                            value="<?= htmlspecialchars($editLesson['lesson_name'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="pdf_file" class="form-label">PDF File</label>
                        <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf"
                            <?= !$editLesson ? 'required' : '' ?>>

                        <?php if ($editLesson && !empty($editLesson['file_url'])): ?>
                        <div class="file-preview mt-2">
                            <strong>Current File:</strong>
                            <a href="<?= htmlspecialchars($editLesson['file_url']) ?>" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> View PDF
                            </a>
                            <small class="text-muted">Upload a new file only if you want to replace this one.</small>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= ($editLesson['status'] ?? '') == 'active' ? 'selected' : '' ?>>
                                Active</option>
                            <option value="inactive"
                                <?= ($editLesson['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="draft" <?= ($editLesson['status'] ?? '') == 'draft' ? 'selected' : '' ?>>
                                Draft</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <?= $editLesson ? 'Update Lesson' : 'Add Lesson' ?>
                    </button>

                    <?php if ($editLesson): ?>
                    <a href="managegrammar.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Grammar Lessons List
        
            </div>
            <div class="card-body">
                <?php if (count($lessons) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Lesson Name</th>
                                <th>File</th>
                                <th>Created Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lessons as $lesson): ?>
                            <tr>
                                <td><?= htmlspecialchars($lesson['grammar_ID']) ?></td>
                                <td><?= htmlspecialchars($lesson['lesson_name']) ?></td>
                                <td>
                                    <a href="<?= htmlspecialchars($lesson['file_url']) ?>" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-pdf"></i> View
                                    </a>
                                </td>
                                <td><?= date('M d, Y H:i', strtotime($lesson['created_date'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                                $lesson['status'] == 'active' ? 'success' : 
                                                ($lesson['status'] == 'draft' ? 'warning' : 'secondary') 
                                            ?>">
                                        <?= ucfirst(htmlspecialchars($lesson['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="managegrammar.php?action=edit&id=<?= $lesson['grammar_ID'] ?>"
                                        class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="managegrammar.php?action=delete&id=<?= $lesson['grammar_ID'] ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this lesson?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">No grammar lessons found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>