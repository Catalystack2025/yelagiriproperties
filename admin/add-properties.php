<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-layout">
  <main class="admin-main admin-main--wide">

    <div class="form-page">

      <!-- PAGE HEADER -->
      <header class="form-header">
        <h1>Add Property</h1>
        <p>Create or update a property listing</p>
      </header>

      <!-- FORM -->
      <form class="property-form" method="post" enctype="multipart/form-data">

        <!-- BASIC INFORMATION -->
        <section class="form-card">
          <h3>Basic Information</h3>

          <div class="form-grid">
            <div class="col-6">
              <label>Property Name *</label>
              <input type="text" name="name" placeholder="Green Valley Plot" required>
            </div>

            <div class="col-6">
              <label>Property Type *</label>
              <select name="type" required>
                <option value="">Select Type</option>
                <option>Plot</option>
                <option>Villa</option>
                <option>Home Stays</option>
               
              </select>
            </div>

            <div class="col-6">
              <label>Location *</label>
              <input type="text" name="location" placeholder="Yelagiri" required>
            </div>

            <div class="col-6">
              <label>Size *</label>
              <input type="text" name="size" placeholder="2500 sqft / 2 Acres" required>
            </div>

            <div class="col-6">
              <label>Facing *</label>
              <input type="text" name="facing" placeholder="North, South, East, West" required>
            </div>

            <div class="col-6">
              <label>Status *</label>
              <select name="status" required>
                <option value="Draft">Draft</option>
                <option value="Published" selected>Published</option>
                <option value="Sold">Sold</option>
                <option value="Blocked">Blocked</option>
              </select>
            </div>
          </div>
        </section>

        <!-- PROPERTY IMAGES -->
        <section class="form-card">
          <h3>Property Images</h3>
          <div class="upload-box">
            <input type="file" name="images[]" multiple accept="image/*" required>
            <p>Click or drag images here (multiple allowed)</p>
          </div>
        </section>
        

        <!-- DESCRIPTION -->
        <section class="form-card">
          <h3>Description</h3>
          <textarea name="description" rows="5"
            placeholder="Write a detailed description about the property..." required></textarea>
        </section>

        <!-- AMENITIES -->
        <section class="form-card">
          <h3>Amenities</h3>
          <input type="text" name="amenities"
            placeholder="Water Supply, Road Access, EB Connection, Parking">
        </section>

        <!-- ACTIONS -->
        <div class="form-actions">
          <a href="properties.php" class="btn-outline">Cancel</a>
          <button type="submit" class="btn-primary">Save Property</button>
        </div>

      </form>

    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</div>
