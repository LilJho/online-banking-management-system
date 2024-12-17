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
$sql = "SELECT id, first_name, middle_name, last_name, gender, email, address, phone_number, birth_date, is_verified, is_blocked, is_archived, bank_id_no 
        FROM users 
        WHERE isAdmin = 0 AND is_archived = 0";
$result = $conn->query($sql);

if ($result) {
    $users = [];

    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    // Return the offers as JSON
    echo json_encode($users);
} else {
    echo json_encode(['error' => 'Failed to fetch users: ' . $conn->error]);
}

$conn->close();
?>
