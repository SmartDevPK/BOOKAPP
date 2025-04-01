<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'BooksAppForChildren');
if ($conn->connect_error) {
    die("❌ Database Connection Failed: " . $conn->connect_error);
}

// Fetch all books from the database
$sql = "SELECT id, title, author, summary, cover_image FROM books ORDER BY id DESC";
$result = $conn->query($sql);

// Store books in an array
$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
} else {
    die("❌ No books found in the database.");
}

// Set default book cover
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        .books-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .book-card {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 250px;
            text-align: center;
        }

        .book-card img {
            max-width: 150px;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .book-card h2 {
            font-size: 18px;
            margin: 10px 0;
        }

        .book-card p {
            font-size: 14px;
            color: #666;
        }

        .read-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .read-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

    <h1>Available Books</h1>

    <div class="books-container">
        <?php foreach ($books as $book): ?>
            <div class="book-card">
                <!-- Book Cover Image -->
                <img src="/BookAppForChildren/src/<?php echo htmlspecialchars($book['cover_image']); ?>" <img
                    src="<?php echo $imageFilePath; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">

                <h2><?php echo htmlspecialchars($book['title']); ?></h2>
                <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                <p><?php echo htmlspecialchars(substr($book['summary'], 0, 100)); ?>...</p>

                <!-- Read Book Button -->
                <a href="book.php?id=<?php echo $book['id']; ?>" class="read-btn">Read Book</a>
                <a href="user_profile.php" class="read-btn">RETURN TO USER PROFILE</a>

            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>

<?php
$conn->close(); // Close the database connection here, after everything is loaded
?>