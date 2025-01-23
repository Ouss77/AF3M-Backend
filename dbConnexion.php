<?php
// dbConnexion.php
header("Access-Control-Allow-Origin: *");  // Allow all origins (you can restrict this to specific origins if needed)
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");  // Allow necessary HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");  // Allow necessary headers

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$servername = "localhost";
$username = "root";  // Default MySQL username (change if needed)
$password = "1963";  // Default password (change if needed)
$dbname = "afmassnadmin"; // Replace with your database name

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection errors, return JSON formatted error
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit(); // Stop execution if connection fails
}
?> 
