<?php
session_start();

// Redirect if token is missing
if (!isset($_GET['token'])) {
    $_SESSION['error'] = "Invalid or missing token.";
    header("Location: forgot_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to the CSS file -->
</head>

<style>
    /* styles.css */
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
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 300px;
        text-align: center;
    }

    h1 {
        margin-bottom: 20px;
        font-size: 24px;
        color: #333;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin-bottom: 8px;
        font-weight: bold;
        color: #555;
    }

    input[type="password"] {
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    button {
        padding: 10px;
        background-color: #28a745;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
    }

    button:hover {
        background-color: #218838;
    }

    .error {
        color: red;
        margin-bottom: 20px;
    }

    .success {
        color: green;
        margin-bottom: 20px;
    }
</style>

<body>
    <div class="container">
        <h1>Reset Password</h1>

        <?php
        // Display error message
        if (isset($_SESSION['error'])) {
            echo "<p class='error'>{$_SESSION['error']}</p>";
            unset($_SESSION['error']);
        }

        // Display success message
        if (isset($_SESSION['message'])) {
            echo "<p class='success'>{$_SESSION['message']}</p>";
            unset($_SESSION['message']);
        }
        ?>

        <form action="update_password.php" method="POST">
            <!-- Hidden input to pass the token -->
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

            <!-- New Password Field -->
            <label for="password">New Password:</label>
            <input type="password" name="password" id="password" required>

            <!-- Confirm Password Field -->
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <!-- Submit Button -->
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>

</html>