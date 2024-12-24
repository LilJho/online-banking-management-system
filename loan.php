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
    $loanAmount = floatval($_POST['loan-amount']); 
    $isAdmin = isset($_POST['isAdmin']) ? filter_var($_POST['isAdmin'], FILTER_VALIDATE_BOOLEAN) : false;
    $accountType = 'loan';

    // Validate required fields
    if (empty($userId) || empty($loanAmount)) {
        echo json_encode(['error' => 'User ID and Loan Amount are required']);
        exit;
    }

    // Get loan account by user ID (if it exists)
    $getLoanAccount = $conn->prepare("SELECT account_id, balance FROM accounts WHERE user_id = ? AND account_type = ?");
    $getLoanAccount->bind_param("is", $userId, $accountType);
    $getLoanAccount->execute();
    $loanResult = $getLoanAccount->get_result();

    // Check if loan account exists
    if ($loanResult->num_rows > 0) {
        // Loan account exists, return a message saying the user already has a loan
        echo json_encode(['message' => 'This account already has a loan.']);
        $getLoanAccount->close();
        exit;
    }

    // Non-admin: only create a pending transaction, no loan account updates
    if (!$isAdmin) {
        // Create a loan account with 0 balance if it's the first time the user is loaning
        $activeStatus = 'active';
        $zeroBalance = 0;
        $createLoanAccount = $conn->prepare("INSERT INTO accounts (user_id, account_type, balance, status) VALUES (?, ?, ?, ?)");
        $createLoanAccount->bind_param("isis", $userId, $accountType, $zeroBalance, $activeStatus);
        if ($createLoanAccount->execute()) {
            $pendingStatus = 'pending';

            $accountId = $createLoanAccount->insert_id;
            // Create pending transaction for non-admin users
            $transactionStmt = $conn->prepare("INSERT INTO transactions (account_id, transaction_type, amount, transaction_status) VALUES (?, ?, ?, ?)");
            $transactionStmt->bind_param("isis", $accountId, $accountType, $loanAmount, $pendingStatus);
            $transactionStmt->execute();

            echo json_encode([
                'message' => 'Loan transaction is pending, admin approval required.',
                'status' => $pendingStatus,
                'user_id' => $userId,
                'account_id' => $accountId
            ]);
        } else {
            echo json_encode(['error' => 'Error creating loan account for first-time loan: ' . $createLoanAccount->error]);
        }
        $createLoanAccount->close();
    } else {
        // Admin: update loan account and set transaction status to completed
        $activeStatus = 'active';
        // Create loan account with loan amount if it does not exist
        $createLoanAccount = $conn->prepare("INSERT INTO accounts (user_id, account_type, balance, status) VALUES (?, ?, ?, ?)");
        $createLoanAccount->bind_param("isis", $userId, $accountType, $loanAmount, $activeStatus);
        if ($createLoanAccount->execute()) {
            $accountId = $createLoanAccount->insert_id;
            // Insert a completed transaction
            $completedStatus = 'completed';
            $transactionStmt = $conn->prepare("INSERT INTO transactions (account_id, transaction_type, amount, transaction_status) VALUES (?, ?, ?, ?)");
            $transactionStmt->bind_param("isis", $accountId, $accountType, $loanAmount, $completedStatus);
            $transactionStmt->execute();

            addPoints($conn, $userId, $loanAmount);

            echo json_encode([
                'message' => 'Loan account created and loan amount added successfully',
                'user_id' => $userId,
                'balance' => $loanAmount,
                'status' => $activeStatus,
                'account_id' => $accountId
            ]);
        } else {
            echo json_encode(['error' => 'Error inserting loan account: ' . $createLoanAccount->error]);
        }
        $createLoanAccount->close();
    }

    $getLoanAccount->close();
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
