<?php
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

// Fetch user data
$email = $_SESSION['email'];
$query = "SELECT * FROM registration WHERE email='$email' LIMIT 1";
$result = mysqli_query($db, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
} else {
    die("User not found.");
}

// Handle form submission
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $phone_number = mysqli_real_escape_string($db, $_POST['phone_number']);
    $country = mysqli_real_escape_string($db, $_POST['country']);
    $bio = mysqli_real_escape_string($db, $_POST['bio']);

    // Update query
    $update_query = "UPDATE registration SET name='$name', phone_number='$phone_number', country='$country', bio='$bio' WHERE email='$email'";
    if (mysqli_query($db, $update_query)) {
        $_SESSION['success'] = "Profile updated successfully!";
        header('location: profile.php');
        exit();
    } else {
        die("Update failed: " . mysqli_error($db));
    }
}
?>