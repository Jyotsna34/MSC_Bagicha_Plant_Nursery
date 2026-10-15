<?php
/**
 * Bagicha 2.0 - Admin Executive Dashboard with Chart.js Analytics
 */
require_once __DIR__ . '/../api/helpers/auth.php';
requireAdminPage();

$adminTitle = "Executive Dashboard - Bagicha Admin";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-content">
  <!-- Top Header Bar -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h1 class="h3 font-heading mb-1 text-emerald">Executive Dashboard</h1>
      <p class="text-muted small mb-0">Real-time sales performance, plant inventory metrics, and analytics.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= BASE_URL ?>/admin/products.php" class="btn btn-admin-primary">
        <i class="bi bi-plus-lg"></i> Add Plant Product
      </a>
    </div>
  </div>

  <!-- Metric Cards Grid -->
  <div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
      <div class="admin-card p-3 d-flex justify-content-between align-items-start">
        <div>
          <span class="text-muted small font-heading">Total Revenue</span>
          <h3 class="font-heading mb-1 mt-1" id="statRevenue">₹0.00</h3>
          <span class="trend-badge trend-up"><i class="bi bi-graph-up-arrow"></i> +14.2% mo/mo</span>
        </div>
        <div class="stat-icon-wrap bg-stat-green"><i class="bi bi-currency-rupee"></i></div>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="admin-card p-3 d-flex justify-content-between align-items-start">
        <div>
          <span class="text-muted small font-heading">Total Orders</span>
          <h3 class="font-heading mb-1 mt-1" id="statOrders">0</h3>
          <span class="trend-badge trend-up"><i class="bi bi-arrow-up-right"></i> +8.5% this week</span>
        </div>
        <div class="stat-icon-wrap bg-stat-orange"><i class="bi bi-bag-check"></i></div>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="admin-card p-3 d-flex justify-content-between align-items-start">
        <div>
          <span class="text-muted small font-heading">Active Products</span>
          <h3 class="font-heading mb-1 mt-1" id="statProducts">0</h3>
          <span class="trend-badge trend-up"><i class="bi bi-check-circle"></i> In Stock (<a href="<?= BASE_URL ?>/admin/inventory.php?filter=low_stock" class="text-warning text-decoration-underline fw-bold"><span id="statLowStock">0</span> low</a>)</span>
        </div>
        <div class="stat-icon-wrap bg-stat-blue"><i class="bi bi-tree"></i></div>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="admin-card p-3 d-flex justify-content-between align-items-start">
        <div>
          <span class="text-muted small font-heading">Registered Customers</span>
          <h3 class="font-heading mb-1 mt-1" id="statCustomers">0</h3>
          <span class="trend-badge trend-up"><i class="bi bi-people"></i> Active Users</span>
        </div>
        <div class="stat-icon-wrap bg-stat-purple"><i class="bi bi-people"></i></div>
      </div>
    </div>
  </div>

  <!-- Analytics Charts Section -->
  <div class="row g-4 mb-4">
    <!-- Revenue Chart -->
    <div class="col-lg-8">
      <div class="admin-card p-4 h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="font-heading h6 mb-0"><i class="bi bi-graph-up text-success me-2"></i> Sales & Revenue Growth Trend</h5>
          <span class="badge bg-success-subtle text-success px-2 py-1 small">2026 Analytics</span>
        </div>
        <div style="height: 280px; position: relative;">
          <canvas id="revenueChartCanvas"></canvas>
        </div>
      </div>
    </div>

    <!-- Category Doughnut Share -->
    <div class="col-lg-4">
      <div class="admin-card p-4 h-100">
        <h5 class="font-heading h6 mb-3"><i class="bi bi-pie-chart text-warning me-2"></i> Category Revenue Share</h5>
        <div style="height: 280px; position: relative;">
          <canvas id="categoryChartCanvas"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Low Stock Alert & Recent Orders Table -->
  <div class="admin-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="font-heading h6 mb-0"><i class="bi bi-clock-history me-2"></i> Recent Orders</h5>
      <a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-link text-success text-decoration-none small">View All Orders &rarr;</a>
    </div>

    <div class="table-responsive">
      <table class="table table-admin-custom align-middle mb-0">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Payment Status</th>
            <th>Fulfillment Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody id="recentOrdersTable">
          <tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-success"></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  await AdminApp.loadDashboardStats();

  // Load Charts Data
  try {
    const repRes = await axios.get('/admin/reports/index.php');
    if (repRes.data.status === 'success') {
      AdminCharts.renderRevenueChart('revenueChartCanvas', null);
      AdminCharts.renderCategoryChart('categoryChartCanvas', repRes.data.data.category_share);
    }
  } catch (err) {
    AdminCharts.renderRevenueChart('revenueChartCanvas', null);
    AdminCharts.renderCategoryChart('categoryChartCanvas', null);
  }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
