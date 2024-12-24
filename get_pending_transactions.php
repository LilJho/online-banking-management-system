<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Check if the request is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page = intval($_GET['page'] ?? 1); // Current page
    $limit = intval($_GET['limit'] ?? 10); // Records per page
    $offset = ($page - 1) * $limit; // Offset for SQL query

    // Count total transactions
    $countQuery = "
        SELECT COUNT(*) as total 
        FROM transactions t
        WHERE t.transaction_status = 'pending'
    ";
    $countStmt = $conn->prepare($countQuery);
    if (!$countStmt) {
        echo json_encode(['error' => 'Error preparing count statement: ' . $conn->error]);
        exit;
    }
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalCount = $countResult->fetch_assoc()['total'];

    // Fetch transactions with pagination
    $query = "
        SELECT t.transaction_id, t.transaction_type, t.amount, t.transaction_date, t.transaction_status, t.account_id
        FROM transactions t
        WHERE t.transaction_status = 'pending'
        ORDER BY t.transaction_date DESC
        LIMIT ? OFFSET ?
    ";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }

    echo json_encode([
        'transactions' => $transactions,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($totalCount / $limit),
            'total_transactions' => $totalCount,
        ],
    ]);

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
