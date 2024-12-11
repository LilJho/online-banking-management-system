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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new-title']) && isset($_POST['id'])) {

    $newTitle = htmlspecialchars($_POST['new-title']); // Sanitize the title
    $newDescription = htmlspecialchars($_POST['new-description']); // Sanitize the title
    $announcementId = intval($_POST['id']); // Get the ID of the offer to update

    // Validate ID
    if ($announcementId <= 0) {
        echo json_encode(['error' => 'Invalid offer ID']);
        exit;
    }

    // Move the file to the upload directory
    if ($announcementId && $newDescription && $newTitle) {

        // Prepare the SQL statement for updating
        $stmt = $conn->prepare("UPDATE announcements SET title = ?, description = ? WHERE id = ?");
        if (!$stmt) {
            error_log('Error preparing statement: ' . $conn->error);
            echo json_encode(['error' => 'Server error']);
            exit;
        }

        // Bind parameters
        $stmt->bind_param("ssi", $newTitle, $newDescription, $announcementId);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode([
                'message' => 'Announcement updated successfully',
                'announcementTitle' => $newTitle,
                'announcementDescription' => $newDescription,
            ]);
        } else {
            error_log('Error updating record: ' . $stmt->error);
            echo json_encode(['error' => 'Error updating record']);
        }
    } else {
        echo json_encode(['error' => 'Failed to update the announcement']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

?>
