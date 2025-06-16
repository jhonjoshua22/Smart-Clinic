<?php
session_start();
require 'db.php';

echo "<pre>";
print_r($_FILES);
echo "</pre>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = trim($_POST['gender']);
    $birth_date = trim($_POST['birth_date']);
    $address = trim($_POST['address']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

    // Check if email already exists
    $check_email = $conn->prepare("SELECT id FROM patients WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        die("Error: Email already exists! <a href='registration.html'>Go back</a>");
    }
    $check_email->close();

    // Prepare insert statement with profile_picture column
    $stmt = $conn->prepare("INSERT INTO patients (first_name, last_name, email, phone, gender, birth_date, address, profile_picture, password) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sssssssss", $first_name, $last_name, $email, $phone, $gender, $birth_date, $address, $profile_picture_path, $password);

    if ($stmt->execute()) {
        // Get the last inserted user ID
        $_SESSION['patient_id'] = $stmt->insert_id;

        // Redirect to home page after successful registration
        header("Location: home.php");
        exit();
    } else {
        die("Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>
