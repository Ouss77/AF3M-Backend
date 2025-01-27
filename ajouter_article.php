<?php
header("Content-Type: application/json");

include 'dbConnexion.php';  // Include your DB connection file

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "Invalid input"]);
    exit();
}

$titre = $data['titre'];
$annee = $data['annee'];
$theme = $data['theme'];
$resume = $data['resume'];
$author_name = $data['author_name'];

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if author exists
    $stmt = $pdo->prepare("SELECT author_id FROM authors WHERE author_name = :author_name");
    $stmt->execute(['author_name' => $author_name]);
    $author = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($author) {
        $author_id = $author['author_id'];
    } else {
        // Insert Author if not exists
        $stmt = $pdo->prepare("INSERT INTO authors (author_name) VALUES (:author_name)");
        $stmt->execute(['author_name' => $author_name]);
        $author_id = $pdo->lastInsertId();
    }

    // Insert Article
    $stmt = $pdo->prepare("INSERT INTO articales (titre, annee, theme, resume) 
                           VALUES (:titre, :annee, :theme, :resume)");
    $stmt->execute([
        'titre' => $titre,
        'annee' => $annee,
        'theme' => $theme,
        'resume' => $resume,
    ]);
    $article_id = $pdo->lastInsertId();

    // Insert into Articles_Authors (link table)
    $stmt = $pdo->prepare("INSERT INTO article_authors (article_id, author_id) 
                           VALUES (:article_id, :author_id)");
    $stmt->execute([
        'article_id' => $article_id,
        'author_id' => $author_id,
    ]);

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
