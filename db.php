<?php
$host = 'localhost'; // MySQL host
$user = 'root';      // MySQL username (default is 'root' for XAMPP)
$password = '';      // MySQL password (default is empty for XAMPP)
$database = 'mentors'; // Your database name

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>
