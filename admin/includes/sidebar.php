<?php
// sidebar.php
$baseURL = "/yelagiriproperties/admin/"; 
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="../assets/css/admin.css">

<aside class="admin-sidebar" id="adminSidebar">
  <nav class="sidebar-nav">

    <a href="<?php echo $baseURL; ?>dashboard.php"
       class="nav-item <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
      <span class="nav-icon"><i class="fas fa-th-large"></i></span>
      <span class="nav-text">Dashboard</span>
    </a>

    <a href="<?php echo $baseURL; ?>hero-banner.php"
       class="nav-item <?php echo ($currentPage == 'hero-banner.php') ? 'active' : ''; ?>">
      <span class="nav-icon"><i class="fas fa-image"></i></span>
      <span class="nav-text">Hero Banner</span>
    </a>

    <a href="<?php echo $baseURL; ?>properties/list.php"
       class="nav-item <?php echo (strpos($_SERVER['PHP_SELF'], '/properties/') !== false) ? 'active' : ''; ?>">
      <span class="nav-icon"><i class="fas fa-building"></i></span>
      <span class="nav-text">Properties</span>
    </a>

    <a href="<?php echo $baseURL; ?>./blogs/add-blog.php"
       class="nav-item <?php echo (strpos($_SERVER['PHP_SELF'], './blogs/') !== false) ? 'active' : ''; ?>">
      <span class="nav-icon"><i class="fas fa-pen-nib"></i></span>
      <span class="nav-text">Blogs</span>
    </a>

    <a href="<?php echo $baseURL; ?>enquiries.php"
       class="nav-item <?php echo ($currentPage == 'enquiries.php') ? 'active' : ''; ?>">
      <span class="nav-icon"><i class="fas fa-envelope-open-text"></i></span>
      <span class="nav-text">Enquiries</span>
    </a>

    <a href="<?php echo $baseURL; ?>settings.php"
       class="nav-item <?php echo ($currentPage == 'settings.php') ? 'active' : ''; ?>">
      <span class="nav-icon"><i class="fas fa-cog"></i></span>
      <span class="nav-text">Settings</span>
    </a>

  </nav>
</aside>
