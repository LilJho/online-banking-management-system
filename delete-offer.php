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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the offerId is provided
    if (isset($_POST['id'])) {
        $offerId = intval($_POST['id']); // Get the offerId and cast it to an integer

        // Validate the offerId
        if ($offerId <= 0) {
            echo json_encode(['error' => 'Invalid offer ID']);
            exit;
        }

        // Prepare the SQL statement for deletion
        $stmt = $conn->prepare("DELETE FROM offers WHERE id = ?");
        if (!$stmt) {
            error_log('Error preparing statement: ' . $conn->error);
            echo json_encode(['error' => 'Server error']);
            exit;
        }

        // Bind the offerId parameter
        $stmt->bind_param("i", $offerId);

        // Execute the statement
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['message' => 'Offer deleted successfully']);
            } else {
                echo json_encode(['error' => 'No offer found with the given ID']);
            }
        } else {
            error_log('Error executing statement: ' . $stmt->error);
            echo json_encode(['error' => 'Error deleting the offer']);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Missing offer ID']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

// Close the database connection
$conn->close();
?>