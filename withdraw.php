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
    $withdrawAmount = floatval($_POST['withdraw-amount']); 
    $accountType = 'savings';

    // Validate required fields
    if (empty($userId) || empty($withdrawAmount) || $withdrawAmount <= 0) {
        echo json_encode(['error' => 'Invalid User ID or Withdrawal Amount']);
        exit;
    }

    // Check if the user already has an account
    $getUser = $conn->prepare("SELECT account_id, balance FROM accounts WHERE user_id = ? AND account_type = ?");
    $getUser->bind_param("is", $userId, $accountType);
    $getUser->execute();
    $result = $getUser->get_result();

    $transactionStatus = 'completed'; // Default transaction status for a successful withdrawal
    $transactionDate = date('Y-m-d H:i:s'); // Current timestamp

    if ($result->num_rows > 0) {
        // Update the balance if the account exists
        $account = $result->fetch_assoc();
        $accountId = $account['account_id']; // Get account_id from the accounts table
        $currentBalance = floatval($account['balance']);

        if ($currentBalance < $withdrawAmount) {
            echo json_encode([
                'error' => 'Insufficient balance. The balance is not enough for the requested amount.',
                'current_balance' => $currentBalance,
                'requested_amount' => $withdrawAmount,
            ]);
            exit; // Stop further execution
        }

        $newBalance = $currentBalance - $withdrawAmount;

        $updateBalance = $conn->prepare("UPDATE accounts SET balance = ? WHERE account_id = ?");
        if (!$updateBalance) {
            echo json_encode(['error' => 'Error preparing update statement: ' . $conn->error]);
            exit;
        }

        $updateBalance->bind_param("di", $newBalance, $accountId);
        if ($updateBalance->execute()) {
            // Log the transaction
            $logTransaction = $conn->prepare(
                "INSERT INTO transactions (account_id, transaction_type, amount, transaction_date, transaction_status) 
                 VALUES (?, 'withdrawal', ?, ?, ?)"
            );
            if ($logTransaction) {
                $logTransaction->bind_param("idss", $accountId, $withdrawAmount, $transactionDate, $transactionStatus);
                $logTransaction->execute();
                $logTransaction->close();
            }

            echo json_encode([
                'message' => 'Withdrawal successful',
                'new_balance' => $newBalance,
                'account_id' => $accountId,
            ]);
        } else {
            echo json_encode(['error' => 'Error updating balance: ' . $updateBalance->error]);
        }

        $updateBalance->close();
    } else {
        echo json_encode([
            'error' => 'No existing savings account found for this user',
            'user_id' => $userId,
        ]);
    }

    $getUser->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
    