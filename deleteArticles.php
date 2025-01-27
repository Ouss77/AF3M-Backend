<?php

include 'dbConnexion.php';  // Include your DB connection file
header('Content-Type: application/json');  // Set the content type to JSON
 
// Validate the article_id
if (isset($_GET['article_id']) && is_numeric($_GET['article_id'])) {
    $articleId = (int) $_GET['article_id'];  // Cast to integer

    // Start a transaction
    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Step 1: Delete related entries from Article_Authors (junction table)
        $deleteAuthorsQuery = "DELETE FROM article_authors WHERE article_id = :article_id";
        $stmt = $pdo->prepare($deleteAuthorsQuery);
        $stmt->bindParam(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        // Step 2: Delete the article from the Articles table
        $deleteArticleQuery = "DELETE FROM articales WHERE article_id = :article_id";
        $stmt = $pdo->prepare($deleteArticleQuery);
        $stmt->bindParam(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        // Send success response
        echo json_encode(['success' => true]);

    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();

        // Handle the error
        echo json_encode(['success' => false, 'error' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    // Handle case where article_id is missing or invalid
    echo json_encode(['success' => false, 'error' => 'Invalid or missing article_id']);
}
?>
