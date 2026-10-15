<?php

/**
 * Bagicha 2.0 - Navbar Component with Role-Based Navigation
 */
?>
<?php $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: ''; ?>

<!-- Top Announcement Bar -->
<div class="announcement-bar py-2 text-center text-white small fw-medium" style="background: linear-gradient(90deg, #033B20 0%, #064E2B 50%, #033B20 100%); letter-spacing: 0.02em;">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="d-none d-md-flex gap-3 text-white-50 small">
      <span><i class="bi bi-truck me-1 text-warning"></i> Free Express Shipping over ₹999</span>
      <span><i class="bi bi-shield-check me-1 text-warning"></i> 100% Arrive-Alive Guarantee</span>
    </div>
    <div class="mx-auto mx-md-0">
      <span>🌿 Summer Plant Sale: Get 10% OFF with code <strong class="text-warning">BAGICHA10</strong></span>
    </div>
    <div class="d-none d-lg-flex gap-3 text-white-50 small">
      <a href="tel:+919876543210" class="text-white-50 text-decoration-none hover-white"><i class="bi bi-telephone me-1"></i> Support</a>
    </div>
  </div>
</div>

<nav class="navbar navbar-expand-lg sticky-top bagicha-navbar" aria-label="Primary navigation">
  <div class="container">
    <a class="brand-logo me-4" href="<?= BASE_URL ?>/index.php">
      <img src="<?= BASE_URL ?>/img/logo-3.png" alt="Bagicha Logo" class="brand-logo-img">
    </a>

    <button class="navbar-toggler border-0 shadow-none p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link nav-link-custom <?= str_ends_with($currentPath, '/index.php') || $currentPath === BASE_URL . '/' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-custom <?= str_ends_with($currentPath, '/products.php') || str_ends_with($currentPath, '/product-detail.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>/pages/products.php">Shop</a>
        </li>

        <li class="nav-item">
          <a class="nav-link nav-link-custom" href="<?= BASE_URL ?>/index.php#about" id="navAboutLink">About Us</a>
        </li>
      </ul>

      <!-- Search Bar with Live Autocomplete -->
      <form class="d-flex me-lg-3 my-3 my-lg-0 position-relative" action="<?= BASE_URL ?>/pages/products.php" method="GET" role="search" id="navbarSearchForm">
        <div class="search-bar-wrap w-100">
          <i class="bi bi-search search-bar-icon"></i>
          <input type="search" name="search" id="navbarSearchInput" class="form-control search-bar-input" placeholder="Search plants, seeds..." aria-label="Search products" autocomplete="off">
          <span class="search-kbd-badge d-none d-xl-inline-block">⌘K</span>
          <div id="searchAutocompleteDropdown" class="search-autocomplete-dropdown"></div>
        </div>
      </form>

      <!-- Cart, Wishlist, Auth Buttons -->
      <div class="d-flex align-items-center gap-3">
        <!-- Real-time Notifications Bell -->
        <div class="dropdown d-none" id="userNotificationWrap">
          <button class="btn btn-link p-0 nav-icon-button text-dark position-relative fs-5 text-decoration-none" id="userNotificationBell" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications" style="border: none; background: transparent;">
            <i class="bi bi-bell"></i>
            <span class="badge bg-danger icon-badge d-none" id="userNotifBadge">0</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0" id="userNotifDropdown" style="width: 320px; max-height: 400px; overflow-y: auto; border-radius: 16px; z-index: 1050;">
            <li class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
              <span class="fw-bold text-dark font-heading">Notifications</span>
              <button class="btn btn-link text-success p-0 text-decoration-none small" id="userMarkAllReadBtn" style="font-size: 0.85rem;">Mark all read</button>
            </li>
            <div id="userNotifList">
              <li class="p-3 text-center text-muted">No notifications yet</li>
            </div>
          </ul>
        </div>

        <a href="<?= BASE_URL ?>/pages/wishlist.php" class="nav-icon-button text-dark position-relative fs-5 text-decoration-none" aria-label="Wishlist" title="Wishlist">
          <i class="bi bi-heart" id="navbarWishlistIcon"></i>
          <span class="badge bg-danger icon-badge d-none" id="wishlistCountBadge">0</span>
        </a>
        <button type="button" class="btn btn-link p-0 nav-icon-button text-dark position-relative fs-5 text-decoration-none" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer" aria-label="Open shopping cart" title="Shopping Cart" style="border: none; background: transparent;">
          <i class="bi bi-bag"></i>
          <span class="badge bg-success icon-badge cart-badge" id="cartCountBadge">0</span>
        </button>

        <!-- Guest Links -->
        <div id="guestNav" class="d-flex gap-2">
          <button class="btn btn-bagicha-outline btn-sm" data-bs-toggle="modal" data-bs-target="#authModal" data-tab="login">
            Login
          </button>
          <button class="btn btn-bagicha-primary btn-sm" data-bs-toggle="modal" data-bs-target="#authModal" data-tab="register">
            Register
          </button>
        </div>

        <!-- User Profile Dropdown -->
        <div id="userNav" class="dropdown d-none">
          <button class="btn btn-bagicha-outline btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i> <span id="userNameDisplay">Account</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2" style="border-radius: 16px; min-width: 220px;">
            <!-- Dedicated Admin Panel Dropdown Item -->
            <li id="adminNavDropdownItem" class="d-none">
              <a class="dropdown-item fw-bold text-success bg-success-subtle rounded-3" href="<?= BASE_URL ?>/admin/index.php">
                <i class="bi bi-speedometer2 me-2"></i> Admin Dashboard
              </a>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>/pages/profile.php"><i class="bi bi-columns-gap me-2"></i> User Dashboard</a></li>
            <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>/pages/orders.php"><i class="bi bi-box-seam me-2"></i> My Orders</a></li>
            <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>/pages/wishlist.php"><i class="bi bi-heart me-2"></i> Wishlist</a></li>

            <li><a class="dropdown-item rounded-2" href="<?= BASE_URL ?>/pages/profile.php#settings"><i class="bi bi-gear me-2"></i> Account Settings</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item text-danger rounded-2" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</nav>

<?php require_once __DIR__ . '/cart-drawer.php'; ?>

