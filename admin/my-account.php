<?php
include __DIR__ . '/includes/auth-guard.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
include __DIR__ . '/includes/db.php';

$userId = $_SESSION['admin_user']['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($name !== '') {
        $stmt = $conn->prepare("UPDATE admin_users SET name=? WHERE id=?");
        $stmt->bind_param("si", $name, $userId);
        $stmt->execute();
        $_SESSION['admin_user']['name'] = $name;
        $stmt->close();
    }

    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admin_users SET password_hash=? WHERE id=?");
        $stmt->bind_param("si", $hash, $userId);
        $stmt->execute();
        $stmt->close();
    }

    $message = 'Profile updated.';
}

$userRes = $conn->query("SELECT * FROM admin_users WHERE id=$userId LIMIT 1");
$user = $userRes->fetch_assoc();
?>
<div class="admin-layout">
  <main class="admin-main">
    <div class="page-header">
      <h1>My Account</h1>
      <p>Update your profile and password</p>
    </div>

    <section class="dashboard-section">
      <?php if ($message): ?><div class="notice success"><?= htmlspecialchars($message); ?></div><?php endif; ?>
      <form method="post" class="form-grid" style="max-width:520px;">
        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="form-group">
          <label>Username (read-only)</label>
          <input type="text" value="<?= htmlspecialchars($user['username']); ?>" disabled>
        </div>
        <div class="form-group">
          <label>New Password (leave blank to keep current)</label>
          <input type="password" name="password" placeholder="••••••••">
        </div>
        <button type="submit" class="btn-primary">Save Changes</button>
      </form>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</div>
