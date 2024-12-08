<?php
header('Content-Type: application/json'); // Ensure the response is JSON
error_reporting(E_ALL); // Display errors for debugging
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

// Check if the request is POST and the file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['offer-image'])) {
    $uploads_dir = 'uploads/offers'; // Directory to save the uploaded files

    // Ensure the upload directory exists
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }

    $file = $_FILES['offer-image'];
    $imageTitle = htmlspecialchars($_POST['image-title']); // Sanitize the title

    // Get file details
    $fileName = basename($file['name']);
    $fileTmpName = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $fileExt; // Create a unique file name
    $uploadPath = $uploads_dir . '/' . $newFileName;

    // Validate file type (allow only images)
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileExt, $allowed)) {
        echo json_encode(['error' => 'Invalid file type']);
        exit;
    }

    // Move the file to the upload directory
    if (move_uploaded_file($fileTmpName, $uploadPath)) {
        $imageUrl = $uploadPath; // This is the saved file path

        $stmt = $conn->prepare("INSERT INTO offers (title, image_url, is_active) VALUES (?, ?, ?)");
        if (!$stmt) {
            echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
            exit;
        }

        $isActive = 1;

        // Bind parameters
        $stmt->bind_param("ssi", $imageTitle, $imageUrl, $isActive);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode([
                'imageUrl' => $imageUrl,
                'imageTitle' => $imageTitle,
            ]);
        } else {
            echo json_encode(['error' => 'Error inserting record: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['error' => 'Failed to upload the file']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
