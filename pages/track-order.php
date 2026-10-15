<?php
/**
 * Bagicha 2.0 - Public Live Order Tracking Portal
 * Tracks plants in transit with interactive botanical milestone timeline.
 */
$pageTitle = "Track Plant Order - Bagicha | Live Botanical Transit Status";
$pageDescription = "Check real-time delivery and botanical inspection status of your Bagicha plant order by entering your order number.";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<main id="main-content" class="container py-4" tabindex="-1">
  <!-- Tracking Header -->
  <div class="text-center mb-4" style="max-width: 650px; margin: 0 auto;">
    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background: rgba(6, 78, 43, 0.08); color: var(--color-primary); font-weight: 600; font-size: 0.86rem;">
      <i class="bi bi-truck text-success"></i> Live Botanical Transit Tracker
    </div>
    <h1 class="font-heading display-6 fw-bold text-primary mb-2">Track Your Plant Sanctuary</h1>
    <p class="text-secondary small">Enter your Bagicha order number from your confirmation SMS or email to view real-time transit and packaging milestones.</p>

    <!-- Search Input Box -->
    <div class="card border-0 p-3 shadow-sm rounded-4 bg-white border border-light-subtle mt-3 text-start">
      <form id="trackOrderForm" onsubmit="handleTrackOrder(event)">
        <div class="input-group">
          <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
          <input type="text" id="trackOrderInput" class="form-control border-0 shadow-none" placeholder="e.g. BAG-20261006-1234" required style="font-size: 1rem;">
          <button type="submit" id="trackSubmitBtn" class="btn btn-bagicha-primary px-4 fw-semibold rounded-3">
            Track Order
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Results Section -->
  <div class="row justify-content-center">
    <div class="col-lg-8">
      
      <!-- Loading State -->
      <div id="trackLoading" class="text-center py-5 d-none">
        <div class="spinner-border text-success" role="status"></div>
        <p class="text-muted small mt-2">Checking nursery courier dispatched status...</p>
      </div>

      <!-- Error State -->
      <div id="trackError" class="alert alert-danger d-none rounded-4 py-3 small text-center mb-4"></div>

      <!-- Result Card -->
      <div id="trackResultCard" class="card border-0 rounded-4 shadow-sm bg-white border border-light-subtle p-4 p-md-5 d-none">
        <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 mb-4 border-bottom border-light-subtle gap-2">
          <div>
            <span class="text-uppercase small text-muted fw-bold">Order Number</span>
            <h4 class="font-heading text-primary mb-0" id="resOrderNumber">BAG-XXXX</h4>
          </div>
          <div class="text-md-end">
            <span class="badge px-3 py-2 rounded-pill fs-6" id="resStatusBadge">Shipped</span>
          </div>
        </div>

        <!-- Milestones Timeline -->
        <h5 class="font-heading text-dark mb-3"><i class="bi bi-shield-check text-success me-2"></i> Botanical Transit Milestones</h5>
        <div class="tracking-timeline mb-4" id="resTimelineList"></div>

        <!-- Plant Items in Transit -->
        <h5 class="font-heading text-dark mb-3 pt-3 border-top border-light-subtle"><i class="bi bi-box-seam text-primary me-2"></i> Plants in This Package</h5>
        <div class="d-flex flex-column gap-2 mb-4" id="resItemsList"></div>

        <!-- Footer Help Link -->
        <div class="p-3 rounded-3 bg-light d-flex justify-content-between align-items-center flex-wrap gap-2 small">
          <span class="text-muted"><i class="bi bi-chat-heart text-success me-1"></i> Need help with delivery schedule?</span>
          <a href="<?= BASE_URL ?>/pages/contact.php" class="text-success text-decoration-none fw-bold">Contact Support <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>

    </div>
  </div>
</main>

<script>
async function handleTrackOrder(e) {
  if (e) e.preventDefault();
  const orderNum = document.getElementById('trackOrderInput').value.trim();
  const loading = document.getElementById('trackLoading');
  const errorEl = document.getElementById('trackError');
  const resultCard = document.getElementById('trackResultCard');
  const btn = document.getElementById('trackSubmitBtn');

  if (!orderNum) return;

  loading.classList.remove('d-none');
  errorEl.classList.add('d-none');
  resultCard.classList.add('d-none');
  btn.disabled = true;

  try {
    const res = await axios.get(`${App.getBaseUrl()}/api/orders/track.php?order_number=${encodeURIComponent(orderNum)}`);
    const data = res.data.data;
    renderTrackingResult(data);
    resultCard.classList.remove('d-none');
  } catch (err) {
    errorEl.textContent = err.response?.data?.message || 'Order not found. Please verify the order number and try again.';
    errorEl.classList.remove('d-none');
  } finally {
    loading.classList.add('d-none');
    btn.disabled = false;
  }
}

function renderTrackingResult(data) {
  const o = data.order;
  document.getElementById('resOrderNumber').textContent = o.order_number;
  
  const badge = document.getElementById('resStatusBadge');
  badge.textContent = o.status.toUpperCase();
  if (o.status === 'delivered') badge.className = 'badge bg-success px-3 py-2 rounded-pill fs-6';
  else if (o.status === 'shipped') badge.className = 'badge bg-primary px-3 py-2 rounded-pill fs-6';
  else if (o.status === 'processing') badge.className = 'badge bg-warning text-dark px-3 py-2 rounded-pill fs-6';
  else badge.className = 'badge bg-secondary px-3 py-2 rounded-pill fs-6';

  // Timeline rendering
  const timelineEl = document.getElementById('resTimelineList');
  timelineEl.innerHTML = data.timeline.map(t => `
    <div class="tracking-step ${t.completed ? 'completed' : ''}">
      <div class="tracking-step-dot"></div>
      <div class="d-flex justify-content-between align-items-start">
        <h6 class="font-heading mb-1 ${t.completed ? 'text-dark fw-bold' : 'text-muted'}">${t.title}</h6>
        <span class="small text-muted" style="font-size: 0.78rem;">${t.time}</span>
      </div>
      <p class="small text-secondary mb-0">${t.desc}</p>
    </div>
  `).join('');

  // Items rendering
  const itemsEl = document.getElementById('resItemsList');
  itemsEl.innerHTML = data.items.map(it => `
    <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-white border border-light-subtle">
      <div class="d-flex align-items-center gap-2">
        <img src="${it.main_image || App.getBaseUrl() + '/img/logo-3.png'}" class="rounded-2" style="width: 44px; height: 44px; object-fit: cover;">
        <div>
          <strong class="small d-block text-dark">${it.product_name}</strong>
          <small class="text-muted">Qty: ${it.quantity}</small>
        </div>
      </div>
      <span class="fw-bold text-success small font-heading">${App.formatCurrency(it.subtotal || it.price)}</span>
    </div>
  `).join('');
}

// Auto-track if ?order_number= in URL
document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const qOrder = urlParams.get('order_number');
  if (qOrder) {
    document.getElementById('trackOrderInput').value = qOrder;
    handleTrackOrder(null);
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
