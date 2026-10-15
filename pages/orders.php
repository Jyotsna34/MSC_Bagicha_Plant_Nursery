<?php
/**
 * Bagicha 2.0 - Customer Order History Page
 */
$pageTitle = "My Orders - Bagicha";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<main id="main-content" class="container customer-dashboard py-4 py-lg-5" tabindex="-1">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-success text-decoration-none">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/profile.php" class="text-success text-decoration-none">My Garden</a></li>
      <li class="breadcrumb-item active" aria-current="page">Orders</li>
    </ol>
  </nav>

  <section class="portal-page-header portal-orders-header mb-4" aria-labelledby="ordersTitle">
    <div class="portal-page-icon"><i class="bi bi-box-seam"></i></div>
    <div>
      <p class="account-eyebrow mb-1">Your purchases</p>
      <h1 id="ordersTitle" class="font-heading mb-1">Order history</h1>
      <p class="mb-0">Track every Bagicha delivery and revisit its details anytime.</p>
    </div>
    <a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-bagicha-primary ms-md-auto"><i class="bi bi-bag-plus me-2"></i>Shop plants</a>
  </section>

  <div id="ordersListContainer" aria-live="polite">
    <div class="portal-loading"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading orders</span></div><span>Loading your orders…</span></div>
  </div>
</main>

<!-- Order Detail Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-bottom p-4">
        <h5 class="modal-title font-heading text-primary" id="modalOrderTitle">Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4" id="modalOrderBody">
        <div class="text-center py-4"><div class="spinner-border text-success"></div></div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  const container = document.getElementById('ordersListContainer');
  if (!App.getToken()) {
    window.location.href = App.getBaseUrl() + '/pages/login.php';
    return;
  }

  try {
    const res = await axios.get('/orders/index.php');
    if (res.data.status === 'success') {
      const orders = res.data.data.orders;
      if (orders.length === 0) {
        container.innerHTML = `
          <div class="portal-empty-state">
            <span class="portal-empty-icon"><i class="bi bi-bag-heart"></i></span>
            <h2 class="h4 font-heading mt-3">Your plant shelf is waiting</h2>
            <p>You haven’t placed an order yet. Find a leafy companion for your space.</p>
            <a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-bagicha-primary"><i class="bi bi-arrow-right me-2"></i>Explore plants</a>
          </div>
        `;
        return;
      }

      container.innerHTML = orders.map(order => {
        const status = (order.order_status || 'processing').toLowerCase();
        const statusLabel = status.charAt(0).toUpperCase() + status.slice(1);
        const statusIcon = status === 'delivered' ? 'bi-check2-circle' : (status === 'shipped' ? 'bi-truck' : (status === 'cancelled' ? 'bi-x-circle' : 'bi-clock-history'));

        // Order Progress Stepper Math
        const stages = ['pending', 'processing', 'shipped', 'delivered'];
        const currentStageIdx = stages.indexOf(status);
        const isCancelled = status === 'cancelled';

        const stepperHtml = isCancelled ? `
          <div class="alert alert-danger bg-danger-subtle text-danger border-0 small py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2">
            <i class="bi bi-x-circle-fill"></i>
            <span>This order was cancelled.</span>
          </div>
        ` : `
          <div class="d-flex align-items-center justify-content-between position-relative my-3 px-2">
            <div class="position-absolute top-50 start-0 end-0 bg-secondary-subtle" style="height: 3px; z-index: 0; transform: translateY(-50%);"></div>
            <div class="position-absolute top-50 start-0 bg-success" style="height: 3px; z-index: 0; transform: translateY(-50%); width: ${Math.max(0, currentStageIdx) / 3 * 100}%; transition: width 0.4s;"></div>

            <div class="text-center position-relative" style="z-index: 1;">
              <span class="rounded-circle d-inline-flex align-items-center justify-content-center ${currentStageIdx >= 0 ? 'bg-success text-white' : 'bg-light text-muted border'}" style="width: 28px; height: 28px; font-size: 0.75rem;"><i class="bi bi-receipt"></i></span>
              <div class="small fw-semibold mt-1" style="font-size: 0.72rem;">Placed</div>
            </div>
            <div class="text-center position-relative" style="z-index: 1;">
              <span class="rounded-circle d-inline-flex align-items-center justify-content-center ${currentStageIdx >= 1 ? 'bg-success text-white' : 'bg-light text-muted border'}" style="width: 28px; height: 28px; font-size: 0.75rem;"><i class="bi bi-gear-fill"></i></span>
              <div class="small fw-semibold mt-1" style="font-size: 0.72rem;">Processing</div>
            </div>
            <div class="text-center position-relative" style="z-index: 1;">
              <span class="rounded-circle d-inline-flex align-items-center justify-content-center ${currentStageIdx >= 2 ? 'bg-success text-white' : 'bg-light text-muted border'}" style="width: 28px; height: 28px; font-size: 0.75rem;"><i class="bi bi-truck"></i></span>
              <div class="small fw-semibold mt-1" style="font-size: 0.72rem;">Shipped</div>
            </div>
            <div class="text-center position-relative" style="z-index: 1;">
              <span class="rounded-circle d-inline-flex align-items-center justify-content-center ${currentStageIdx >= 3 ? 'bg-success text-white' : 'bg-light text-muted border'}" style="width: 28px; height: 28px; font-size: 0.75rem;"><i class="bi bi-check-lg"></i></span>
              <div class="small fw-semibold mt-1" style="font-size: 0.72rem;">Delivered</div>
            </div>
          </div>
        `;

        return `
          <article class="order-card order-status-${status} mb-4 p-3 p-lg-4 rounded-4 shadow-sm border bg-white">
            <div class="order-card-main pb-3 mb-3 border-bottom">
              <div class="order-number-wrap">
                <span class="order-card-icon"><i class="bi bi-receipt"></i></span>
                <div><span class="order-label">Order number</span><h2 class="order-number mb-0">${order.order_number}</h2></div>
              </div>
              <div class="order-meta"><span class="order-label">Placed on</span><strong><i class="bi bi-calendar3 me-1"></i>${new Date(order.created_at).toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' })}</strong></div>
              <div class="order-meta"><span class="order-label">Payment</span><strong class="text-capitalize">${(order.payment_status || 'pending').replace(/_/g, ' ')}</strong></div>
              <div class="order-total"><span class="order-label">Total paid</span><strong>${App.formatCurrency(order.final_amount)}</strong></div>
            </div>
            ${stepperHtml}
            <div class="order-card-actions pt-3 mt-2 border-top d-flex justify-content-between align-items-center">
              <span class="order-status-pill"><i class="bi ${statusIcon}"></i> ${statusLabel}</span>
              <div class="order-action-buttons d-flex gap-2">
                <button class="btn btn-reorder btn-sm" onclick="reorderOrder(${order.id}, this)"><i class="bi bi-arrow-repeat me-1"></i> Buy again</button>
                <button class="btn btn-bagicha-outline btn-sm" onclick="showOrderDetails(${order.id})"><i class="bi bi-eye me-1"></i> View details</button>
              </div>
            </div>
          </article>
        `;
      }).join('');
    }
  } catch (err) {
    container.innerHTML = `<div class="portal-error-state"><i class="bi bi-exclamation-circle"></i><div><strong>We couldn’t load your orders.</strong><p class="mb-0">Please refresh the page and try again.</p></div></div>`;
  }
});

async function showOrderDetails(orderId) {
  const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
  modal.show();
  const body = document.getElementById('modalOrderBody');
  const title = document.getElementById('modalOrderTitle');

  try {
    const res = await axios.get(`/orders/detail.php?id=${orderId}`);
    if (res.data.status === 'success') {
      const { order, items } = res.data.data;
      title.textContent = `Order Details #${order.order_number}`;

      body.innerHTML = `
        <div class="order-detail-overview">
          <div><span>Shipping address</span><p>${order.shipping_address}</p></div>
          <div><span>Order date</span><p>${new Date(order.created_at).toLocaleString()}</p></div>
        </div>

        <h6 class="font-heading text-primary mb-3">Items in this order</h6>
        <div class="order-items-list mb-4">
          ${items.map(item => `
            <div class="order-detail-item">
              <div class="d-flex align-items-center gap-3">
                <img src="${item.main_image || 'https://via.placeholder.com/50'}" alt="${item.product_name}" class="order-item-image">
                <div>
                  <h6 class="mb-0 font-heading text-dark">${item.product_name}</h6>
                  <small class="text-muted">${App.formatCurrency(item.price)} x ${item.quantity}</small>
                </div>
              </div>
              <span class="fw-bold text-success">${App.formatCurrency(item.subtotal)}</span>
            </div>
          `).join('')}
        </div>

        <div class="order-detail-total">
          <span>Total Amount Paid</span>
          <span>${App.formatCurrency(order.final_amount)}</span>
        </div>
      `;
    }
  } catch (err) {
    body.innerHTML = `<div class="alert alert-danger">Failed to load details.</div>`;
  }
}

async function reorderOrder(orderId, button) {
  const originalContent = button.innerHTML;
  button.disabled = true;
  button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding';

  try {
    const res = await axios.post('/orders/reorder.php', { order_id: orderId });
    if (res.data.status === 'success') {
      await CartModule.fetchCart();
      CartModule.openCartDrawer();
      App.showToast(res.data.message);
    }
  } catch (err) {
    App.showToast(err.response?.data?.message || 'Unable to add this order to your cart.', 'error');
  } finally {
    button.disabled = false;
    button.innerHTML = originalContent;
  }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
