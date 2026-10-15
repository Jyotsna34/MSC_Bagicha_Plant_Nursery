<?php
/**
 * Bagicha - Admin Inventory & Stock Control Dashboard
 */
require_once __DIR__ . '/../api/helpers/auth.php';
requireAdminPage();

$adminTitle = "Inventory & Stock Control - Bagicha Admin";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-content">
  <!-- Top Header -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h1 class="font-heading h2 text-primary mb-1">Inventory & Stock Control</h1>
      <p class="text-muted small mb-0">Monitor real-time plant quantities, update stock inline, and manage reorder alerts.</p>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" onclick="exportInventoryCSV()">
        <i class="bi bi-download"></i> Export CSV
      </button>

      <a href="<?= BASE_URL ?>/admin/products.php" class="btn btn-bagicha-primary btn-sm d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Add New Product
      </a>
    </div>
  </div>

  <!-- Inventory Summary KPI Cards -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
      <div class="admin-card p-3 d-flex justify-content-between align-items-center">
        <div>
          <span class="text-muted small font-heading">Total SKUs</span>
          <h3 class="font-heading mb-0 mt-1" id="kpiTotalSkus">0</h3>
          <small class="text-muted" id="kpiValuation">Asset: ₹0.00</small>
        </div>
        <div class="stat-icon-wrap bg-stat-blue"><i class="bi bi-boxes"></i></div>
      </div>
    </div>

    <div class="col-6 col-lg-3">
      <div class="admin-card p-3 d-flex justify-content-between align-items-center">
        <div>
          <span class="text-muted small font-heading">Healthy Stock</span>
          <h3 class="font-heading mb-0 mt-1 text-success" id="kpiHealthy">0</h3>
          <small class="text-success">&gt; 5 units</small>
        </div>
        <div class="stat-icon-wrap bg-stat-green"><i class="bi bi-check-circle-fill"></i></div>
      </div>
    </div>

    <div class="col-6 col-lg-3">
      <div class="admin-card p-3 d-flex justify-content-between align-items-center">
        <div>
          <span class="text-muted small font-heading">Low Stock Alerts</span>
          <h3 class="font-heading mb-0 mt-1 text-warning" id="kpiLowStock">0</h3>
          <small class="text-warning">1 &ndash; 5 units</small>
        </div>
        <div class="stat-icon-wrap bg-stat-orange"><i class="bi bi-exclamation-triangle-fill"></i></div>
      </div>
    </div>

    <div class="col-6 col-lg-3">
      <div class="admin-card p-3 d-flex justify-content-between align-items-center">
        <div>
          <span class="text-muted small font-heading">Out of Stock</span>
          <h3 class="font-heading mb-0 mt-1 text-danger" id="kpiOutOfStock">0</h3>
          <small class="text-danger">0 units</small>
        </div>
        <div class="stat-icon-wrap bg-danger-subtle text-danger"><i class="bi bi-x-circle-fill"></i></div>
      </div>
    </div>
  </div>

  <!-- Filter & Search Toolbar -->
  <div class="admin-card p-3 mb-4">
    <div class="row g-3 align-items-center">
      <!-- Search Input -->
      <div class="col-md-4">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
          <input type="text" id="inventorySearchInput" class="form-control border-start-0 ps-0" placeholder="Search product name or SKU..." oninput="onSearchInput()">
        </div>
      </div>

      <!-- Category Filter -->
      <div class="col-md-3">
        <select id="inventoryCategoryFilter" class="form-select form-select-sm" onchange="applyFilters()">
          <option value="0">All Categories</option>
        </select>
      </div>

      <!-- Status Filter Pill Group -->
      <div class="col-md-5 d-flex justify-content-md-end gap-2 flex-wrap">
        <div class="btn-group btn-group-sm" role="group">
          <button class="btn btn-outline-secondary active" id="btnFilterAll" onclick="setFilter('all', this)">All</button>
          <button class="btn btn-outline-secondary" id="btnFilterHealthy" onclick="setFilter('healthy', this)">Healthy</button>
          <button class="btn btn-outline-secondary" id="btnFilterLow" onclick="setFilter('low_stock', this)">Low Stock</button>
          <button class="btn btn-outline-secondary" id="btnFilterOut" onclick="setFilter('out_of_stock', this)">Out of Stock</button>
        </div>

        <!-- Sort Select -->
        <select id="inventorySortSelect" class="form-select form-select-sm" style="width: auto;" onchange="applyFilters()">
          <option value="stock_asc">Stock: Low &rarr; High</option>
          <option value="stock_desc">Stock: High &rarr; Low</option>
          <option value="name_asc">Name: A &rarr; Z</option>
          <option value="price_desc">Price: High &rarr; Low</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Main Inventory Data Table -->
  <div class="admin-card p-4">
    <div class="table-responsive">
      <table class="table table-admin-custom align-middle mb-0">
        <thead class="small">
          <tr>
            <th style="width: 60px;">Image</th>
            <th>Product & SKU</th>
            <th>Category</th>
            <th>Price</th>
            <th>Current Stock</th>
            <th>Stock Status</th>
            <th style="min-width: 220px;">Quick Stock Adjustment</th>
            <th class="text-end">Quick Action</th>
          </tr>
        </thead>
        <tbody id="inventoryTable">
          <tr><td colspan="8" class="text-center py-4"><div class="spinner-border text-success"></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<script>
let currentInventoryState = {
  filter: 'all',
  category_id: 0,
  search: '',
  sort: 'stock_asc',
  loadedItems: []
};

let searchDebounceTimer = null;

document.addEventListener('DOMContentLoaded', async () => {
  if (!AdminApp.checkAdminAuth()) return;
  await loadInventory();
});

function onSearchInput() {
  clearTimeout(searchDebounceTimer);
  searchDebounceTimer = setTimeout(() => {
    currentInventoryState.search = document.getElementById('inventorySearchInput').value.trim();
    loadInventory();
  }, 350);
}

function setFilter(filterType, btn) {
  currentInventoryState.filter = filterType;
  document.querySelectorAll('.btn-group button').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  loadInventory();
}

function applyFilters() {
  currentInventoryState.category_id = parseInt(document.getElementById('inventoryCategoryFilter').value) || 0;
  currentInventoryState.sort = document.getElementById('inventorySortSelect').value;
  loadInventory();
}

async function loadInventory() {
  const table = document.getElementById('inventoryTable');
  try {
    const { filter, category_id, search, sort } = currentInventoryState;
    const url = `/admin/inventory/index.php?filter=${encodeURIComponent(filter)}&category_id=${category_id}&search=${encodeURIComponent(search)}&sort=${encodeURIComponent(sort)}`;
    
    const res = await axios.get(url, { timeout: 8000 });
    if (res.data.status === 'success') {
      const items = res.data.data.inventory;
      const stats = res.data.data.stats;
      const categories = res.data.data.categories;

      currentInventoryState.loadedItems = items;

      // Update KPI Cards
      updateKpiCards(stats);

      // Populate Categories Dropdown if not done
      populateCategoryDropdown(categories);

      if (items.length === 0) {
        table.innerHTML = `
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">
              <i class="bi bi-box-seam fs-2 d-block mb-2"></i>
              No products found matching the selected filter and search criteria.
            </td>
          </tr>`;
        return;
      }

      table.innerHTML = items.map(p => {
        const isLow = p.stock_quantity <= 5 && p.stock_quantity > 0;
        const isOut = p.stock_quantity == 0;
        
        let badge = '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i>Healthy Stock</span>';
        if (isOut) {
          badge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-x-circle me-1"></i>Out of Stock</span>';
        } else if (isLow) {
          badge = '<span class="badge bg-warning-subtle text-dark border border-warning-subtle"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock</span>';
        }

        const effectivePrice = parseFloat(p.sale_price || p.price);

        return `
          <tr id="row_prod_${p.id}">
            <td>
              <img src="${p.main_image}" class="rounded-3 shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
            </td>
            <td>
              <div class="fw-bold text-dark dark-text-light">${p.name}</div>
              <small class="text-muted">SKU: <code>${p.sku || 'N/A'}</code></small>
            </td>
            <td><span class="badge bg-light text-dark border">${p.category_name}</span></td>
            <td>
              <div class="fw-semibold text-primary">₹${effectivePrice.toFixed(2)}</div>
              ${p.sale_price ? `<small class="text-muted text-decoration-line-through">₹${parseFloat(p.price).toFixed(2)}</small>` : ''}
            </td>
            <td>
              <span id="stockValueDisplay_${p.id}" class="fw-bold fs-6 ${isOut ? 'text-danger' : (isLow ? 'text-warning' : 'text-success')}">
                ${p.stock_quantity} units
              </span>
            </td>
            <td><div id="stockBadge_${p.id}">${badge}</div></td>
            <td>
              <div class="input-group input-group-sm" style="width: 170px;">
                <button type="button" class="btn btn-outline-secondary" onclick="adjustInputValue(${p.id}, -1)">-</button>
                <input type="number" id="stockInput_${p.id}" class="form-control text-center fw-bold" value="${p.stock_quantity}" min="0">
                <button type="button" class="btn btn-outline-secondary" onclick="adjustInputValue(${p.id}, 1)">+</button>
                <button type="button" id="saveBtn_${p.id}" class="btn btn-bagicha-primary ms-1" onclick="saveStockInput(${p.id})" title="Save Stock">
                  <i class="bi bi-check-lg"></i>
                </button>
              </div>
            </td>
            <td class="text-end">
              <button class="btn btn-outline-success btn-sm font-heading" onclick="quickRestock(${p.id}, 10)" title="Quick Restock +10 units">
                +10 Restock
              </button>
            </td>
          </tr>
        `;
      }).join('');
    }
  } catch (err) {
    console.error('Inventory Load Error', err);
    table.innerHTML = `<tr><td colspan="8" class="text-danger text-center py-4">Failed to load inventory data. Please try refreshing.</td></tr>`;
  }
}

function updateKpiCards(stats) {
  if (!stats) return;
  document.getElementById('kpiTotalSkus').textContent = stats.total_products || 0;
  document.getElementById('kpiHealthy').textContent = stats.healthy_count || 0;
  document.getElementById('kpiLowStock').textContent = stats.low_stock_count || 0;
  document.getElementById('kpiOutOfStock').textContent = stats.out_of_stock_count || 0;
  
  const valuation = parseFloat(stats.total_valuation || 0);
  document.getElementById('kpiValuation').textContent = `Asset Value: ${App.formatCurrency(valuation)}`;
}

function populateCategoryDropdown(categories) {
  const select = document.getElementById('inventoryCategoryFilter');
  if (select.options.length > 1 || !categories) return;

  categories.forEach(c => {
    const opt = document.createElement('option');
    opt.value = c.id;
    opt.textContent = c.name;
    select.appendChild(opt);
  });
}

function adjustInputValue(productId, delta) {
  const input = document.getElementById(`stockInput_${productId}`);
  if (input) {
    let val = parseInt(input.value) || 0;
    val = Math.max(0, val + delta);
    input.value = val;
  }
}

async function saveStockInput(productId) {
  const input = document.getElementById(`stockInput_${productId}`);
  const btn = document.getElementById(`saveBtn_${productId}`);
  if (!input) return;

  const newQty = parseInt(input.value);
  if (isNaN(newQty) || newQty < 0) {
    App.showToast('Stock quantity must be a non-negative integer', 'error');
    return;
  }

  // Show loading state
  const originalHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;

  try {
    const res = await axios.post('/admin/inventory/index.php', {
      product_id: productId,
      stock_quantity: newQty
    });

    if (res.data.status === 'success') {
      App.showToast(res.data.message);
      
      const updatedProduct = res.data.data.product;
      const stats = res.data.data.stats;

      // Update row UI dynamically
      updateRowUI(updatedProduct);

      // Update KPI cards
      updateKpiCards(stats);
    }
  } catch (err) {
    App.showToast(err.response?.data?.message || 'Failed to update stock', 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalHtml;
  }
}

async function quickRestock(productId, delta) {
  try {
    const res = await axios.post('/admin/inventory/index.php', {
      product_id: productId,
      adjustment: delta
    });

    if (res.data.status === 'success') {
      App.showToast(res.data.message);
      const updatedProduct = res.data.data.product;
      const stats = res.data.data.stats;

      updateRowUI(updatedProduct);
      updateKpiCards(stats);
    }
  } catch (err) {
    App.showToast(err.response?.data?.message || 'Quick restock failed', 'error');
  }
}

function updateRowUI(p) {
  if (!p) return;
  const isLow = p.stock_quantity <= 5 && p.stock_quantity > 0;
  const isOut = p.stock_quantity == 0;

  // Update input
  const input = document.getElementById(`stockInput_${p.id}`);
  if (input) input.value = p.stock_quantity;

  // Update display count
  const display = document.getElementById(`stockValueDisplay_${p.id}`);
  if (display) {
    display.textContent = `${p.stock_quantity} units`;
    display.className = `fw-bold fs-6 ${isOut ? 'text-danger' : (isLow ? 'text-warning' : 'text-success')}`;
  }

  // Update badge
  const badgeWrap = document.getElementById(`stockBadge_${p.id}`);
  if (badgeWrap) {
    let badge = '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i>Healthy Stock</span>';
    if (isOut) {
      badge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-x-circle me-1"></i>Out of Stock</span>';
    } else if (isLow) {
      badge = '<span class="badge bg-warning-subtle text-dark border border-warning-subtle"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock</span>';
    }
    badgeWrap.innerHTML = badge;
  }
}

function exportInventoryCSV() {
  const items = currentInventoryState.loadedItems;
  if (!items || items.length === 0) {
    App.showToast('No inventory data to export', 'error');
    return;
  }

  const headers = ["Product ID", "Product Name", "SKU", "Category", "Price (INR)", "Sale Price (INR)", "Current Stock", "Status"];
  const rows = items.map(p => {
    const status = p.stock_quantity == 0 ? "Out of Stock" : (p.stock_quantity <= 5 ? "Low Stock" : "Healthy");
    return [
      p.id,
      `"${p.name.replace(/"/g, '""')}"`,
      `"${(p.sku || '').replace(/"/g, '""')}"`,
      `"${p.category_name.replace(/"/g, '""')}"`,
      p.price,
      p.sale_price || '',
      p.stock_quantity,
      status
    ];
  });

  const csvContent = "data:text/csv;charset=utf-8," + [headers.join(","), ...rows.map(r => r.join(","))].join("\n");
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", `Bagicha_Inventory_Report_${new Date().toISOString().slice(0,10)}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
