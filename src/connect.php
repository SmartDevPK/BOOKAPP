<?php

$HOSTNAME = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'BooksAppForChildren';

// Establish a connection to the database
$con = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

// Check if the connection was successful
if ($con) {
    // echo "Connection successful";
} else {
    // If the connection failed, display the error
    die("Connection failed: " . mysqli_connect_error());
}

?>