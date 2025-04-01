<?php

//  include "user_payment.php";
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);


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

// Fetch user data
$email = $_SESSION['email'];
$query = "SELECT * FROM registration WHERE email='$email' LIMIT 1";
$result = mysqli_query($db, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    die("User not found.");
}

// Fetch free books
$free_books_query = "SELECT * FROM books WHERE type='free' LIMIT 5";
$free_books_result = mysqli_query($db, $free_books_query);
$free_books = [];
if ($free_books_result) {
    while ($row = mysqli_fetch_assoc($free_books_result)) {
        $free_books[] = $row;
    }
}

// Fetch paid books
$paid_books_query = "SELECT * FROM books WHERE type='paid' LIMIT 5";
$paid_books_result = mysqli_query($db, $paid_books_query);
$paid_books = [];
if ($paid_books_result) {
    while ($row = mysqli_fetch_assoc($paid_books_result)) {
        $paid_books[] = $row;
    }
}

// Function to check if the user has paid for a book
function hasUserPaidForBook($db, $user_id, $book_id)
{
    $query = "SELECT payment_status FROM user_payments WHERE user_id = ? AND book_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['payment_status'] === 'completed';
    }
    return false;
}
?>

<div class="books-section">
    <h3>Paid Books</h3>
    <div class="book-list">
        <?php if (!empty($paid_books)): ?>
            <?php foreach ($paid_books as $book): ?>
                <div class="book-item">
                    <img src="<?php echo htmlspecialchars($book['cover_image']); ?>"
                        alt="<?php echo htmlspecialchars($book['title']); ?>">
                    <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                    <p><?php echo htmlspecialchars($book['author']); ?></p>
                    <p><?php echo htmlspecialchars($book['summary']); ?></p>
                    <?php if (hasUserPaidForBook($db, $user['id'], $book['id'])): ?>
                        <!-- User has paid, show book content -->
                        <div class="paid-book-content">
                            <p>Book content goes here...</p>
                        </div>
                    <?php else: ?>
                        <!-- User hasn't paid, show payment link -->
                        <a href="payment.php?book_id=<?php echo $book['id']; ?>" class="btn">Pay to Access</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No paid books available.</p>
        <?php endif; ?>
    </div>
</div>