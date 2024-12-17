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
    $creditPayment = intval($_POST['credit-payment']); 
    $accountType = 'credit';
    $status = "active";

    // Validate required fields
    if (empty($userId) || empty($creditPayment)) {
        echo json_encode(['error' => 'User ID and Credit Payment Amount are required']);
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
        $newBalance = floatval($user['balance']) - floatval($creditPayment); // Minus the deposit amount to the current balance

        if ($newBalance < 0) {
            echo json_encode([
                'error' => 'Credit payment is too much.',
                'current_credit_to_pay' => $user['balance'],
                'requested_amount' => $creditPayment,
            ]);
            exit; // Stop further execution
        }

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
        
            echo json_encode([
                'message' => 'No existing loan account!',
                'user_id' => $userId,
                'balance' => $creditPayment,
            ]);
    }

    $getUser->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
