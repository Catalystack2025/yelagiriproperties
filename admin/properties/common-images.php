<?php
ob_start();

include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';

$commonDir = __DIR__ . '/../uploads/common-images';
if (!is_dir($commonDir)) mkdir($commonDir, 0777, true);


// DELETE COMMON IMAGE (DELETE DB + MAP + STORAGE)
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);

    // Get image record
    $img = $conn->query("SELECT image_path FROM property_common_images WHERE id=$deleteId")->fetch_assoc();

    if ($img) {
        $imagePath = $img['image_path'];
        $fullPath = __DIR__ . '/../uploads/' . $imagePath;

        // Delete mapping records
        $conn->query("DELETE FROM property_common_image_map WHERE common_image_id=$deleteId");

        // Delete main record
        $conn->query("DELETE FROM property_common_images WHERE id=$deleteId");

        // Delete file from storage
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    echo "<script>window.location.replace('common-images.php');</script>";
    exit;
}

// UPLOAD LOGIC
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['common_images']['name'][0])) {

        foreach ($_FILES['common_images']['name'] as $i => $file) {
            $tmp = $_FILES['common_images']['tmp_name'][$i];

            if ($tmp) {
                $clean = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/','_',$file);
                $path = "common-images/$clean";

                if (move_uploaded_file($tmp, "$commonDir/$clean")) {

                    $stmt = $conn->prepare("INSERT INTO property_common_images (image_path) VALUES (?)");
                    $stmt->bind_param("s", $path);
                    $stmt->execute();
                    $commonImageId = $stmt->insert_id;
                    $stmt->close();

                    // Attach to all properties
                    $props = $conn->query("SELECT id FROM properties");
                    while ($p = $props->fetch_assoc()) {
                        $pid = $p['id'];
                        $conn->query("
                            INSERT IGNORE INTO property_common_image_map (property_id, common_image_id) 
                            VALUES ($pid, $commonImageId)
                        ");
                    }
                }
            }
        }
    }

    echo "<script>window.location.replace('common-images.php');</script>";
    exit;
}

// LOAD COMMON IMAGES
$commonImages = [];
$res = $conn->query("SELECT * FROM property_common_images ORDER BY id DESC");
while ($row = $res->fetch_assoc()) {
    $commonImages[] = $row;
}
?>

<style>
#uploadLoader {
  display:none;
  position:fixed;
  inset:0;
  background:white;
  z-index:9999;
  align-items:center;
  justify-content:center;
  font-size:18px;
  font-weight:600;
}
</style>

<div id="uploadLoader">Uploading images…</div>

<div class="admin-layout">
<main class="admin-main admin-main--wide">

<div class="form-page">
<header class="form-header">
<h1>Manage Common Images</h1>
<p>Upload images shared across all properties</p>
</header>

<form method="post" enctype="multipart/form-data" class="form-card">
    <h3>Upload New Common Images</h3>
    <input type="file" name="common_images[]" multiple accept="image/*">
    <br><br>
    <button class="btn-primary">Upload Images</button>
</form>
<br>
<section class="form-card">
<h3>Existing Common Images</h3>

<div style="display:flex; flex-wrap:wrap; gap:12px;">

<?php foreach ($commonImages as $img): ?>
    <div style="border:1px solid #ddd; padding:10px; border-radius:10px; text-align:center; width:160px;">
        
        <!-- Preview -->
        <a href="../uploads/<?= htmlspecialchars($img['image_path']); ?>" target="_blank">
            <img src="../uploads/<?= htmlspecialchars($img['image_path']); ?>" style="width:140px; height:100px; object-fit:cover; border-radius:6px;">
        </a>

        <!-- Delete Button -->
        <a href="?delete=<?= $img['id']; ?>" 
           onclick="return confirm('Delete this common image from ALL properties?')" 
           class="btn-danger" 
           style="display:block; margin-top:8px; font-size:13px;">
           Delete
        </a>

    </div>
<?php endforeach; ?>

</div>

</section>
</div>

</main>
</div>

<script>
document.querySelector("form")?.addEventListener("submit", () => {
  document.getElementById("uploadLoader").style.display = "flex";
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
