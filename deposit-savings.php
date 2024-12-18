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
    $depositAmount = floatval($_POST['deposit-amount']); 
    $accountType = 'savings';
    $status = "active";

    // Validate required fields
    if ($userId <= 0 || $depositAmount <= 0) {
        echo json_encode(['error' => 'Invalid User ID or Deposit Amount']);
        exit;
    }

    // Check if the user already has an account
    $getUser = $conn->prepare("SELECT account_id, balance FROM accounts WHERE user_id = ? AND account_type = ?");
    $getUser->bind_param("is", $userId, $accountType);
    $getUser->execute();
    $result = $getUser->get_result();

    $transactionStatus = 'completed'; // Default transaction status for a successful deposit
    $transactionDate = date('Y-m-d H:i:s'); // Current timestamp

    if ($result->num_rows > 0) {
        // Update the balance if the account exists
        $account = $result->fetch_assoc();
        $accountId = $account['account_id']; // Get account_id from the accounts table
        $newBalance = $depositAmount + floatval($account['balance']); // Add the deposit amount to the current balance

        $updateDeposit = $conn->prepare("UPDATE accounts SET balance = ? WHERE account_id = ?");
        if (!$updateDeposit) {
            echo json_encode(['error' => 'Error preparing update statement: ' . $conn->error]);
            exit;
        }

        $updateDeposit->bind_param("di", $newBalance, $accountId); // Ensure data type matches
        if ($updateDeposit->execute()) {
            // Log the transaction
            $logTransaction = $conn->prepare(
                "INSERT INTO transactions (account_id, transaction_type, amount, transaction_date, transaction_status) 
                 VALUES (?, 'deposit', ?, ?, ?)"
            );
            if ($logTransaction) {
                $logTransaction->bind_param("idss", $accountId, $depositAmount, $transactionDate, $transactionStatus);
                $logTransaction->execute();
                $logTransaction->close();
            }

            echo json_encode([
                'message' => 'Balance updated successfully',
                'new_balance' => $newBalance,
                'account_id' => $accountId,
            ]);
        } else {
            echo json_encode(['error' => 'Error updating balance: ' . $updateDeposit->error]);
        }

        $updateDeposit->close();
    } else {
        // Insert a new account if none exists
        $stmt = $conn->prepare("INSERT INTO accounts (user_id, account_type, balance, status) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            echo json_encode(['error' => 'Error preparing insert statement: ' . $conn->error]);
            exit;
        }

        $stmt->bind_param("isss", $userId, $accountType, $depositAmount, $status);
        if ($stmt->execute()) {
            $accountId = $conn->insert_id; // Get the newly inserted account_id

            // Log the transaction
            $logTransaction = $conn->prepare(
                "INSERT INTO transactions (account_id, transaction_type, amount, transaction_date, transaction_status) 
                 VALUES (?, 'deposit', ?, ?, ?)"
            );
            if ($logTransaction) {
                $logTransaction->bind_param("idss", $accountId, $depositAmount, $transactionDate, $transactionStatus);
                $logTransaction->execute();
                $logTransaction->close();
            }

            echo json_encode([
                'message' => 'Account created and deposit added successfully',
                'user_id' => $userId,
                'account_id' => $accountId,
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
