<?php
/**
 * Bagicha 2.0 - Offcanvas Cart Drawer Component
 */
?>
<div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="cartDrawer" style="border-top-left-radius: 24px; border-bottom-left-radius: 24px; width: 420px; max-width: 90vw;">
  <div class="offcanvas-header bg-white border-bottom p-3">
    <div>
      <h5 class="offcanvas-title font-heading text-primary mb-0"><i class="bi bi-bag-check-fill text-success me-2"></i> Your Shopping Bag</h5>
      <small class="text-muted" id="cartDrawerItemCount">0 items</small>
    </div>
    <button type="button" class="btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  
  <!-- Free Shipping Progress Tracker Banner -->
  <div class="px-3 py-2 border-bottom text-center" style="background: var(--color-primary-subtle); color: var(--color-primary);">
    <div class="d-flex justify-content-between align-items-center mb-1 small fw-semibold">
      <span><i class="bi bi-truck me-1"></i> Express Shipping Goal</span>
      <span id="freeShipStatus" class="fw-bold">Add ₹999 for FREE Delivery</span>
    </div>
    <div class="progress" style="height: 6px; border-radius: 10px; background-color: #C8E3D2;">
      <div id="freeShipProgressBar" class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
  </div>

  <div class="offcanvas-body p-3" id="cartDrawerItems">
    <div class="text-center py-5 text-muted">
      <div class="spinner-border text-success" role="status"></div>
    </div>
  </div>

  <div class="offcanvas-footer p-3 bg-white border-top shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <span class="text-muted fw-semibold">Subtotal:</span>
      <span class="fw-bold fs-4 text-success font-heading" id="cartDrawerSubtotal">₹0.00</span>
    </div>
    <p class="small text-muted mb-3" style="font-size: 0.78rem;"><i class="bi bi-info-circle me-1"></i> Taxes & shipping calculated at checkout</p>
    <div class="d-grid gap-2">
      <a href="<?= BASE_URL ?>/pages/checkout.php" class="btn btn-bagicha-primary py-2.5 fw-bold text-center">
        Proceed to Checkout <i class="bi bi-arrow-right me-1"></i>
      </a>
      <a href="<?= BASE_URL ?>/pages/cart.php" class="btn btn-bagicha-outline btn-sm text-center">View Shopping Cart</a>
    </div>
  </div>
</div>

