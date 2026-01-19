<?php
// Frontend Blog Listing Page
require_once __DIR__ . '/../admin/includes/db.php';

$sql = "
  SELECT slug, title, excerpt, image, published_date
  FROM blogs
  WHERE status = 1
  ORDER BY published_date DESC
";
$result = $conn->query($sql);
?>

<?php require_once __DIR__ . '/partials/header.php'; ?>

<main class="blog-page">
  <div class="blog-container">

    <div class="blog-header">
      <h1>Latest Blogs</h1>
      <p>Expert insights and property updates from Yelagiri Properties</p>
    </div>

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

  </div>
</main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

<style>
.blog-page {
  padding: 80px 0;
  background: #f7f8fa;
}

.blog-container {
  max-width: 1200px;
  margin: auto;
  padding: 0 20px;
}

.blog-header {
  text-align: center;
  margin-bottom: 50px;
}

.blog-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 30px;
}

.blog-card {
  background: #fff;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.08);
  cursor: pointer;
}

.blog-image {
  height: 220px;
  overflow: hidden;
}

.blog-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.blog-body {
  padding: 22px;
}

.blog-link {
  color: #00a300;
  font-weight: 600;
}
</style>
