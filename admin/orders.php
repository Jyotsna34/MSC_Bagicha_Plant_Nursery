<?php
/**
 * Bagicha 2.0 - Admin Orders Management & Fulfillment Page
 */
require_once __DIR__ . '/../api/helpers/auth.php';
requireAdminPage();

$adminTitle = "Manage Orders - Bagicha Admin";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-content">
  <!-- Breadcrumb Navigation -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/index.php" class="text-success text-decoration-none">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Orders</li>
    </ol>
  </nav>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="font-heading h2 text-primary mb-1">Customer Orders Fulfillment</h1>
      <p class="text-muted small mb-0">Track customer purchases, update order dispatch status, and process payments.</p>
    </div>
    <select id="adminStatusFilter" class="form-select form-select-sm w-auto rounded-3">
      <option value="">All Order Statuses</option>
      <option value="pending">Pending</option>
      <option value="processing">Processing</option>
      <option value="shipped">Shipped</option>
      <option value="delivered">Delivered</option>
      <option value="cancelled">Cancelled</option>
    </select>
  </div>

  <div class="admin-card p-4">
    <div class="table-responsive">
      <table class="table table-admin-custom align-middle mb-0">
        <thead class="small">
          <tr>
            <th>Order #</th>
            <th>Customer Info</th>
            <th>Total Amount</th>
            <th>Payment Status</th>
            <th>Order Fulfillment Status</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="adminOrdersTable">
          <tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-success"></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-bottom p-3">
        <h5 class="modal-title font-heading text-primary" id="orderModalTitle">Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4" id="orderModalBody">
        <div class="text-center py-4"><div class="spinner-border text-success"></div></div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  if (!AdminApp.checkAdminAuth()) return;
  await loadAdminOrders();

  document.getElementById('adminStatusFilter').addEventListener('change', async (e) => {
    await loadAdminOrders(e.target.value);
  });
});

async function loadAdminOrders(status = '') {
  const table = document.getElementById('adminOrdersTable');
  try {
    const res = await axios.get(`/admin/orders/index.php?status=${status}`, { timeout: 8000 });
    if (res.data.status === 'success') {
      const orders = res.data.data.orders;
      if (orders.length === 0) {
        table.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No orders found.</td></tr>`;
        return;
      }

      table.innerHTML = orders.map(o => `
        <tr>
          <td class="fw-bold text-primary font-heading">${o.order_number}</td>
          <td>
            <div class="fw-semibold">${o.customer_name}</div>
            <small class="text-muted">${o.customer_email} | ${o.customer_phone || 'No phone'}</small>
          </td>
          <td class="fw-bold text-success fs-6">${App.formatCurrency(o.final_amount)}</td>
          <td>
            <select class="form-select form-select-sm w-auto d-inline-block fw-semibold ${o.payment_status === 'paid' ? 'text-success border-success' : 'text-warning border-warning'}" 
                    onchange="AdminApp.updatePaymentStatus(${o.id}, this.value)">
              <option value="pending" ${o.payment_status === 'pending' ? 'selected' : ''}>PENDING</option>
              <option value="paid" ${o.payment_status === 'paid' ? 'selected' : ''}>PAID</option>
              <option value="failed" ${o.payment_status === 'failed' ? 'selected' : ''}>FAILED</option>
            </select>
          </td>
          <td>
            <select class="form-select form-select-sm w-auto d-inline-block fw-semibold" onchange="AdminApp.updateOrderStatus(${o.id}, this.value)">
              <option value="pending" ${o.order_status === 'pending' ? 'selected' : ''}>Pending</option>
              <option value="processing" ${o.order_status === 'processing' ? 'selected' : ''}>Processing</option>
              <option value="shipped" ${o.order_status === 'shipped' ? 'selected' : ''}>Shipped</option>
              <option value="delivered" ${o.order_status === 'delivered' ? 'selected' : ''}>Delivered</option>
              <option value="cancelled" ${o.order_status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
            </select>
          </td>
          <td class="small text-muted">${new Date(o.created_at).toLocaleDateString()}</td>
          <td>
            <button class="btn btn-outline-primary btn-sm" onclick="viewOrderDetails(${o.id})" title="View Details">
              <i class="bi bi-eye"></i> Details
            </button>
          </td>
        </tr>
      `).join('');
    }
  } catch (err) {
    table.innerHTML = `<tr><td colspan="7" class="text-danger text-center">Failed to load orders.</td></tr>`;
  }
}

async function viewOrderDetails(id) {
  const modalBody = document.getElementById('orderModalBody');
  document.getElementById('orderModalTitle').textContent = `Order Details #${id}`;
  modalBody.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-success"></div></div>`;
  const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
  modal.show();

  try {
    const res = await axios.get(`/admin/orders/index.php?id=${id}`);
    if (res.data.status === 'success') {
      const o = res.data.data.order;
      document.getElementById('orderModalTitle').textContent = `Order Details (${o.order_number})`;
      
      const itemsHtml = (o.items || []).map(item => `
        <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
          <div class="d-flex align-items-center gap-3">
            <img src="${item.main_image || 'https://via.placeholder.com/40'}" class="rounded" style="width: 45px; height: 45px; object-fit: cover;">
            <div>
              <div class="fw-bold text-dark dark-text-light">${item.product_name}</div>
              <small class="text-muted">Qty: ${item.quantity} &times; ${App.formatCurrency(item.price)}</small>
            </div>
          </div>
          <div class="fw-bold text-success">${App.formatCurrency(item.subtotal)}</div>
        </div>
      `).join('');

      modalBody.innerHTML = `
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <h6 class="fw-bold text-primary font-heading mb-1"><i class="bi bi-person me-1"></i> Customer Information</h6>
            <div class="small"><strong>Name:</strong> ${o.customer_name}</div>
            <div class="small"><strong>Email:</strong> ${o.customer_email}</div>
            <div class="small"><strong>Phone:</strong> ${o.customer_phone || 'N/A'}</div>
          </div>
          <div class="col-md-6">
            <h6 class="fw-bold text-primary font-heading mb-1"><i class="bi bi-geo-alt me-1"></i> Shipping Address</h6>
            <div class="small text-muted" style="white-space: pre-line;">${o.shipping_address || 'No address provided'}</div>
          </div>
        </div>
        <h6 class="fw-bold text-primary font-heading mb-2"><i class="bi bi-boxes me-1"></i> Order Items</h6>
        <div class="border rounded mb-3">${itemsHtml || '<div class="p-3 text-muted">No items found</div>'}</div>
        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
          <span class="fw-semibold">Payment Method: <span class="badge bg-light text-dark border">${(o.payment_method || 'Razorpay').toUpperCase()}</span></span>
          <h5 class="fw-bold text-success mb-0">Total: ${App.formatCurrency(o.final_amount)}</h5>
        </div>
      `;
    }
  } catch (err) {
    modalBody.innerHTML = `<div class="alert alert-danger mb-0">Failed to load order details.</div>`;
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
