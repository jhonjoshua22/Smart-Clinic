<?php
header('Content-Type: application/json');
include 'db.php';

if (isset($_GET['query'])) {
    $search = "%" . $_GET['query'] . "%";
    $stmt = $conn->prepare("SELECT full_name, specialization, clinic FROM doctors WHERE full_name LIKE ? OR specialization LIKE ? OR clinic LIKE ?");
    $stmt->bind_param("sss", $search, $search, $search);

    if (!$stmt->execute()) {
        echo json_encode(['error' => $stmt->error]);
        exit;
    }

    $result = $stmt->get_result();
    $doctors = [];

    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }

    echo json_encode($doctors);
} else {
    echo json_encode(['error' => 'No query provided']);
}
