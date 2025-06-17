<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "edoc";
$port = 3307; // 👈 Add this line

$database = new mysqli($servername, $username, $password, $dbname, $port); // 👈 Pass port here

if ($database->connect_error) {
    die("Connection failed: " . $database->connect_error);
}
?>
