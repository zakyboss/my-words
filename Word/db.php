<?php
$servername = "localhost";
$username = "postgres";
$password = "";
$dbname = "word";

// Create connection string
$conn_string = "host=$servername dbname=$dbname user=$username password=$password";

// Create connection
$conn = pg_connect($conn_string);

// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
} else {
    echo "Connected successfully!";
}
?>
