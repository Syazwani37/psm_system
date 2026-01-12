<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php';
/**
 * PSM System - Upload Resource Handler
 * Backend API for handling file uploads from professionals
 */

require_once '../config/database.php';
require_once '../helpers/functions.php';
require_once '../includes/auth_check.php';

// Secure Session Start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Only Professionals can upload
if ($_SESSION['role'] !== 'professional') {
    http_response_code(403);
    die('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['resource_file'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category = sanitize($_POST['category']); // expert_article, support_resource, nutrition_plan, exercise_plan
    
    // File Properties
    $file = $_FILES['resource_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    // Allowed Extensions
    $allowed = ['pdf', 'doc', 'docx', 'mp4', 'avi'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 50000000) { // 50MB Max
                // Generate unique name
                $fileNameNew = uniqid('', true) . "." . $fileExt;
                $uploadDir = '../../frontend/assets/uploads/resources/';
                
                // Create dir if not exists
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileDestination = $uploadDir . $fileNameNew;
                $dbPath = '/frontend/assets/uploads/resources/' . $fileNameNew;

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // Update Database
                    // Note: 'resources' table columns: id, title, description, category, file_path, created_at
                    $stmt = $conn->prepare("INSERT INTO resources (title, description, category, file_path, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->bind_param("ssss", $title, $description, $category, $dbPath);
                    
                    if ($stmt->execute()) {
                        setFlashMessage('success', 'Resource uploaded successfully!');
                    } else {
                        setFlashMessage('error', 'Database error: ' . $stmt->error);
                    }
                    $stmt->close();
                    
                    // Redirect back
                    header("Location: " . BASE_URL . "/frontend/views/resources/library.php");
                    exit();
                } else {
                    setFlashMessage('error', 'Failed to move uploaded file.');
                }
            } else {
                setFlashMessage('error', 'File is too large (Max 50MB).');
            }
        } else {
            setFlashMessage('error', 'Error uploading file.');
        }
    } else {
        setFlashMessage('error', 'Invalid file type. Allowed: PDF, DOC, Video.');
    }
    
    // Redirect on error
    header("Location: " . BASE_URL . "/frontend/views/resources/library.php");
    exit();
}
?>
