<?php
include __DIR__ . '/../admin/includes/db.php';
include __DIR__ . '/../admin/includes/blueprint-helpers.php';

ensureBlueprintTable($conn);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    http_response_code(404);
    echo "<div style='padding:100px 20px; text-align:center; font-family:sans-serif;'>
            <h2 style='color:#333;'>Property not found</h2>
            <p style='color:#666;'>Invalid property id.</p>
            <a href='properties.php' style='color:#2e7d32; font-weight:bold;'>Return to Listings</a>
          </div>";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM properties WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$propertyRes = $stmt->get_result();
$property = $propertyRes && $propertyRes->num_rows ? $propertyRes->fetch_assoc() : null;

if (!$property) {
    http_response_code(404);
    echo "<div style='padding:100px 20px; text-align:center; font-family:sans-serif;'>
            <h2 style='color:#333;'>Property not found</h2>
            <p style='color:#666;'>The property ID you requested ($id) does not exist.</p>
            <a href='properties.php' style='color:#2e7d32; font-weight:bold;'>Return to Listings</a>
          </div>";
    exit;
}

$images = [];
$imgRes = $conn->query("SELECT image_path FROM property_images WHERE property_id=$id ORDER BY id ASC");
if ($imgRes) {
    while ($row = $imgRes->fetch_assoc()) {
        $images[] = "../admin/uploads/" . $row['image_path'];
    }
}
if (empty($images)) {
    $images[] = "./assets/images/no-image.jpg";
}

$amenities = [];
$amenRes = $conn->query("
    SELECT a.name 
    FROM amenities a 
    JOIN property_amenities pa ON pa.amenity_id = a.id
    WHERE pa.property_id = $id
");
if ($amenRes) {
    while ($row = $amenRes->fetch_assoc()) {
        $amenities[] = $row['name'];
    }
}

$blueprint = getBlueprint($conn, $id);
$blueprintImg = $blueprint && !empty($blueprint['annotated_path'])
    ? "../admin/uploads/blueprints/" . $blueprint['annotated_path']
    : null;

function getIcon($name) {
    $name = strtolower($name);
    if (strpos($name, 'security') !== false) return 'fas fa-shield-alt';
    if (strpos($name, 'water') !== false) return 'fas fa-faucet';
    if (strpos($name, 'road') !== false) return 'fas fa-road';
    if (strpos($name, 'power') !== false) return 'fas fa-bolt';
    if (strpos($name, 'park') !== false || strpos($name, 'garden') !== false) return 'fas fa-leaf';
    return 'fas fa-check-circle';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['name']); ?> | Details</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- External CSS -->
    <link rel="stylesheet" href="./assets/css/style.css">
    
    <style>
        .property-details-section { padding-top: 90px; padding-bottom: 60px; background: var(--bg); }
        .back-link { text-decoration: none; color: var(--primary); font-weight: 700; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 25px; transition: 0.3s; }
        .back-link:hover { transform: translateX(-5px); }
        .layout-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 30px; }
        .gallery-main { width: 100%; aspect-ratio: 16/9; border-radius: 12px; overflow: hidden; background: #ddd; margin-bottom: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .gallery-main img { width: 100%; height: 100%; object-fit: cover; transition: opacity 0.3s ease; }
        .thumbs { display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto; padding-bottom: 5px; }
        .thumb { flex: 0 0 80px; height: 80px; border-radius: 8px; cursor: pointer; overflow: hidden; border: 2px solid transparent; transition: 0.3s; }
        .thumb.active { border-color: var(--primary); opacity: 1; }
        .thumb:not(.active) { opacity: 0.7; }
        .thumb img { width: 100%; height: 100%; object-fit: cover; }
        .card { background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #eee; margin-bottom: 20px; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 25px 0; padding: 25px 0; border-top: 1px solid #eee; border-bottom: 1px solid #eee; }
       
        .stat-label { font-size: 11px; color: #888; text-transform: uppercase; display: block; margin-bottom: 5px; letter-spacing: 0.5px; }
        .stat-value { font-weight: 700; font-size: 16px; color: var(--dark); }
        .amenity-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; margin-top: 15px; }
        .amenity { background: #f9fbf9; padding: 12px 15px; border-radius: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; border: 1px solid #edf2ed; }
        .amenity i { color: var(--primary); width: 20px; text-align: center; }
        .blueprint-box { margin-top: 24px; padding: 16px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .blueprint-box img { width: 100%; border-radius: 10px; display: block; }
        .inquiry-form input, .inquiry-form textarea { width: 100%; padding: 14px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 14px; }
        .inquiry-form input:focus, .inquiry-form textarea:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1); }
        .btn-send { width: 100%; padding: 16px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.3s; font-size: 16px; }
        .btn-send:hover { background: #1b5e20; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3); }
        .sticky-col { position: sticky; top: 100px; }
        .stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    background: #f9f9f9;
    border-radius: 10px;
}

.stat-icon {
    font-size: 22px;
    color: #2c7be5;
    min-width: 28px;
    text-align: center;
}



        @media (max-width: 991px) { .layout-grid { grid-template-columns: 1fr; } .sticky-col { position: static; } }
    </style>
</head>
<body>

    <!-- HEADER -->
    <?php include 'partials/header.php'; ?>

    <section class="property-details-section">
        <div class="container">
            <a href="properties.php" class="back-link">
                <i class="fas fa-arrow-left"></i> BACK TO PROPERTIES
            </a>

            <div class="layout-grid">
                <!-- Left Column: Gallery & Info -->
                <div class="left-col">
                    <div class="gallery-main">
                        <img id="mainImg" src="<?php echo htmlspecialchars($images[0]); ?>" alt="<?php echo htmlspecialchars($property['name']); ?>">
                    </div>
                    
                    <div class="thumbs">
                        <?php foreach ($images as $index => $img): ?>
                            <div class="thumb <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeImg(this, '<?php echo htmlspecialchars($img); ?>')">
                                <img src="<?php echo htmlspecialchars($img); ?>" alt="Thumbnail">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="card">
                        <h1 style="color: var(--dark); margin: 0 0 8px 0; font-size: 32px;"><?php echo htmlspecialchars($property['name']); ?></h1>
                        <p style="color: #666; margin-bottom: 25px; font-size: 16px;">
                            <i class="fas fa-map-marker-alt" style="color: #ef4444; margin-right: 5px;"></i> 
                            <?php echo htmlspecialchars($property['location']); ?>
                        </p>
<div class="stats">
    <div class="stat-item">
        <span class="stat-icon"><i class="fa-solid fa-ruler-combined"></i></span>
        <div>
            <span class="stat-label">Total Area</span>
            <span class="stat-value"><?php echo htmlspecialchars($property['size']); ?> SQFT</span>
        </div>
    </div>

    <div class="stat-item">
        <span class="stat-icon"><i class="fa-solid fa-up-right-and-down-left-from-center"></i></span>
        <div>
            <span class="stat-label">Dimensions</span>
            <span class="stat-value"><?php echo htmlspecialchars($property['dimensions']); ?></span>
        </div>
    </div>

    <div class="stat-item">
        <span class="stat-icon"><i class="fa-solid fa-compass"></i></span>
        <div>
            <span class="stat-label">Facing</span>
            <span class="stat-value"><?php echo htmlspecialchars($property['facing']); ?></span>
        </div>
    </div>

    <div class="stat-item">
        <span class="stat-icon"><i class="fa-solid fa-house"></i></span>
        <div>
            <span class="stat-label">Property Type</span>
            <span class="stat-value"><?php echo htmlspecialchars($property['type']); ?></span>
        </div>
    </div>
</div>


                        <h3 style="color: var(--dark); margin: 30px 0 15px 0;">Property Description</h3>
                        <p style="line-height: 1.8; color: #555; font-size: 15px;">
                            <?php echo nl2br(htmlspecialchars($property['description'])); ?>
                        </p>

                        <h3 style="color: var(--dark); margin: 35px 0 15px 0;">Premium Amenities</h3>
                        <div class="amenity-list">
                            <?php foreach ($amenities as $amenity): ?>
                                <div class="amenity">
                                    <i class="<?php echo getIcon($amenity); ?>"></i>
                                    <?php echo htmlspecialchars($amenity); ?>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($amenities)): ?>
                                <div class="amenity" style="color:#667085;">No amenities listed.</div>
                            <?php endif; ?>
                        </div>

                        <?php if ($blueprintImg): ?>
                            <div class="blueprint-box">
                                <h3 style="margin-bottom:12px;">Plot Blueprint</h3>
                                <img src="<?php echo htmlspecialchars($blueprintImg); ?>" alt="Blueprint for <?php echo htmlspecialchars($property['name']); ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column: Inquiry Form -->
                <div class="right-col">
                    <div class="card sticky-col">
                        <h3 style="margin: 0 0 20px 0; text-align: center; color: var(--dark); font-size: 22px;">Enquire Now</h3>
                        <form class="inquiry-form" action="submit-enquiry.php" method="POST">
                            <input type="hidden" name="property_id" value="<?php echo $id; ?>">
                            <input type="text" name="name" placeholder="Your Full Name" required>
                            <input type="tel" name="phone" placeholder="Mobile Number" required>
                            <input type="email" name="email" placeholder="Email Address (Optional)">
                            <textarea name="message" rows="5">I am interested in <?php echo htmlspecialchars($property['name']); ?>. Please share more details and pricing.</textarea>
                            <button type="submit" class="btn-send">Request Callback</button>
                        </form>
                        <div style="margin-top: 20px; padding: 15px; background: #fff9db; border-radius: 8px; border: 1px solid #ffe066;">
                            <p style="font-size: 13px; color: #856404; margin: 0; line-height: 1.5;">
                                <i class="fas fa-clock"></i> <strong>Quick Response:</strong> Our sales team usually responds within 2 hours during business days.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include 'partials/footer.php'; ?>

    <script>
        function changeImg(el, src) {
            const mainImg = document.getElementById('mainImg');
            mainImg.style.opacity = '0';
            
            setTimeout(() => {
                mainImg.src = src;
                mainImg.style.opacity = '1';
                
                document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
                el.classList.add('active');
            }, 200);
        }
    </script>

    <!-- Project Scripts -->
    <script src="assets/js/content.js"></script>
    <script src="assets/js/script.js"></script>

</body>
</html>
