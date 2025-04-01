<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session at the very beginning
session_start();

// Database connection
$db = mysqli_connect('localhost', 'root', '', 'BooksAppForChildren');
if (!$db) {
    $_SESSION['error'] = "Database connection failed.";
    header('Location: login.php');
    exit();
}

// LOGIN USER
if (isset($_POST['login_user'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Form validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and Password are required.";
        header('Location: login.php');
        exit();
    }

    // Fetch user securely using prepared statements
    $query = "SELECT name, email, password FROM registration WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($db, $query);

    if (!$stmt) {
        $_SESSION['error'] = "Database query failed.";
        header('Location:login.php');
        exit();
    }

    // Bind parameters and execute
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['success'] = "You are now logged in";

            // Redirect to user profile
            header('Location:user_profile.php');
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password.";
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Email not found.";
        header('Location:login.php');
        exit();
    }
}
?>