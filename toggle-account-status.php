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

try {
    // Database connection
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    // Check for POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = intval($_POST['user_id']);

        // Validate user_id
        if (empty($userId) || $userId <= 0) {
            echo json_encode(['error' => 'Invalid user_id provided']);
            exit;
        }

        // Step 1: Get the current status
        $getStatus = $conn->prepare("SELECT status FROM accounts WHERE user_id = ? AND account_type = 'savings'");
        $getStatus->bind_param("i", $userId);
        $getStatus->execute();
        $result = $getStatus->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(['error' => 'Account not found']);
            exit;
        }

        $account = $result->fetch_assoc();
        $currentStatus = $account['status'];

        // Step 2: Toggle the status
        $newStatus = ($currentStatus === 'locked') ? 'active' : 'locked';

        // Step 3: Update the status in the database
        $updateStatus = $conn->prepare("UPDATE accounts SET status = ? WHERE user_id = ? AND account_type = 'savings'");
        $updateStatus->bind_param("si", $newStatus, $userId);

        if ($updateStatus->execute()) {
            echo json_encode([
                'message' => 'Account status updated successfully',
                'new_status' => $newStatus
            ]);
        } else {
            echo json_encode(['error' => 'Failed to update account status']);
        }

        // Close statements
        $getStatus->close();
        $updateStatus->close();
    } else {
        echo json_encode(['error' => 'Invalid request method']);
    }

    $conn->close();
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
