<?php
include __DIR__ . '/../includes/auth-guard.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/blueprint-helpers.php';

ensureBlueprintTable($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: list.php');
    exit;
}

// Remove gallery images from disk
$imgRes = $conn->query("SELECT image_path FROM property_images WHERE property_id=$id");

$uploadDir = realpath(__DIR__ . '/../uploads');
if ($uploadDir === false) {
    $uploadDir = __DIR__ . '/../uploads';
}

if ($imgRes) {
    while ($row = $imgRes->fetch_assoc()) {
        $path = $uploadDir . "/" . $row['image_path'];
        if (is_file($path)) {
            @unlink($path);
        }
    }
}

// Remove blueprint files from disk
$bpRes = $conn->query("SELECT annotated_path, original_path FROM property_blueprints WHERE property_id=$id LIMIT 1");
$blueprintDir = realpath(__DIR__ . '/../uploads/blueprints');
if ($blueprintDir === false) {
    $blueprintDir = __DIR__ . '/../uploads/blueprints';
}

if ($bpRes && $bpRes->num_rows > 0) {
    $bp = $bpRes->fetch_assoc();

    if (!empty($bp['annotated_path'])) {
        $annotatedPath = $blueprintDir . "/" . $bp['annotated_path'];
        if (is_file($annotatedPath)) {
            @unlink($annotatedPath);
        }
    }

    if (!empty($bp['original_path'])) {
        $originalPath = $blueprintDir . "/" . $bp['original_path'];
        if (is_file($originalPath)) {
            @unlink($originalPath);
        }
    }
}

// Remove DB records
$conn->query("DELETE FROM property_images WHERE property_id=$id");
$conn->query("DELETE FROM property_amenities WHERE property_id=$id");
$conn->query("DELETE FROM property_blueprints WHERE property_id=$id");
$conn->query("DELETE FROM properties WHERE id=$id");

header('Location: list.php');
exit;
