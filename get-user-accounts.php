<?php
header('Content-Type: application/json'); // Ensure the response is JSON
error_reporting(E_ALL); // Enable error reporting
ini_set('display_errors', 1); // Display errors

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

// Check if the user_id is provided via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['user_id']); // Get the user_id from the request

    if (!$userId) {
        echo json_encode(['error' => 'Invalid or missing user_id']);
        exit;
    }

    // SQL query to get sums of savings, loan, and credit for the given user_id
    $query = "
        SELECT 
            user_id,
            SUM(CASE WHEN account_type = 'savings' THEN balance ELSE 0 END) AS savings,
            SUM(CASE WHEN account_type = 'loan' THEN balance ELSE 0 END) AS loan,
            SUM(CASE WHEN account_type = 'credit' THEN balance ELSE 0 END) AS credit,
            MAX(CASE WHEN account_type = 'savings' THEN status ELSE NULL END) AS savings_status
        FROM 
            accounts
        WHERE 
            user_id = ?
        GROUP BY 
            user_id
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
        exit;
    }

    // Bind the user_id parameter
    $stmt->bind_param("i", $userId);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode([
            'user_id' => $user['user_id'],
            'savings' => floatval($user['savings']),
            'loan' => floatval($user['loan']),
            'credit' => floatval($user['credit']),
            'savingsStatus' => $user['savings_status'],
        ]);
    } else {
        // If no data found for the given user_id
        echo json_encode([
            'user_id' => $userId,
            'savings' => 0.0,
            'loan' => 0.0,
            'credit' => 0.0,
            'savingsStatus' => "no account yet",
        ]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
