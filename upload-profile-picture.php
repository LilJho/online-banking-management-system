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

// Check if the file and user ID are received
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK && isset($_POST['user_id'])) {
    $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
    $fileName = $_FILES['profile_picture']['name'];
    $fileSize = $_FILES['profile_picture']['size'];
    $fileType = $_FILES['profile_picture']['type'];

    // Define the upload directory
    $uploadDir = __DIR__ . '/uploads/profile-pictures/';
    // Create the directory if it does not exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Set the new file path (you can modify this logic to prevent overwriting)
    $newFilePath = $uploadDir . uniqid() . '-' . basename($fileName);

    // Move the uploaded file to the desired location
    if (move_uploaded_file($fileTmpPath, $newFilePath)) {
        // Image URL relative to the server
        $imageUrl = '/uploads/profile-pictures/' . basename($newFilePath);

        // Get the user ID passed from the frontend (through FormData)
        $userId = $_POST['user_id'];  // This will get the user_id from the FormData

        // Update the user's image URL in the database
        $stmt = $conn->prepare("UPDATE users SET img_url = ? WHERE id = ?");
        $stmt->bind_param("si", $imageUrl, $userId);

        if ($stmt->execute()) {
            // Respond with success
            echo json_encode(['success' => true, 'image_url' => $imageUrl]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user image URL.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'File upload failed.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or missing user ID.']);
}

$conn->close();
?>
