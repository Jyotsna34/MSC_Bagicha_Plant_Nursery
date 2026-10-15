<?php
/**
 * Bagicha 2.0 - Order Success Page
 */
$pageTitle = "Order Placed - Bagicha";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4" style="max-width: 680px;">
  <!-- Breadcrumb Navigation -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-success text-decoration-none">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/products.php" class="text-success text-decoration-none">Shop</a></li>
      <li class="breadcrumb-item active" aria-current="page">Order Confirmation</li>
    </ol>
  </nav>

  <div id="orderSuccessBox" class="text-center">
    <div class="spinner-border text-success" role="status"></div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  const urlParams = new URLSearchParams(window.location.search);
  const orderId = urlParams.get('order_id');
  const box = document.getElementById('orderSuccessBox');

  if (!orderId) {
    window.location.href = App.getBaseUrl() + '/index.php';
    return;
  }

  try {
    const res = await axios.get(`/orders/detail.php?id=${orderId}`);
    if (res.data.status === 'success') {
      const { order, items } = res.data.data;
      
      box.innerHTML = `
        <div class="card border-0 shadow-lg p-5 rounded-4 bg-white text-center">
          <div class="display-1 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
          <span class="badge bg-success-subtle text-success px-3 py-2 fs-6 mb-2 rounded-pill">Payment Successful</span>
          <h1 class="font-heading text-primary h2 mb-2">Thank You For Your Order!</h1>
          <p class="text-muted mb-4">Order Number: <strong class="text-dark">${order.order_number}</strong></p>

          <div class="p-3 bg-light rounded-4 text-start mb-4">
            <h6 class="font-heading text-primary mb-2"><i class="bi bi-truck me-2"></i> Shipping Details</h6>
            <p class="small text-muted mb-1">Deliver to: ${order.shipping_address}</p>
            <p class="small text-success mb-0"><i class="bi bi-calendar-event me-1"></i> Estimated Delivery: <strong>3-5 Business Days</strong></p>
          </div>

          <h6 class="font-heading text-start mb-3">Purchased Plants</h6>
          <div class="list-group list-group-flush text-start mb-4">
            ${items.map(item => `
              <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 bg-transparent">
                <div class="d-flex align-items-center gap-2">
                  <img src="${item.main_image || 'https://via.placeholder.com/50'}" class="rounded-2" style="width: 45px; height: 45px; object-fit: cover;">
                  <div>
                    <span class="fw-semibold text-dark small d-block">${item.product_name}</span>
                    <small class="text-muted">Qty: ${item.quantity}</small>
                  </div>
                </div>
                <span class="fw-bold text-success">${App.formatCurrency(item.subtotal)}</span>
              </div>
            `).join('')}
          </div>

          <div class="d-flex justify-content-between fw-bold fs-5 mb-4 text-primary border-top pt-3">
            <span>Total Paid</span>
            <span>${App.formatCurrency(order.final_amount)}</span>
          </div>

          <div class="d-flex gap-2 justify-content-center">
            <a href="<?= BASE_URL ?>/pages/orders.php" class="btn btn-bagicha-primary">
              <i class="bi bi-box-seam me-1"></i> View Order History
            </a>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-bagicha-outline">
              Return Home
            </a>
          </div>
        </div>
      `;
    }
  } catch (err) {
    box.innerHTML = `<div class="alert alert-danger">Failed to load order confirmation details.</div>`;
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
