<?php
include '../includes/db.php';
include '../includes/auth-guard.php';

$id = $_GET['id'];
$conn->query("DELETE FROM blogs WHERE id=$id");

header("Location: list.php");
