<?php
// Include the Composer autoloader (ensure PhpSpreadsheet is installed via Composer)
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Set content type to JSON
include 'dbConnexion.php';  // Include your DB connection file
header('Content-Type: application/json');

// Check if a file was uploaded
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'error' => 'File upload error.']);
        exit;
    }

    // Check if the file is an Excel file
    $allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'error' => 'Invalid file type. Please upload an Excel file.']);
        exit;
    }

    // Load the Excel file
    $spreadsheet = IOFactory::load($file['tmp_name']);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    // Loop through the rows and insert each row into the database
    foreach ($rows as $row) {
        if (count($row) < 5) {
            // Skip row if it doesn't contain enough columns
            continue;
        }

        $titre = $row[0]; // Assuming the columns are in this order
        $annee = $row[1];
        $theme = $row[2];
        $resume = $row[3];
        $author_name = $row[4];

        // Trim whitespace from author_name
        $author_name = trim($author_name);

        if (empty($author_name)) {
            // Skip this row if author_name is empty or invalid
            continue;
        }

        // Start a transaction
        $pdo->beginTransaction();

        // Check if author exists in the Authors table
        $stmt = $pdo->prepare("SELECT author_id FROM Authors WHERE author_name = :author_name");
        $stmt->execute(['author_name' => $author_name]);
        $author = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($author) {
            // Author exists, get their ID
            $author_id = $author['author_id'];
        } else {
            // Author doesn't exist, so insert the author into the Authors table
            $stmt = $pdo->prepare("INSERT INTO Authors (author_name) VALUES (:author_name)");
            $stmt->execute(['author_name' => $author_name]);
            $author_id = $pdo->lastInsertId();  // Get the ID of the newly inserted author
        }

        // Insert the article data into the Articles table (without the author information)
        $stmt = $pdo->prepare("INSERT INTO Articales (titre, annee, theme, resume) 
                               VALUES (:titre, :annee, :theme, :resume)");
        $stmt->execute([
            'titre' => $titre,
            'annee' => $annee,
            'theme' => $theme,
            'resume' => $resume,
        ]);
        $article_id = $pdo->lastInsertId();  // Get the ID of the newly inserted article

        // Link the article to the author in the Article_Authors table
        $stmt = $pdo->prepare("INSERT INTO Article_Authors (article_id, author_id) 
                               VALUES (:article_id, :author_id)");
        $stmt->execute([
            'article_id' => $article_id,
            'author_id' => $author_id,
        ]);

        // Commit the transaction
        $pdo->commit();
    }

    // Final success message after all data has been processed
    echo json_encode(['success' => true, 'message' => 'Articles added from the Excel file!']);
} else {
    echo json_encode(['success' => false, 'error' => 'No file uploaded.']);
}
?>
