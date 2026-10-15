<?php
/**
 * Bagicha 2.0 - Admin Sidebar Component
 * Supports Collapsible/Expandable desktop view, mobile drawer, quick actions, and secure logout.
 */
$currentAdminPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Mobile Admin Header Bar (< 992px) -->
<div class="admin-mobile-topbar d-lg-none d-flex align-items-center justify-content-between px-3 py-2">
  <div class="d-flex align-items-center gap-2">
    <button type="button" class="btn btn-outline-secondary btn-sm p-1 px-2" id="sidebarMobileOpenBtn" aria-label="Open Navigation Menu">
      <i class="bi bi-list fs-5"></i>
    </button>
    <a href="<?= BASE_URL ?>/admin/index.php" class="d-flex align-items-center gap-2 text-decoration-none">
      <img src="<?= BASE_URL ?>/img/logo-3.png" alt="Bagicha Logo" style="height: 26px;">
      <span class="fw-bold text-light small text-uppercase tracking-wider">Admin</span>
    </a>
  </div>
  <div class="d-flex align-items-center gap-2">
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="AdminApp.toggleTheme()" aria-label="Toggle theme">
      <i class="bi bi-sun-fill text-warning"></i>
    </button>
    <a href="<?= BASE_URL ?>/index.php" target="_blank" class="btn btn-outline-secondary btn-sm" title="View Storefront">
      <i class="bi bi-shop"></i>
    </a>
    <!-- Mobile Header Quick Logout -->
    <button type="button" class="btn btn-outline-danger btn-sm" onclick="AdminApp.logout()" title="Sign Out of Admin" aria-label="Sign out">
      <i class="bi bi-box-arrow-right"></i>
    </button>
  </div>
</div>

<!-- Backdrop Overlay for Mobile Drawer -->
<div class="admin-sidebar-backdrop" id="adminSidebarBackdrop"></div>

<!-- Admin Sidebar Navigation -->
<aside class="admin-sidebar" id="adminSidebar">
  <div class="admin-sidebar-header d-flex align-items-center justify-content-between">
    <a class="brand d-flex align-items-center gap-2 text-decoration-none" href="<?= BASE_URL ?>/admin/index.php">
      <img src="<?= BASE_URL ?>/img/logo-3.png" alt="Bagicha Logo" class="admin-brand-logo-img">
      <span class="brand-text fs-6 text-uppercase tracking-wider fw-bold text-light opacity-75">Admin</span>
    </a>
    <button type="button" class="btn-sidebar-toggle d-none d-lg-flex" id="sidebarCollapseBtn" aria-label="Toggle Sidebar" title="Collapse sidebar (Ctrl+B)">
      <i class="bi bi-chevron-left" id="sidebarToggleIcon"></i>
    </button>
  </div>

  <nav class="nav flex-column mt-2">
    <div class="admin-sidebar-section"><span>Core Operations</span></div>
    <a class="nav-link-admin <?= $currentAdminPage === 'index.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/index.php" title="Dashboard">
      <i class="bi bi-speedometer2"></i>
      <span class="nav-link-text">Dashboard</span>
    </a>
    <a class="nav-link-admin <?= $currentAdminPage === 'products.php' || $currentAdminPage === 'inventory.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/products.php" title="Products">
      <i class="bi bi-tree"></i>
      <span class="nav-link-text">Products</span>
    </a>
    <a class="nav-link-admin <?= $currentAdminPage === 'categories.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/categories.php" title="Categories">
      <i class="bi bi-grid"></i>
      <span class="nav-link-text">Categories</span>
    </a>
    <a class="nav-link-admin <?= $currentAdminPage === 'orders.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/orders.php" title="Orders">
      <i class="bi bi-box-seam"></i>
      <span class="nav-link-text">Orders</span>
    </a>
    <a class="nav-link-admin <?= $currentAdminPage === 'customers.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/customers.php" title="Users">
      <i class="bi bi-people"></i>
      <span class="nav-link-text">Users</span>
    </a>
    <a class="nav-link-admin <?= $currentAdminPage === 'messages.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/messages.php" title="Messages">
      <i class="bi bi-chat-left-text"></i>
      <span class="nav-link-text">Messages</span>
    </a>

    <div class="admin-sidebar-section"><span>Analytics & System</span></div>
    <a class="nav-link-admin <?= $currentAdminPage === 'reports.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/reports.php" title="Reports">
      <i class="bi bi-bar-chart"></i>
      <span class="nav-link-text">Reports</span>
    </a>
    <a class="nav-link-admin <?= $currentAdminPage === 'settings.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/settings.php" title="Settings">
      <i class="bi bi-gear"></i>
      <span class="nav-link-text">Settings</span>
    </a>
  </nav>

  <div class="mt-auto p-3 border-top border-secondary-subtle">
    <!-- Admin Profile Link -->
    <a class="nav-link-admin <?= $currentAdminPage === 'profile.php' ? 'active' : '' ?> text-light p-0 mb-2" href="<?= BASE_URL ?>/admin/profile.php" title="Admin Profile">
      <i class="bi bi-person-badge"></i>
      <span class="nav-link-text">Admin Profile</span>
    </a>

    <!-- Theme Switcher Button -->
    <button type="button" class="btn btn-outline-secondary btn-sm w-100 mb-2 d-flex align-items-center justify-content-center gap-2" onclick="AdminApp.toggleTheme()" title="Switch Light/Dark Mode">
      <i id="themeToggleIcon" class="bi bi-sun-fill text-warning"></i>
      <span class="nav-link-text" id="themeToggleText">Light Mode</span>
    </button>

    <a class="nav-link-admin text-light p-0 mb-2" href="<?= BASE_URL ?>/index.php" target="_blank" title="View Storefront">
      <i class="bi bi-shop"></i>
      <span class="nav-link-text">Storefront</span>
    </a>

    <!-- Clearly Visible Admin Logout Button -->
    <button type="button" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2 btn-admin-logout" onclick="AdminApp.logout()" title="Sign Out from Admin" aria-label="Sign out of Admin">
      <i class="bi bi-box-arrow-right"></i>
      <span class="nav-link-text">Logout</span>
    </button>
  </div>
</aside>