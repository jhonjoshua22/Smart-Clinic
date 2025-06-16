<?php
session_start();
require 'db.php';

$user_id = $_SESSION['doctor_id'];

// Fetch patient info
$query = "SELECT full_name, email, phone, specialization, clinic, availability, profile_picture FROM doctors WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $user = null;
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Home Page</title>
		<link rel="stylesheet" href="../Login/homedes.css">
		<link rel="icon" type="image/x-icon" href="images\favicon.ico">
		<script src="script.js"></script>
		<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
	</head>
<body>
	<div class="nav">
		<img style="width:4.5vw; float:left; margin-left: 1vw" src="images/SClogo.png">
		<div class="name">Dr. <?php echo htmlspecialchars($user['full_name']); ?></div>
		<input type="text" id="search-bar" placeholder="Search...">
		<button id="search-button">🔍</button>


		<div class="time" id="time">localTime();</div>
	</div>
	
	<div class="welcome">SMART CLINIC</div>

	<div class="buttons">
  		<div class="choices"><p><a href="DocAccount\index.php">My Account</a></p></div>
		<div class="choices"><p><a href="DocAppointment\index.php">Book an Appointment</a></p></div>
		<div class="choices"><p><a href="DocOC\index.php">Online Consultation</a></p></div>
		<div class="choices"><p><a href="#clinics">Our Clinic</a></p></div>
		<div class="choices"><p><a href="#news">News</a></p></div>
  		<div class="choices"><p><a href="#services">Services</a></p></div>
		<div class="choices"><p><a href="#contact">Contact Us</a></p></div>
		<div class="choices"><p><a href="logout.php">Login</a></p></div>
	</div>
	
	<div class="mission">
		<p>We’re here to make healthcare easier by helping clinics manage patient records and appointments without the hassle.</p>
		<p>Smart Clinic was born from a vision to bridge healthcare and technology, partnering with doctors and clinics to bring their services into the 		digital age.</p>
		<p>With a foundation built on collaboration and innovation, we offer a wide range of cutting-edge solutions designed to enhance patient care 			and streamline medical practices.</p>
		<p>Our goal is to empower healthcare providers by providing them with the tools they need to improve patient outcomes and optimize their daily 			operations.</p>
	</div>

	<div class="doctor"><img src="images\doctor.png"></div>

	<h4 id="services">Services we offer</h2>
	<div class="services">
		<img src="images\online consultation.webp">
		<img src="images\medical certificate.webp">
		<img src="images\clinics.webp">
		<img src="images\appointment.webp">
	</div>

	<h4 id="news">News</h4>
	
	<div class="news">
	
	<?php

	// Add news
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_news']) && $user_id) {
		$title = mysqli_real_escape_string($conn, $_POST['title']);
    		$content = mysqli_real_escape_string($conn, $_POST['content']);

    		$sql = "INSERT INTO news (title, content, posted_by) VALUES ('$title', '$content', '$user_id')";
    		mysqli_query($conn, $sql);
	}

	// Delete news
	if (isset($_GET['delete']) && $user_id) {
    		$news_id = intval($_GET['delete']);
    		$sql = "DELETE FROM news WHERE id = $news_id AND posted_by = $user_id";
    		mysqli_query($conn, $sql);
	}
	?>

	<!-- News Form -->
	<?php if ($user_id): ?>
    		<form method="POST" class="news-form">
        		<input type="text" name="title" placeholder="Enter News Title" required><br>
        		<textarea name="content" placeholder="Enter News Content" required></textarea>
        		<button type="submit" name="add_news">Post News</button>
    		</form>

	<?php else: ?>
    		<p>You must be logged in as a doctor to post news.</p>
	<?php endif; ?>

		<!-- News Display Section -->
	<div class="news">

    	<?php
    		$result = mysqli_query($conn, "SELECT * FROM news WHERE posted_by = $user_id ORDER BY posted_at DESC");

    		while ($row = mysqli_fetch_assoc($result)) {
        	echo "<div class='news-item'>";
		$formattedDate = date("m/d/Y", strtotime($row['posted_at']));
        	echo "<h3>" . htmlspecialchars($row['title'])."<br>"."<small>$formattedDate</small>". "</h3>";
        	echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
		echo "<a href='?delete=" . $row['id'] . "' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this news?');\">Delete</a>";
        	echo "<br><br><br><br></div>";
    		echo "<hr>"; // ← This draws the visible break

    	}
    		$stmt->close();
    		$conn->close();
    	?>
		</div>
	</div>


	<h4 id="clinics">Find Us</h4>
	<div class="search-container">
       		<input id="search-box" type="text" placeholder="Enter location..." oninput="fetchSuggestions()">
    	</div>
        <div id="suggestions"></div>
    	<div id="map"></div>

    	<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    	<script src="script.js"></script> 

	<div class="contact" id="contact">Contacts</div>
        <footer>&copy; 2025 Smart Clinic. All Rights Reserved.</footer>
</body>
</html>