<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include __DIR__ . '/../admin/includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>Yelagiri Properties | Premium Villas & Plots</title>

  <link rel="stylesheet" href="./assets/css/style.css" />
  <script src="./assets/js/content.js" defer></script>

  <style>
    .footer-logo {
      font-family: 'Ethnocentric', sans-serif;
      letter-spacing: 1.5px;
      font-size: 20px;
      color: #ffffff;
      text-transform: uppercase;
    }

    .header-logo {
      font-family: 'Ethnocentric', sans-serif;
      letter-spacing: 1px;
      line-height: 1;
      font-size: 28px;
      color: #00A300;
      text-transform: uppercase;
    }

    /* HERO SLIDER */
    .hero {
        position: relative;
        overflow: hidden;
        height: 90vh;
        display: flex;
        align-items: center;
    }

    .hero-slider {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
    }

    .hero-slide {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0;
        transition: opacity 1s ease-in-out;
    }

    .hero-slide.active {
        opacity: 1;
    }

    .view-all-wrap {
  text-align: center;
  margin-top: 40px;
}

  </style>

</head>
<body>

<!-- LOADER -->
<div id="loader">
  <div class="loader-content">
    <div class="loader-text">YELAGIRI PROPERTIES</div>
    <div class="loader-bar-container">
      <div class="loader-bar" id="loaderBar"></div>
    </div>
    <p style="color: rgba(255,255,255,0.4); font-size: 10px; letter-spacing: 4px;">
      PROPERTIES
    </p>
  </div>
</div>

<!-- HEADER -->
<?php include './partials/header.php'; ?>

<main>

<!-- ============================
     STATIC HERO SECTION
============================ -->
<section id="hero" class="hero">

  <!-- Slider Wrapper -->
  <div class="hero-slider">

      <!-- Premium Plots -->
      <div class="hero-slide active"
           style="background-image:url('./assets/images/plots.jpg');">
      </div>

      <!-- Luxury Villas -->
      <!-- <div class="hero-slide"
           style="background-image:url('./assets/images/villa.png');">
      </div> -->

      <!-- Mountain Homestays -->
      <!-- <div class="hero-slide"
           style="background-image:url('frontend/assets/images/homestay-premium.jpg');">
      </div> -->

  </div>

  <!-- Gradient Overlay -->
  <div class="hero-overlay"></div>

  <!-- Slide Content -->
  <div class="container hero-content">
    <h1>Premium Villas, Scenic Plots & Luxury Homestays</h1>
    <p>Experience peaceful hill-station living with curated properties surrounded by nature and breathtaking views.</p>
<br>
    <div class="hero-actions">
      <a href="properties.php" class="btn btn-primary">View Properties</a>
      <a href="contact.php" class="btn btn-outline">Book Free Site Visit</a>
    </div>
  </div>

</section>
<script>
document.addEventListener("DOMContentLoaded", function () {

  const slides = document.querySelectorAll(".hero-slide");
  let index = 0;
  const interval = 5000; // 5 sec

  function showSlide(i) {
    slides.forEach(slide => slide.classList.remove("active"));
    slides[i].classList.add("active");
  }

  showSlide(index);

  setInterval(() => {
    index = (index + 1) % slides.length;
    showSlide(index);
  }, interval);

});
</script>


<!-- FEATURED PROPERTIES -->
<?php
$properties = [];
$resProps = $conn->query("SELECT * FROM properties ORDER BY id DESC");
if ($resProps) {
    while ($row = $resProps->fetch_assoc()) {
        $imgRes = $conn->query("SELECT image_path FROM property_images WHERE property_id={$row['id']} ORDER BY id ASC LIMIT 1");
        $imgRow = $imgRes ? $imgRes->fetch_assoc() : null;
        $row['main_image'] = $imgRow['image_path'] ?? null;
        $properties[] = $row;
    }
}
shuffle($properties);
$featured = array_slice($properties, 0, 6);
?>
<!-- BLOGS SECTION -->
<?php
require_once __DIR__ . '/../admin/includes/db.php';

$sql = "
  SELECT slug, title, excerpt, image, published_date
  FROM blogs
  WHERE status = 1
  ORDER BY published_date DESC
  LIMIT 3
";
$result = $conn->query($sql);
?>


<section class="featured-properties-section">
    <div class="featured-container">

        <div class="section-title">
            <h2 class="ethno-font">Featured Properties</h2>
            <p>Discover our curated collection of plots, villas, and homestays across Yelagiri.</p>
        </div>

        <br>

        <div class="property-grid">
            <?php foreach ($featured as $p): ?>
                <?php $img = $p['main_image'] ? "../admin/uploads/" . $p['main_image'] : "./assets/images/no-image.jpg"; ?>

                <div class="property-card">
                    <div class="card-media">
                        <span class="type-badge"><?= htmlspecialchars($p['type']); ?></span>

                        <img src="<?= htmlspecialchars($img); ?>" alt="<?= htmlspecialchars($p['name']); ?>">

                        <div class="card-overlay">
                            <a href="property-details.php?id=<?= $p['id']; ?>" class="overlay-btn">View Details</a>
                        </div>
                    </div>

                    <div class="card-info">

                        <div class="location-row">
                            <svg class="loc-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"></path>
                            </svg>
                            <?= htmlspecialchars($p['location']); ?>
                        </div>

                        <h3 class="property-title"><?= htmlspecialchars($p['name']); ?></h3>

                        <div class="specs-grid">
                            <div class="spec-item">
                                <span class="spec-label">Area</span>
                                <span class="spec-val"><?= htmlspecialchars($p['size']); ?> SQFT</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Facing</span>
                                <span class="spec-val"><?= htmlspecialchars($p['facing']); ?></span>
                            </div>
                        </div>

                    </div>

                </div>

            <?php endforeach; ?>
        </div>

        <?php if (count($properties) > 6): ?>
        <div class="view-all-wrapper">
            <a href="./properties.php" class="view-all-btn">View All Properties</a>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- WHY CHOOSE US -->
<section class="section-padding why-choose">
  <div class="container">

    <div class="section-title">
      <h2>Why Choose Yelagiri Properties</h2>
      <p>
        Trusted hill-station real estate experts delivering legally verified,
        investment-ready properties in Yelagiri Hills.
      </p>
    </div>

    <div class="why-grid">
      <div class="why-card"><div class="why-icon">✓</div><h3>100% Legal Verification</h3><p>All our plots and villas are thoroughly verified.</p></div>
      <div class="why-card"><div class="why-icon">⛰️</div><h3>Prime Hilltop Locations</h3><p>Carefully selected scenic locations.</p></div>
      <div class="why-card"><div class="why-icon">🤝</div><h3>End-to-End Support</h3><p>From site visits to registration.</p></div>
      <div class="why-card"><div class="why-icon">📈</div><h3>High Investment Returns</h3><p>Perfect for long-term investment.</p></div>
      <div class="why-card"><div class="why-icon">🧭</div><h3>Local Market Expertise</h3><p>15+ years of experience.</p></div>
      <div class="why-card"><div class="why-icon">💰</div><h3>Transparent Pricing</h3><p>No hidden charges.</p></div>
    </div>

  </div>
        </br>
</section><!-- BLOG SECTION -->
<div class="container">
  <section id="blogListSection">
    <div class="section-title">
      <h1>Latest Blogs</h1>
      <p>Expert insights, guides, and updates.</p>
    </div>
<br>
    <div class="blog-grid">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($blog = $result->fetch_assoc()): ?>

          <div class="blog-card"
               onclick="window.location.href='blog-detail.php?slug=<?= htmlspecialchars($blog['slug']) ?>'">

            <div class="blog-image">
              <img src="../admin/<?= htmlspecialchars($blog['image']) ?>"
                   alt="<?= htmlspecialchars($blog['title']) ?>"
                   loading="lazy">
            </div>

            <div class="blog-body">
              <span class="blog-date">
                <?= date('d M Y', strtotime($blog['published_date'])) ?>
              </span>

              <h3><?= htmlspecialchars($blog['title']) ?></h3>
              <p><?= htmlspecialchars($blog['excerpt']) ?></p>

              <span class="blog-link">Read More →</span>
            </div>

          </div>

        <?php endwhile; ?>
      <?php else: ?>
        <p>No blogs available.</p>
      <?php endif; ?>
    </div>

    <div class="view-all-wrap">
      <a href="blog.php" class="view-all-btn">View All Blogs</a>
    </div>

  </section>
</div>
<br>

<!-- CTA FORM -->
<section class="section-padding cta-section">
  <div class="container">

    <div class="cta-wrapper">

      <div class="cta-content">
        <span class="sub-heading">Free Consultation</span>
        <h2>Looking to Invest in Yelagiri?</h2>
        <p>Get expert guidance on verified plots and villas.</p>
      </div>

      <form class="cta-form" method="post" action="submit-enquiry.php">
        <div class="form-group"><label>Full Name</label><input type="text" name="name" required /></div>
        <div class="form-group"><label>Mobile Number</label><input type="tel" name="phone" required /></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" /></div>
        <div class="form-group"><label>Interested In</label>
            <select name="interest">
                <option value="">Select Property Type</option>
                <option value="Plots">Plots</option>
                <option value="Villas">Villas</option>
                <option value="Weekend Home">Weekend Home</option>
                <option value="Investment">Investment</option>
            </select>
        </div>
        <div class="form-group"><label>Message</label><textarea name="message" rows="3"></textarea></div>
        <button type="submit" class="btn btn-primary btn-full">Request Free Call Back</button>
      </form>

    </div>

  </div>
</section>

</main>

<?php include './partials/footer.php'; ?>

<script src="assets/js/script.js"></script>

</body>
</html>
