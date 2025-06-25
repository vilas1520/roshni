<?php
$servername = "localhost";
$username = "root";      // adjust if different
$password = "";          // adjust if different
$database = "user";   // change to your database name

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
