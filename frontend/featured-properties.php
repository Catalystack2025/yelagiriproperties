<?php
// Property Data Array
$properties = [
    [
        "id" => 1,
        "name" => "Athanavoor Heights",
        "type" => "Villa",
        "location" => "Yelagiri",
        "area" => "2500 sqft",
        "facing" => "North",
        "image" => "https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=800"
    ],
    [
        "id" => 2,
        "name" => "Nilavoor Greens",
        "type" => "Plot",
        "location" => "Yelagiri",
        "area" => "1200 sqft",
        "facing" => "East",
        "image" => "https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800"
    ],
    [
        "id" => 3,
        "name" => "Skyview Residency",
        "type" => "Home Stay",
        "location" => "Yelagiri",
        "area" => "1100 sqft",
        "facing" => "West",
        "image" => "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800"
    ],
    [
        "id" => 4,
        "name" => "Mangalam Retreat",
        "type" => "Villa",
        "location" => "Yelagiri",
        "area" => "2800 sqft",
        "facing" => "North",
        "image" => "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800"
    ],
    [
        "id" => 5,
        "name" => "Golden Valley Plots",
        "type" => "Plot",
        "location" => "Yelagiri",
        "area" => "1500 sqft",
        "facing" => "South",
        "image" => "https://images.unsplash.com/photo-1592595825315-724f113f3640?auto=format&fit=crop&w=800"
    ],
    [
        "id" => 6,
        "name" => "Sunrise Homestay",
        "type" => "Home Stay",
        "location" => "Yelagiri",
        "area" => "950 sqft",
        "facing" => "East",
        "image" => "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=800"
    ],
    [
        "id" => 7,
        "name" => "The Pine Estates",
        "type" => "Villa",
        "location" => "Yelagiri",
        "area" => "2200 sqft",
        "facing" => "West",
        "image" => "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800"
    ],
    [
        "id" => 8,
        "name" => "Emerald Meadows",
        "type" => "Plot",
        "location" => "Yelagiri",
        "area" => "1800 sqft",
        "facing" => "North",
        "image" => "https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800"
    ],
    [
        "id" => 9,
        "name" => "Hilltop Sanctuary",
        "type" => "Home Stay",
        "location" => "Yelagiri",
        "area" => "1300 sqft",
        "facing" => "East",
        "image" => "https://images.unsplash.com/photo-1449156001446-5076e7859061?auto=format&fit=crop&w=800"
    ]
];
?>

<style>
:root {
  --primary: #2e7d32;
  --primary-dark: #1b3e5e;
  --accent: #ffa000;
  --text-dark: #222;
  --text-muted: #666;
  --white: #ffffff;
  --bg-light: #f9f9f9;
  --radius: 8px;
  --shadow: 0 4px 20px rgba(0,0,0,0.06);
}

.featured-properties-section {
  padding: 80px 0;
  background: var(--bg-light);
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.featured-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.section-header {
  text-align: center;
  margin-bottom: 60px;
}

.section-header h2 {
  font-family: 'Ethnocentric', sans-serif;
  font-size: 1.8rem;
  color: var(--primary-dark);
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 15px;
}

.accent-line {
  width: 50px;
  height: 4px;
  background: var(--accent);
  margin: 0 auto 20px;
  border-radius: 2px;
}

.section-header p {
  color: var(--text-muted);
  max-width: 600px;
  margin: 0 auto;
  font-size: 1rem;
  line-height: 1.6;
}

/* Grid Layout */
.property-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
}

/* Property Card */
.property-card {
  background: var(--white);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid rgba(0,0,0,0.03);
  display: flex;
  flex-direction: column;
  height: 100%;
}

.property-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.card-media {
  position: relative;
  height: 220px;
  overflow: hidden;
}

.card-media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.property-card:hover .card-media img {
  transform: scale(1.1);
}

.type-badge {
  position: absolute;
  top: 15px;
  left: 15px;
  background: var(--primary);
  color: #fff;
  padding: 4px 12px;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  border-radius: 4px;
  z-index: 2;
  letter-spacing: 0.5px;
}

.card-overlay {
  position: absolute;
  inset: 0;
  background: rgba(27, 62, 94, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 1;
}

.property-card:hover .card-overlay {
  opacity: 1;
}

.overlay-btn {
  color: #fff;
  border: 1px solid #fff;
  padding: 10px 20px;
  text-transform: uppercase;
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 1px;
  text-decoration: none;
  transition: all 0.3s ease;
}

.overlay-btn:hover {
  background: #fff;
  color: var(--primary-dark);
}

.card-info {
  padding: 20px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.location-row {
  display: flex;
  align-items: center;
  font-size: 0.8rem;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 8px;
}

.loc-icon {
  width: 14px;
  height: 14px;
  margin-right: 5px;
  color: var(--accent);
}

.property-title {
  color: var(--primary-dark);
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 15px;
  line-height: 1.3;
}

.specs-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  padding-top: 15px;
  border-top: 1px solid #f0f0f0;
  margin-top: auto;
}

.spec-item {
  display: flex;
  flex-direction: column;
}

.spec-label {
  font-size: 0.65rem;
  text-transform: uppercase;
  color: var(--text-muted);
  margin-bottom: 2px;
}

.spec-val {
  font-size: 0.9rem;
  color: var(--primary-dark);
  font-weight: 600;
}

/* View All Link */
.view-all-wrapper {
  text-align: center;
  margin-top: 60px;
}

.view-all-btn {
  display: inline-block;
  padding: 14px 40px;
  background: var(--primary-dark);
  color: var(--white);
  border-radius: 4px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  font-size: 0.9rem;
  text-decoration: none;
  transition: 0.3s ease;
  border: 2px solid var(--primary-dark);
}

.view-all-btn:hover {
  background: transparent;
  color: var(--primary-dark);
}

/* Responsive */
@media (max-width: 1024px) {
  .property-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
}

@media (max-width: 650px) {
  .property-grid { grid-template-columns: 1fr; }
  .section-header h2 { font-size: 1.5rem; }
}
</style>

<section class="featured-properties-section">
    <div class="featured-container">
        <div class="section-header">
            <h2 class="ethno-font">Featured Properties</h2>
            <div class="accent-line"></div>
            <p>Discover our curated collection of plots, villas, and homestays across the beautiful landscape of Yelagiri.</p>
        </div>
        
        <div class="property-grid">
            <?php foreach ($properties as $p): ?>
            <div class="property-card">
                <div class="card-media">
                    <span class="type-badge"><?php echo htmlspecialchars($p['type']); ?></span>
                    <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                    <div class="card-overlay">
                        <a href="property-details.php?id=<?php echo $p['id']; ?>" class="overlay-btn">View Details</a>
                    </div>
                </div>
                <div class="card-info">
                    <div class="location-row">
                        <svg class="loc-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        <?php echo htmlspecialchars($p['location']); ?>
                    </div>
                    <h3 class="property-title"><?php echo htmlspecialchars($p['name']); ?></h3>
                    <div class="specs-grid">
                        <div class="spec-item">
                            <span class="spec-label">Area</span>
                            <span class="spec-val"><?php echo htmlspecialchars($p['area']); ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Facing</span>
                            <span class="spec-val"><?php echo htmlspecialchars($p['facing']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="view-all-wrapper">
            <a href="properties.php" class="view-all-btn">Explore All Properties</a>
        </div>
    </div>
</section>