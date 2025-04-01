<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('location: login.php');
    exit();
}

// Display success message if set
if (isset($_SESSION['success'])) {
    echo "<div style='color: green; text-align: center;'>" . $_SESSION['success'] . "</div>";
    unset($_SESSION['success']); // Clear the message after displaying it
}

// Database connection
$db = mysqli_connect('localhost', 'root', '', 'BooksAppForChildren');
if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch user data from the database
$email = $_SESSION['email'];
$query = "SELECT * FROM registration WHERE email='$email' LIMIT 1";
$result = mysqli_query($db, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    die("User not found.");
}

// Handle form submission
if (isset($_POST['update_profile'])) {
    // Sanitize and escape user inputs
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $phone_number = mysqli_real_escape_string($db, $_POST['phone_number']);
    $country = mysqli_real_escape_string($db, $_POST['country']);


    // Update the user's profile in the database
    $update_query = "UPDATE registration SET 
                     name='$name', 
                     phone_number='$phone_number', 
                     country='$country' 
                     WHERE email='$email'";
    $update_result = mysqli_query($db, $update_query);

    if ($update_result) {
        $_SESSION['success'] = "Profile updated successfully!";
        header('location:user_profile.php');
        exit();
    } else {
        die("Update failed: " . mysqli_error($db));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        .edit-profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        .btn {
            display: block;
            padding: 5px;
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .edit-profile-container h2 {
            text-align: center;
        }

        .edit-profile-container label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .edit-profile-container input[type="text"],
        .edit-profile-container input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .edit-profile-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-profile-container input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="edit-profile-container">
        <h2>Edit Profile</h2>
        <form action="update_profile.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                disabled>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number"
                value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>

            <label for="country">Country:</label>
            <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($user['country']); ?>"
                required>

            <input type="submit" name="update_profile" value="Update Profile">
        </form>
    </div>
</body>

</html>