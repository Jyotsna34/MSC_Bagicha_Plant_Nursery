<?php
/**
 * Bagicha 2.0 - Shopping Cart Page
 */
$pageTitle = "Your Shopping Cart - Bagicha";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-success text-decoration-none">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
    </ol>
  </nav>

  <h1 class="font-heading h2 text-primary mb-4"><i class="bi bi-bag-check me-2"></i> Shopping Cart</h1>

  <div class="row g-4">
    <!-- Cart Items Table -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr class="text-muted small">
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="fullCartItems">
              <tr>
                <td colspan="5" class="text-center py-5">
                  <div class="spinner-border text-success" role="status"></div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Summary Box -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <h5 class="font-heading text-primary mb-3">Order Summary</h5>
        <div class="d-flex justify-content-between mb-2 text-muted">
          <span>Subtotal</span>
          <span id="fullCartSubtotal" class="fw-bold text-dark">₹0.00</span>
        </div>
        <div class="d-flex justify-content-between mb-2 text-muted">
          <span>Estimated Shipping</span>
          <span id="fullCartShipping" class="fw-bold text-success">FREE</span>
        </div>
        <small class="text-muted d-block mb-3">Free shipping on plant orders above ₹999!</small>
        <hr>
        <div class="d-flex justify-content-between mb-4 fs-5 font-heading text-primary">
          <span>Total</span>
          <span id="fullCartTotal">₹0.00</span>
        </div>
        
        <a href="<?= BASE_URL ?>/pages/checkout.php" class="btn btn-bagicha-accent btn-lg w-100 py-3 mb-2 font-heading">
          Proceed to Checkout <i class="bi bi-arrow-right ms-2"></i>
        </a>
        <a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-bagicha-outline w-100">
          Continue Shopping
        </a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  await CartModule.fetchCart();

  const subtotalEl = document.getElementById('fullCartSubtotal');
  const shippingEl = document.getElementById('fullCartShipping');
  const totalEl = document.getElementById('fullCartTotal');

  if (subtotalEl) subtotalEl.textContent = App.formatCurrency(CartModule.cartState.summary.subtotal);
  if (shippingEl) shippingEl.textContent = CartModule.cartState.summary.shipping_fee === 0 ? 'FREE' : App.formatCurrency(CartModule.cartState.summary.shipping_fee);
  if (totalEl) totalEl.textContent = App.formatCurrency(CartModule.cartState.summary.total);
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
