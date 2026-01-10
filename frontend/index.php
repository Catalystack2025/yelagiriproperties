<?php

// Index Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>Yelagiri Properties | Premium Villas & Plots</title>

  <!-- CSS -->
  <link rel="stylesheet" href="./assets/css/style.css" />
  <script src="./assets/js/content.js"></script>

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


</style>

</head>
<body>

<!-- Loader -->
<div id="loader">
  <div class="loader-content">
    <div class="loader-text">YELAGIRI PROPERTIES</div>
    <div class="loader-bar-container">
      <div class="loader-bar" id="loaderBar"></div>
    </div>
    <p style="color: rgba(255,255,255,0.4); font-size: 10px; letter-spacing: 4px; margin-top: 15px; font-family: sans-serif;">
      PROPERTIES
    </p>
  </div>
</div>

<!-- HEADER -->
<?php include './partials/header.php'; ?>

<main>

<!-- HERO SECTION -->
<section id="hero" class="hero">
  <div class="container hero-content">
    <h1 id="heroTitle"></h1>
    <p id="heroDescription"></p>

    <br>

    
    <div class="hero-actions">
      <a href="properties.php" id="heroPrimaryBtn" class="btn btn-primary">
        View Properties
      </a>

      <a href="contact.php" id="heroSecondaryBtn" class="btn btn-outline">
        Book Free Site Visit
      </a>
    </div>
  </div>
</section>
<?php include './data/property-data.php'; ?>

<section class="featured-properties-section">
    <div class="featured-container">

        <div class="section-title">
            <h2 class="ethno-font">Featured Properties</h2>
           
            <p>Discover our curated collection of plots, villas, and homestays across Yelagiri.</p>
        </div>
        <br>

        <?php
            // Shuffle and pick any 6 properties
            $featured = $properties;
            shuffle($featured);
            $featured = array_slice($featured, 0, 6);
        ?>

        <div class="property-grid">
            <?php foreach ($featured as $p): ?>
                <?php $img = $p['images'][0]; ?>

                <div class="property-card">

                    <!-- IMAGE BLOCK -->
                    <div class="card-media">
                        <span class="type-badge"><?= htmlspecialchars($p['type']); ?></span>

                        <img src="<?= htmlspecialchars($img); ?>" 
                             alt="<?= htmlspecialchars($p['name']); ?>">

                        <div class="card-overlay">
                            <!-- FIXED: Redirects DIRECTLY to property-details.php -->
                            <a href="property-details.php?id=<?= $p['id']; ?>" class="overlay-btn">
    View Details
</a>

                        </div>
                    </div>

                    <!-- CONTENT BLOCK -->
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
                                <span class="spec-val"><?= htmlspecialchars($p['size']); ?></span>
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

        <!-- SHOW BUTTON ONLY IF MORE THAN 6 PROPERTIES -->
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

      <div class="why-card">
        <div class="why-icon">✓</div>
        <h3>100% Legal Verification</h3>
        <p>All our plots and villas are thoroughly verified with clear titles, DTCP approvals, and transparent documentation.</p>
      </div>

      <div class="why-card">
        <div class="why-icon">⛰️</div>
        <h3>Prime Hilltop Locations</h3>
        <p>Carefully selected scenic locations offering peaceful living, fresh air, and strong appreciation value.</p>
      </div>

      <div class="why-card">
        <div class="why-icon">🤝</div>
        <h3>End-to-End Support</h3>
        <p>From site visits to registration, our team supports you for a smooth buying experience.</p>
      </div>

      <div class="why-card">
        <div class="why-icon">📈</div>
        <h3>High Investment Returns</h3>
        <p>Yelagiri’s growing demand makes our properties ideal for lifestyle and long-term investment.</p>
      </div>

      <div class="why-card">
        <div class="why-icon">🧭</div>
        <h3>Local Market Expertise</h3>
        <p>15+ years of experience understanding the land, trends, and growth areas.</p>
      </div>

      <div class="why-card">
        <div class="why-icon">💰</div>
        <h3>Transparent Pricing</h3>
        <p>No hidden charges. Clear and honest pricing.</p>
      </div>

    </div>

  </div>
</section>

<br>

<div class="container">
  <!-- BLOG SECTION -->
  <section id="blogListSection">
    <div class="section-title">
      <h1>Latest Blogs</h1>
      <p>Expert insights, guides, and updates.</p>
    </div>

    <div class="blog-grid" id="blogGrid"></div>
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
        <p>
          Get expert guidance on verified plots and villas.
          We help you choose the right property for your needs.
        </p>

        <ul class="cta-points">
          <li>✓ 100% Legal Verification</li>
          <li>✓ Prime Hilltop Locations</li>
          <li>✓ Transparent Pricing</li>
        </ul>
      </div>

      <form class="cta-form" method="post" action="submit-enquiry.php">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="name" required placeholder="Enter your name" />
        </div>

        <div class="form-group">
          <label>Mobile Number</label>
          <input type="tel" name="phone" required placeholder="+91 XXXXX XXXXX" />
        </div>

        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="you@example.com" />
        </div>

        <div class="form-group">
          <label>Interested In</label>
          <select name="interest">
            <option value="">Select Property Type</option>
            <option value="Plots">Plots</option>
            <option value="Villas">Villas</option>
            <option value="Weekend Home">Weekend Home</option>
            <option value="Investment">Investment</option>
          </select>
        </div>

        <div class="form-group">
          <label>Message</label>
          <textarea name="message" rows="3" placeholder="Your requirement..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-full">Request Free Call Back</button>
      </form>

    </div>

  </div>
</section>

</main>

<!-- FOOTER -->
<?php include './partials/footer.php'; ?>

<script src="assets/js/script.js"></script>

</body>
</html>
