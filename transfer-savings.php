<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.log');

$host = "localhost";
$username = "root";
$password = "Password@29263";
$database = "online_bank_db";

// Create a new database connection
try {
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sourceAccountId = intval($_POST['id']);       // Source account ID (from accounts table)
    $destinationBankId = $_POST['bank_id_no'];     // Destination bank_id_no (from users table)
    $transferAmount = floatval($_POST['transfer-amount']);
    $accountType = 'savings';

    if (empty($sourceAccountId) || empty($destinationBankId) || empty($transferAmount)) {
        echo json_encode(['error' => 'Source Account ID, Destination Bank ID, and Transfer Amount are required']);
        exit;
    }

    // Check the Source Account's Balance
    $getSourceAccount = $conn->prepare("SELECT account_id, balance FROM accounts WHERE user_id = ? AND account_type = ?");
    $getSourceAccount->bind_param("is", $sourceAccountId, $accountType);
    $getSourceAccount->execute();
    $sourceResult = $getSourceAccount->get_result();

    if ($sourceResult->num_rows === 0) {
        echo json_encode(['error' => 'Source account does not exist']);
        exit;
    }
    $sourceAccount = $sourceResult->fetch_assoc();

    if ($sourceAccount['balance'] < $transferAmount) {
        echo json_encode(['error' => 'Insufficient balance']);
        exit;
    }

    // Find Destination Account via Users Table
    $getDestinationAccount = $conn->prepare("
        SELECT a.account_id, a.balance 
        FROM users u 
        JOIN accounts a ON u.id = a.user_id 
        WHERE u.bank_id_no = ? AND a.account_type = ?
    ");
    $getDestinationAccount->bind_param("ss", $destinationBankId, $accountType);
    $getDestinationAccount->execute();
    $destinationResult = $getDestinationAccount->get_result();

    if ($destinationResult->num_rows === 0) {
        echo json_encode(['error' => 'Destination account does not exist']);
        exit;
    }
    $destinationAccount = $destinationResult->fetch_assoc();

    // Start Transaction
    $conn->begin_transaction();
    try {
        // Deduct from Source Account
        $newSourceBalance = $sourceAccount['balance'] - $transferAmount;
        $updateSource = $conn->prepare("UPDATE accounts SET balance = ? WHERE account_id = ?");
        $updateSource->bind_param("di", $newSourceBalance, $sourceAccount['account_id']);
        $updateSource->execute();

        // Add to Destination Account
        $newDestinationBalance = $destinationAccount['balance'] + $transferAmount;
        $updateDestination = $conn->prepare("UPDATE accounts SET balance = ? WHERE account_id = ?");
        $updateDestination->bind_param("di", $newDestinationBalance, $destinationAccount['account_id']);
        $updateDestination->execute();

        // Log Source Transaction
        $logSourceTransaction = $conn->prepare("
            INSERT INTO transactions (account_id, transaction_type, amount, transaction_date, transaction_status)
            VALUES (?, 'transfer', ?, NOW(), 'completed')
        ");
        $logSourceTransaction->bind_param("id", $sourceAccount['account_id'], $transferAmount);
        $logSourceTransaction->execute();

        // Log Destination Transaction
        $logDestinationTransaction = $conn->prepare("
            INSERT INTO transactions (account_id, transaction_type, amount, transaction_date, transaction_status)
            VALUES (?, 'transfer', ?, NOW(), 'completed')
        ");
        $logDestinationTransaction->bind_param("id", $destinationAccount['account_id'], $transferAmount);
        $logDestinationTransaction->execute();

        $conn->commit();

        echo json_encode([
            'message' => 'Transfer successful',
            'source_new_balance' => $newSourceBalance,
            'destination_new_balance' => $newDestinationBalance,
            'transfer_amount' => $transferAmount
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['error' => 'Transfer failed: ' . $e->getMessage()]);
    }

    // Close statements
    $getSourceAccount->close();
    $getDestinationAccount->close();
    $updateSource->close();
    $updateDestination->close();
    $logSourceTransaction->close();
    $logDestinationTransaction->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
