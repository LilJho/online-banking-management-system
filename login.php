<?php
// Set the content type to JSON
header('Content-Type: application/json');

// Database connection
$host = "localhost";
$username = "root";
$password = "Password@29263";
$database = "online_bank_db";

// Create a new database connection
$conn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Get POST data
$email = $_POST['login_email'] ?? null;
$password = $_POST['login_pass'] ?? null;

// Validate input
if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "Email and password are required"]);
    exit;
}

// Prepare SQL statement to fetch user details
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, pass, is_verified, is_blocked, login_attempts, isAdmin FROM users WHERE email = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error preparing statement"]);
    exit;
}

// Bind email parameter
$stmt->bind_param("s", $email);

// Execute the query
$stmt->execute();

// Fetch the result
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No account found with this email"]);
    exit;
}

// Get user data
$row = $result->fetch_assoc();
$storedPassword = $row['pass'];
$isBlocked = $row['is_blocked'];
$loginAttempts = $row['login_attempts'];

// Check if the account is blocked
if ($isBlocked) {
    echo json_encode(["success" => false, "message" => "Your account is blocked. Please contact support."]);
    exit;
}

// Verify the password
if ($password === $storedPassword) {
    // Reset login attempts on successful login
    $resetStmt = $conn->prepare("UPDATE users SET login_attempts = 0 WHERE email = ?");
    $resetStmt->bind_param("s", $email);
    $resetStmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Login successful!",
        "user" => $row
    ]);
} else {
    // Increment login attempts
    $loginAttempts++;
    $updateStmt = $conn->prepare("UPDATE users SET login_attempts = ? WHERE email = ?");
    $updateStmt->bind_param("is", $loginAttempts, $email);
    $updateStmt->execute();

    // Block account if attempts reach 3
    if ($loginAttempts >= 3) {
        $blockStmt = $conn->prepare("UPDATE users SET is_blocked = 1 WHERE email = ?");
        $blockStmt->bind_param("s", $email);
        $blockStmt->execute();

        echo json_encode(["success" => false, "message" => "Your account has been blocked due to too many failed login attempts."]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid email or password. Attempts left: " . (3 - $loginAttempts)]);
    }
}

// Close connections
$stmt->close();
$conn->close();
?>
