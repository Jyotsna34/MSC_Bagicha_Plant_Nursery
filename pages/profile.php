<?php
/**
 * Bagicha 2.0 - Customer Dashboard & Profile Page
 * Comprehensive user portal with Overview, Orders, Wishlist, Quick Actions, and Address Book.
 */
$pageTitle = "User Dashboard - Bagicha";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<main id="main-content" class="container customer-dashboard py-4 py-lg-5" tabindex="-1">
  <!-- Breadcrumb Navigation -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-success text-decoration-none">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">User Dashboard</li>
    </ol>
  </nav>

  <!-- Welcome Header Banner -->
  <section class="account-welcome mb-4 mb-lg-5" aria-labelledby="dashboardTitle">
    <div class="account-avatar" aria-hidden="true"><i class="bi bi-person-fill"></i></div>
    <div class="flex-grow-1">
      <p class="account-eyebrow mb-1">Your Bagicha Account</p>
      <h1 id="dashboardTitle" class="font-heading mb-1">Welcome back, <span id="welcomeName">Plant Parent</span></h1>
      <p class="mb-0 account-welcome-copy">Overview of your plant collection, orders, care guides, and settings.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-bagicha-primary account-shop-btn">
        <i class="bi bi-plus-lg me-1"></i> Shop Plants
      </a>
      <button type="button" class="btn btn-outline-danger btn-sm px-3" onclick="App.logout()">
        <i class="bi bi-box-arrow-right me-1"></i> Logout
      </button>
    </div>
  </section>

  <!-- User Dashboard Quick Navigation & Overview KPIs -->
  <section class="row g-3 mb-4" aria-label="Account overview stats">
    <div class="col-sm-6 col-lg-4">
      <a class="dashboard-stat dashboard-stat-orders" href="<?= BASE_URL ?>/pages/orders.php">
        <span class="dashboard-stat-icon"><i class="bi bi-box-seam"></i></span>
        <span><strong id="ordersCount">—</strong><small>Orders placed</small></span>
        <i class="bi bi-arrow-up-right dashboard-stat-arrow"></i>
      </a>
    </div>
    <div class="col-sm-6 col-lg-4">
      <a class="dashboard-stat dashboard-stat-wishlist" href="<?= BASE_URL ?>/pages/wishlist.php">
        <span class="dashboard-stat-icon"><i class="bi bi-heart" id="profileWishlistIcon"></i></span>
        <span><strong id="wishlistCount">—</strong><small>Plants in wishlist</small></span>
        <i class="bi bi-arrow-up-right dashboard-stat-arrow"></i>
      </a>
    </div>
    <div class="col-sm-6 col-lg-4">
      <button type="button" class="dashboard-stat dashboard-stat-address text-start w-100" data-bs-toggle="modal" data-bs-target="#profileAddrModal">
        <span class="dashboard-stat-icon"><i class="bi bi-geo-alt"></i></span>
        <span><strong id="addressCount">—</strong><small>Saved addresses</small></span>
        <i class="bi bi-plus-lg dashboard-stat-arrow"></i>
      </button>
    </div>
  </section>

  <!-- Quick Feature Flow Links -->
  <section class="row g-3 mb-4" aria-label="Customer quick actions">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center fs-4 flex-shrink-0" style="width: 50px; height: 50px;">
            <i class="bi bi-tree"></i>
          </div>
          <div>
            <h6 class="font-heading mb-1 text-dark">Explore Nursery Catalog</h6>
            <p class="small text-muted mb-0">Discover air-purifying indoor plants & fresh arrivals.</p>
          </div>
        </div>
        <a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-bagicha-outline btn-sm">Shop Now</a>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center fs-4 flex-shrink-0" style="width: 50px; height: 50px;">
            <i class="bi bi-truck"></i>
          </div>
          <div>
            <h6 class="font-heading mb-1 text-dark">Live Order Tracking</h6>
            <p class="small text-muted mb-0">Track transit milestones with your order reference.</p>
          </div>
        </div>
        <a href="<?= BASE_URL ?>/pages/track-order.php" class="btn btn-bagicha-outline btn-sm">Track Order</a>
      </div>
    </div>
  </section>

  <!-- Profile & Address Settings -->
  <div class="row g-4" id="settings">
    <div class="col-lg-5">
      <section class="account-panel h-100" aria-labelledby="profileHeading">
        <div class="account-panel-heading">
          <div><p class="account-eyebrow mb-1">Personal details</p><h2 id="profileHeading" class="h4 mb-0">Your profile</h2></div>
          <div class="d-flex align-items-center gap-2">
            <span id="profRole" class="account-role">Customer</span>
            <button class="btn btn-bagicha-outline btn-sm profile-edit-btn" data-bs-toggle="modal" data-bs-target="#changePasswordModal" title="Change Password"><i class="bi bi-shield-lock me-1"></i>Password</button>
            <button class="btn btn-bagicha-outline btn-sm profile-edit-btn" data-bs-toggle="modal" data-bs-target="#profileEditModal"><i class="bi bi-pencil-square me-1"></i>Edit</button>
          </div>
        </div>
        <div class="profile-details mt-4">
          <div class="profile-detail"><span class="profile-detail-icon"><i class="bi bi-person"></i></span><div><small>Full name</small><strong id="profName">Loading…</strong></div></div>
          <div class="profile-detail"><span class="profile-detail-icon"><i class="bi bi-envelope"></i></span><div><small>Email address</small><strong id="profEmail">Loading…</strong></div></div>
          <div class="profile-detail"><span class="profile-detail-icon"><i class="bi bi-telephone"></i></span><div><small>Phone number</small><strong id="profPhone">Not added</strong></div></div>
        </div>
        <div class="account-help mt-4"><i class="bi bi-shield-check"></i><span>Your profile credentials and delivery information are protected.</span></div>
      </section>
    </div>

    <div class="col-lg-7">
      <section class="account-panel h-100" aria-labelledby="addressHeading">
        <div class="account-panel-heading">
          <div><p class="account-eyebrow mb-1">Delivery preferences</p><h2 id="addressHeading" class="h4 mb-0">Saved addresses</h2></div>
          <button class="btn btn-bagicha-primary btn-sm" type="button" onclick="openAddressForm()"><i class="bi bi-plus-lg me-1"></i> Add address</button>
        </div>
        <div id="profileAddressContainer" class="address-list mt-4"><div class="text-center py-4"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading addresses</span></div></div></div>
      </section>
    </div>
  </div>

  <!-- Plant Care Reminders & Personalized Recommendations -->
  <section class="row g-4 mt-1" aria-label="Plant care and recommendations">
    <div class="col-lg-5">
      <div class="account-panel h-100">
        <div class="account-panel-heading"><div><p class="account-eyebrow mb-1">Care today</p><h2 class="h4 mb-0">Your plant reminders</h2></div><span class="care-section-icon"><i class="bi bi-droplet"></i></span></div>
        <div id="careReminders" class="care-reminders mt-4"><div class="portal-mini-loading"><div class="spinner-border spinner-border-sm text-success" role="status"></div>Loading care tips…</div></div>
      </div>
    </div>
    <div class="col-lg-7">
      <div class="account-panel h-100">
        <div class="account-panel-heading"><div><p class="account-eyebrow mb-1">Picked for you</p><h2 class="h4 mb-0">Grow your collection</h2></div><a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-link text-success p-0 fw-semibold">View all <i class="bi bi-arrow-right"></i></a></div>
        <div id="personalRecommendations" class="row g-3 mt-1"></div>
      </div>
    </div>
  </section>

  <!-- Recently Viewed Plants -->
  <section class="account-panel mt-4" aria-labelledby="recentPlantsTitle">
    <div class="account-panel-heading"><div><p class="account-eyebrow mb-1">Browse again</p><h2 id="recentPlantsTitle" class="h4 mb-0">Recently viewed plants</h2></div><a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-link text-success p-0 fw-semibold">Explore plants <i class="bi bi-arrow-right"></i></a></div>
    <div id="recentlyViewedPlants" class="recent-plants-grid mt-4"></div>
  </section>
</main>

<!-- Profile Edit Modal -->
<div class="modal fade" id="profileEditModal" tabindex="-1" aria-labelledby="profileEditModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-0 pb-0">
        <div><p class="account-eyebrow mb-1">Personal details</p><h2 class="modal-title font-heading h4 mb-0" id="profileEditModalTitle">Edit Profile</h2></div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form id="profileEditForm" onsubmit="saveProfile(event)">
          <div class="mb-3"><label for="editProfName" class="form-label small fw-semibold">Full name</label><input type="text" id="editProfName" class="form-control" maxlength="100" required autocomplete="name"></div>
          <div class="mb-3"><label for="editProfEmail" class="form-label small fw-semibold">Email address</label><input type="email" id="editProfEmail" class="form-control bg-light" readonly aria-describedby="profileEmailHelp"><div id="profileEmailHelp" class="form-text">Email changes require support verification.</div></div>
          <div class="mb-4"><label for="editProfPhone" class="form-label small fw-semibold">Phone number</label><input type="tel" id="editProfPhone" class="form-control" maxlength="20" autocomplete="tel" placeholder="10-digit phone number"></div>
          <button id="saveProfileBtn" type="submit" class="btn btn-bagicha-primary w-100 py-2"><i class="bi bi-check2 me-1"></i>Save changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Profile Add / Edit Address Modal -->
<div class="modal fade" id="profileAddrModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title font-heading text-primary" id="addressModalTitle">Add Shipping Address</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="profileAddressForm" onsubmit="saveProfileAddress(event)">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Address Line 1</label>
            <input type="text" id="pAddr1" class="form-control" placeholder="Flat No, Building Name" required style="border-radius: 10px;">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Address Line 2 (Optional)</label>
            <input type="text" id="pAddr2" class="form-control" placeholder="Street, Area" style="border-radius: 10px;">
          </div>
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">City</label>
              <input type="text" id="pCity" class="form-control" placeholder="City" required style="border-radius: 10px;">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">State</label>
              <input type="text" id="pState" class="form-control" placeholder="State" required style="border-radius: 10px;">
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Pincode</label>
              <input type="text" id="pPincode" class="form-control" placeholder="6-digit Pincode" maxlength="6" required style="border-radius: 10px;">
            </div>
            <div class="col-md-6 d-flex align-items-center mt-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="pAddrDefault">
                <label class="form-check-label small" for="pAddrDefault">Default address</label>
              </div>
            </div>
          </div>
          <button type="submit" id="saveAddressBtn" class="btn btn-bagicha-primary w-100 py-2">Save Address</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/profile.js?v=<?= time() ?>"></script>

<?php 
require_once __DIR__ . '/../includes/change-password-modal.php';
require_once __DIR__ . '/../includes/footer.php'; 
?>
