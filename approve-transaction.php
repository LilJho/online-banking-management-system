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
    $accountId = intval($_POST['account_id']);
    $transactionId = intval($_POST['transaction_id']);
    $transactionType = $_POST['transaction_type'];
    $amount = floatval($_POST['amount']);
    $isAdmin = isset($_POST['isAdmin']) ? filter_var($_POST['isAdmin'], FILTER_VALIDATE_BOOLEAN) : false;

    if ($accountId <= 0 || $transactionId <= 0 || $amount <= 0) {
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    // Handle 'transfer' transaction type
    if ($transactionType === 'transfer') {
        $getTransaction = $conn->prepare("
            SELECT t.account_id AS sender_account_id, t.amount, t.destination_bank_id, a.user_id
            FROM transactions t
            JOIN accounts a ON t.account_id = a.account_id
            WHERE t.transaction_id = ? AND t.transaction_status = 'pending'
        ");
        $getTransaction->bind_param("i", $transactionId);
        $getTransaction->execute();
        $transactionResult = $getTransaction->get_result();

        if ($transactionResult->num_rows === 0) {
            echo json_encode(['error' => 'Transaction not found or already approved']);
            exit;
        }

        $transaction = $transactionResult->fetch_assoc();
        $senderAccountId = $transaction['sender_account_id'];
        $amount = floatval($transaction['amount']);
        $destinationBankId = $transaction['destination_bank_id'];

        // Check Sender's Balance
        $getSenderBalance = $conn->prepare("SELECT balance FROM accounts WHERE account_id = ?");
        $getSenderBalance->bind_param("i", $senderAccountId);
        $getSenderBalance->execute();
        $senderResult = $getSenderBalance->get_result();

        if ($senderResult->num_rows === 0) {
            echo json_encode(['error' => 'Sender account not found']);
            exit;
        }

        $sender = $senderResult->fetch_assoc();
        $senderBalance = floatval($sender['balance']);

        if ($senderBalance < $amount) {
            echo json_encode(['error' => 'Insufficient balance in sender account']);
            exit;
        }

        // Fetch Destination Account
        $getDestinationAccount = $conn->prepare("
            SELECT a.account_id, a.balance 
            FROM accounts a
            JOIN users u ON a.user_id = u.id
            WHERE u.bank_id_no = ? AND a.account_type = 'savings'
        ");
        $getDestinationAccount->bind_param("s", $destinationBankId);
        $getDestinationAccount->execute();
        $destinationResult = $getDestinationAccount->get_result();

        if ($destinationResult->num_rows === 0) {
            echo json_encode(['error' => 'Destination account not found']);
            exit;
        }

        $destination = $destinationResult->fetch_assoc();
        $destinationAccountId = $destination['account_id'];
        $destinationBalance = floatval($destination['balance']);

        // Start Transaction
        $conn->begin_transaction();
        try {
            // Deduct from Sender's Account
            $newSenderBalance = $senderBalance - $amount;
            $updateSender = $conn->prepare("UPDATE accounts SET balance = ? WHERE account_id = ?");
            $updateSender->bind_param("di", $newSenderBalance, $senderAccountId);
            $updateSender->execute();

            // Add to Destination Account
            $newDestinationBalance = $destinationBalance + $amount;
            $updateDestination = $conn->prepare("UPDATE accounts SET balance = ? WHERE account_id = ?");
            $updateDestination->bind_param("di", $newDestinationBalance, $destinationAccountId);
            $updateDestination->execute();

            // Update Transaction Status to Completed
            $updateTransaction = $conn->prepare("UPDATE transactions SET transaction_status = 'completed' WHERE transaction_id = ?");
            $updateTransaction->bind_param("i", $transactionId);
            $updateTransaction->execute();

            $conn->commit();

            echo json_encode([
                'message' => 'Transfer approved successfully',
                'sender_new_balance' => $newSenderBalance,
                'destination_new_balance' => $newDestinationBalance,
            ]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['error' => 'Approval failed: ' . $e->getMessage()]);
        }

        // Close statements for transfer
        $getTransaction->close();
        $getSenderBalance->close();
        $getDestinationAccount->close();
        $updateSender->close();
        $updateDestination->close();
        $updateTransaction->close();
    }
    // Handle Deposit, Withdraw, Loan, and Credit Transactions
    else {
        // Update the transaction status to 'completed'
        $updateTransactionQuery = "UPDATE transactions SET transaction_status = 'completed' WHERE transaction_id = ?";
        $stmt = $conn->prepare($updateTransactionQuery);
        $stmt->bind_param("i", $transactionId);
        if (!$stmt->execute()) {
            echo json_encode(['error' => 'Failed to update transaction status: ' . $stmt->error]);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();

        // Get the account balance and account type
        $getAccountQuery = "SELECT balance, account_type, user_id FROM accounts WHERE account_id = ?";
        $stmt = $conn->prepare($getAccountQuery);
        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(['error' => 'Account not found']);
            $stmt->close();
            $conn->close();
            exit;
        }

        $account = $result->fetch_assoc();
        $currentBalance = floatval($account['balance']);
        $accountType = $account['account_type'];
        $stmt->close();

        // Calculate the new balance based on transaction type
        if ($transactionType === 'deposit') {
            $newBalance = $currentBalance + $amount;

            // Award points based on deposit amount
            awardPoints($conn, $account['user_id'], $amount);
        } elseif ($transactionType === 'withdraw') {
            $newBalance = $currentBalance - $amount;
            if ($newBalance < 0) {
                echo json_encode(['error' => 'Insufficient balance']);
                $conn->close();
                exit;
            }
        } elseif ($transactionType === 'loan' && $accountType === 'loan') {
            // Loan transaction: complete the transaction and add the amount to the loan account
            $newBalance = $currentBalance + $amount;
        } elseif ($transactionType === 'credit' && $accountType === 'credit') {
            // Credit transaction: complete the transaction and add the amount to the credit account
            $newBalance = $currentBalance + $amount;
        } else {
            echo json_encode(['error' => 'Invalid transaction type']);
            $conn->close();
            exit;
        }

        // Update the account balance
        $updateBalanceQuery = "UPDATE accounts SET balance = ? WHERE account_id = ?";
        $stmt = $conn->prepare($updateBalanceQuery);
        $stmt->bind_param("di", $newBalance, $accountId);
        if (!$stmt->execute()) {
            echo json_encode(['error' => 'Failed to update account balance: ' . $stmt->error]);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();

        echo json_encode([
            'message' => 'Transaction approved and balance updated successfully.',
            'new_balance' => $newBalance,
        ]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();

// Function to calculate and add points based on deposit amount
function awardPoints($conn, $userId, $depositAmount) {
    // Define points based on deposit amount
    $points = 0;
    if ($depositAmount >= 100000) {
        $points = 50;
    } elseif ($depositAmount >= 50000) {
        $points = 20;
    } elseif ($depositAmount >= 30000) {
        $points = 10;
    } elseif ($depositAmount >= 10000) {
        $points = 5;
    } elseif ($depositAmount >= 5000) {
        $points = 2;
    } elseif ($depositAmount >= 1000) {
        $points = 1;
    }

    // Check if the user already has points
    $getPoints = $conn->prepare("SELECT points FROM points WHERE user_id = ?");
    $getPoints->bind_param("i", $userId);
    $getPoints->execute();
    $pointsResult = $getPoints->get_result();

    if ($pointsResult->num_rows > 0) {
        // User already has points, update them
        $existingPoints = $pointsResult->fetch_assoc()['points'];
        $newPoints = $existingPoints + $points;

        $updatePoints = $conn->prepare("UPDATE points SET points = ? WHERE user_id = ?");
        $updatePoints->bind_param("ii", $newPoints, $userId);
        $updatePoints->execute();
        $updatePoints->close();
    } else {
        // Insert points if not already exists
        $insertPoints = $conn->prepare("INSERT INTO points (user_id, points) VALUES (?, ?)");
        $insertPoints->bind_param("ii", $userId, $points);
        $insertPoints->execute();
        $insertPoints->close();
    }

    $getPoints->close();
}
