<?php
// Set the content type to JSON
header('Content-Type: application/json');

// Include database connection (you can include your DB connection logic here)
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
$stmt = $conn->prepare("SELECT id, first_name, middle_name, last_name, email, pass FROM users WHERE email = ?");
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

// Get the stored password hash
$row = $result->fetch_assoc();
$storedPassword = $row['pass'];

// Verify the password
if ($password === $storedPassword) {
    echo json_encode([
        "success" => true,
        "message" => "Login successful!",
        "user" => $row // Send user data as part of the response
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid email or password"]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
