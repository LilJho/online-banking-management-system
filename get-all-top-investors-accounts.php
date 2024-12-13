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

// Fetch the 10 highest savings investors
$query = "
    SELECT 
        u.first_name, 
        u.middle_name, 
        u.last_name, 
        u.gender, 
        u.email, 
        a.balance AS savings
    FROM 
        users u
    INNER JOIN 
        accounts a 
    ON 
        u.id = a.user_id
    WHERE 
        a.account_type = 'savings'
    ORDER BY 
        a.balance DESC
    LIMIT 10;
";

$result = $conn->query($query);

if ($result) {
    $topInvestors = [];
    while ($row = $result->fetch_assoc()) {
        $topInvestors[] = [
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'gender' => $row['gender'],
            'email' => $row['email'],
            'savings' => number_format((float)$row['savings'], 2), // Format balance to 2 decimal places
        ];
    }
    echo json_encode($topInvestors);
} else {
    echo json_encode(['error' => 'Error fetching data: ' . $conn->error]);
}

$conn->close();
?>
