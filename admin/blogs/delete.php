<?php
include __DIR__ . '/../includes/auth-guard.php';
include __DIR__ . '/../includes/db.php';

$id = intval($_POST['id'] ?? 0);

if ($id > 0) {
    $res = $conn->query("SELECT image FROM blogs WHERE id=$id");
    $row = $res && $res->num_rows ? $res->fetch_assoc() : null;

    if ($row && !empty($row['image'])) {
        $path = realpath(__DIR__ . '/../uploads/blogs');
        if ($path === false) $path = __DIR__ . '/../uploads/blogs';
        $file = $path . "/" . $row['image'];
        if (is_file($file)) @unlink($file);
    }

    $conn->query("DELETE FROM blogs WHERE id=$id");
}

header("Location: list.php");
exit;
