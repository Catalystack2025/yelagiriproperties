<?php
// ===============================
// Frontend Blog Detail Page
// ===============================

require_once __DIR__ . '/../admin/includes/db.php';

$slug = $_GET['slug'] ?? '';

$stmt = $conn->prepare("
  SELECT title, excerpt, content, image, published_date
  FROM blogs
  WHERE slug = ? AND status = 1
  LIMIT 1
");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
  http_response_code(404);
  echo "Blog not found";
  exit;
}

$blog = $result->fetch_assoc();
?>

<?php require_once __DIR__ . '/partials/header.php'; ?>

<main class="blog-detail-page">
  <div class="blog-detail-container">

    <article class="blog-detail-card">

      <!-- Image -->
      <div class="blog-detail-image">
        <img
          src="../admin/<?= htmlspecialchars($blog['image']) ?>"
          alt="<?= htmlspecialchars($blog['title']) ?>"
          loading="lazy">
      </div>

      <!-- Content -->
      <div class="blog-detail-body">

        <span class="blog-detail-date">
          <?= date('d M Y', strtotime($blog['published_date'])) ?>
        </span>

        <h1><?= htmlspecialchars($blog['title']) ?></h1>

        <div class="blog-detail-content">
          <?= $blog['content'] ?>
        </div>

        <a href="blog.php" class="blog-back-link">← Back to Blogs</a>

      </div>

    </article>

  </div>
</main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

<style>
.blog-detail-page {
  padding: 100px 0;
  background: #f7f8fa;
}

.blog-detail-container {
  max-width: 900px;
  margin: auto;
  padding: 0 20px;
}

.blog-detail-card {
  background: #ffffff;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 14px 40px rgba(0,0,0,0.12);
}

.blog-detail-image {
  height: 420px;
  overflow: hidden;
}

.blog-detail-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.blog-detail-body {
  padding: 32px;
}

.blog-detail-date {
  font-size: 14px;
  color: #777;
  margin-bottom: 10px;
  display: block;
}

.blog-detail-body h1 {
  font-size: 34px;
  margin-bottom: 22px;
}

.blog-detail-content p {
  font-size: 16px;
  line-height: 1.9;
  margin-bottom: 18px;
}

.blog-back-link {
  margin-top: 30px;
  display: inline-block;
  font-weight: 600;
  color: #00a300;
  text-decoration: none;
}
</style>
