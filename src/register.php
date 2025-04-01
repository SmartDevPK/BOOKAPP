<?php
// API endpoint to fetch all countries
$api_url = "https://restcountries.com/v3.1/all";

// Fetch data from the API
$response = file_get_contents($api_url);

if ($response === FALSE) {
    die("Failed to fetch data from the API.");
}

// Decode the JSON response
$countries = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Failed to decode JSON response.");
}

// Extract country names and codes
$country_data = [];
foreach ($countries as $country) {
    $country_name = $country['name']['common']; // Get the common name of the country
    $country_code = $country['cca2']; // Get the 2-letter country code (e.g., "US" for the United States)
    $country_data[] = [
        'name' => $country_name,
        'code' => $country_code
    ];
}

// Sort country data alphabetically by country name
usort($country_data, function ($a, $b) {
    return strcmp($a['name'], $b['name']);
});
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOOK APP FOR CHILDREN</title>
    <link rel="stylesheet" href="../css/main.css">


</head>

<body>
    <div class="container">
        <div class="header">
            <h1>BOOK APP FOR CHILDREN REGISTRATION PAGE</h1>
        </div>

        <form method="post" action="server.php">
            <div class="input-group">
                <label>NAME</label>
                <input type="text" name="name" placeholder="Name" required>
            </div>

            <div class="input-group">
                <label>EMAIL</label>
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-group">
                <label>PHONE NUMBER</label>
                <input type="tel" name="phone_number" placeholder="Phone Number" required>
            </div>

            <div class="input-group">
                <label>SELECT COUNTRY:</label>
                <select name="country" required>
                    <option value="" disabled selected>Select your country</option>
                    <?php
                    // Populate the dropdown with country names and codes
                    foreach ($country_data as $country) {
                        $country_name = $country['name'];
                        $country_code = $country['code'];
                        echo "<option value='$country_code'>$country_name ($country_code)</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="input-group">
                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="input-group">
                <button type="submit" class="btn" name="reg_user">Register</button>
            </div>

            <p>
                Already a member? <a href="login.php">Sign in</a>
            </p>
        </form>
    </div>
</body>

</html>