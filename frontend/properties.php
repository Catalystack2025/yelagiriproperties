<?php
// Property data in PHP array (clean + organized)
$properties = [
    [
        "id" => 1,
        "name" => "Athanavoor Heights",
        "type" => "Villa",
        "location" => "Yelagiri",
        "facing" => "North",
        "size" => "2500 sqft",
        "description" => "Luxurious villa located in the heart of Athanavoor...",
        "images" => [
            "https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=800",
            "https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800",
            "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800"
        ],
        "amenities" => ["Gated Community", "Security", "Clubhouse", "Park", "Water Storage", "Swimming Pool", "Jogging Track"]
    ],
    [
        "id" => 2,
        "name" => "Nilavoor Greens",
        "type" => "Plot",
        "location" => "Yelagiri",
        "facing" => "East",
        "size" => "1200 sqft",
        "description" => "Premium residential plots available in Nilavoor...",
        "images" => [
            "https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800",
            "https://images.unsplash.com/photo-1592595825315-724f113f3640?auto=format&fit=crop&w=800"
        ],
        "amenities" => ["Clear Title", "Fenced", "Blacktop Road", "Street Lights", "Drainage System"]
    ],
    [
        "id" => 3,
        "name" => "Skyview Residency",
        "type" => "Home Stay",
        "location" => "Yelagiri",
        "facing" => "West",
        "size" => "1100 sqft",
        "description" => "A modern 2BHK home stay offering panoramic views...",
        "images" => [
            "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800",
            "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=800"
        ],
        "amenities" => ["Lift", "CCTV", "Power Backup", "Gym", "Covered Parking", "Intercom Facility"]
    ],
    [
        "id" => 4,
        "name" => "Mangalam Retreat",
        "type" => "Villa",
        "location" => "Yelagiri",
        "facing" => "North",
        "size" => "2800 sqft",
        "description" => "An exquisite vacation villa in Mangalam...",
        "images" => [
            "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800",
            "https://images.unsplash.com/photo-1600566752355-35792bedcfea?auto=format&fit=crop&w=800"
        ],
        "amenities" => ["Private Pool", "Landscaped Garden", "Servant Room", "Solar Power", "Rain Water Harvesting"]
    ]
];
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

    <!-- Listing -->
    <main class="container">

        <div id="navBack" class="hidden">
            <button onclick="showListing()" class="back-btn">← Back to Listings</button>
        </div>

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

            <h2 class="section-heading">All Plots</h2>
            <div id="plotGrid" class="property-grid"></div>

            <h2 class="section-heading">All Villas</h2>
            <div id="villaGrid" class="property-grid"></div>

            <h2 class="section-heading">All Home Stays</h2>
            <div id="homestayGrid" class="property-grid"></div>
        </div>

        <!-- Detail Page -->
        <div id="detailPage" class="hidden">
            <div id="detailContent"></div>
        </div>

    </main>

    <!-- FOOTER -->
    <?php include './partials/footer.php'; ?>

    <!-- JS: property data injected into JS -->
    <script>
        const data = <?php echo json_encode(["properties" => $properties], JSON_UNESCAPED_SLASHES); ?>;
    </script>

   <script src="./assets/js/script.js"></script>


</body>
</html>
