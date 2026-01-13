<?php
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/blueprint-helpers.php';

ensureBlueprintTable($conn);

/* ============================================================
   LOAD ALL AMENITIES
============================================================ */
$amenities = [];
$resA = $conn->query("SELECT * FROM amenities ORDER BY name ASC");
while ($row = $resA->fetch_assoc()) {
    $amenities[] = $row;
}

/* ============================================================
   CHECK EDIT MODE
============================================================ */
$edit = false;
$property = [];
$selectedAmenities = [];
$blueprint = null;
$propertyImages = [];
$id = 0;

if (isset($_GET['id'])) {
    $edit = true;
    $id = intval($_GET['id']);

    $res = $conn->query("SELECT * FROM properties WHERE id=$id LIMIT 1");
    $property = $res->fetch_assoc();

    $aRes = $conn->query("SELECT amenity_id FROM property_amenities WHERE property_id=$id");
    while ($row = $aRes->fetch_assoc()) {
        $selectedAmenities[] = $row['amenity_id'];
    }

    $imgRes = $conn->query("SELECT * FROM property_images WHERE property_id=$id ORDER BY id ASC");
    while ($row = $imgRes->fetch_assoc()) {
        $propertyImages[] = $row['image_path'];
    }

    $blueprint = getBlueprint($conn, $id);
}

/* ============================================================
   FORM SUBMIT
============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = trim($_POST['name'] ?? '');
    $type        = trim($_POST['type'] ?? '');
    $location    = trim($_POST['location'] ?? '');
    $size        = trim($_POST['size'] ?? '');
    $dimensions  = trim($_POST['dimensions'] ?? '');
    $facing      = trim($_POST['facing'] ?? '');
    $status      = trim($_POST['status'] ?? 'Available');
    $description = trim($_POST['description'] ?? '');
    $selected    = $_POST['amenities'] ?? [];
    $blueprintData = $_POST['blueprint_data'] ?? '';

    /* ---------------------------------------------
       UPDATE PROPERTY
    ----------------------------------------------*/
    if ($edit) {

        $stmt = $conn->prepare("
            UPDATE properties SET
                name=?,
                type=?,
                location=?,
                size=?,
                dimensions=?,
                facing=?,
                status=?,
                description=?
            WHERE id=?
        ");
        $stmt->bind_param("ssssssssi", $name, $type, $location, $size, $dimensions, $facing, $status, $description, $id);
        $stmt->execute();

        $conn->query("DELETE FROM property_amenities WHERE property_id=$id");

    } else {

        /* ---------------------------------------------
           INSERT NEW PROPERTY
        ----------------------------------------------*/
        $stmt = $conn->prepare("
            INSERT INTO properties (name, type, location, size, dimensions, facing, status, description)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("ssssssss", $name, $type, $location, $size, $dimensions, $facing, $status, $description);
        $stmt->execute();

        $id = $stmt->insert_id;
    }

    /* ---------------------------------------------
       INSERT AMENITIES FOR THE PROPERTY
    ----------------------------------------------*/
    if (!empty($selected)) {
        $amenityStmt = $conn->prepare("
            INSERT INTO property_amenities (property_id, amenity_id)
            VALUES (?, ?)
        ");

        foreach ($selected as $a) {
            $amenityId = intval($a);
            $amenityStmt->bind_param("ii", $id, $amenityId);
            $amenityStmt->execute();
        }

        $amenityStmt->close();
    }

    /* ============================================================
       MULTIPLE IMAGE UPLOAD (FIXED & READY)
    ============================================================ */

    $uploadDir = realpath(__DIR__ . '/../uploads');

    if ($uploadDir === false) {
        $uploadDir = __DIR__ . '/../uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
    }

    if (!empty($_FILES['images']['name'][0])) {

        $imgStmt = $conn->prepare("
            INSERT INTO property_images (property_id, image_path)
            VALUES (?, ?)
        ");

        foreach ($_FILES['images']['name'] as $index => $file) {

            $tmp = $_FILES['images']['tmp_name'][$index];

            if ($tmp == "" || !is_uploaded_file($tmp)) {
                continue;
            }

            $clean = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $file);
            $newName = time() . "_" . rand(1000,9999) . "_" . $clean;
            $path = $uploadDir . "/" . $newName;

            if (move_uploaded_file($tmp, $path)) {
                $imgStmt->bind_param("is", $id, $newName);
                $imgStmt->execute();
            }
        }

        $imgStmt->close();
    }

    /* ============================================================
       BLUEPRINT HANDLING (save annotated copy)
    ============================================================ */
    $blueprintDir = realpath(__DIR__ . '/../uploads/blueprints');

    if ($blueprintDir === false) {
        $blueprintDir = __DIR__ . '/../uploads/blueprints';
        if (!is_dir($blueprintDir)) {
            mkdir($blueprintDir, 0777, true);
        }
    }

    $previousOriginal = $blueprint['original_path'] ?? null;
    $previousAnnotated = $blueprint['annotated_path'] ?? null;

    $blueprintOriginal = $previousOriginal;
    $blueprintAnnotated = $previousAnnotated;

    if (!empty($_FILES['blueprint_image']['tmp_name']) && is_uploaded_file($_FILES['blueprint_image']['tmp_name'])) {
        $clean = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $_FILES['blueprint_image']['name']);
        $originalName = time() . "_" . rand(1000,9999) . "_" . $clean;
        $target = $blueprintDir . "/" . $originalName;

        if (move_uploaded_file($_FILES['blueprint_image']['tmp_name'], $target)) {
            if (!empty($previousOriginal) && $previousOriginal !== $originalName) {
                $old = $blueprintDir . "/" . $previousOriginal;
                if (is_file($old)) {
                    @unlink($old);
                }
            }
            $blueprintOriginal = $originalName;
        }
    }

    if (!empty($blueprintData)) {
        $parts = explode(',', $blueprintData, 2);
        $payload = count($parts) === 2 ? $parts[1] : $parts[0];
        $decoded = base64_decode($payload);

        if ($decoded !== false) {
            $annotatedName = "bp_" . time() . "_" . rand(1000,9999) . ".png";
            $annotatedPath = $blueprintDir . "/" . $annotatedName;

            if (file_put_contents($annotatedPath, $decoded) !== false) {
                if (!empty($previousAnnotated) && $previousAnnotated !== $annotatedName) {
                    $old = $blueprintDir . "/" . $previousAnnotated;
                    if (is_file($old)) {
                        @unlink($old);
                    }
                }

                $blueprintAnnotated = $annotatedName;
            }
        }
    }

    if ($blueprintAnnotated || $blueprintOriginal) {
        $bpStmt = $conn->prepare("
            INSERT INTO property_blueprints (property_id, original_path, annotated_path)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
                original_path = VALUES(original_path),
                annotated_path = VALUES(annotated_path)
        ");
        $bpStmt->bind_param("iss", $id, $blueprintOriginal, $blueprintAnnotated);
        $bpStmt->execute();
        $bpStmt->close();
    }

    header("Location: list.php");
    exit;
}
?>

<!-- ============================================================
     PAGE UI
============================================================ -->
<div class="admin-layout">
  <main class="admin-main admin-main--wide">

    <div class="form-page">

      <header class="form-header">
        <h1><?= $edit ? "Edit Property" : "Add Property"; ?></h1>
        <p><?= $edit ? "Update this property listing" : "Create a new property in the system"; ?></p>
      </header>

      <form class="property-form" method="post" enctype="multipart/form-data">

        <!-- BASIC INFORMATION -->
        <section class="form-card">
          <h3>Basic Information</h3>

          <div class="form-grid">

            <div class="col-6">
              <label>Property Name *</label>
              <input type="text" name="name" required
                     value="<?= $edit ? $property['name'] : '' ?>">
            </div>

            <div class="col-6">
              <label>Property Type *</label>
              <select name="type" required>
                <option value="">Select Type</option>
                <option <?= $edit && $property['type']=="Plot" ? "selected" : "" ?>>Plot</option>
                <option <?= $edit && $property['type']=="Villa" ? "selected" : "" ?>>Villa</option>
                <option <?= $edit && $property['type']=="Home Stays" ? "selected" : "" ?>>Home Stays</option>
              </select>
            </div>

            <div class="col-6">
              <label>Location *</label>
              <input type="text" name="location" required
                     value="<?= $edit ? $property['location'] : '' ?>">
            </div>

            <div class="col-6">
              <label>Size *</label>
              <input type="text" name="size" required
                     value="<?= $edit ? $property['size'] : '' ?>">
            </div>

            <div class="col-6">
              <label>Dimensions *</label>
              <input type="text" name="dimensions" placeholder="20x10x12x12" required
                     value="<?= $edit ? $property['dimensions'] : '' ?>">
            </div>

            <div class="col-6">
              <label>Facing *</label>
              <input type="text" name="facing" required
                     value="<?= $edit ? $property['facing'] : '' ?>">
            </div>

            <div class="col-6">
              <label>Status *</label>
              <select name="status">
                <option <?= $edit && $property['status']=="Available" ? "selected" : "" ?>>Available</option>
                <option <?= $edit && $property['status']=="Sold" ? "selected" : "" ?>>Sold</option>
                <option <?= $edit && $property['status']=="Blocked" ? "selected" : "" ?>>Blocked</option>
              </select>
            </div>

          </div>
        </section>

        <!-- IMAGES -->
        <section class="form-card">
          <h3>Property Images</h3>
          <div class="upload-box">
            <input type="file" name="images[]" accept="image/*" multiple>
            <p>Upload multiple images (optional)</p>
          </div>
          <?php if ($edit && !empty($propertyImages)): ?>
            <div class="current-images" style="margin-top:12px; display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:10px;">
              <?php foreach ($propertyImages as $img): ?>
                <div style="border:1px solid #e2e8f0; padding:4px; border-radius:8px; background:#fff;">
                  <img src="../uploads/<?= htmlspecialchars($img); ?>" alt="" style="width:100%; height:120px; object-fit:cover; border-radius:6px;">
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>

        <!-- BLUEPRINT EDITOR -->
        <section class="form-card">
          <h3>Plot Blueprint</h3>
          <p class="text-muted">Upload the layout, sketch highlights directly on it, and we'll store the annotated copy.</p>

          <div class="blueprint-editor">
            <div class="blueprint-controls">
              <label class="blueprint-upload">
                <input type="file" id="blueprintInput" name="blueprint_image" accept="image/*">
                <span>Select blueprint image</span>
              </label>
              <button type="button" class="btn-outline" id="blueprintRect" disabled>Rectangle Mode</button>
              <button type="button" class="btn-outline" id="blueprintReset" disabled>Reset Drawing</button>
            </div>

            <div class="blueprint-canvas-wrap">
              <canvas id="blueprintCanvas"></canvas>
              <div id="blueprintPlaceholder" class="blueprint-placeholder">
                <strong>Blueprint highlighter</strong>
                <p>Drop a layout image, then draw to mark the plot.</p>
              </div>
            </div>

            <?php if ($edit && !empty($blueprint['annotated_path'])): ?>
              <div class="blueprint-current">
                <p>Current saved blueprint:</p>
                <img src="../uploads/blueprints/<?= htmlspecialchars($blueprint['annotated_path']) ?>" alt="Existing blueprint">
              </div>
            <?php endif; ?>
          </div>

          <input type="hidden" name="blueprint_data" id="blueprintData">
        </section>

        <!-- DESCRIPTION -->
        <section class="form-card">
          <h3>Description</h3>
          <textarea name="description" rows="5" required>
<?= $edit ? $property['description'] : '' ?>
          </textarea>
        </section>

        <!-- AMENITIES -->
        <section class="form-card">
          <h3>Amenities</h3>

          <?php foreach ($amenities as $a): ?>
            <label style="display:block; margin-bottom:8px;">
              <input
                type="checkbox"
                name="amenities[]"
                value="<?= $a['id'] ?>"
                <?= in_array($a['id'], $selectedAmenities) ? "checked" : "" ?>
              >
              <?= $a['name'] ?>
            </label>
          <?php endforeach; ?>

        </section>

        <!-- ACTION BUTTONS -->
        <div class="form-actions">
          <a href="list.php" class="btn-outline">Cancel</a>
          <button type="submit" class="btn-primary">Save Property</button>
        </div>

      </form>

    </div>
  </main>

  <?php include __DIR__ . '/../includes/footer.php'; ?>
</div>

<script>
(function() {
  const fileInput = document.getElementById('blueprintInput');
  const canvas = document.getElementById('blueprintCanvas');
  const placeholder = document.getElementById('blueprintPlaceholder');
  const resetBtn = document.getElementById('blueprintReset');
  const rectBtn = document.getElementById('blueprintRect');
  const dataInput = document.getElementById('blueprintData');
  const form = document.querySelector('.property-form');

  if (!canvas || !dataInput) return;

  const ctx = canvas.getContext('2d');
  const existingFile = <?= json_encode($blueprint['annotated_path'] ?? null); ?>;
  const basePath = "../uploads/blueprints/";

  let img = new Image();
  let hasBase = false;
  let drawing = false;
  let blueprintTouched = false;
  let rectMode = false;
  let startPoint = null;
  let snapshot = null;
  let lastPos = null;

  function sizeCanvas(w, h) {
    const maxW = 920;
    const maxH = 680;
    const ratio = Math.min(maxW / w, maxH / h, 1);
    const finalW = Math.max(320, Math.round(w * ratio));
    const finalH = Math.round(h * ratio);
    canvas.width = finalW;
    canvas.height = finalH;
    canvas.style.width = "100%";
    canvas.style.maxWidth = finalW + "px";
  }

  function renderImage(src) {
    img = new Image();
    img.onload = () => {
      sizeCanvas(img.width, img.height);
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
      hasBase = true;
      if (resetBtn) resetBtn.disabled = false;
      if (rectBtn) rectBtn.disabled = false;
      if (placeholder) placeholder.classList.add('hidden');
    };
    img.src = src;
  }

  if (existingFile) {
    renderImage(basePath + existingFile);
  }

  if (fileInput) {
    fileInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;

      blueprintTouched = true;
      const reader = new FileReader();
      reader.onload = (ev) => renderImage(ev.target.result);
      reader.readAsDataURL(file);
    });
  }

  function getPos(evt) {
    const rect = canvas.getBoundingClientRect();
    const clientX = evt.touches ? evt.touches[0].clientX : evt.clientX;
    const clientY = evt.touches ? evt.touches[0].clientY : evt.clientY;
    return {
      x: (clientX - rect.left) * (canvas.width / rect.width),
      y: (clientY - rect.top) * (canvas.height / rect.height)
    };
  }

  function startDraw(e) {
    if (!hasBase) return;
    const pos = getPos(e);

    if (rectMode) {
      startPoint = pos;
      snapshot = ctx.getImageData(0, 0, canvas.width, canvas.height);
      drawing = true;
      e.preventDefault();
      return;
    }

    drawing = true;
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
    e.preventDefault();
  }

  function draw(e) {
    if (!drawing || !hasBase) return;
    const pos = getPos(e);
    lastPos = pos;

    if (rectMode) {
      if (snapshot) ctx.putImageData(snapshot, 0, 0);
      ctx.lineWidth = 3;
      ctx.lineCap = 'square';
      ctx.strokeStyle = 'rgba(34,197,94,0.9)';
      ctx.strokeRect(startPoint.x, startPoint.y, pos.x - startPoint.x, pos.y - startPoint.y);
      blueprintTouched = true;
      e.preventDefault();
      return;
    }

    ctx.lineWidth = 4;
    ctx.lineCap = 'round';
    ctx.strokeStyle = 'rgba(34,197,94,0.9)';
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
    blueprintTouched = true;
    e.preventDefault();
  }

  function endDraw(e) {
    if (rectMode && drawing) {
      const pos = e ? getPos(e) : (lastPos || startPoint);
      if (snapshot) ctx.putImageData(snapshot, 0, 0);
      ctx.lineWidth = 3;
      ctx.lineCap = 'square';
      ctx.strokeStyle = 'rgba(34,197,94,0.9)';
      ctx.strokeRect(startPoint.x, startPoint.y, pos.x - startPoint.x, pos.y - startPoint.y);
      blueprintTouched = true;
      snapshot = null;
    }
    drawing = false;
    startPoint = null;
  }

  ['mousedown', 'touchstart'].forEach(evt => canvas.addEventListener(evt, startDraw));
  ['mousemove', 'touchmove'].forEach(evt => canvas.addEventListener(evt, draw));
  ['mouseup', 'mouseleave', 'touchend', 'touchcancel'].forEach(evt => canvas.addEventListener(evt, endDraw));

  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      if (!hasBase) return;
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
      snapshot = null;
      startPoint = null;
    });
  }

  if (rectBtn) {
    rectBtn.addEventListener('click', () => {
      if (rectBtn.disabled) return;
      rectMode = !rectMode;
      rectBtn.classList.toggle('active', rectMode);
      snapshot = null;
      startPoint = null;
    });
  }

  if (form) {
    form.addEventListener('submit', () => {
      dataInput.value = hasBase ? canvas.toDataURL('image/png') : '';
    });
  }
})();
</script>
