<?php
include __DIR__ . '/../admin/includes/db.php';

// Ensure table
$conn->query("
CREATE TABLE IF NOT EXISTS enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    message TEXT,
    property_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');
$propertyId = isset($_POST['property_id']) ? intval($_POST['property_id']) : null;

if ($name && $phone) {
    $stmt = $conn->prepare("INSERT INTO enquiries (name, phone, email, message, property_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $name, $phone, $email, $message, $propertyId);
    $stmt->execute();
    $stmt->close();
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/yelagiriproperties/frontend/index.php'));
exit;
