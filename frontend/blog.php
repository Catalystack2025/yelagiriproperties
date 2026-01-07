<?php
// Blog Page
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title id="pageTitle">Blog | Yelagiri Properties</title>
  <meta name="description" id="pageDesc" content="Latest blogs from Yelagiri Properties">

  <link rel="stylesheet" href="assets/css/style.css">

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
  <?php include 'partials/header.php'; ?>

  <br>

  <main class="section-padding">
    <div class="container">

      <!-- BLOG LIST -->
      <section id="blogListSection">
        <div class="section-title">
          <h1>Latest Blogs</h1>
          <p>Expert insights, guides, and updates from Yelagiri Properties.</p>
        </div>

        <div class="blog-grid" id="blogGrid"></div>

      </section>

    </div>
  </main>

  <!-- FOOTER -->
  <?php include 'partials/footer.php'; ?>


  <!-- BLOG DATA -->
  <script src="assets/js/content.js"></script>

  <!-- COMMON JS -->
  <script src="assets/js/script.js"></script>

</body>

</html>
