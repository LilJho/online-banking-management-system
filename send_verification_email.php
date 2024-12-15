<?php
// Load PHPMailer via Composer's autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set response headers for JSON output
header('Content-Type: application/json');

// Enable detailed error reporting (use only in development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$dbConfig = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => 'Password@29263',
    'database' => 'online_bank_db',
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

// Function to update the user's token in the database
function updateUserToken($conn, $userId, $token)
{
    $query = $conn->prepare("UPDATE users SET token = ? WHERE id = ?");
    $query->bind_param("ss", $token, $userId);

    if (!$query->execute()) {
        throw new Exception('Error updating user token: ' . $conn->error);
    }

    $query->close();
}

// Function to send the verification email
function sendVerificationEmail($email, $verificationLink)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jhonnelg25@gmail.com'; // Replace with your email
        $mail->Password = 'ndei dola xrgy cncq'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email Content
        $mail->setFrom('jhonnelg25@gmail.com', 'E-Bank'); // Sender
        $mail->addAddress($email); // Recipient

        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email';
        $mail->Body = "
            <h1>Email Verification</h1>
            <p>Click the link below to verify your email address:</p>
            <a href='$verificationLink'>Verify Email</a>
            <p>If you did not request this, you can safely ignore this email.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
    }
}

// Main script
try {
    // Ensure the request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    // Get POST data
    $email = $_POST['email'] ?? null;
    $userId = $_POST['user_id'] ?? null;

    // Validate input
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address.');
    }
    if (!$userId) {
        throw new Exception('User ID is required.');
    }

    // Generate a unique verification token
    $verificationToken = bin2hex(random_bytes(16));
    $verificationLink = "http://localhost:8000/verify_email.php?token=$verificationToken";

    // Create a database connection
    $conn = getDbConnection($dbConfig);

    // Update the user's token
    updateUserToken($conn, $userId, $verificationToken);

    // Send the verification email
    sendVerificationEmail($email, $verificationLink);

    // Success response
    echo json_encode(['status' => 'success', 'message' => 'Verification email sent successfully.']);
} catch (Exception $e) {
    // Error response
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    // Close the database connection if it was opened
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
