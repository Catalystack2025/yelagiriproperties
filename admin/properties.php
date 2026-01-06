<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

/* ===== LOAD PROPERTIES FROM JSON ===== */
$dataPath = realpath(__DIR__ . '/../frontend/data');
$contentFile = $dataPath . '/content.json';

$data = file_exists($contentFile)
  ? json_decode(file_get_contents($contentFile), true)
  : [];

$properties = $data['properties'] ?? [];
?>

<div class="admin-layout">
  <main class="admin-main">

    <!-- PAGE HEADER -->
    <div class="page-header">
      <h1>Properties</h1>
      <a href="add-properties.php" class="action-btn">➕ Add Property</a>
    </div>

    <!-- PROPERTIES TABLE -->
    <section class="dashboard-section">
      <table class="dashboard-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Property Name</th>
            <th>Type</th>
            <th>Location</th>
            <th>Size</th>
            <th>Facing</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          <?php if (empty($properties)): ?>
            <tr>
              <td colspan="8">No properties found.</td>
            </tr>
          <?php endif; ?>

          <?php foreach ($properties as $index => $p): ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['type']) ?></td>
              <td><?= htmlspecialchars($p['location']) ?></td>
              <td><?= htmlspecialchars($p['size']) ?></td>
              <td><?= htmlspecialchars($p['facing']) ?></td>

              <td>
                <span class="status <?= strtolower($p['status'] ?? 'available') ?>">
                  <?= htmlspecialchars($p['status'] ?? 'Available') ?>
                </span>
              </td>

              <td>
                <a href="add-properties.php?id=<?= $p['id'] ?>" class="link">Edit</a>
                |
                <a href="add-properties.php?id=<?= $p['id'] ?>"
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

  <?php include __DIR__ . '/includes/footer.php'; ?>
</div>
