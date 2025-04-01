<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are set
    if (!isset($_POST['book_id'], $_POST['book_title'], $_POST['currency'])) {
        die("Invalid request. Missing required fields.");
    }

    // Store data in session
    $_SESSION['book_id'] = intval($_POST['book_id']);
    $_SESSION['book_title'] = htmlspecialchars($_POST['book_title']);
    $_SESSION['currency'] = $_POST['currency'];

    // Use an absolute path for redirection
    $redirectPath = 'readBook.php';

    // Redirect to readbook.php
    header("Location: $redirectPath");
    exit();
} else {
    die("This script only accepts POST requests.");
}
?>