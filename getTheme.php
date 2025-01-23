<?php

header('Content-Type: application/json');

include 'dbConnexion.php';

if (isset($_GET['themeID'])) {
    $themeID = $_GET['themeID'];

    // Prepare and execute the query using PDO to prevent SQL injection
    $stmt = $pdo->prepare("SELECT themeName FROM themelist WHERE themeID = :themeID");
    $stmt->bindParam(':themeID', $themeID, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    } else {
        echo json_encode(["themeName" => "Theme not found"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "themeID is required"]);
}

$pdo = null; // Close the PDO connection
?>
