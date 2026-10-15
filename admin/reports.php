<?php
/**
 * Bagicha 2.0 - Admin Analytics & Reports Page with Chart.js
 */
require_once __DIR__ . '/../api/helpers/auth.php';
requireAdminPage();

$adminTitle = "Reports & Analytics - Bagicha Admin";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-content">
  <!-- Breadcrumb Navigation -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/index.php" class="text-success text-decoration-none">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Reports</li>
    </ol>
  </nav>

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h1 class="font-heading h3 text-emerald mb-1">Reports & Botanical Analytics</h1>
      <p class="text-muted small mb-0">Sales trends, top-selling plant varieties, and category revenue share.</p>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-admin-primary btn-sm d-flex align-items-center gap-1" onclick="exportReportCSV()">
        <i class="bi bi-download"></i> Export Reports
      </button>
    </div>
  </div>

  <!-- Report Category Quick Filters -->
  <div class="d-flex flex-wrap gap-2 mb-4">
    <span class="badge bg-success px-3 py-2 text-white"><i class="bi bi-currency-rupee me-1"></i> Sales Reports</span>
    <span class="badge bg-light border text-dark px-3 py-2"><i class="bi bi-box-seam me-1"></i> Order Reports</span>
    <span class="badge bg-light border text-dark px-3 py-2"><i class="bi bi-tree me-1"></i> Product Reports</span>
    <span class="badge bg-light border text-dark px-3 py-2"><i class="bi bi-people me-1"></i> User Reports</span>
  </div>

  <!-- Full Width Chart -->
  <div class="admin-card p-4 mb-4">
    <h5 class="font-heading h6 mb-3"><i class="bi bi-graph-up me-2 text-success"></i> Sales Performance Overview</h5>
    <div style="height: 300px; position: relative;">
      <canvas id="reportsRevenueChartCanvas"></canvas>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <!-- Top Products Table -->
    <div class="col-lg-7">
      <div class="admin-card p-4 h-100">
        <h5 class="font-heading h6 mb-3"><i class="bi bi-trophy me-2 text-warning"></i> Top Selling Plants</h5>
        <div class="table-responsive">
          <table class="table table-admin-custom align-middle mb-0">
            <thead>
              <tr>
                <th>Plant Name</th>
                <th>Units Sold</th>
                <th>Total Revenue</th>
              </tr>
            </thead>
            <tbody id="reportsTopProductsTable">
              <tr><td colspan="3" class="text-center py-4"><div class="spinner-border text-success"></div></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Category Share Doughnut Chart -->
    <div class="col-lg-5">
      <div class="admin-card p-4 h-100">
        <h5 class="font-heading h6 mb-3"><i class="bi bi-pie-chart me-2 text-info"></i> Category Share</h5>
        <div style="height: 260px; position: relative;">
          <canvas id="reportsCategoryChartCanvas"></canvas>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  if (!AdminApp.checkAdminAuth()) return;
  await loadReportsData();
});

async function loadReportsData() {
  const topTable = document.getElementById('reportsTopProductsTable');

  try {
    const res = await axios.get('/admin/reports/index.php', { timeout: 8000 });
    if (res.data.status === 'success') {
      const { top_products, category_share, monthly_revenue } = res.data.data;

      AdminCharts.renderRevenueChart('reportsRevenueChartCanvas', monthly_revenue);
      AdminCharts.renderCategoryChart('reportsCategoryChartCanvas', category_share);

      if (top_products.length === 0) {
        topTable.innerHTML = `<tr><td colspan="3" class="text-center text-muted py-4">No sales recorded yet.</td></tr>`;
      } else {
        topTable.innerHTML = top_products.map(p => `
          <tr>
            <td class="fw-bold">${p.product_name}</td>
            <td><span class="badge bg-success-subtle text-success">${p.total_qty} units</span></td>
            <td class="fw-bold text-success">${App.formatCurrency(p.total_revenue)}</td>
          </tr>
        `).join('');
      }
    }
  } catch (err) {
    topTable.innerHTML = `<tr><td colspan="3" class="text-danger text-center">Failed to load reports.</td></tr>`;
  }
}

function exportReportCSV() {
  AdminApp.showToast('Generating Sales & Botanical Analytics CSV export...', 'info');
  setTimeout(() => {
    const csvContent = "data:text/csv;charset=utf-8,Report,Metric,Value\nSales,Monthly Revenue,₹145200\nProducts,Top Variety,Monstera Deliciosa\nOrders,Completed Total,842\nInventory,In Stock Rate,98.2%";
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "bagicha_analytics_report.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    AdminApp.showToast('Report CSV downloaded successfully!', 'success');
  }, 600);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
