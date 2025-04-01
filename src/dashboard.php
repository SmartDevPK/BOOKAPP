<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        .profile-container h2 {
            text-align: center;
        }

        .profile-container img {
            display: block;
            margin: 0 auto;
            border-radius: 50%;
            width: 150px;
            height: 150px;
        }

        .profile-container p {
            font-size: 18px;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="profile-container">
        <h2>User Profile</h2>
        <?php if (!empty($user['profile_picture'])): ?>
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            <!-- <?php else: ?>
            <img src="default-profile.jpg" alt="Default Profile Picture">
        <?php endif; ?> -->
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
        <p><strong>Country:</strong> <?php echo htmlspecialchars($user['country']); ?></p>
        <a href="edit.profile.php">Edit Profile</a> | <a href="logout.php">Logout</a>
    </div>
</body>

</html>