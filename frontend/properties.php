<?php
include __DIR__ . '/../admin/includes/db.php'; // DB connection

/* ========================
   FETCH ALL PROPERTIES
======================== */
$properties = [];
$sql = "SELECT * FROM properties ORDER BY id DESC";
$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {

    // Fetch main image (first uploaded image)
    $imgRes = $conn->query("SELECT image_path FROM property_images WHERE property_id={$row['id']} LIMIT 1");
    $imgRow = $imgRes->fetch_assoc();
    $row['main_image'] = $imgRow['image_path'] ?? null;

    // Fetch amenities
    $amens = [];
    $amenRes = $conn->query("
        SELECT a.name 
        FROM amenities a 
        JOIN property_amenities pa 
        ON pa.amenity_id = a.id
        WHERE pa.property_id = {$row['id']}
    ");
    while ($a = $amenRes->fetch_assoc()) {
        $amens[] = $a['name'];
    }
    $row['amenities'] = $amens;

    $properties[] = $row;

    
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties | Yelagiri Properties</title>

    <link rel="stylesheet" href="./assets/css/property.css">
</head>

<body>

    <!-- HEADER -->
    <?php include './partials/header.php'; ?>

    <main class="container">

        <!-- LISTING PAGE -->
        <div id="listingPage" class="properties-margin">

            <!-- Filters -->
            <div class="filter-bar">
                <div class="filter-group">
                    <label class="filter-label">Location</label>
                    <select id="locationFilter" onchange="applyFilters()" class="filter-select">
                        <option value="all">All Locations</option>

                        <?php
                        $locs = $conn->query("SELECT DISTINCT location FROM properties ORDER BY location ASC");
                        while ($l = $locs->fetch_assoc()) {
                            echo "<option value='{$l['location']}'>{$l['location']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Property Type</label>
                    <select id="typeFilter" onchange="applyFilters()" class="filter-select">
                        <option value="all">All Types</option>
                        <option value="Plot">Plot</option>
                        <option value="Villa">Villa</option>
                        <option value="Home Stays">Home Stays</option>
                    </select>
                </div>
            </div>

            <!-- PLOT SECTION -->
            <h2 id="plotSection" class="section-heading">All Plots</h2>
            <div id="plotGrid" class="property-grid"></div>

            <!-- VILLA SECTION -->
            <h2 id="villaSection" class="section-heading">All Villas</h2>
            <div id="villaGrid" class="property-grid"></div>

            <!-- HOMESTAY SECTION -->
            <h2 id="homestaySection" class="section-heading">All Home Stays</h2>
            <div id="homestayGrid" class="property-grid"></div>

        </div>

        <!-- DETAIL PAGE -->
        <div id="detailPage" class="hidden">
            <div id="detailContent"></div>
        </div>

    </main>

    <!-- FOOTER -->
    <?php include './partials/footer.php'; ?>

    <!-- Inject Dynamic Data to JS -->
    <script>
        const data = <?php echo json_encode(["properties" => $properties], JSON_UNESCAPED_SLASHES); ?>;
    </script>

    <script src="./assets/js/script.js"></script>

</body>
</html>
