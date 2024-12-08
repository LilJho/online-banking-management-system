<?php
header('Content-Type: application/json'); // Ensure the response is JSON
error_reporting(E_ALL); // Display errors for debugging during development
ini_set('display_errors', 1); // Enable error display

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

// Fetch all offers
$sql = "SELECT id, title, image_url, is_active, created_at FROM offers";
$result = $conn->query($sql);

if ($result) {
    $offers = [];

    while ($row = $result->fetch_assoc()) {
        $offers[] = $row;
    }

    // Return the offers as JSON
    echo json_encode($offers);
} else {
    echo json_encode(['error' => 'Failed to fetch offers: ' . $conn->error]);
}

$conn->close();
?>
