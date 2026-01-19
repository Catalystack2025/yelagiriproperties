<?php
$host = "localhost";
$user = "u847031000_yelagiri";
$pass = "Yelagiri@2026";
$db   = "u847031000_yelagiri_prop";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}
?>
