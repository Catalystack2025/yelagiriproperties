<?php
include __DIR__ . '/includes/auth-guard.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
include __DIR__ . '/includes/db.php';

$conn->query("
CREATE TABLE IF NOT EXISTS enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    message TEXT,
    property_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

$enquiries = [];
$res = $conn->query("SELECT * FROM enquiries ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $enquiries[] = $row;
    }
}
?>
<div class="admin-layout">
  <main class="admin-main">

    <div class="page-header">
      <h1>Enquiries</h1>
      <p>Leads submitted from the website</p>
    </div>

    <section class="dashboard-section">
      <table class="dashboard-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Message</th>
            <th>Property ID</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($enquiries)): ?>
            <tr><td colspan="7">No enquiries yet.</td></tr>
          <?php endif; ?>

          <?php foreach ($enquiries as $e): ?>
            <tr>
              <td><?= $e['id']; ?></td>
              <td><?= htmlspecialchars($e['name']); ?></td>
              <td><?= htmlspecialchars($e['phone']); ?></td>
              <td><?= htmlspecialchars($e['email']); ?></td>
              <td><?= htmlspecialchars($e['message']); ?></td>
              <td><?= $e['property_id'] ?: '-'; ?></td>
              <td><?= $e['created_at']; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

  </main>
  <?php include __DIR__ . '/includes/footer.php'; ?>
</div>
