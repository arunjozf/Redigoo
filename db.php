<?php
$host = "localhost";          // Database server host (e.g., "localhost")
$user = "root";               // Database username
$password = "";               // Database password
$dbname = "redigoo";          // Your database name

// Create the database connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
