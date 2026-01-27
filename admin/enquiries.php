<?php
include __DIR__ . '/includes/db.php';

// Toggle read/unread BEFORE output
if (isset($_GET['toggle_read'])) {
    $id = intval($_GET['toggle_read']);
    $conn->query("UPDATE enquiries SET is_read = IF(is_read = 1, 0, 1) WHERE id = $id");
    echo "<script>window.location='enquiries.php';</script>";
    exit;
}

include __DIR__ . '/includes/auth-guard.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

// Ensure table exists
$conn->query("
CREATE TABLE IF NOT EXISTS enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    message TEXT,
    property_id INT DEFAULT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

// Ensure is_read exists
$check = $conn->query("SHOW COLUMNS FROM enquiries LIKE 'is_read'");
if ($check->num_rows == 0) {
    $conn->query("ALTER TABLE enquiries ADD COLUMN is_read TINYINT(1) DEFAULT 0");
}

// Fetch enquiries
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
            <th>Status</th>
          </tr>
        </thead>

        <tbody>
          <?php if (empty($enquiries)): ?>
            <tr><td colspan="8">No enquiries yet.</td></tr>
          <?php endif; ?>

          <?php foreach ($enquiries as $e): ?>
            <tr class="<?= $e['is_read'] ? '' : 'unread-row'; ?>">
              <td><?= $e['id']; ?></td>
              <td><?= htmlspecialchars($e['name']); ?></td>
              <td><?= htmlspecialchars($e['phone']); ?></td>
              <td><?= htmlspecialchars($e['email']); ?></td>
              <td><?= htmlspecialchars($e['message']); ?></td>
              <td><?= $e['property_id'] ?: '-'; ?></td>
              <td><?= $e['created_at']; ?></td>
              <td>
                <?php if ($e['is_read']): ?>
                  <a href="?toggle_read=<?= $e['id']; ?>" class="status-badge read">Read</a>
                <?php else: ?>
                  <a href="?toggle_read=<?= $e['id']; ?>" class="status-badge unread">Unread</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

  </main>
  <?php include __DIR__ . '/includes/footer.php'; ?>
</div>

<style>
.unread-row {
  background: #f8fbff;
  font-weight: 600;
}

.status-badge {
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-decoration: none;
  display: inline-block;
  transition: 0.2s ease;
}

.status-badge.read {
  background: #e8f8ef;
  color: #15803d;
  border: 1px solid #bbf7d0;
}

.status-badge.unread {
  background: #eff6ff;
  color: #f70505;
  border: 1px solid #bfdbfe;
}

.status-badge:hover {
  transform: scale(1.05);
}
</style>
