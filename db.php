<?php
$host = "localhost";
$user = "root";  // Default XAMPP MySQL user
$pass = "";  // Default XAMPP MySQL password (leave empty)
$dbname = "smart_clinic";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
