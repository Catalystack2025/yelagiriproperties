<?php
include __DIR__ . '/../includes/auth-guard.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
include __DIR__ . '/../includes/db.php';

$blogs = [];
$res = $conn->query("SELECT * FROM blogs ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $blogs[] = $row;
    }
}
?>
<div class="admin-layout">
  <main class="admin-main">

    <div class="page-header">
      <h1>Blogs</h1>
      <a href="add-blog.php" class="action-btn">Add Blog</a>
    </div>

    <section class="dashboard-section">
      <table class="dashboard-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>Date</th>
            <th>Slug</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($blogs)): ?>
            <tr><td colspan="5">No blogs yet.</td></tr>
          <?php endif; ?>

          <?php foreach ($blogs as $b): ?>
            <tr>
              <td><?= $b['id']; ?></td>
              <td><?= htmlspecialchars($b['title']); ?></td>
              <td><?= htmlspecialchars($b['date']); ?></td>
              <td><?= htmlspecialchars($b['slug']); ?></td>
              <td>
                <a class="link" href="edit-blog.php?id=<?= $b['id']; ?>">Edit</a> |
                <form action="delete.php" method="POST" style="display:inline;" onsubmit="return confirm('Delete this blog?')">
                  <input type="hidden" name="id" value="<?= $b['id']; ?>">
                  <input type="hidden" name="type" value="blog">
                  <button type="submit" class="link danger" style="border:none;background:none;padding:0;cursor:pointer;">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

  </main>
  <?php include __DIR__ . '/../includes/footer.php'; ?>
</div>
