<?php
/**
 * Bagicha 2.0 - Admin Coupon Management Page
 */
require_once __DIR__ . '/../api/helpers/auth.php';
requireAdminPage();

$adminTitle = "Manage Coupons - Bagicha Admin";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-content">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="font-heading h2 text-primary mb-1">Discount Coupon Codes</h1>
      <p class="text-muted small mb-0">Create and edit promotional discount codes for customer marketing campaigns.</p>
    </div>
    <button class="btn btn-admin-primary" onclick="openCouponModal()">
      <i class="bi bi-plus-lg"></i> Create Coupon
    </button>
  </div>

  <div class="admin-card p-4">
    <div class="table-responsive">
      <table class="table table-admin-custom align-middle mb-0">
        <thead class="small">
          <tr>
            <th>Coupon Code</th>
            <th>Discount Type</th>
            <th>Discount Value</th>
            <th>Min Order Amount</th>
            <th>Expiry Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="adminCouponsTable">
          <tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-success"></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Add / Edit Coupon Modal -->
<div class="modal fade" id="couponModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-bottom p-3">
        <h5 class="modal-title font-heading text-primary" id="couponModalTitle">Create Discount Coupon</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="couponForm" onsubmit="saveCoupon(event)">
          <input type="hidden" id="cpId" value="">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Coupon Code</label>
            <input type="text" id="cpCode" class="form-control text-uppercase" placeholder="MONSTERA15" required style="border-radius: 10px;">
          </div>
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Discount Type</label>
              <select id="cpType" class="form-select" style="border-radius: 10px;">
                <option value="percentage">Percentage (%)</option>
                <option value="fixed">Fixed Amount (₹)</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Discount Value</label>
              <input type="number" step="0.01" id="cpAmount" class="form-control" placeholder="15" required style="border-radius: 10px;">
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Min Order Amount (₹)</label>
              <input type="number" step="0.01" id="cpMin" class="form-control" placeholder="499.00" value="0" style="border-radius: 10px;">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Expiry Date (Optional)</label>
              <input type="date" id="cpExpires" class="form-control" style="border-radius: 10px;">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Status</label>
            <select id="cpStatus" class="form-select" style="border-radius: 10px;">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <button type="submit" class="btn btn-bagicha-primary w-100 py-2" id="cpSubmitBtn">Create Coupon</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
let globalCouponsData = [];

document.addEventListener('DOMContentLoaded', async () => {
  if (!AdminApp.checkAdminAuth()) return;
  await loadCoupons();
});

async function loadCoupons() {
  const table = document.getElementById('adminCouponsTable');
  try {
    const res = await axios.get('/admin/coupons/index.php', { timeout: 8000 });
    if (res.data.status === 'success') {
      globalCouponsData = res.data.data.coupons;
      if (globalCouponsData.length === 0) {
        table.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No coupons created yet.</td></tr>`;
        return;
      }

      table.innerHTML = globalCouponsData.map(c => `
        <tr>
          <td class="fw-bold text-primary font-heading fs-6">${c.code}</td>
          <td><span class="badge badge-neutral">${c.discount_type.toUpperCase()}</span></td>
          <td class="fw-bold text-success">${c.discount_type === 'percentage' ? c.discount_amount + '%' : App.formatCurrency(c.discount_amount)}</td>
          <td>${App.formatCurrency(c.min_order_amount)}</td>
          <td class="small text-muted">${c.expires_at ? new Date(c.expires_at).toLocaleDateString() : 'No expiry'}</td>
          <td><span class="badge ${c.is_active == 1 ? 'bg-success' : 'bg-secondary'}">${c.is_active == 1 ? 'Active' : 'Inactive'}</span></td>
          <td>
            <button class="btn btn-outline-primary btn-sm me-1" onclick="editCoupon(${c.id})" title="Edit Coupon"><i class="bi bi-pencil"></i> Edit</button>
            <button class="btn btn-outline-danger btn-sm" onclick="AdminApp.deleteCoupon(${c.id})" title="Delete Coupon"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
      `).join('');
    }
  } catch (err) {
    table.innerHTML = `<tr><td colspan="7" class="text-danger text-center">Failed to load coupons.</td></tr>`;
  }
}

function openCouponModal() {
  document.getElementById('cpId').value = '';
  document.getElementById('couponForm').reset();
  document.getElementById('cpExpires').value = '';
  document.getElementById('couponModalTitle').textContent = 'Create Discount Coupon';
  document.getElementById('cpSubmitBtn').textContent = 'Create Coupon';
  new bootstrap.Modal(document.getElementById('couponModal')).show();
}

function editCoupon(id) {
  const coupon = globalCouponsData.find(c => c.id == id);
  if (!coupon) return;

  document.getElementById('cpId').value = coupon.id;
  document.getElementById('cpCode').value = coupon.code;
  document.getElementById('cpType').value = coupon.discount_type;
  document.getElementById('cpAmount').value = coupon.discount_amount;
  document.getElementById('cpMin').value = coupon.min_order_amount;
  document.getElementById('cpExpires').value = coupon.expires_at ? coupon.expires_at.slice(0, 10) : '';
  document.getElementById('cpStatus').value = coupon.is_active;

  document.getElementById('couponModalTitle').textContent = 'Edit Discount Coupon';
  document.getElementById('cpSubmitBtn').textContent = 'Update Coupon';
  new bootstrap.Modal(document.getElementById('couponModal')).show();
}

async function saveCoupon(e) {
  e.preventDefault();
  const id = document.getElementById('cpId').value;
  const data = {
    id: id ? parseInt(id) : null,
    code: document.getElementById('cpCode').value,
    discount_type: document.getElementById('cpType').value,
    discount_amount: document.getElementById('cpAmount').value,
    min_order_amount: document.getElementById('cpMin').value,
    expires_at: document.getElementById('cpExpires').value || null,
    is_active: parseInt(document.getElementById('cpStatus').value)
  };

  try {
    const res = await axios.post('/admin/coupons/index.php', data);
    if (res.data.status === 'success') {
      App.showToast(res.data.message || (id ? 'Coupon updated!' : 'Coupon created!'));
      const modalEl = document.getElementById('couponModal');
      const modal = bootstrap.Modal.getInstance(modalEl);
      if (modal) modal.hide();
      await loadCoupons();
    }
  } catch (err) {
    App.showToast(err.response?.data?.message || 'Failed to save coupon', 'error');
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
