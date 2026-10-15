<?php
/**
 * Bagicha 2.0 - Checkout & Payment Page
 */
$pageTitle = "Checkout - Bagicha";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-success text-decoration-none">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/cart.php" class="text-success text-decoration-none">Cart</a></li>
      <li class="breadcrumb-item active" aria-current="page">Checkout</li>
    </ol>
  </nav>

  <h1 class="font-heading h2 text-primary mb-4"><i class="bi bi-credit-card me-2"></i> Checkout</h1>

  <div class="row g-4">
    <!-- Left Col: Delivery Address & Coupon -->
    <div class="col-lg-7">
      <!-- Shipping Address Box -->
      <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="font-heading text-primary mb-0"><i class="bi bi-geo-alt me-2"></i> Delivery Address</h5>
          <button class="btn btn-bagicha-outline btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
            <i class="bi bi-plus-lg me-1"></i> Add New Address
          </button>
        </div>

        <div id="checkoutAddressList">
          <div class="text-center py-4"><div class="spinner-border text-success"></div></div>
        </div>
      </div>

      <!-- Coupon Promo Code Box -->
      <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <h5 class="font-heading text-primary mb-3"><i class="bi bi-ticket-perforated me-2"></i> Have a Promo Code?</h5>
        <div class="input-group">
          <input type="text" id="couponCodeInput" class="form-control text-uppercase" placeholder="Try GREEN10 or BAGICHA100" style="border-radius: 10px 0 0 10px;">
          <button class="btn btn-bagicha-primary" type="button" onclick="CheckoutModule.applyCoupon()">Apply Coupon</button>
        </div>
        <small class="text-muted mt-2 d-block">Use <strong>GREEN10</strong> for 10% off or <strong>BAGICHA100</strong> for ₹100 off!</small>
      </div>
    </div>

    <!-- Right Col: Order Summary & Razorpay Pay Button -->
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm rounded-4 bg-white p-4 sticky-top" style="top: 100px;" id="checkoutSummaryBox">
        <div class="text-center py-5"><div class="spinner-border text-success"></div></div>
      </div>
    </div>
  </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title font-heading text-primary">New Delivery Address</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form onsubmit="CheckoutModule.saveNewAddress(event)">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Address Line 1</label>
            <input type="text" id="addrLine1" class="form-control" placeholder="House/Flat No, Building Name" required style="border-radius: 10px;">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Address Line 2 (Optional)</label>
            <input type="text" id="addrLine2" class="form-control" placeholder="Street, Area, Landmark" style="border-radius: 10px;">
          </div>
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">City</label>
              <input type="text" id="addrCity" class="form-control" placeholder="Mumbai" required style="border-radius: 10px;">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">State</label>
              <input type="text" id="addrState" class="form-control" placeholder="Maharashtra" required style="border-radius: 10px;">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Pincode</label>
            <input type="text" id="addrPincode" class="form-control" placeholder="400001" required style="border-radius: 10px;">
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="addrDefault" checked>
            <label class="form-check-label small" for="addrDefault">Set as default shipping address</label>
          </div>
          <button type="submit" class="btn btn-bagicha-primary w-100 py-2">Save Address</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  CheckoutModule.initCheckout();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
