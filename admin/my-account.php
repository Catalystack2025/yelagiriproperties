<?php
require_once __DIR__ . '/includes/auth-guard.php';
require_once __DIR__ . '/includes/db.php';

$userId = $_SESSION['admin_user']['id'] ?? 0;
$message = '';
$error = '';

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($name === '') {
    $error = 'Name cannot be empty.';
  } else {

    // Update name
    $stmt = $conn->prepare("UPDATE admin_users SET name=? WHERE id=?");
    $stmt->bind_param("si", $name, $userId);
    $stmt->execute();
    $_SESSION['admin_user']['name'] = $name;
    $stmt->close();

    // Update password if provided
    if ($password !== '') {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("UPDATE admin_users SET password_hash=? WHERE id=?");
      $stmt->bind_param("si", $hash, $userId);
      $stmt->execute();
      $stmt->close();
    }

    $message = 'Profile updated successfully.';
  }
}

// Fetch user safely
$user = [
  'name' => 'Admin',
  'username' => 'admin'
];

$userRes = $conn->prepare("SELECT * FROM admin_users WHERE id=? LIMIT 1");
$userRes->bind_param("i", $userId);
$userRes->execute();
$result = $userRes->get_result();

if ($result && $result->num_rows > 0) {
  $user = $result->fetch_assoc();
}
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-wrapper">

  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="admin-main">

    <div class="page-header">
      <h1>My Account</h1>
      <p>Manage your profile information and password</p>
    </div>

    <div class="content-card account-card">

      <?php if ($message): ?>
        <div class="notice success"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="notice error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" class="account-form">

        <div class="avatar-box">
          <div class="avatar-circle">
            <?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>
          </div>
          <div class="avatar-text">
            <strong><?= htmlspecialchars($user['name'] ?? 'Admin') ?></strong><br>
            <small><?= htmlspecialchars($user['username'] ?? 'admin') ?></small>
          </div>
        </div>

        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label>Username</label>
          <input type="text" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled>
        </div>

        <div class="form-group">
          <label>New Password</label>
          <input type="password" name="password" placeholder="Leave blank to keep current">
        </div>

        <button type="submit" class="btn-primary">Save Changes</button>

      </form>

    </div>

  </main>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<style>
.account-card {
  max-width: 520px;
}

.account-form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.avatar-box {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 10px;
}

.avatar-circle {
  width: 56px;
  height: 56px;
  background: #1e7c43;
  color: #fff;
  border-radius: 50%;
  font-size: 22px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-text small {
  color: #6b7280;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-weight: 600;
  margin-bottom: 6px;
  font-size: 14px;
}

.form-group input {
  padding: 11px 12px;
  border-radius: 8px;
  border: 1px solid #d1d5db;
  font-size: 14px;
}

.notice {
  padding: 12px;
  border-radius: 8px;
  font-weight: 600;
  margin-bottom: 14px;
}

.notice.success {
  background: #e6f6ea;
  color: #065f46;
}

.notice.error {
  background: #fee2e2;
  color: #991b1b;
}

.btn-primary {
  align-self: flex-start;
  background: #1e7c43;
  color: #fff;
  padding: 12px 22px;
  border-radius: 10px;
  border: none;
  font-weight: 600;
  cursor: pointer;
  margin-top: 10px;
}

@media (max-width: 768px) {
  .account-card {
    max-width: 100%;
  }
}
</style>
