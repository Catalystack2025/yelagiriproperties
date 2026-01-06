
<?php

// sidebar.php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<link rel="stylesheet" href="../assets/css/admin.css">
<aside class="admin-sidebar" id="adminSidebar">

  

  <!-- Navigation -->
  <nav class="sidebar-nav">

    <a href="dashboard.php"
       class="nav-item <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
      <span class="nav-icon">🏠</span>
      <span class="nav-text">Dashboard</span>
    </a>

    <a href="hero-banner.php"
       class="nav-item <?php echo ($currentPage == 'hero-banner.php') ? 'active' : ''; ?>">
      <span class="nav-icon">➕</span>
      <span class="nav-text">Hero Banner </span>
    </a>

    <a href="properties.php"
       class="nav-item <?php echo ($currentPage == 'properties.php') ? 'active' : ''; ?>">
      <span class="nav-icon">🏘️</span>
      <span class="nav-text">Properties</span>
    </a>

    

    <a href="blogs.php"
       class="nav-item <?php echo ($currentPage == 'blogs.php') ? 'active' : ''; ?>">
      <span class="nav-icon">📝</span>
      <span class="nav-text">Blogs</span>
    </a>

    <a href="enquiries.php"
       class="nav-item <?php echo ($currentPage == 'enquiries.php') ? 'active' : ''; ?>">
      <span class="nav-icon">📩</span>
      <span class="nav-text">Enquiries</span>
    </a>

    <a href="settings.php"
       class="nav-item <?php echo ($currentPage == 'settings.php') ? 'active' : ''; ?>">
      <span class="nav-icon">⚙️</span>
      <span class="nav-text">Settings</span>
    </a>

  </nav>


</aside>
