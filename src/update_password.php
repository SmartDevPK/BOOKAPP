<?php
session_start();

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'BooksAppForChildren');

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit();
    }

    // Check if the token is valid and not expired
    $stmt = $conn->prepare("SELECT id FROM registration WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Fetch user ID
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        // Hash new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update password and clear the reset token
        $stmt = $conn->prepare("UPDATE registration SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Your password has been reset successfully.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['error'] = "Error updating password.";
            header("Location: reset_password.php?token=" . urlencode($token));
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid or expired token.";
        header("Location: login.php");
        exit();
    }
}
?>