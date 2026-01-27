<?php
// sidebar.php
include __DIR__ . '/db.php';

$baseURL = "admin/"; 
$currentPage = basename($_SERVER['PHP_SELF']);

// Fetch unread enquiry count
$unreadCount = 0;
$resUnread = $conn->query("SELECT COUNT(*) AS total FROM enquiries WHERE is_read = 0");
if ($resUnread) {
    $unreadCount = $resUnread->fetch_assoc()['total'];
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="../assets/css/admin.css">

<style>
.badge-icon {
  position: relative;
  display: inline-flex;
  align-items: center;
}

/* Minimal modern badge */
.mobile-dot {
  position: absolute;
  top: -6px;
  right: -10px;

  background: #3b82f6; /* soft modern blue */
  color: #ffffff;

  font-size: 9px;
  font-weight: 600;

  min-width: 16px;
  height: 16px;

  border-radius: 999px;
  display: flex;
  align-items: center;
  justify-content: center;

  border: 2px solid #0b1220; /* match sidebar bg */
  box-shadow: 0 2px 6px rgba(59, 130, 246, 0.35);

  transform: scale(0.95);
}

</style>

<aside class="admin-sidebar" id="adminSidebar">
  <nav class="sidebar-nav">

    <a href="<?php echo $baseURL; ?>dashboard.php"
       class="nav-item <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
      <span class="nav-icon"><i class="fas fa-th-large"></i></span>
      <span class="nav-text">Dashboard</span>
    </a>

    <a href="<?php echo $baseURL; ?>properties/list.php"
       class="nav-item <?php echo (strpos($_SERVER['PHP_SELF'], '/properties/') !== false) ? 'active' : ''; ?>">
      <span class="nav-icon"><i class="fas fa-building"></i></span>
      <span class="nav-text">Properties</span>
    </a>

    <a href="<?php echo $baseURL; ?>blogs/list.php"
       class="nav-item <?php echo (strpos($_SERVER['PHP_SELF'], '/blogs/') !== false) ? 'active' : ''; ?>">
      <span class="nav-icon"><i class="fas fa-pen-nib"></i></span>
      <span class="nav-text">Blogs</span>
    </a>

    <!-- Enquiries with Mobile-Style Dot -->
    <a href="<?php echo $baseURL; ?>enquiries.php"
       class="nav-item <?php echo ($currentPage == 'enquiries.php') ? 'active' : ''; ?>">

      <span class="nav-icon badge-icon">
        <i class="fas fa-envelope-open-text"></i>

        <?php if ($unreadCount > 0): ?>
          <span class="mobile-dot"><?= $unreadCount > 9 ? '9+' : $unreadCount; ?></span>
        <?php endif; ?>
      </span>

      <span class="nav-text">Enquiries</span>
    </a>

    <a href="<?php echo $baseURL; ?>my-account.php"
       class="nav-item <?php echo ($currentPage == 'my-account.php') ? 'active' : ''; ?>">
      <span class="nav-icon"><i class="fas fa-user-cog"></i></span>
      <span class="nav-text">My Profile</span>
    </a>

  </nav>
</aside>
