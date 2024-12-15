<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root";
$password = "Password@29263";
$database = "online_bank_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pageSize = 6;
$offset = ($page - 1) * $pageSize;

$sql = "SELECT id, title, image_url, is_active, created_at FROM offers LIMIT $pageSize OFFSET $offset";
$result = $conn->query($sql);

if ($result) {
    $offers = [];
    while ($row = $result->fetch_assoc()) {
        $offers[] = $row;
    }

    // Fetch the total number of offers for calculating total pages
    $totalOffersQuery = "SELECT COUNT(*) as total FROM offers";
    $totalOffersResult = $conn->query($totalOffersQuery);
    $totalOffers = $totalOffersResult->fetch_assoc()['total'];
    $totalPages = ceil($totalOffers / $pageSize);

    echo json_encode([
        'offers' => $offers,
        'totalPages' => $totalPages,
        'currentPage' => $page,
    ]);
} else {
    echo json_encode(['error' => 'Failed to fetch offers: ' . $conn->error]);
}

$conn->close();
?>
