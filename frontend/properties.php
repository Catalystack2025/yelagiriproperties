<?php
// Load shared dataset
include './data/property-data.php';
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

    <!-- MAIN CONTENT -->
    <main class="container">

      

        <!-- LISTING PAGE -->
        <div id="listingPage" class="properties-margin">

            <!-- Filters -->
            <div class="filter-bar">
                <div class="filter-group">
                    <label class="filter-label">Location</label>
                    <select id="locationFilter" onchange="applyFilters()" class="filter-select">
                        <option value="all">All Locations</option>
                        <option value="Yelagiri">Yelagiri</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Property Type</label>
                    <select id="typeFilter" onchange="applyFilters()" class="filter-select">
                        <option value="all">All Types</option>
                        <option value="Plot">Plot</option>
                        <option value="Villa">Villa</option>
                        <option value="Home Stay">Home Stay</option>
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

    <!-- DATA Injected to JS -->
    <script>
        const data = <?php echo json_encode(["properties" => $properties], JSON_UNESCAPED_SLASHES); ?>;
    </script>

    <script src="./assets/js/script.js"></script>

</body>
</html>
