<?php
// add-blog.php
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/db.php';

$uploadDir = realpath(__DIR__ . '/../admin/uploads/blogs');
if ($uploadDir === false) {
    $uploadDir = __DIR__ . '/../admin/uploads/blogs';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
}

// Save blog data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title      = $_POST['title'];
    $slug       = $_POST['slug'];
    $date       = $_POST['date'];
    $excerpt    = $_POST['excerpt'];
    $content    = $_POST['content'];

    /* --- IMAGE UPLOAD --- */
    $imageName = null;

    if (!empty($_FILES['image']['name'])) {
        $fileTmp  = $_FILES['image']['tmp_name'];
        $fileName = time() . '-' . basename($_FILES['image']['name']);
        $uploadPath = $uploadDir . "/" . $fileName;
        move_uploaded_file($fileTmp, $uploadPath);
        $imageName = $fileName;
    }

    /* --- DB INSERT --- */
    $stmt = $conn->prepare("INSERT INTO blogs (title, slug, date, excerpt, image, content)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $title, $slug, $date, $excerpt, $imageName, $content);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Blog added successfully!'); window.location='list.php';</script>";
}
?>

<!-- ============================
          MAIN CONTENT
============================ -->
<main class="admin-content">
    <h2 class="page-title">Add New Blog</h2>

    <div class="blog-wrapper">

        <!-- LEFT FORM -->
        <form method="POST" enctype="multipart/form-data" class="blog-form">

            <div class="form-group">
                <label>Blog Title</label>
                <input type="text" name="title" id="titleInput" required>
            </div>

            <div class="form-group">
                <label>Slug (auto)</label>
                <input type="text" name="slug" id="slugInput" readonly>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" id="dateInput" required>
            </div>

            <div class="form-group">
                <label>Short Excerpt</label>
                <textarea name="excerpt" id="excerptInput" rows="3" required></textarea>
            </div>

            <!-- IMAGE UPLOAD -->
            <div class="form-group">
                <label>Blog Image</label>
                <input type="file" name="image" id="imageInput" accept="image/*">

                <div class="image-preview-container" id="imagePreviewContainer" style="display:none;">
                    <img id="imagePreview">
                    <button type="button" id="removeImage" class="remove-image-btn">✖</button>
                </div>
            </div>

            <div class="form-group">
                <label>Blog Content</label>
                <textarea name="content" id="contentInput" rows="10" required></textarea>
            </div>

            <button class="btn-primary" type="submit">Publish Blog</button>
        </form>

        <!-- RIGHT LIVE PREVIEW -->
        <div class="blog-preview">
            <div class="blog-card">
                <img id="pImage" class="blog-img" style="display:none;">

                <div class="blog-content">
                    <p id="pDate" class="blog-date">Blog Date</p>

                    <h3 id="pTitle" class="blog-title">Blog Title Preview</h3>

                    <p id="pExcerpt" class="blog-excerpt">Short excerpt will appear here.</p>

                    <a href="#" class="blog-readmore">Read More →</a>

                    <hr style="margin:20px 0;">

                    <div id="pContent" class="blog-full-content"></div>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- ============================
       LIVE PREVIEW JS
============================ -->
<script>
// Title + slug
document.getElementById("titleInput").addEventListener("input", function () {
    const value = this.value.trim();
    document.getElementById("pTitle").innerText = value || "Blog Title Preview";
    document.getElementById("slugInput").value = value.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
});

// Date
document.getElementById("dateInput").addEventListener("input", function () {
    document.getElementById("pDate").innerText = this.value || "Blog Date";
});

// Excerpt
document.getElementById("excerptInput").addEventListener("input", function () {
    document.getElementById("pExcerpt").innerText = this.value || "Short excerpt here.";
});

// Content
document.getElementById("contentInput").addEventListener("input", function () {
    document.getElementById("pContent").innerHTML = this.value;
});

// Image preview + remove
const imgInput = document.getElementById("imageInput");
const previewBox = document.getElementById("imagePreviewContainer");
const previewImg = document.getElementById("imagePreview");
const removeBtn = document.getElementById("removeImage");
const pImage = document.getElementById("pImage");

imgInput.addEventListener("change", function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        previewBox.style.display = "block";
        previewImg.src = e.target.result;

        pImage.style.display = "block";
        pImage.src = e.target.result;
    };
    reader.readAsDataURL(file);
});

// Remove image button
removeBtn.addEventListener("click", function () {
    imgInput.value = "";
    previewBox.style.display = "none";
    pImage.style.display = "none";
});
</script>

<!-- ============================
            PAGE CSS
============================ -->
<style>
.admin-content {
    margin-left: 260px;
    padding: 40px;
    min-height: 100vh;
    background: #f9fafb;
}

.blog-wrapper {
    display: flex;
    gap: 40px;
}

.blog-form {
    width: 55%;
    background: #fff;
    padding: 30px;
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.form-group { margin-bottom: 20px; }

/* Inputs */
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 15px;
}

/* Image preview + remove */
.image-preview-container {
    position: relative;
    width: 180px;
    margin-top: 12px;
}

.image-preview-container img {
    width: 180px;
    border-radius: 10px;
}

.remove-image-btn {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #7c7577;
    color: #fff;
    border: none;
    padding: 4px 7px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 12px;
}

/* Submit */
.btn-primary {
    background: #2e7d32;
    color: #fff;
    border: none;
    padding: 12px 25px;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
}

/* Preview panel */
.blog-preview {
    width: 45%;
    position: sticky;
    top: 120px;
}

.blog-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    overflow: hidden;
}

.blog-img {
    width: 100%;
    height: 230px;
    object-fit: cover;
}

/* Content text */
.blog-content { padding: 24px; }
.blog-title { font-size: 20px; font-weight: 700; }
.blog-date { color: #6b7280; font-size: 14px; }
.blog-excerpt { color: #4b5563; margin-bottom: 12px; }
.blog-readmore { color: #2e7d32; font-weight: 600; }

/* Mobile */
@media (max-width: 900px) {
    .blog-wrapper { flex-direction: column; }
    .blog-form, .blog-preview { width: 100%; }
}
</style>
