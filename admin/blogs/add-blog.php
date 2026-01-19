<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth-guard.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Check duplicate slug
  $check = $conn->prepare("SELECT id FROM blogs WHERE slug = ?");
  $check->bind_param("s", $_POST['slug']);
  $check->execute();

  if ($check->get_result()->num_rows > 0) {
    $error = "Slug already exists. Please use a unique slug.";
  } else {

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
      $error = "Image upload failed.";
    } else {

      $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
      if (!in_array($_FILES['image']['type'], $allowedTypes)) {
        $error = "Only JPG, PNG, and WEBP images allowed.";
      } else {

        $uploadDir = __DIR__ . '/../uploads/blogs/';
        if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = time() . '-' . preg_replace('/[^a-zA-Z0-9]/', '', $_POST['slug']) . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName);

        $imagePath = 'uploads/blogs/' . $fileName;

        $stmt = $conn->prepare("
          INSERT INTO blogs (title, slug, excerpt, content, image, published_date, status)
          VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
          "ssssssi",
          $_POST['title'],
          $_POST['slug'],
          $_POST['excerpt'],
          $_POST['content'],
          $imagePath,
          $_POST['published_date'],
          $_POST['status']
        );

        $stmt->execute();
        header("Location: list.php");
        exit;
      }
    }
  }
}
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="admin-wrapper">
  <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

  <main class="admin-main">

    <div class="page-header">
      <h1>Add Blog</h1>
      <a href="list.php" class="btn-secondary">← Back</a>
    </div>

    <div class="content-card">

      <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="form-grid">

        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" required>
        </div>

        <div class="form-group">
          <label>Slug</label>
          <input type="text" name="slug" placeholder="unique-slug" required>
        </div>

        <div class="form-group">
          <label>Publish Date</label>
          <input type="date" name="published_date" required>
        </div>

        <!-- IMAGE PREVIEW -->
        <div class="form-group full">
          <label>Featured Image</label>

          <input type="file" name="image" id="imageInput" accept="image/*" required>

          <div class="image-preview" id="imagePreview" style="display:none;">
            <img id="previewImg" src="">
            <span class="remove-img" id="removeImage">&times;</span>
          </div>
        </div>

        <div class="form-group full">
          <label>Short Description</label>
          <textarea name="excerpt" rows="3" required></textarea>
        </div>

        <div class="form-group full">
          <label>Blog Content</label>
          <textarea name="content" rows="8" required></textarea>
        </div>

        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <option value="1">Publish</option>
            <option value="0">Draft</option>
          </select>
        </div>

        <div class="form-group full">
          <button type="submit" class="btn-primary">Save Blog</button>
        </div>

      </form>

    </div>

  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<!-- IMAGE PREVIEW SCRIPT -->
<script>
const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');
const previewImg = document.getElementById('previewImg');
const removeImage = document.getElementById('removeImage');

imageInput.addEventListener('change', function () {
  const file = this.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function (e) {
    previewImg.src = e.target.result;
    imagePreview.style.display = 'block';
  };
  reader.readAsDataURL(file);
});

removeImage.addEventListener('click', function () {
  imageInput.value = '';
  previewImg.src = '';
  imagePreview.style.display = 'none';
});
</script>

<style>
.admin-wrapper {
  display: flex;
  min-height: calc(100vh - 70px);
}

.admin-main {
  flex: 1;
  padding: 32px 36px 90px;
  background: #fff;
}

.page-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 24px;
}

.content-card {
  background: #fff;
  padding: 24px;
  border-radius: 14px;
  box-shadow: 0 10px 30px rgba(0,0,0,.06);
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full {
  grid-column: span 2;
}

.form-group label {
  font-weight: 600;
  margin-bottom: 6px;
}

.form-group input,
.form-group textarea,
.form-group select {
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #d1d5db;
}

.btn-primary {
  background: #1e7c43;
  color: #fff;
  padding: 12px 22px;
  border-radius: 10px;
  border: none;
  font-weight: 600;
}

.btn-secondary {
  background: #e5e7eb;
  padding: 10px 16px;
  border-radius: 8px;
  text-decoration: none;
}

/* IMAGE PREVIEW */
.image-preview {
  position: relative;
  width: 260px;
  margin-top: 12px;
}

.image-preview img {
  width: 100%;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
}

.remove-img {
  position: absolute;
  top: -10px;
  right: -10px;
  background: #dc2626;
  color: #fff;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  font-size: 18px;
  line-height: 26px;
  text-align: center;
  cursor: pointer;
}

.error-msg {
  background: #fee2e2;
  color: #991b1b;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 15px;
}

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
  .form-group.full {
    grid-column: span 1;
  }
}
</style>
