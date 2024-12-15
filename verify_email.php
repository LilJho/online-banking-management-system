<?php
// Load PHPMailer via Composer's autoloader
require 'vendor/autoload.php';

// Database configuration
$dbConfig = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => 'Password@29263', // Update this to your actual password
    'database' => 'online_bank_db', // Update to your actual database name
];

// Function to create a database connection
function getDbConnection($config)
{
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    return $conn;
}

// Main script
try {
    // Ensure the request method is GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Invalid request method. Please use a valid verification link.');
    }

    // Get the token from the query string
    $token = $_GET['token'] ?? null;

    if (!$token) {
        throw new Exception('Missing verification token.');
    }

    // Create a database connection
    $conn = getDbConnection($dbConfig);

    // Check if the token exists in the database
    $query = $conn->prepare("SELECT id FROM users WHERE token = ?");
    $query->bind_param("s", $token);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Invalid or expired verification token.');
    }

    // Mark the user as verified
    $user = $result->fetch_assoc();
    $userId = $user['id'];

    $updateQuery = $conn->prepare("UPDATE users SET token = NULL, is_verified = 1 WHERE id = ?");
    $updateQuery->bind_param("s", $userId);

    if (!$updateQuery->execute()) {
        throw new Exception('Error updating verification status: ' . $conn->error);
    }

    // Success response
    echo "<h1>Email Verified Successfully!</h1>";
    echo "<p>Your email has been successfully verified. You can now log in to your account.</p>";
} catch (Exception $e) {
    // Error response
    echo "<h1>Error Verifying Email</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
} finally {
    // Close the database connection if it was opened
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
