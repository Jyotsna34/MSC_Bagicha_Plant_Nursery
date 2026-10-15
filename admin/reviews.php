<?php
/**
 * Bagicha 2.0 - Admin Review Management Page
 */
require_once __DIR__ . '/../api/helpers/auth.php';
requireAdminPage();

$adminTitle = "Product Reviews - Bagicha Admin";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-content">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="font-heading h2 text-primary mb-1">Customer Product Reviews</h1>
      <p class="text-muted small mb-0">Monitor customer ratings, feedback comments, and moderate reviews.</p>
    </div>
    <div class="btn-group btn-group-sm" role="group">
      <button type="button" class="btn btn-outline-success active" onclick="filterReviews('all', this)">All</button>
      <button type="button" class="btn btn-outline-success" onclick="filterReviews('approved', this)">Approved</button>
      <button type="button" class="btn btn-outline-success" onclick="filterReviews('pending', this)">Pending</button>
    </div>
  </div>

  <div class="admin-card p-4">
    <div class="table-responsive">
      <table class="table table-admin-custom align-middle mb-0">
        <thead class="small">
          <tr>
            <th>Product</th>
            <th>Reviewer</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="adminReviewsTable">
          <tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-success"></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<script>
let allReviews = [];
let currentFilter = 'all';

document.addEventListener('DOMContentLoaded', async () => {
  if (!AdminApp.checkAdminAuth()) return;
  await loadReviews();
});

async function loadReviews() {
  const table = document.getElementById('adminReviewsTable');
  try {
    const res = await axios.get('/admin/reviews/index.php', { timeout: 8000 });
    if (res.data.status === 'success') {
      allReviews = res.data.data.reviews || [];
      renderReviews();
    }
  } catch (err) {
    table.innerHTML = `<tr><td colspan="7" class="text-danger text-center">Failed to load reviews.</td></tr>`;
  }
}

function filterReviews(type, btnEl) {
  currentFilter = type;
  document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
  if (btnEl) btnEl.classList.add('active');
  renderReviews();
}

function renderReviews() {
  const table = document.getElementById('adminReviewsTable');
  let filtered = allReviews;
  if (currentFilter === 'approved') {
    filtered = allReviews.filter(r => r.is_approved == 1);
  } else if (currentFilter === 'pending') {
    filtered = allReviews.filter(r => r.is_approved == 0);
  }

  if (filtered.length === 0) {
    table.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No reviews found for this filter.</td></tr>`;
    return;
  }

  table.innerHTML = filtered.map(r => {
    const stars = '★'.repeat(r.rating) + '☆'.repeat(5 - r.rating);
    const isApproved = (r.is_approved == 1);

    return `
      <tr>
        <td class="fw-bold">${r.product_name}</td>
        <td>
          <div class="fw-semibold">${r.reviewer_name}</div>
          <small class="text-muted">${r.reviewer_email}</small>
        </td>
        <td>
          <span class="text-warning fw-bold fs-6">${stars}</span>
          <small class="text-muted d-block">${r.rating} / 5</small>
        </td>
        <td class="text-secondary small" style="max-width: 280px;">${r.comment || '<i>No comment provided.</i>'}</td>
        <td>
          <span class="badge ${isApproved ? 'bg-success-subtle text-success border border-success' : 'bg-warning-subtle text-warning border border-warning'} px-2 py-1">
            ${isApproved ? '<i class="bi bi-check-circle me-1"></i> Approved' : '<i class="bi bi-clock me-1"></i> Pending Approval'}
          </span>
        </td>
        <td class="small text-muted">${new Date(r.created_at).toLocaleDateString()}</td>
        <td class="text-nowrap">
          <button class="btn btn-sm ${isApproved ? 'btn-outline-warning' : 'btn-outline-success'} me-1" 
                  onclick="toggleApproval(${r.id}, ${isApproved ? 0 : 1})" 
                  title="${isApproved ? 'Unapprove Review' : 'Approve Review'}">
            <i class="bi ${isApproved ? 'bi-x-circle me-1' : 'bi-check-lg me-1'}"></i>
            ${isApproved ? 'Unapprove' : 'Approve'}
          </button>
          <button class="btn btn-outline-danger btn-sm" onclick="deleteReview(${r.id})" title="Delete Review">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>
    `;
  }).join('');
}

async function toggleApproval(id, newStatus) {
  try {
    const res = await axios.post('/admin/reviews/index.php', {
      id: id,
      is_approved: newStatus
    });
    if (res.data.status === 'success') {
      App.showToast(res.data.message);
      await loadReviews();
    }
  } catch (err) {
    App.showToast(err.response?.data?.message || 'Failed to update review status', 'error');
  }
}

async function deleteReview(id) {
  const confirmed = await AdminApp.confirmModal({
    title: 'Delete Review',
    message: 'Are you sure you want to delete this customer product review?',
    confirmText: 'Delete Review',
    confirmClass: 'btn-danger'
  });
  if (confirmed) {
    try {
      await axios.delete(`/admin/reviews/index.php?id=${id}`);
      App.showToast('Review deleted');
      await loadReviews();
    } catch (err) {
      App.showToast(err.response?.data?.message || 'Failed to delete review', 'error');
    }
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
