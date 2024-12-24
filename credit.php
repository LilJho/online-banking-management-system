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
    $amount = floatval($_POST['credit-amount']); 
    $isAdmin = isset($_POST['isAdmin']) ? filter_var($_POST['isAdmin'], FILTER_VALIDATE_BOOLEAN) : false;
    $accountType = 'credit'; // Handle credit account type

    // Validate required fields
    if (empty($userId) || empty($amount)) {
        echo json_encode(['error' => 'User ID and Credit Amount are required']);
        exit;
    }

    // Get credit account by user ID (if it exists)
    $getCreditAccount = $conn->prepare("SELECT account_id, balance FROM accounts WHERE user_id = ? AND account_type = ?");
    $getCreditAccount->bind_param("is", $userId, $accountType);
    $getCreditAccount->execute();
    $creditResult = $getCreditAccount->get_result();

    // Check if credit account exists
    if ($creditResult->num_rows > 0) {
        // Credit account exists, fetch the account_id
        $creditAccount = $creditResult->fetch_assoc();
        $accountId = $creditAccount['account_id'];
        $existingBalance = floatval($creditAccount['balance']);
    } else {
        // Credit account does not exist, create it with 0 balance
        $accountId = null; // Initially no account created
    }

    // Non-admin: only create a pending transaction, no credit account updates
    if (!$isAdmin) {
        if ($accountId === null) {
            // Create a credit account with 0 balance if it's the first time the user is crediting
            $createCreditAccount = $conn->prepare("INSERT INTO accounts (user_id, account_type, balance, status) VALUES (?, ?, ?, ?)");
            $activeStatus = 'active';
            $zeroBalance = 0;
            $createCreditAccount->bind_param("isis", $userId, $accountType, $zeroBalance, $activeStatus);
            if ($createCreditAccount->execute()) {
                $pendingStatus = 'pending';
                $accountId = $createCreditAccount->insert_id;
                // Create pending transaction for non-admin users
                $transactionStmt = $conn->prepare("INSERT INTO transactions (account_id, transaction_type, amount, transaction_status) VALUES (?, ?, ?, ?)");
                $transactionStmt->bind_param("isis", $accountId, $accountType, $amount, $pendingStatus);
                $transactionStmt->execute();

                echo json_encode([
                    'message' => 'Credit transaction is pending, admin approval required.',
                    'status' => $pendingStatus,
                    'user_id' => $userId,
                    'account_id' => $accountId
                ]);
            } else {
                echo json_encode(['error' => 'Error creating credit account for first-time credit: ' . $createCreditAccount->error]);
            }
            $createCreditAccount->close();
        } else {
            // Credit account already exists, create pending transaction
            $pendingStatus = 'pending';
            $transactionStmt = $conn->prepare("INSERT INTO transactions (account_id, transaction_type, amount, transaction_status) VALUES (?, ?, ?, ?)");
            $transactionStmt->bind_param("isis", $accountId, $accountType, $amount, $pendingStatus);
            if ($transactionStmt->execute()) {
                echo json_encode([
                    'message' => 'Credit transaction is pending, admin approval required.',
                    'status' => $pendingStatus,
                    'user_id' => $userId,
                    'account_id' => $accountId
                ]);
            } else {
                echo json_encode(['error' => 'Error creating pending credit transaction: ' . $transactionStmt->error]);
            }
            $transactionStmt->close();
        }
    } else {
        // Admin: update or create credit account and set transaction status to completed
        if ($accountId !== null) {
            // Credit account exists, update balance
            $newBalance = $existingBalance + floatval($amount); // Add credit amount to current balance

            // Update credit balance
            $updateCredit = $conn->prepare("UPDATE accounts SET balance = ? WHERE account_id = ?");
            $updateCredit->bind_param("di", $newBalance, $accountId);
            if ($updateCredit->execute()) {
                // Insert a completed transaction
                $completedStatus = 'completed';
                $transactionStmt = $conn->prepare("INSERT INTO transactions (account_id, transaction_type, amount, transaction_status) VALUES (?, ?, ?, ?)");
                $transactionStmt->bind_param("isis", $accountId, $accountType, $amount, $completedStatus);
                $transactionStmt->execute();

            addPoints($conn, $userId, floatval($amount));

                echo json_encode([
                    'message' => 'Credit account updated successfully',
                    'new_balance' => $newBalance,
                    'status' => $completedStatus,
                    'user_id' => $userId
                ]);
            } else {
                echo json_encode(['error' => 'Error updating credit account: ' . $updateCredit->error]);
            }
            $updateCredit->close();
        } else {
            // Credit account does not exist, create new account with credit amount
            $activeStatus = 'active';
            $createCreditAccount = $conn->prepare("INSERT INTO accounts (user_id, account_type, balance, status) VALUES (?, ?, ?, ?)");
            $createCreditAccount->bind_param("isis", $userId, $accountType, $amount, $activeStatus);
            if ($createCreditAccount->execute()) {
                $accountId = $createCreditAccount->insert_id;
                // Insert a completed transaction
                $completedStatus = 'completed';
                $transactionStmt = $conn->prepare("INSERT INTO transactions (account_id, transaction_type, amount, transaction_status) VALUES (?, ?, ?, ?)");
                $transactionStmt->bind_param("isis", $accountId, $accountType, $amount, $completedStatus);
                $transactionStmt->execute();

                addPoints($conn, $userId, floatval($amount));


                echo json_encode([
                    'message' => 'Credit account created and credit amount added successfully',
                    'user_id' => $userId,
                    'balance' => $amount,
                    'status' => $activeStatus,
                    'account_id' => $accountId
                ]);
            } else {
                echo json_encode(['error' => 'Error inserting credit account: ' . $createCreditAccount->error]);
            }
            $createCreditAccount->close();
        }
    }

    $getCreditAccount->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();

// Function to calculate and add points based on deposit amount
function addPoints($conn, $userId, $depositAmount) {
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
?>
