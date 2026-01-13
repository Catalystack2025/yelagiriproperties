<?php
function ensureBlueprintTable(mysqli $conn): void
{
    $conn->query("
        CREATE TABLE IF NOT EXISTS property_blueprints (
            id INT AUTO_INCREMENT PRIMARY KEY,
            property_id INT NOT NULL UNIQUE,
            original_path VARCHAR(255) DEFAULT NULL,
            annotated_path VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
}

function getBlueprint(mysqli $conn, int $propertyId): ?array
{
    $res = $conn->query("SELECT * FROM property_blueprints WHERE property_id={$propertyId} LIMIT 1");
    return ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
}
