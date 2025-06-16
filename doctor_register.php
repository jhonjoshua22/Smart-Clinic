<?php
session_start();
require 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $full_name      = trim($_POST['full_name']);
    $email          = trim($_POST['email']);
    $phone          = trim($_POST['phone']);
    $specialization = trim($_POST['specialization']);
    $clinic         = trim($_POST['clinic']);
    $availability   = trim($_POST['availability']);
    $password       = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure hash

    // Check if email already exists
    $check_email = $conn->prepare("SELECT id FROM doctors WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        die("Error: Email already exists! <a href='doctor_register.html'>Go back</a>");
    }
    $check_email->close();

    // Insert into doctors table
    $stmt = $conn->prepare("INSERT INTO doctors (full_name, email, phone, specialization, clinic, availability, password)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sssssss", $full_name, $email, $phone, specialization, $clinic, $availability, $password);

    if ($stmt->execute()) {
        $_SESSION['doctor_id'] = $stmt->insert_id;
        header("Location: doctor_home.php");
        exit();
    } else {
        die("Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>
