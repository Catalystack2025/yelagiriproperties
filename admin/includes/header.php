<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_user'])) {
    header("Location: /yelagiriproperties/admin/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel | Yelagiri Properties</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Admin CSS -->
  <link rel="stylesheet" href="/yelagiriproperties/admin/assets/css/admin.css">

</head>
<body>

<!-- ===== TOP HEADER ===== -->
<header class="admin-header">

  <!-- LEFT SIDE: BRAND -->
  <div class="header-left">
    <div class="brand">
      <span class="brand-text">Yelagiri Properties</span>
    </div>
  </div>

  <!-- RIGHT SIDE: NAME + AVATAR + DROPDOWN -->
  <div class="header-right">
    <div class="admin-profile">

      <span class="admin-name">Admin</span>

      <div class="admin-avatar" id="adminDropdownTrigger" role="button" aria-label="Admin Menu">A</div>

      <div class="dropdown-menu" id="adminDropdownMenu">
        <a href="profile.php" class="dropdown-item">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
          My Profile
        </a>

        <a href="logout.php" class="dropdown-item logout-link">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          Logout
        </a>
      </div>

    </div>
  </div>

</header>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('adminDropdownTrigger');
    const menu = document.getElementById('adminDropdownMenu');

    if (trigger && menu) {
      trigger.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        menu.classList.toggle('active');
      });

      document.addEventListener('click', function(e) {
        if (!trigger.contains(e.target) && !menu.contains(e.target)) {
          menu.classList.remove('active');
        }
      });
    }
  });

  // Safety: remove any unexpected full-page overlays that block clicks
  document.addEventListener('DOMContentLoaded', function() {
    const blockers = Array.from(document.body.children).filter(el => {
      const s = getComputedStyle(el);
      if (s.position !== 'fixed') return false;
      const w = el.offsetWidth;
      const h = el.offsetHeight;
      return w >= window.innerWidth * 0.9 && h >= window.innerHeight * 0.9 && (parseInt(s.zIndex || '0', 10) >= 900);
    });
    blockers.forEach(el => { el.style.display = 'none'; });
  });
</script>
