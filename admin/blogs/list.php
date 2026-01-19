<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth-guard.php';

$blogs = $conn->query("SELECT * FROM blogs ORDER BY id DESC");
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="admin-wrapper">

  <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

  <!-- MAIN CONTENT -->
  <main class="admin-main">

    <div class="page-header">
      <h1>Blogs</h1>
      <a href="add-blog.php" class="btn-primary">+ Add Blog</a>
    </div>

    <div class="content-card">

      <?php if ($blogs && $blogs->num_rows > 0): ?>
        <table class="data-table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Slug</th>
              <th>Status</th>
              <th>Date</th>
              <th style="width:140px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $blogs->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['slug']) ?></td>
                <td>
                  <span class="status <?= $row['status'] ? 'published' : 'draft' ?>">
                    <?= $row['status'] ? 'Published' : 'Draft' ?>
                  </span>
                </td>
                <td><?= htmlspecialchars($row['published_date']) ?></td>
                <td class="actions">
                  <a href="edit-blog.php?id=<?= $row['id'] ?>" class="edit">Edit</a>
                  <a href="delete.php?id=<?= $row['id'] ?>"
                     class="delete"
                     onclick="return confirm('Delete this blog?')">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="empty-state">No blogs found.</p>
      <?php endif; ?>

    </div>

  </main>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<!-- =========================
     PAGE CSS (SAFE + FIXED)
========================== -->
<style>
/* Layout */
.admin-wrapper {
  display: flex;
  min-height: calc(100vh - 70px);
  background: #f4f6f8;
}

.admin-main {
  flex: 1;
  padding: 32px 36px 90px;
  background: #ffffff;
}

/* Header row */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-header h1 {
  font-size: 22px;
  font-weight: 600;
  margin: 0;
}

/* Button */
.btn-primary {
  background: #1e7c43;
  color: #fff;
  padding: 10px 18px;
  border-radius: 10px;
  font-weight: 600;
  text-decoration: none;
}

.btn-primary:hover {
  background: #166534;
}

/* Card */
.content-card {
  background: #ffffff;
  padding: 20px;
  border-radius: 14px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.06);
}

/* Table */
.data-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

.data-table th,
.data-table td {
  padding: 14px 16px;
  border-bottom: 1px solid #e5e7eb;
  text-align: left;
  word-wrap: break-word;
}

.data-table th {
  background: #f9fafb;
  font-weight: 600;
  color: #374151;
}

.data-table td:nth-child(2) {
  max-width: 260px;
}

/* Status */
.status {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}

.status.published {
  background: #e6f6ea;
  color: #0a7a32;
}

.status.draft {
  background: #fff4e5;
  color: #a66300;
}

/* Actions */
.actions a {
  margin-right: 12px;
  font-weight: 600;
  text-decoration: none;
}

.actions .edit {
  color: #2563eb;
}

.actions .delete {
  color: #dc2626;
}

/* Empty */
.empty-state {
  padding: 30px;
  color: #777;
}

/* Responsive */
@media (max-width: 768px) {
  .admin-main {
    padding: 24px;
  }

  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
}
</style>
