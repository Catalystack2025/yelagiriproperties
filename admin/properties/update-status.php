<?php
include __DIR__ . '/../includes/auth-guard.php';
include __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

if ($id > 0 && in_array($status, ['Available', 'Sold', 'Blocked'], true)) {
    $stmt = $conn->prepare("UPDATE properties SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

header('Location: list.php');
exit;
