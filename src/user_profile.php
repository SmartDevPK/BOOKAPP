<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);


// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

// Database connection with error handling
$db = mysqli_connect('localhost', 'root', '', 'BooksAppForChildren');
if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch user data using prepared statement
$email = $_SESSION['email'];
$query = "SELECT * FROM registration WHERE email = ? LIMIT 1";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['id'];
} else {
    die("User not found.");
}

// Fetch all books with error handling
$books_query = "SELECT * FROM books";
$books_result = mysqli_query($db, $books_query);
$books = [];

if ($books_result) {
    while ($row = mysqli_fetch_assoc($books_result)) {
        $books[] = $row;
    }
} else {
    die("Error fetching books: " . mysqli_error($db));
}

// Function to check payment status with prepared statement
function hasUserPaidForBook($db, $user_id, $book_id)
{
    $query = "SELECT payment_status FROM user_payments WHERE user_id = ? AND book_id = ? AND payment_status = 'completed' LIMIT 1";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $book_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result) > 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Books - Children's Book App</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 1rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .profile-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .profile-info p {
            margin-bottom: 0.5rem;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 0.5rem;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-logout {
            background-color: #dc3545;
        }

        .btn-logout:hover {
            background-color: #bb2d3b;
        }

        .books-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .book-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }

        .book-card:hover {
            transform: translateY(-5px);
        }

        .book-cover {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .book-details {
            padding: 1rem;
        }

        .book-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .book-author {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .book-summary {
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .book-action {
            display: block;
            text-align: center;
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .btn-free {
            background-color: #28a745;
        }

        .btn-paid {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-pay {
            background-color: #dc3545;
        }

        .no-books {
            text-align: center;
            padding: 2rem;
            color: #666;
        }

        @media (max-width: 768px) {
            .profile-info {
                grid-template-columns: 1fr;
            }

            .books-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Children's Book App</h1>
    </div>

    <div class="container">
        <div class="profile-card">
            <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?></h2>

            <div class="profile-info">
                <div>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
                </div>
                <div>
                    <p><strong>Country:</strong> <?php echo htmlspecialchars($user['country']); ?></p>
                </div>
            </div>

            <div class="profile-actions">
                <a href="update_profile.php" class="btn">Update Profile</a>
                <a href="login.php" class="btn btn-logout">Logout</a>
            </div>
        </div>

        <h2>Available Books</h2>

        <div class="books-container">
            <?php if (!empty($books)): ?>
                <?php foreach ($books as $book): ?>
                    <div class="book-card">
                        <img src="/BookAppForChildren/src/<?php echo htmlspecialchars($book['cover_image']); ?>"
                            lt="<?php echo htmlspecialchars($book['title']); ?>" class="book-cover">

                        <div class="book-details">
                            <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="book-author">By <?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="book-summary"><?php echo htmlspecialchars($book['summary']); ?></p>

                            <?php if ($book['type'] === 'free'): ?>
                                <a href="readBook.php?book_id=<?php echo $book['id']; ?>" class="btn book-action btn-free">
                                    Read for Free
                                </a>
                            <?php else: ?>
                                <?php if (hasUserPaidForBook($db, $user_id, $book['id'])): ?>
                                    <a href="view_book.php?book_id=<?php echo $book['id']; ?>" class="btn book-action btn-paid">
                                        Read Now
                                    </a>
                                <?php else: ?>
                                    <a href="payment.php?book_id=<?php echo $book['id']; ?>" class="btn book-action btn-pay">
                                        Purchase Book
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-books">
                    <p>No books available at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>