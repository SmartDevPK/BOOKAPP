<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "BooksAppForChildren";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Get form data
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $type = !empty($_POST['type']) ? $_POST['type'] : 'Unknown';
    $summary = !empty($_POST['summary']) ? trim($_POST['summary']) : 'No summary available';

    // Define upload directory
    $uploadDir = "src/uploads/";

    // Ensure directory exists
    if (!file_exists($uploadDir) && !mkdir($uploadDir, 0777, true)) {
        die("❌ Error: Failed to create upload directory.");
    }

    // Handle Cover Image Upload
    if (!empty($_FILES["cover_image"]["name"])) {
        $coverImageName = basename($_FILES["cover_image"]["name"]);
        $coverImagePath = $uploadDir . $coverImageName;

        // Ensure the file is uploaded
        if (!move_uploaded_file($_FILES["cover_image"]["tmp_name"], $coverImagePath)) {
            die("❌ Error: Failed to upload cover image.");
        }
    } else {
        die("❌ Error: No cover image uploaded.");
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO books (title, author, cover_image, type, summary) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $author, str_replace("src/", "", $coverImagePath), $type, $summary);

    if ($stmt->execute()) {
        header('location: login.php');
    } else {
        echo "❌ Error inserting into database: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        input,
        select,
        button,
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            text-align: left;
        }

        button {
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 18px;
        }

        button:hover {
            background: #218838;
        }

        input[type="file"] {
            border: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Upload a Book</h1>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Enter Book Title" required>
            <input type="text" name="author" placeholder="Enter Author Name" required>
            <label>Upload Cover Image:</label>
            <input type="file" name="cover_image" accept="image/*" required>
            <textarea name="summary" placeholder="Enter book summary" required></textarea>
            <button type="submit" name="submit">Upload</button>
        </form>
    </div>
</body>

</html>