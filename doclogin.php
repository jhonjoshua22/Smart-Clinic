<?php
session_start(); // Start session at the beginning
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT id, password FROM doctors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // Verify the entered password with the hashed password in the database
        if (password_verify($password, $hashed_password)) {
            $_SESSION['doctor_id'] = $id; // Save doctor ID in session
            header("Location: dochome.php"); // Redirect to home page after login
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='doclogin.html';</script>";
        }
    } else {
        echo "<script>alert('No account found with this email!'); window.location.href='doclogin.html';</script>";
    }
    $stmt->close();
}
$conn->close();
?>
