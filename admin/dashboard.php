<?php
require_once __DIR__ . '/includes/auth-guard.php';
require_once __DIR__ . '/includes/db.php';

/* =====================
   DASHBOARD METRICS
===================== */
$totalBlogs = $conn->query("SELECT COUNT(*) c FROM blogs")->fetch_assoc()['c'];
$publishedBlogs = $conn->query("SELECT COUNT(*) c FROM blogs WHERE status=1")->fetch_assoc()['c'];
$draftBlogs = $conn->query("SELECT COUNT(*) c FROM blogs WHERE status=0")->fetch_assoc()['c'];

$totalProperties = $conn->query("SELECT COUNT(*) c FROM properties")->fetch_assoc()['c'];
$totalEnquiries = $conn->query("SELECT COUNT(*) c FROM enquiries")->fetch_assoc()['c'];

$latestBlogs = $conn->query("
  SELECT title, published_date
  FROM blogs
  ORDER BY id DESC
  LIMIT 5
");

$latestEnquiries = $conn->query("
  SELECT name, created_at
  FROM enquiries
  ORDER BY id DESC
  LIMIT 5
");

$adminName = $_SESSION['admin_user']['name'];
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-wrapper">
<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

<main class="admin-main dashboard-bordered">

  <!-- HEADER -->
  <header class="dash-header">
    <h1>Dashboard</h1>
    <p>Welcome back, <strong><?= htmlspecialchars($adminName) ?></strong></p>
  </header>

  <!-- OVERVIEW -->
  <section class="dash-section">
    <h2 class="section-title">Properties & Enquiries</h2>

    <div class="metric-row two-small">

      <div class="metric-card small clickable bg-light"
           onclick="location.href='properties/list.php'">
        <span>Total Properties</span>
        <strong><?= $totalProperties ?></strong>
      </div>

      <div class="metric-card small clickable bg-light-alt"
           onclick="location.href='enquiries.php'">
        <span>Total Enquiries</span>
        <strong><?= $totalEnquiries ?></strong>
      </div>

    </div>
  </section>

  <!-- BLOGS -->
  <section class="dash-section">
    <h2 class="section-title">Blogs</h2>

    <div class="metric-row">
      <div class="metric-card clickable bg-white"
           onclick="location.href='blogs/list.php'">
        <span>Total Blogs</span>
        <strong><?= $totalBlogs ?></strong>
      </div>

      <div class="metric-card clickable bg-light"
           onclick="location.href='blogs/list.php?status=published'">
        <span>Published Blogs</span>
        <strong><?= $publishedBlogs ?></strong>
      </div>

      <div class="metric-card clickable bg-light-alt"
           onclick="location.href='blogs/list.php?status=draft'">
        <span>Draft Blogs</span>
        <strong><?= $draftBlogs ?></strong>
      </div>
    </div>
  </section>

  <!-- LISTS -->
  <section class="dash-content">

    <div class="dash-panel bg-white">
      <div class="panel-head">
        <h3>Latest Blogs</h3>
        <a href="blogs/list.php">View all</a>
      </div>

      <ul class="clean-list">
        <?php while ($b = $latestBlogs->fetch_assoc()): ?>
          <li>
            <span><?= htmlspecialchars($b['title']) ?></span>
            <time><?= date('d M Y', strtotime($b['published_date'])) ?></time>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>

    <div class="dash-panel bg-light">
      <div class="panel-head">
        <h3>Latest Enquiries</h3>
        <a href="enquiries.php">View all</a>
      </div>

      <ul class="clean-list">
        <?php while ($e = $latestEnquiries->fetch_assoc()): ?>
          <li>
            <span><?= htmlspecialchars($e['name']) ?></span>
            <time><?= date('d M Y', strtotime($e['created_at'])) ?></time>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>

  </section>

</main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<style>
/* ===============================
   MODERN CLEAN DASHBOARD UI
================================ */
:root {
  --primary: #6366f1;
  --bg-main: #f8fafc;
  --border-color: #e2e8f0;
  --text-dark: #1e293b;
  --text-muted: #64748b;
  --radius: 8px; /* Your requested radius */
}

.admin-main {
  background: var(--bg-main);
  padding: 30px;
  font-family: 'Inter', system-ui, sans-serif;
  color: var(--text-dark);
}

/* Header */
.dash-header {
  margin-bottom: 40px;
}

.dash-header h1 {
  font-size: 24px;
  font-weight: 800;
  letter-spacing: -0.025em;
  margin: 0;
}

.dash-header p {
  color: var(--text-muted);
  margin-top: 4px;
}

/* Sections */
.dash-section {
  margin-bottom: 40px;
}

.section-title {
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--text-muted);
  font-weight: 700;
  margin-bottom: 16px;
}

/* Layout Grid */
.metric-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
}

.metric-row.two-small {
  grid-template-columns: repeat(2, 1fr);
  max-width: 600px;
}

/* Cards & Panels */
.metric-card,
.dash-panel {
  background: #ffffff;
  border: 1px solid var(--border-color);
  border-radius: var(--radius);
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  transition: all 0.2s ease;
}

.metric-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  border-color: var(--primary);
}

/* Light Colorful Backgrounds */
.bg-white { background: #ffffff; border-left: 4px solid var(--primary); }
.bg-light { background: #f0fdf4; border-left: 4px solid #22c55e; } /* Soft Green */
.bg-light-alt { background: #eff6ff; border-left: 4px solid #3b82f6; } /* Soft Blue */

/* Card text */
.metric-card span {
  display: block;
  font-size: 13px;
  color: var(--text-muted);
  font-weight: 500;
}

.metric-card strong {
  display: block;
  font-size: 28px;
  margin-top: 10px;
  color: var(--text-dark);
}

/* Panels */
.dash-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}

.panel-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.panel-head h3 {
  font-size: 16px;
  margin: 0;
  font-weight: 600;
}

.panel-head a {
  font-size: 13px;
  color: var(--primary);
  text-decoration: none;
  font-weight: 600;
}

.panel-head a:hover { text-decoration: underline; }

/* Lists */
.clean-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.clean-list li {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f1f5f9;
}

.clean-list li:last-child { border-bottom: none; }

.clean-list span {
  font-size: 14px;
  font-weight: 500;
}

.clean-list time {
  font-size: 12px;
  color: var(--text-muted);
  background: #f1f5f9;
  padding: 4px 8px;
  border-radius: 4px;
}

/* Responsive */
@media (max-width: 768px) {
  .dash-content, .metric-row.two-small {
    grid-template-columns: 1fr;
  }
}
</style>