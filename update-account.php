<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "Password@29263";
$database = "online_bank_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set header for JSON response
header('Content-Type: application/json');

// Get the raw POST data (for JSON)
$inputData = json_decode(file_get_contents('php://input'), true);

// Check if data is received properly
if ($inputData && isset($inputData['firstName'])) {
    $first_name = $conn->real_escape_string($inputData['firstName']);
    $middle_name = $conn->real_escape_string($inputData['middleName']);
    $last_name = $conn->real_escape_string($inputData['lastName']);
    $birth_date = $conn->real_escape_string($inputData['birthDate']);
    $phone_number = $conn->real_escape_string($inputData['phoneNumber']);
    $gender = $conn->real_escape_string($inputData['gender']);
    $address = $conn->real_escape_string($inputData['address']);
    $user_id = $conn->real_escape_string($inputData['userId']);
    $email = $conn->real_escape_string($inputData['email']);

    // Use prepared statements to avoid SQL injection
    $updateQuery = $conn->prepare("UPDATE users SET first_name = ?, middle_name = ?, last_name = ?, birth_date = ?, phone_number = ?, gender = ?, address = ?, email = ? WHERE id = ?");
    $updateQuery->bind_param("ssssssssi", $first_name, $middle_name, $last_name, $birth_date, $phone_number, $gender, $address, $email, $user_id);

    if ($updateQuery->execute()) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating user: ' . $conn->error]);
    }

    $updateQuery->close();
} else {
    // Return error if the input data is missing or invalid
    echo json_encode(['success' => false, 'message' => 'Invalid data received']);
}

$conn->close();
?>
