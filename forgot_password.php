<?php
// Load PHPMailer via Composer's autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database configuration
$dbConfig = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => 'Password@29263', // Replace with actual password
    'database' => 'online_bank_db', // Replace with your database name
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

// Function to send a reset password email
function sendResetPasswordEmail($email, $resetLink)
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
        $mail->Subject = 'Password Reset Request';
        $mail->Body = "
            <h1>Reset Your Password</h1>
            <p>Click the link below to reset your password:</p>
            <a href='$resetLink'>Reset Password</a>
            <p>If you did not request a password reset, you can safely ignore this email.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
    }
}

try {
    // Ensure the request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    // Get the user's email from the POST request
    $email = $_POST['email'] ?? null;

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address.');
    }

    // Create a database connection
    $conn = getDbConnection($dbConfig);

    // Check if the email exists in the database
    $query = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('No account found with this email.');
    }

    $user = $result->fetch_assoc();
    $userId = $user['id'];

    // Generate a unique reset token
    $resetToken = bin2hex(random_bytes(16));
    $resetLink = "http://localhost:8000/reset_password.php?token=$resetToken";

    // Save the reset token in the database
    $updateQuery = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?");
    $updateQuery->bind_param("ss", $resetToken, $userId);
    if (!$updateQuery->execute()) {
        throw new Exception('Error saving reset token.');
    }

    // Send the reset email
    sendResetPasswordEmail($email, $resetLink);

    echo json_encode(['status' => 'success', 'message' => 'Password reset email sent.']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
