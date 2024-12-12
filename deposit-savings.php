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
    $userId = intval($_POST['id']); 
    $depositAmount = intval($_POST['deposit-amount']); 
    $accountType = 'savings';
    $status = "active";

    // Validate required fields
    if (empty($userId) || empty($depositAmount)) {
        echo json_encode(['error' => 'User ID and Deposit Amount are required']);
        exit;
    }

    // Check if the user already has an account
    $getUser = $conn->prepare("SELECT user_id, balance FROM accounts WHERE user_id = ? AND account_type = ?");
    $getUser->bind_param("is", $userId, $accountType);
    $getUser->execute();
    $result = $getUser->get_result();

    if ($result->num_rows > 0) {
        // Update the balance if the account exists
        $user = $result->fetch_assoc();
        $newBalance = floatval($depositAmount) + floatval($user['balance']); // Add the deposit amount to the current balance

        $updateDeposit = $conn->prepare("UPDATE accounts SET balance = ? WHERE user_id = ? AND account_type = ?");
        if (!$updateDeposit) {
            echo json_encode(['error' => 'Error preparing update statement: ' . $conn->error]);
            exit;
        }

        $updateDeposit->bind_param("iis", $newBalance, $userId, $accountType);
        if ($updateDeposit->execute()) {
            echo json_encode([
                'message' => 'Balance updated successfully',
                'new_balance' => $newBalance,
                'id' => $userId,
            ]);
        } else {
            echo json_encode(['error' => 'Error updating balance: ' . $updateDeposit->error]);
        }

        $updateDeposit->close();
    } else {
        // Insert a new account if it does not exist
        $stmt = $conn->prepare("INSERT INTO accounts (user_id, account_type, balance, status) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            echo json_encode(['error' => 'Error preparing insert statement: ' . $conn->error]);
            exit;
        }

        $stmt->bind_param("isis", $userId, $accountType, $depositAmount, $status);
        if ($stmt->execute()) {
            echo json_encode([
                'message' => 'Account created and deposit added successfully',
                'user_id' => $userId,
                'balance' => $depositAmount,
            ]);
        } else {
            echo json_encode(['error' => 'Error inserting account: ' . $stmt->error]);
        }

        $stmt->close();
    }

    $getUser->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
