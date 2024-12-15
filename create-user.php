<?php
header('Content-Type: application/json'); // Ensure the response is JSON
error_reporting(E_ALL); // Enable all error reporting
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

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName']; 
    $middleName = $_POST['middleName']; 
    $lastName = $_POST['lastName']; 
    $address = $_POST['address']; 
    $gender = $_POST['gender']; 
    // $account = $_POST['account']; 
    // $balance = $_POST['balance']; 
    $birthDate = $_POST['birthDate'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $isAdmin = 0;
    $is_verified = 0;

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email)) {
        echo json_encode(['error' => 'First Name, Last Name, and Email are required']);
        exit;
    }

    // Prepare and execute the SQL query for inserting user
    $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, address, gender, birth_date, phone_number, email, pass, isAdmin, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("sssssssssii", $firstName, $middleName, $lastName, $address, $gender, $birthDate, $phoneNumber, $email, $password, $isAdmin, $is_verified);

    if ($stmt->execute()) {
        // Get the auto-generated user_id
        $user_id = $conn->insert_id;
        // $status = "active"; // Fixed missing semicolon
        // $convertedBalance = number_format((float)$balance, 2, '.', '');

        // // Prepare and execute the SQL query for inserting account details
        // $accountstmt = $conn->prepare("INSERT INTO accounts (user_id, account_type, balance, status) VALUES (?, ?, ?, ?)");
        // if (!$accountstmt) {
        //     echo json_encode(['error' => 'Error preparing account statement: ' . $conn->error]);
        //     exit;
        // }

        // Bind parameters for account insertion
        // $accountstmt->bind_param("ssis", $user_id, $account, $convertedBalance, $status);

        echo json_encode([
            'user_id' => $user_id, // Return the generated user ID
            'firstName' => $firstName,
            'message' => 'User and account created successfully.'
        ]);

        // if ($accountstmt->execute()) {
            
        // } else {
        //     echo json_encode(['error' => 'Error inserting account record: ' . $accountstmt->error]);
        // }

        // $accountstmt->close();
    } else {
        echo json_encode(['error' => 'Error inserting user record: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();

?>
