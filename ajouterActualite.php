<?php
// Set content type to JSON
include 'dbConnexion.php';  // Include your DB connection file
header('Content-Type: application/json');

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get data from POST request
        $Titre = $_POST['title']; // Change to match the form data
        $Description = $_POST['description']; // Change to match the form data
        $image_url = null;

        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/'; // Ensure this directory exists and is writable
            $imageName = basename($_FILES['image']['name']);
            $imagePath = $uploadDir . $imageName;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                $image_url = $imagePath;
            } else {
                echo json_encode(['error' => 'Failed to upload image']);
                exit;
            }
        }

        // Insert data into the database
        $stmt = $pdo->prepare("INSERT INTO Actualites (Titre, Description, image_url) VALUES (?, ?, ?)");
        $stmt->execute([$Titre, $Description, $image_url]);

        // Return a success response
        echo json_encode(['success' => true, 'message' => 'Data inserted successfully']);
    } else {
        echo json_encode(['error' => 'Invalid request method']);
    }
} catch (PDOException $e) {
    // Handle errors
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
?>
