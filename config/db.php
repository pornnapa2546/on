<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "on_the_way"; // ชื่อ database ใน phpMyAdmin

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}
?>
