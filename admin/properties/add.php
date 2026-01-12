<?php
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
include __DIR__ . '/../includes/db.php';

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
}

/* ============================================================
   FORM SUBMIT
============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = $_POST['name'];
    $type        = $_POST['type'];
    $location    = $_POST['location'];
    $size        = $_POST['size'];
    $dimensions  = $_POST['dimensions'];
    $facing      = $_POST['facing'];
    $status      = $_POST['status'];
    $description = $_POST['description'];
    $selected    = $_POST['amenities'] ?? [];

    /* ---------------------------------------------
       UPDATE PROPERTY
    ----------------------------------------------*/
    if ($edit) {

        $conn->query("
            UPDATE properties SET
                name='$name',
                type='$type',
                location='$location',
                size='$size',
                dimensions='$dimensions',
                facing='$facing',
                status='$status',
                description='$description'
            WHERE id=$id
        ");

        $conn->query("DELETE FROM property_amenities WHERE property_id=$id");

    } else {

        /* ---------------------------------------------
           INSERT NEW PROPERTY
        ----------------------------------------------*/
        $conn->query("
            INSERT INTO properties (name, type, location, size, dimensions, facing, status, description)
            VALUES ('$name', '$type', '$location', '$size', '$dimensions', '$facing', '$status', '$description')
        ");

        $id = $conn->insert_id;
    }

    /* ---------------------------------------------
       INSERT AMENITIES FOR THE PROPERTY
    ----------------------------------------------*/
    foreach ($selected as $a) {
        $conn->query("
            INSERT INTO property_amenities (property_id, amenity_id)
            VALUES ($id, $a)
        ");
    }

    /* ============================================================
       MULTIPLE IMAGE UPLOAD (FIXED & READY)
    ============================================================ */

    $uploadDir = realpath(__DIR__ . '/../uploads');

    if ($uploadDir === false) {
        $uploadDir = __DIR__ . '/../uploads';
        mkdir($uploadDir, 0777, true);
    }

    if (!empty($_FILES['images']['name'][0])) {

        foreach ($_FILES['images']['name'] as $index => $file) {

            $tmp = $_FILES['images']['tmp_name'][$index];

            if ($tmp == "" || !is_uploaded_file($tmp)) {
                continue;
            }

            $clean = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $file);
            $newName = time() . "_" . rand(1000,9999) . "_" . $clean;
            $path = $uploadDir . "/" . $newName;

            if (move_uploaded_file($tmp, $path)) {
                $conn->query("
                    INSERT INTO property_images (property_id, image_path)
                    VALUES ($id, '$newName')
                ");
            }
        }
    }

    header("Location: ../properties.php");
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
