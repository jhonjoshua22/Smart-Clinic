<?php
session_start();
require '../db.php'; // Adjust path as needed

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['patient_id'])) {
    die("Error: Not logged in");
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Error: Invalid request method");
}

if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    die("Error: No file uploaded or upload error");
}

$user_id = $_SESSION['patient_id'];

$file = $_FILES['photo'];

// Validate file extension
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
$file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($file_ext, $allowed_extensions)) {
    die("Error: Invalid file type. Only JPG, JPEG, PNG, GIF allowed.");
}

// Set upload directory and create if doesn't exist
$uploadDir = '../Uploads/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        die("Error: Failed to create upload directory");
    }
}

// Generate new unique file name
$newFileName = 'profile_' . $user_id . '_' . time() . '.' . $file_ext;
$destination = $uploadDir . $newFileName;

// Move uploaded file to destination
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    die("Error: Failed to move uploaded file");
}

// Save relative path to DB
$relativePath = 'Uploads/' . $newFileName;

// Update profile_picture in database using prepared statement
$stmt = $conn->prepare("UPDATE patients SET profile_picture = ? WHERE id = ?");
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("si", $relativePath, $user_id);

if (!$stmt->execute()) {
    die("Error executing statement: " . $stmt->error);
}

$stmt->close();
$conn->close();

// Redirect back to myaccount page after successful upload
header("Location: myaccount/index.php");
exit();
