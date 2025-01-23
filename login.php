<?php
include 'dbConnexion.php';  // Include your DB connection file
header('Content-Type: application/json');

// Get the login credentials from the request body
$requestData = json_decode(file_get_contents("php://input"));
$username = $requestData->username;
$password = $requestData->password;

// Check if the credentials match the predefined admin credentials
if ($username === 'admin' && $password === 'admin') {
    // Return a success response if valid
    echo json_encode(['success' => true, 'message' => 'Login successful']);
} else {
    // Return an error response if invalid
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}
?>
