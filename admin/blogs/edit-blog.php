<?php
include __DIR__ . '/../includes/auth-guard.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
include __DIR__ . '/../includes/db.php';

$uploadDir = realpath(__DIR__ . '/../uploads/blogs');
if ($uploadDir === false) {
    $uploadDir = __DIR__ . '/../uploads/blogs';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$blog = null;
if ($id > 0) {
    $res = $conn->query("SELECT * FROM blogs WHERE id=$id LIMIT 1");
    $blog = $res && $res->num_rows ? $res->fetch_assoc() : null;
}

if (!$blog) {
    echo "<script>alert('Blog not found'); window.location='list.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title      = $_POST['title'];
    $slug       = $_POST['slug'];
    $date       = $_POST['date'];
    $excerpt    = $_POST['excerpt'];
    $content    = $_POST['content'];
    $imageName  = $blog['image'];

    if (!empty($_FILES['image']['name'])) {
        $tmp = $_FILES['image']['tmp_name'];
        $clean = time() . '-' . basename($_FILES['image']['name']);
        $target = $uploadDir . "/" . $clean;
        if (move_uploaded_file($tmp, $target)) {
            if (!empty($blog['image'])) {
                $old = $uploadDir . "/" . $blog['image'];
                if (is_file($old)) @unlink($old);
            }
            $imageName = $clean;
        }
    }

    $stmt = $conn->prepare("UPDATE blogs SET title=?, slug=?, date=?, excerpt=?, image=?, content=? WHERE id=?");
    $stmt->bind_param("ssssssi", $title, $slug, $date, $excerpt, $imageName, $content, $id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Blog updated'); window.location='list.php';</script>";
    exit;
}
?>

<main class="admin-content">
    <h2 class="page-title">Edit Blog</h2>

    <div class="blog-wrapper">

        <form method="POST" enctype="multipart/form-data" class="blog-form">

            <div class="form-group">
                <label>Blog Title</label>
                <input type="text" name="title" id="titleInput" required value="<?= htmlspecialchars($blog['title']); ?>">
            </div>

            <div class="form-group">
                <label>Slug</label>
                <input type="text" name="slug" id="slugInput" required value="<?= htmlspecialchars($blog['slug']); ?>">
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" id="dateInput" required value="<?= htmlspecialchars($blog['date']); ?>">
            </div>

            <div class="form-group">
                <label>Short Excerpt</label>
                <textarea name="excerpt" id="excerptInput" rows="3" required><?= htmlspecialchars($blog['excerpt']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Blog Image</label>
                <input type="file" name="image" id="imageInput" accept="image/*">
                <?php if (!empty($blog['image'])): ?>
                    <div style="margin-top:8px;">
                        <img src="../uploads/blogs/<?= htmlspecialchars($blog['image']); ?>" alt="" style="width:160px; border-radius:8px;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Blog Content</label>
                <textarea name="content" id="contentInput" rows="10" required><?= htmlspecialchars($blog['content']); ?></textarea>
            </div>

            <button class="btn-primary" type="submit">Update Blog</button>
        </form>

        <div class="blog-preview">
            <div class="blog-card">
                <div class="blog-content">
                    <p class="blog-date"><?= htmlspecialchars($blog['date']); ?></p>
                    <h3 class="blog-title"><?= htmlspecialchars($blog['title']); ?></h3>
                    <p class="blog-excerpt"><?= htmlspecialchars($blog['excerpt']); ?></p>
                </div>
            </div>
        </div>
    </div>
</main>
