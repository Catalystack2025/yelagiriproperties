<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

/* ===== LOAD JSON ===== */
$jsonFile = __DIR__ . '/../frontend/data/content.json';

$data = file_exists($jsonFile)
  ? json_decode(file_get_contents($jsonFile), true)
  : [];

$hero = $data['hero'] ?? [
  'title' => '',
  'description' => '',
  'slides' => [],
  'interval' => 4000
];

/* ===== SAVE ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $hero['title'] = $_POST['title'];
  $hero['description'] = $_POST['description'];

  /* IMAGE UPLOAD (SLIDES) */
  if (!empty($_FILES['slides']['name'][0])) {

    $uploadDir = __DIR__ . '/../uploads/hero/';
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0777, true);
    }

    foreach ($_FILES['slides']['tmp_name'] as $i => $tmp) {

      [$width, $height] = getimagesize($tmp);

      /* STRICT SIZE CHECK */
      if ($width != 3648 || $height != 5472) {
        die('Image must be exactly 3648 × 5472 px');
      }

      $ext = pathinfo($_FILES['slides']['name'][$i], PATHINFO_EXTENSION);
      $fileName = 'hero-' . time() . '-' . $i . '.' . $ext;
      $target = $uploadDir . $fileName;

      if (move_uploaded_file($tmp, $target)) {
        $hero['slides'][] = 'uploads/hero/' . $fileName;
      }
    }
  }

  $data['hero'] = $hero;
  file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
}
?>

<div class="admin-layout">
  <main class="admin-main admin-main--wide">

    <div class="form-page">

      <header class="form-header">
        <h1>Hero Banner Edit</h1>
        <p>Edit Homepage Hero Title, Description, and Slider Images</p>
      </header>

      <form method="post" enctype="multipart/form-data" class="property-form">

        <!-- TEXT -->
        <section class="form-card">
          <h3>Hero Content</h3>

          <label>Title</label>
          <input type="text" name="title"
            value="<?= htmlspecialchars($hero['title']) ?>" required>

          <label>Description</label>
          <textarea name="description" rows="4" required><?= htmlspecialchars($hero['description']) ?></textarea>
        </section>

        <!-- SLIDES -->
        <section class="form-card">
          <h3>Hero Slides</h3>
          <p class="helper-text">
            Upload images in <strong>3648 × 5472 px</strong> only
          </p>

          <div class="upload-box upload-box--hero">
            <input type="file" name="slides[]" multiple accept="image/*">
            <p>Choose or drag hero images</p>
          </div>
        </section>

        <!-- ACTION -->
        <div class="form-actions">
          <button class="btn-primary">Save Hero Banner</button>
        </div>

      </form>

    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</div>
