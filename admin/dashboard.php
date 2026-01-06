<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

/* ===== BASE PATH ===== */
$dataPath = realpath(__DIR__ . '/../frontend/data/content.json');

/* ===== LOAD CONTENT.JSON (ENQUIRIES) ===== */
$contentFile = $dataPath . '/content.json';

$contentData = file_exists($contentFile)
  ? json_decode(file_get_contents($contentFile), true)
  : [];

$enquiries = $contentData['enquiries'] ?? [];

/* ===== LOAD PROPERTIES (JSON IF EXISTS, ELSE EMPTY) ===== */
$propertiesFile = $dataPath . '/properties.html';

$properties = file_exists($propertiesFile)
  ? json_decode(file_get_contents($propertiesFile), true)
  : [];

/* ===== CALCULATIONS ===== */
$totalProperties = count($properties);
$activeProperties = count(array_filter($properties, fn($p) => ($p['status'] ?? '') === 'Available'));
$closedDeals = count(array_filter($properties, fn($p) => ($p['status'] ?? '') === 'Sold'));
$newEnquiries = count($enquiries);

/* ===== LOCATION INSIGHTS ===== */
$locations = [];
foreach ($properties as $p) {
  if (!empty($p['location'])) {
    $locations[$p['location']] = ($locations[$p['location']] ?? 0) + 1;
  }
}
?>

<div class="admin-layout">

  <main class="admin-main">

    <!-- SUMMARY CARDS -->
    <section class="dashboard-cards">
      <div class="card"><h3>Total Properties</h3><p><?= $totalProperties ?></p></div>
      <div class="card"><h3>Active Listings</h3><p><?= $activeProperties ?></p></div>
      <div class="card"><h3>Closed Deals</h3><p><?= $closedDeals ?></p></div>
      <div class="card"><h3>New Enquiries</h3><p><?= $newEnquiries ?></p></div>
    </section>


    <!-- PROPERTY STATUS -->
    <section class="dashboard-grid">
      <div class="dashboard-box">
        <h2>Property Status</h2>
        <ul>
          <li>Available: <?= $activeProperties ?></li>
          <li>Sold: <?= $closedDeals ?></li>
          <li>Blocked: <?= count(array_filter($properties, fn($p) => $p['status'] === 'Blocked')) ?></li>
        </ul>
      </div>

      <div class="dashboard-box">
        <h2>Location Insights</h2>
        <ul>
          <?php foreach ($locations as $loc => $count): ?>
            <li><?= htmlspecialchars($loc) ?> – <?= $count ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>

    <!-- RECENT ENQUIRIES -->
    <section class="dashboard-section">
      <h2>Recent Enquiries</h2>
      <table class="dashboard-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Property</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (array_slice($enquiries, 0, 5) as $e): ?>
          <tr>
            <td><?= htmlspecialchars($e['name']) ?></td>
            <td><?= htmlspecialchars($e['phone']) ?></td>
            <td><?= htmlspecialchars($e['property']) ?></td>
            <td><?= htmlspecialchars($e['date']) ?></td>
            <td><?= htmlspecialchars($e['status']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    

    <!-- RECENT PROPERTIES -->
    <section class="dashboard-section">
      <h2>Recently Added Properties</h2>
      <table class="dashboard-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Location</th>
            <th>Type</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (array_slice($properties, 0, 5) as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['title']) ?></td>
            <td><?= htmlspecialchars($p['location']) ?></td>
            <td><?= htmlspecialchars($p['type']) ?></td>
            <td><?= htmlspecialchars($p['status']) ?></td>
            <td><?= htmlspecialchars($p['date']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

</div>
