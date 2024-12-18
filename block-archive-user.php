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


$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['userId'];
$action = $data['action'];

// Ensure the action is either 'block' or 'archive'
if (!in_array($action, ['block', 'archive', 'unblock'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

// Set the status based on the action
$status = ($action == 'block') ? 'blocked' : 'archived';
$value = ($action == 'unblock') ? 0 : 1;
$loginAttempts = ($action == 'unblock') ? 0 : 3;

// Prepare the SQL query based on the action
if ($action == 'block' || $action == 'unblock') {
    // Handle block/unblock actions
    $query = "UPDATE users SET is_blocked = ?, login_attempts = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $value, $loginAttempts, $userId);
} elseif ($action == 'archive') {
    // Handle archive action
    $query = "UPDATE users SET is_archived = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $value, $userId);
} else {
    // Invalid action
    echo json_encode(["success" => false, "message" => "Invalid action"]);
    exit;
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => ucfirst($action) . ' action successful']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed']);
}

$stmt->close();
$conn->close();
?>