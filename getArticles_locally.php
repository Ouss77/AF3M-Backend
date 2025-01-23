<?php
// Include the dbConnexion.php file to set CORS headers and establish the database connection
include 'dbConnexion.php';

// Set the content type to JSON for the response
header('Content-Type: application/json');

try {
    // SQL query to fetch articles with authors
    $sql = "
        SELECT 
            a.article_id,
            a.titre,
            a.annee,
            a.theme,
            a.resume, 
            GROUP_CONCAT(au.author_name SEPARATOR ', ') AS authors,
            a.pdf_files
        FROM 
            Articales a
        LEFT JOIN 
            Article_Authors aa ON a.article_id = aa.article_id
        LEFT JOIN 
            Authors au ON aa.author_id = au.author_id
        GROUP BY 
            a.article_id, a.titre, a.annee, a.theme, a.resume;
    ";

    // Execute the query and fetch the data
    $stmt = $pdo->query($sql);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the data as a JSON response
    echo json_encode($articles);

} catch (PDOException $e) {
    // Handle query execution errors
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch articles: ' . $e->getMessage()]);
}
?>
