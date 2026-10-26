<!-- Write a PHP program to establish a connection between PHP and MySQL and display an
appropriate message indicating whether the connection was successful. -->

<?php
$server = "localhost";
$username = "root";
$password = "";
$dbName = "phplab";

$conn = mysqli_connect($server, $username, $password, $dbName);

if (!$conn) {
    $conn = mysqli_connect($server, $username, $password);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $createDb = "CREATE DATABASE IF NOT EXISTS `$dbName`";
    if (!mysqli_query($conn, $createDb)) {
        die("Database creation failed: " . mysqli_error($conn));
    }

    $conn = mysqli_connect($server, $username, $password, $dbName);
    if (!$conn) {
        die("Failed to connect to database '$dbName': " . mysqli_connect_error());
    }
}

mysqli_set_charset($conn, "utf8mb4");
echo "Connected successfully to database: $dbName";

mysqli_close($conn);
