<?php
// Database configuration
$dbConfig = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => 'Password@29263',
    'database' => 'online_bank_db',
];

function getDbConnection($config)
{
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    return $conn;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get token from the query string
        $token = $_GET['token'] ?? null;

        if (!$token) {
            throw new Exception('Missing reset token.');
        }

        // Create a database connection
        $conn = getDbConnection($dbConfig);

        // Validate the token and check expiry
        $query = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
        $query->bind_param("s", $token);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows === 0) {
            throw new Exception('Invalid or expired reset token.');
        }

        echo "<form method='POST'>
            <input type='hidden' name='token' value='$token'>
            <label>New Password</label>
            <input type='password' name='password' required>
            <button type='submit'>Reset Password</button>
        </form>";
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle password reset
        $token = $_POST['token'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$token || !$password) {
            throw new Exception('Invalid input.');
        }

        // Create a database connection
        $conn = getDbConnection($dbConfig);

        // Update the user's password and clear the reset token
        $query = $conn->prepare("UPDATE users SET pass = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
        $query->bind_param("ss", $password, $token);
        if (!$query->execute()) {
            throw new Exception('Error updating password.');
        }

        echo "<h1>Password Reset Successfully!</h1>";
        echo "<p>Your password has been updated. You can now log in with your new password.</p>";
        // echo "<a href="/authentication.php">Login</a>";
        header("refresh:3;url=authentication.php");
    }
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
