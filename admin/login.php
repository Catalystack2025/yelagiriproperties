<?php
include __DIR__ . '/includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure admin_users table exists
$conn->query("
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(150) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

// Seed default admin if empty
$check = $conn->query("SELECT COUNT(*) as total FROM admin_users");
$total = ($check && $check->num_rows) ? (int)$check->fetch_assoc()['total'] : 0;
if ($total === 0) {
    $defaultHash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO admin_users (username, password_hash, name) VALUES ('admin', ?, 'Administrator')");
    $stmt->bind_param("s", $defaultHash);
    $stmt->execute();
    $stmt->close();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res && $res->num_rows ? $res->fetch_assoc() : null;
    $stmt->close();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'name' => $user['name']
        ];
        header("Location: /yelagiriproperties/admin/dashboard.php");
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="./assets/css/admin.css">
    <style>
        body { background:#f8fafc; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .login-card { width: 380px; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 12px 30px rgba(0,0,0,0.08); }
        .login-card h1 { margin: 0 0 10px 0; }
        .login-card p { color:#6b7280; margin-bottom:20px; }
        .login-card input { width:100%; padding:12px; border:1px solid #e5e7eb; border-radius:10px; margin-bottom:12px; }
        .login-card button { width:100%; padding:12px; background:var(--primary); color:#fff; border:none; border-radius:10px; font-weight:700; cursor:pointer; }
        .error { color:#dc2626; margin-bottom:10px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>Admin Login</h1>
        <p>Use your credentials to access the admin panel.</p>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p style="font-size:12px; color:#94a3b8; margin-top:12px;">Default: admin / admin123 (change in My Account)</p>
    </div>
</body>
</html>
