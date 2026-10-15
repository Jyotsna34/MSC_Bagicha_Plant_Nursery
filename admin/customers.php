<?php
/**
 * Bagicha 2.0 - Admin Customer Management Page
 */
require_once __DIR__ . '/../api/helpers/auth.php';
requireAdminPage();

$adminTitle = "Customer Accounts - Bagicha Admin";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-content">
  <!-- Breadcrumb Navigation -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/index.php" class="text-success text-decoration-none">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Users</li>
    </ol>
  </nav>

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h1 class="font-heading h2 text-primary mb-1">Registered Customers</h1>
      <p class="text-muted small mb-0">View registered user accounts, order volume, and purchasing history.</p>
    </div>
    <div style="max-width: 300px;">
      <div class="input-group input-group-sm">
        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="text" id="customerSearchInput" class="form-control border-start-0 ps-0" placeholder="Search customer name or email..." oninput="onCustomerSearchInput()">
      </div>
    </div>
  </div>

  <div class="admin-card p-4">
    <div class="table-responsive">
      <table class="table table-admin-custom align-middle mb-0">
        <thead class="small">
          <tr>
            <th>Customer ID</th>
            <th>Full Name</th>
            <th>Email Address</th>
            <th>Phone</th>
            <th>Orders Placed</th>
            <th>Total Spent</th>
            <th>Joined Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="adminCustomersTable">
          <tr><td colspan="8" class="text-center py-4"><div class="spinner-border text-success"></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Customer Order History Modal -->
<div class="modal fade" id="customerHistoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-bottom p-3">
        <h5 class="modal-title font-heading text-primary" id="custHistoryModalTitle">Customer Order History</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4" id="custHistoryModalBody">
        <div class="text-center py-4"><div class="spinner-border text-success"></div></div>
      </div>
    </div>
  </div>
</div>

<script>
let custSearchTimer = null;

document.addEventListener('DOMContentLoaded', async () => {
  if (!AdminApp.checkAdminAuth()) return;
  await loadCustomers();
});

function onCustomerSearchInput() {
  clearTimeout(custSearchTimer);
  custSearchTimer = setTimeout(() => {
    loadCustomers(document.getElementById('customerSearchInput').value.trim());
  }, 350);
}

async function loadCustomers(search = '') {
  const table = document.getElementById('adminCustomersTable');
  try {
    const res = await axios.get(`/admin/users/index.php?search=${encodeURIComponent(search)}`, { timeout: 8000 });
    if (res.data.status === 'success') {
      const customers = res.data.data.customers;
      if (customers.length === 0) {
        table.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">No registered customers found matching search criteria.</td></tr>`;
        return;
      }

      table.innerHTML = customers.map(c => `
        <tr>
          <td><span class="badge badge-neutral">#${c.id}</span></td>
          <td class="fw-bold">${c.full_name}</td>
          <td>${c.email}</td>
          <td>${c.phone || 'N/A'}</td>
          <td><span class="badge bg-success-subtle text-success">${c.total_orders} Orders</span></td>
          <td class="fw-bold text-primary">${App.formatCurrency(c.total_spent)}</td>
          <td class="small text-muted">${new Date(c.created_at).toLocaleDateString()}</td>
          <td>
            <button class="btn btn-outline-primary btn-sm" onclick="viewCustomerOrders(${c.id}, '${c.full_name.replace(/'/g, "\\'")}')">
              <i class="bi bi-clock-history"></i> Orders
            </button>
          </td>
        </tr>
      `).join('');
    }
  } catch (err) {
    table.innerHTML = `<tr><td colspan="8" class="text-danger text-center">Failed to load customer list.</td></tr>`;
  }
}

async function viewCustomerOrders(customerId, name) {
  const body = document.getElementById('custHistoryModalBody');
  document.getElementById('custHistoryModalTitle').textContent = `Order History: ${name}`;
  body.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-success"></div></div>`;
  const modal = new bootstrap.Modal(document.getElementById('customerHistoryModal'));
  modal.show();

  try {
    const res = await axios.get(`/admin/users/index.php?customer_id=${customerId}`);
    if (res.data.status === 'success') {
      const orders = res.data.data.orders;
      if (orders.length === 0) {
        body.innerHTML = `<div class="text-center text-muted py-4">This customer has not placed any orders yet.</div>`;
        return;
      }

      body.innerHTML = `
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Amount</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              ${orders.map(o => `
                <tr>
                  <td class="fw-bold text-primary">${o.order_number}</td>
                  <td class="fw-bold text-success">${App.formatCurrency(o.final_amount)}</td>
                  <td><span class="badge ${o.payment_status === 'paid' ? 'bg-success' : 'bg-warning'}">${o.payment_status.toUpperCase()}</span></td>
                  <td><span class="badge bg-secondary">${o.order_status.toUpperCase()}</span></td>
                  <td class="small text-muted">${new Date(o.created_at).toLocaleDateString()}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      `;
    }
  } catch (err) {
    body.innerHTML = `<div class="alert alert-danger mb-0">Failed to load customer order history.</div>`;
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
