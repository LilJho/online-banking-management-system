<?php
header('Content-Type: application/json'); // Ensure the response is JSON
error_reporting(E_ALL); // Enable all error reporting
ini_set('display_errors', 1); // Display errors

$host = "localhost";
$username = "root";
$password = "Password@29263";
$database = "online_bank_db";

// Create a new database connection
$conn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title'])); // Sanitize title input
    $description = htmlspecialchars(trim($_POST['description'])); // Sanitize description input

    // Validate inputs
    if (empty($title) || empty($description)) {
        echo json_encode(['error' => 'Title and description are required']);
        exit;
    }

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("INSERT INTO announcements (title, description, is_active) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
        exit;
    }

    $isActive = 1; // Default active state

    $stmt->bind_param("ssi", $title, $description, $isActive);

    if ($stmt->execute()) {
        echo json_encode([
            'announcementTitle' => $title,
            'announcementDescription' => $description,
        ]);
    } else {
        echo json_encode(['error' => 'Error inserting record: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
