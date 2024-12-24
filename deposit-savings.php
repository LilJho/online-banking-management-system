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
    $isAdmin = isset($_POST['isAdmin']) && filter_var($_POST['isAdmin'], FILTER_VALIDATE_BOOLEAN);

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

    $transactionDate = date('Y-m-d H:i:s'); // Current timestamp
    $transactionStatus = $isAdmin ? 'completed' : 'pending'; // Determine transaction status
    $accountId = null;

    if ($result->num_rows > 0) {
        // Account exists
        $account = $result->fetch_assoc();
        $accountId = $account['account_id']; // Get account_id from the accounts table

        if ($isAdmin) {
            // Admin deposits directly into the existing account
            $newBalance = $depositAmount + floatval($account['balance']);

            // Update the existing account balance
            $updateDeposit = $conn->prepare("UPDATE accounts SET balance = ? WHERE account_id = ?");
            $updateDeposit->bind_param("di", $newBalance, $accountId);
            if ($updateDeposit->execute()) {
                // Add points based on deposit amount
                addPoints($conn, $userId, $depositAmount);

                // Log the completed transaction
                $logTransaction = $conn->prepare(
                    "INSERT INTO transactions (account_id, transaction_type, amount, transaction_date, transaction_status) 
                    VALUES (?, 'deposit', ?, ?, ?)"
                );
                $logTransaction->bind_param("idss", $accountId, $depositAmount, $transactionDate, $transactionStatus);
                $logTransaction->execute();
                $logTransaction->close();

                echo json_encode([
                    'message' => 'Balance updated and transaction completed successfully',
                    'new_balance' => $newBalance,
                    'account_id' => $accountId
                ]);
            } else {
                echo json_encode(['error' => 'Error updating balance: ' . $updateDeposit->error]);
            }

            $updateDeposit->close();
        } else {
            // Non-admin: create pending transaction without updating balance
            $logTransaction = $conn->prepare(
                "INSERT INTO transactions (account_id, transaction_type, amount, transaction_date, transaction_status) 
                VALUES (?, 'deposit', ?, ?, ?)"
            );
            $logTransaction->bind_param("idss", $accountId, $depositAmount, $transactionDate, $transactionStatus);
            $logTransaction->execute();
            $logTransaction->close();

            echo json_encode([
                'message' => 'Deposit transaction created successfully',
                'account_id' => $accountId
            ]);
        }
    } else {
        // No account exists
        if ($isAdmin) {
            // Admin creates the account and deposits immediately
            $stmt = $conn->prepare("INSERT INTO accounts (user_id, account_type, balance, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $userId, $accountType, $depositAmount, $status);
            if ($stmt->execute()) {
                $accountId = $conn->insert_id; // Get the newly inserted account_id
                // Log the completed transaction
                $logTransaction = $conn->prepare(
                    "INSERT INTO transactions (account_id, transaction_type, amount, transaction_date, transaction_status) 
                    VALUES (?, 'deposit', ?, ?, ?)"
                );
                $completedStatus = 'completed';
                $logTransaction->bind_param("idss", $accountId, $depositAmount, $transactionDate, $completedStatus);
                $logTransaction->execute();
                $logTransaction->close();

                // Add points based on deposit amount
                addPoints($conn, $userId, $depositAmount);

                echo json_encode([
                    'message' => 'Account created and deposit added successfully',
                    'account_id' => $accountId,
                    'balance' => $depositAmount
                ]);
            } else {
                echo json_encode(['error' => 'Error inserting account: ' . $stmt->error]);
            }

            $stmt->close();
        } else {
            // Non-admin creates account with 0 balance and the transaction is pending
            $zeroBalance = 0;
            $stmt = $conn->prepare("INSERT INTO accounts (user_id, account_type, balance, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $userId, $accountType, $zeroBalance, $status);
            if ($stmt->execute()) {
                $accountId = $conn->insert_id; // Get the newly inserted account_id

                // Log the pending transaction
                $logTransaction = $conn->prepare(
                    "INSERT INTO transactions (account_id, transaction_type, amount, transaction_date, transaction_status) 
                    VALUES (?, 'deposit', ?, ?, ?)"
                );
                $pendingStatus = 'pending';
                $logTransaction->bind_param("idss", $accountId, $depositAmount, $transactionDate, $pendingStatus);
                $logTransaction->execute();
                $logTransaction->close();

                echo json_encode([
                    'message' => 'Account created and deposit transaction created (pending)',
                    'account_id' => $accountId
                ]);
            } else {
                echo json_encode(['error' => 'Error inserting account: ' . $stmt->error]);
            }

            $stmt->close();
        }
    }

    $getUser->close();
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
