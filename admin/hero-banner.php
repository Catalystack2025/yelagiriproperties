<?php
session_start();
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
include __DIR__ . '/includes/db.php';

/* Fetch Settings */
$q = $conn->query("SELECT * FROM hero_settings WHERE id=1");
$hero = ($q->num_rows > 0)
    ? $q->fetch_assoc()
    : ['title' => '', 'description' => '', 'slide_interval' => 4000];

/* Fetch Slides */
$slidesQuery = $conn->query("SELECT * FROM hero_slides ORDER BY sort_order ASC, id DESC");

/* Handle POST */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* Update fields */
    $stmt = $conn->prepare("UPDATE hero_settings SET title=?, description=?, slide_interval=? WHERE id=1");
    $stmt->bind_param("ssi", $_POST['title'], $_POST['description'], $_POST['interval']);
    $stmt->execute();

    /* Remove slides */
    if (!empty($_POST['remove_slide'])) {
        foreach ($_POST['remove_slide'] as $id) {

            $res = $conn->query("SELECT image FROM hero_slides WHERE id=$id");
            if ($res->num_rows > 0) {
                $img = $res->fetch_assoc()['image'];
                $path = __DIR__ . "/../admin/uploads/hero/" . $img;
                if (file_exists($path)) unlink($path);
            }

            $conn->query("DELETE FROM hero_slides WHERE id=$id");
        }
    }

    /* MULTIPLE IMAGE UPLOAD FIXED */
    if (!empty($_FILES['slides']['name'][0])) {

        $dir = __DIR__ . '/../admin/uploads/hero/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        foreach ($_FILES['slides']['tmp_name'] as $i => $tmp) {
            if (!$tmp) continue;

            $ext = pathinfo($_FILES['slides']['name'][$i], PATHINFO_EXTENSION);
            $rand = "hero-" . time() . "-" . rand(1000,9999) . "." . $ext;

            move_uploaded_file($tmp, $dir . $rand);

            $ins = $conn->prepare("INSERT INTO hero_slides (image) VALUES (?)");
            $ins->bind_param("s", $rand);
            $ins->execute();
        }
    }

    header("Location: hero-banner.php");
    exit;
}
?>

<main class="admin-wrapper">

<h2 class="page-title">Hero Banner Editor</h2>

<div class="layout-grid">

    <!-- LEFT -->
    <div class="left-section">
        <div class="panel">

            <h3 class="panel-title">Edit Hero Banner</h3>

            <form id="heroForm" method="POST" enctype="multipart/form-data">

                <!-- TITLE -->
                <div class="form-group">
                    <label class="label">Hero Title</label>
                    <input type="text" name="title" class="input"
                        value="<?= htmlspecialchars($hero['title']) ?>" required>
                </div>

                <!-- DESCRIPTION -->
                <div class="form-group">
                    <label class="label">Description</label>
                    <textarea name="description" class="textarea" rows="3"><?= htmlspecialchars($hero['description']) ?></textarea>
                </div>

                <!-- INTERVAL -->
                <div class="form-group">
                    <label class="label">Slide Interval (ms)</label>
                    <input type="number" name="interval" class="input"
                        value="<?= $hero['slide_interval'] ?>" required>
                </div>

                <!-- UPLOAD -->
                <div class="form-group">
                    <label class="label">Upload Slides</label>

                    <!-- FIXED: MULTIPLE selection ALWAYS works -->
                    <input type="file" class="file-input" name="slides[]" accept="image/*" multiple>

                    <p class="help">Select multiple images at once or multiple times.</p>
                </div>

                <!-- EXISTING THUMBNAILS -->
                <div id="thumbGrid" class="thumb-grid">
                    <?php
                    $slides2 = $conn->query("SELECT * FROM hero_slides ORDER BY sort_order ASC, id DESC");
                    while ($row = $slides2->fetch_assoc()):
                    ?>
                        <div class="thumb">
                            <img src="../admin/uploads/hero/<?= $row['image'] ?>">
                            <button name="remove_slide[]" value="<?= $row['id'] ?>" class="remove-thumb">×</button>
                        </div>
                    <?php endwhile; ?>
                </div>

                <button type="submit" class="save-btn">Save Changes</button>

            </form>

        </div>
    </div>


    <!-- RIGHT -->
    <div class="right-section">
        <div class="panel">

            <h3 class="panel-title">Live Preview</h3>

            <div class="hero-preview">

                <div id="liveSlider" class="hero-slider">
                    <?php
                    $slidesAgain = $conn->query("SELECT * FROM hero_slides ORDER BY sort_order ASC, id DESC");
                    while ($s = $slidesAgain->fetch_assoc()):
                    ?>
                        <div class="hero-slide">
                            <img src="../admin/uploads/hero/<?= $s['image'] ?>">
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="hero-overlay">
                    <h2 id="liveTitle"><?= htmlspecialchars($hero['title']) ?></h2>
                    <p id="liveDesc"><?= htmlspecialchars($hero['description']) ?></p>
                    <small><span id="liveInterval"><?= $hero['slide_interval'] ?></span> ms</small>
                </div>

            </div>

        </div>
    </div>

</div>

</main>

<script>

/* Live Text Updates */
document.addEventListener("input", e => {
    if (e.target.name === "title") liveTitle.textContent = e.target.value;
    if (e.target.name === "description") liveDesc.textContent = e.target.value;
    if (e.target.name === "interval") liveInterval.textContent = e.target.value;
});

/* FIXED: MULTIPLE FILES + APPEND PREVIEW */
document.querySelector(".file-input").addEventListener("change", function () {

    const slider = document.getElementById("liveSlider");
    const grid = document.getElementById("thumbGrid");

    slider.innerHTML = "";
    grid.innerHTML = "";

    [...this.files].forEach(file => {

        const reader = new FileReader();

        reader.onload = e => {

            /* Add new thumbnail */
            let t = document.createElement("div");
            t.className = "thumb";
            t.innerHTML = `<img src="${e.target.result}">`;
            grid.appendChild(t);

            /* Add to live slider */
            let s = document.createElement("div");
            s.className = "hero-slide";
            s.innerHTML = `<img src="${e.target.result}">`;
            slider.appendChild(s);
        };

        reader.readAsDataURL(file);
    });
});

/* Reset preview on SAVE */
document.getElementById("heroForm").addEventListener("submit", () => {
    document.getElementById("liveSlider").innerHTML = "";
    document.querySelector(".file-input").value = "";
});


/* SLIDER AUTO-PLAY */
let idx = 0;
function autoRotate() {
    let slides = document.querySelectorAll("#liveSlider .hero-slide");
    if (slides.length <= 1) return;
    idx = (idx + 1) % slides.length;
    document.getElementById("liveSlider").style.transform = `translateX(-${idx * 100}%)`;
}
setInterval(autoRotate, <?= $hero['slide_interval'] ?>);

</script>

<style>

/* MAIN LAYOUT */
.admin-wrapper {
    margin-left: 240px;
    padding: 30px;
}

.layout-grid {
    display: flex;
    gap: 30px;
}

.left-section { flex: 1.4; }
.right-section { flex: 1; }

.panel {
    background: #fff;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.panel-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
}

/* FORM */
.form-group {
    margin-bottom: 18px;
}

.label {
    font-weight: 600;
    margin-bottom: 6px;
    display: block;
}

.input, .textarea, .file-input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #ccc;
    background: #fafafa;
    border-radius: 10px;
}

.textarea { min-height: 100px; }

/* THUMB GRID */
.thumb-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
}

.thumb {
    width: 130px;
    height: 170px;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.remove-thumb {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 20px;
    height: 20px;
    border: none;
    background: #ff3333;
    color: #fff;
    font-size: 14px;
    border-radius: 50%;
}

/* SAVE BUTTON */
.save-btn {
    width: 100%;
    padding: 14px;
    background: #2e7d32;
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 17px;
    margin-top: 20px;
}

/* HERO PREVIEW BOX */
.hero-preview {
    position: relative;
    height: 320px;
    border-radius: 14px;
    overflow: hidden;
    background: #000;
}

/* SLIDER */
.hero-slider {
    display: flex;
    height: 100%;
    transition: transform .6s ease-in-out;
}

.hero-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Overlay */
.hero-preview::after {
    content:"";
    position:absolute;
    bottom:0;
    width:100%;
    height:45%;
    background:linear-gradient(to top, rgba(0,0,0,0.7), transparent);
}

.hero-overlay {
    position:absolute;
    bottom:25px;
    left:25px;
    z-index:10;
    color:#fff;
}

.hero-overlay h2 {
    font-size:22px;
    font-weight:700;
}

.help {
    font-size:13px;
    color:#666;
}

</style>

