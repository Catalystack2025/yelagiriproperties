<?php
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
include __DIR__ . '/../includes/db.php';

/* ===== LOAD PROPERTIES FROM DATABASE ===== */
$properties = [];
$sql = "SELECT * FROM properties ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
}
?>

<div class="admin-layout">
  <main class="admin-main">

    <!-- PAGE HEADER -->
    <div class="page-header">
      <h1>Properties</h1>
      <a href="add.php" class="action-btn">Add Property</a>
    </div>

    <!-- PROPERTIES TABLE -->
    <section class="dashboard-section">
      <table class="dashboard-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Type</th>
            <th>Location</th>
            <th>Dimensions</th>
            <th>Size</th>
            <th>Facing</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          <?php if (empty($properties)): ?>
            <tr>
              <td colspan="9">No properties found.</td>
            </tr>
          <?php endif; ?>

          <?php foreach ($properties as $p): ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['type']) ?></td>
              <td><?= htmlspecialchars($p['location']) ?></td>
              <td><?= htmlspecialchars($p['dimensions']) ?></td>
              <td><?= htmlspecialchars($p['size']) ?></td>
              <td><?= htmlspecialchars($p['facing']) ?></td>

              <td>
                <form action="update-status.php" method="POST" style="display:inline-block;">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <select name="status" onchange="this.form.submit()" class="status-dropdown">
                    <option value="Available" <?= $p['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                    <option value="Sold" <?= $p['status'] == 'Sold' ? 'selected' : '' ?>>Sold</option>
                    <option value="Blocked" <?= $p['status'] == 'Blocked' ? 'selected' : '' ?>>Blocked</option>
                  </select>
                </form>
              </td>

              <td>
                <a href="add-properties.php?id=<?= $p['id'] ?>" class="link">Edit</a>
                |
                <a href="delete-property.php?id=<?= $p['id'] ?>"
                   class="link danger"
                   onclick="return confirm('Delete this property?')">
                  Delete
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>

      </table>
    </section>

  </main>

  <?php include __DIR__ . '/../includes/footer.php'; ?>
</div>
