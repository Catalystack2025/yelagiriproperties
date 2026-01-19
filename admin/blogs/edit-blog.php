<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth-guard.php';

$id = (int)($_GET['id'] ?? 0);
$error = '';

$stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$blog = $stmt->get_result()->fetch_assoc();

if (!$blog) {
  echo "Blog not found";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  /* -------------------------
     SLUG DUPLICATE CHECK
  -------------------------- */
  $check = $conn->prepare("SELECT id FROM blogs WHERE slug = ? AND id != ?");
  $check->bind_param("si", $_POST['slug'], $id);
  $check->execute();
  if ($check->get_result()->num_rows > 0) {
    $error = "Slug already exists. Use a different slug.";
  } else {

    /* -------------------------
       IMAGE UPLOAD (OPTIONAL)
    -------------------------- */
    $imagePath = $blog['image'];

    if (!empty($_FILES['image']['name'])) {

      $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
      if (!in_array($_FILES['image']['type'], $allowedTypes)) {
        $error = "Only JPG, PNG, WEBP images allowed.";
      } else {

        $uploadDir = __DIR__ . '/../uploads/blogs/';
        if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = time() . '-' . $_POST['slug'] . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName);

        $imagePath = 'uploads/blogs/' . $fileName;
      }
    }

    if (!$error) {
      $update = $conn->prepare("
        UPDATE blogs
        SET title=?, slug=?, excerpt=?, content=?, image=?, published_date=?, status=?
        WHERE id=?
      ");

      $update->bind_param(
        "ssssssii",
        $_POST['title'],
        $_POST['slug'],
        $_POST['excerpt'],
        $_POST['content'],
        $imagePath,
        $_POST['published_date'],
        $_POST['status'],
        $id
      );

      $update->execute();
      header("Location: list.php");
      exit;
    }
  }
}
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="admin-wrapper">

  <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

  <main class="admin-main">

    <div class="page-header">
      <h1>Edit Blog</h1>
      <a href="list.php" class="btn-secondary">← Back</a>
    </div>

    <div class="content-card">

      <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="form-grid">

        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" value="<?= htmlspecialchars($blog['title']) ?>" required>
        </div>

        <div class="form-group">
          <label>Slug</label>
          <input type="text" name="slug" value="<?= htmlspecialchars($blog['slug']) ?>" required>
        </div>

        <div class="form-group">
          <label>Publish Date</label>
          <input type="date" name="published_date" value="<?= $blog['published_date'] ?>" required>
        </div>

        <div class="form-group">
          <label>Replace Image (optional)</label>
          <input type="file" name="image" accept="image/*">
        </div>

        <div class="form-group full">
          <label>Current Image</label>
          <img src="../<?= htmlspecialchars($blog['image']) ?>" class="preview-img">
        </div>

        <div class="form-group full">
          <label>Short Description</label>
          <textarea name="excerpt" rows="3"><?= htmlspecialchars($blog['excerpt']) ?></textarea>
        </div>

        <div class="form-group full">
          <label>Blog Content (HTML allowed)</label>
          <textarea name="content" rows="8"><?= htmlspecialchars($blog['content']) ?></textarea>
        </div>

        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <option value="1" <?= $blog['status'] ? 'selected' : '' ?>>Published</option>
            <option value="0" <?= !$blog['status'] ? 'selected' : '' ?>>Draft</option>
          </select>
        </div>

        <div class="form-group full">
          <button type="submit" class="btn-primary">Update Blog</button>
        </div>

      </form>

    </div>

  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

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

.preview-img {
  max-width: 260px;
  border-radius: 10px;
  margin-top: 10px;
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
