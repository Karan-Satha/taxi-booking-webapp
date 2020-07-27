<?php
// Connect to database
$serverName = "localhost";
$userName = "karan";
$password = "**************";
$databaseName = "taxi_db";

$conn = new mysqli($serverName, $userName, $password, $databaseName);

// Check connection
if ($conn->connect_error) {
    die("connection error: " . $conn->connect_error);
}
