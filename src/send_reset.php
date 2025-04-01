<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;

require '../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'BooksAppForChildren');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // Validate email format
    $validator = new EmailValidator();
    if (!$validator->isValid($email, new RFCValidation())) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: forgot_password.php");
        exit();
    }

    // Check if the email exists
    $stmt = $conn->prepare("SELECT id FROM registration WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate a secure reset token
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", time() + 3600);

        // Store token in the database
        $stmt = $conn->prepare("UPDATE registration SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USERNAME'];
            $mail->Password = $_ENV['EMAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Optional for debugging
            // $mail->SMTPDebug = 2;

            $mail->setFrom($_ENV['EMAIL_USERNAME'], 'Book App For Children');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";
            $mail->Body = "<p>Click the link below to reset your password:</p>
            <p><a href='http://localhost/BookAppForChildren/src/reset_form.php?token=$token' style='padding: 10px; background-color: blue; color: white; text-decoration: none;'>Reset Password</a></p>
            <p>If you did not request this, please ignore this email.</p>";

            if ($mail->send()) {
                $_SESSION['message'] = "A password reset link has been sent to your email.";
            } else {
                $_SESSION['error'] = "Failed to send the reset link. Please try again.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Mail Error: " . $mail->ErrorInfo;
        }
    } else {
        $_SESSION['error'] = "No account found with that email address.";
    }

    // Redirect user to forgot_password.php
    header("Location: forgot_password.php");
    exit();
}
?>