<?php
$host = "localhost";       // Hostname
$username = "root";        // Your MySQL username
$password = "Password@29263";            // Your MySQL password
$database = "online_bank_db"; // Database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to the database!";
?>