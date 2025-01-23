<?php
include 'dbConnexion.php';
// Set the content type to JSON
 header('Content-Type: application/json');

// Check if 'Titre' is provided in the query parameter
if (isset($_GET['Titre'])) {
    $titre = $_GET['Titre'];

    try {
        // SQL query to delete based on 'Titre'
        $sql = "DELETE FROM actualites WHERE Titre = :titre";

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind the parameter
        $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => "Actualite deleted successfully"]);
        } else {
            echo json_encode(["error" => "No actualite found with the provided Titre"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Failed to delete actualite: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Titre parameter is missing"]);
}

// Close the database connection (PDO connection will close automatically when script ends)
?>
