<?php
// read_book.php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('location: login.php');
    exit();
}

// Database connection
$db = mysqli_connect('localhost', 'root', '', 'BooksAppForChildren');
if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get the book ID from the URL
if (!isset($_GET['book_id'])) {
    die("Book ID not provided.");
}
$book_id = intval($_GET['book_id']);

// Fetch book details
$query = "SELECT * FROM books WHERE id = $book_id LIMIT 1";
$result = mysqli_query($db, $query);
if (!$result || mysqli_num_rows($result) === 0) {
    die("Book not found.");
}
$book = mysqli_fetch_assoc($result);

// Check if the user has paid for any book
$user_id = $_SESSION['user_id'];
$query = "SELECT payment_status FROM user_payments WHERE user_id = ? AND payment_status = 'completed' LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("You do not have access to this book.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read Book</title>
</head>

<body>
    <h1><?php echo htmlspecialchars($book['title']); ?></h1>
    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
    <p><strong>Summary:</strong> <?php echo htmlspecialchars($book['summary']); ?></p>
    <div>
        <h2>Book Content</h2>
        <p><?php echo nl2br(htmlspecialchars($book['content'])); ?></p>
    </div>
    <a href="paid_books.php">Back to Paid Books</a>
</body>

</html>