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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new-offer-image']) && isset($_POST['id'])) {
    $uploads_dir = 'uploads/offers'; // Directory to save the uploaded files

    // Ensure the upload directory exists
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0755, true);
    }

    $file = $_FILES['new-offer-image'];
    $imageTitle = htmlspecialchars($_POST['new-image-title']); // Sanitize the title
    $offerId = intval($_POST['id']); // Get the ID of the offer to update

    // Validate ID
    if ($offerId <= 0) {
        echo json_encode(['error' => 'Invalid offer ID']);
        exit;
    }

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

    // Validate file size (5 MB limit)
    $maxFileSize = 5 * 1024 * 1024; // 5 MB
    if ($file['size'] > $maxFileSize) {
        echo json_encode(['error' => 'File size exceeds the 5MB limit']);
        exit;
    }

    // Move the file to the upload directory
    if (move_uploaded_file($fileTmpName, $uploadPath)) {
        $imageUrl = $uploads_dir . '/' . $newFileName; // Use relative path

        // Prepare the SQL statement for updating
        $stmt = $conn->prepare("UPDATE offers SET title = ?, image_url = ? WHERE id = ?");
        if (!$stmt) {
            error_log('Error preparing statement: ' . $conn->error);
            echo json_encode(['error' => 'Server error']);
            exit;
        }

        // Bind parameters
        $stmt->bind_param("ssi", $imageTitle, $imageUrl, $offerId);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode([
                'message' => 'Offer updated successfully',
                'imageUrl' => $imageUrl,
                'imageTitle' => $imageTitle,
            ]);
        } else {
            error_log('Error updating record: ' . $stmt->error);
            echo json_encode(['error' => 'Error updating record']);
        }
    } else {
        echo json_encode(['error' => 'Failed to upload the file']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

?>
