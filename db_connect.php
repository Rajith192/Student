<?php
$host = "localhost";      // Your DB host
$user = "root";           // Your DB username
$pass = "";               // Your DB password
$db   = "school_db";      // Your DB name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>