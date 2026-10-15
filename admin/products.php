<?php
/**
 * Bagicha 2.0 - Admin Product Management Page
 */
require_once __DIR__ . '/../api/helpers/auth.php';
requireAdminPage();

$adminTitle = "Manage Products - Bagicha Admin";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-content">
  <!-- Breadcrumb Navigation -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/index.php" class="text-success text-decoration-none">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Products</li>
    </ol>
  </nav>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="font-heading h2 text-primary mb-1">Plant Products Catalog</h1>
      <p class="text-muted small mb-0">Add, edit, manage stock, and toggle store availability for plants.</p>
    </div>
    <button class="btn btn-admin-primary" onclick="openProductModal()">
      <i class="bi bi-plus-lg"></i> Add Plant Product
    </button>
  </div>

  <div class="admin-card p-4">
    <div class="table-responsive">
      <table class="table table-admin-custom align-middle mb-0">
        <thead class="small">
          <tr>
            <th>Image</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="adminProductsTable">
          <tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-success"></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Add / Edit Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-bottom p-3">
        <h5 class="modal-title font-heading text-primary" id="modalTitle">Add Plant Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="productForm" enctype="multipart/form-data" onsubmit="saveProduct(event)">
          <input type="hidden" id="prodId" name="id">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label small fw-semibold">Product Name</label>
              <input type="text" id="prodName" name="name" class="form-control" placeholder="Monstera Deliciosa" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Category</label>
              <select id="prodCategory" name="category_id" class="form-select" required></select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Regular Price (₹)</label>
              <input type="number" step="0.01" id="prodPrice" name="price" class="form-control" placeholder="1299.00" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Sale Price (₹ Optional)</label>
              <input type="number" step="0.01" id="prodSalePrice" name="sale_price" class="form-control" placeholder="999.00">
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Stock Quantity</label>
              <input type="number" id="prodStock" name="stock_quantity" class="form-control" value="20" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Light Requirement</label>
              <input type="text" id="prodLight" name="light_requirement" class="form-control" placeholder="Bright Indirect Sunlight">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Water Requirement</label>
              <input type="text" id="prodWater" name="water_requirement" class="form-control" placeholder="Once a week">
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Short Description</label>
              <input type="text" id="prodShortDesc" name="short_description" class="form-control" placeholder="Brief summary for cards">
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Full Description</label>
              <textarea id="prodDesc" name="description" class="form-control" rows="3" placeholder="Detailed plant care & features"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Main Image URL</label>
              <input type="text" id="prodImage" name="main_image" class="form-control" placeholder="https://...">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Or Upload Local Image</label>
              <input type="file" name="image_file" class="form-control" accept="image/*">
            </div>
            <div class="col-12 d-flex gap-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="prodPet" name="is_pet_friendly" checked>
                <label class="form-check-label small" for="prodPet">Pet Friendly</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="prodFeatured" name="is_featured">
                <label class="form-check-label small" for="prodFeatured">Featured Product</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="prodActive" name="is_active" checked>
                <label class="form-check-label small" for="prodActive">Store Active</label>
              </div>
            </div>
          </div>
          <div class="mt-4 text-end">
            <button type="submit" class="btn btn-bagicha-primary">Save Product</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
let categoriesCache = [];
let allProductsCache = [];

document.addEventListener('DOMContentLoaded', () => {
  if (!AdminApp.checkAdminAuth()) return;
  loadCategoriesSelect();
  loadProducts();
});

async function loadCategoriesSelect() {
  try {
    const res = await axios.get('/categories/index.php', { timeout: 8000 });
    if (res.data.status === 'success') {
      categoriesCache = res.data.data.categories;
      const sel = document.getElementById('prodCategory');
      sel.innerHTML = categoriesCache.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    }
  } catch (err) {}
}

async function loadProducts() {
  const table = document.getElementById('adminProductsTable');
  try {
    const res = await axios.get('/admin/products/index.php', { timeout: 8000 });
    if (res.data.status === 'success') {
      allProductsCache = res.data.data.products;
      if (allProductsCache.length === 0) {
        table.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No plant products added yet.</td></tr>`;
        return;
      }
      table.innerHTML = allProductsCache.map(p => `
        <tr>
          <td><img src="${p.main_image}" class="rounded-3" style="width: 45px; height: 45px; object-fit: cover;"></td>
          <td>
            <div class="fw-bold">${p.name}</div>
            <small class="text-muted">SKU: ${p.sku || 'N/A'}</small>
          </td>
          <td><span class="badge badge-category">${p.category_name}</span></td>
          <td>${p.sale_price ? `<span class="fw-bold text-success">₹${p.sale_price}</span> <small class="text-decoration-line-through text-muted">₹${p.price}</small>` : `₹${p.price}`}</td>
          <td>${p.stock_quantity <= 5 ? `<span class="badge bg-danger">${p.stock_quantity} Low Stock</span>` : `<span class="badge bg-success-subtle text-success">${p.stock_quantity}</span>`}</td>
          <td><span class="badge ${p.is_active == 1 ? 'bg-success' : 'bg-secondary'}">${p.is_active == 1 ? 'Active' : 'Inactive'}</span></td>
          <td>
            <button class="btn btn-outline-primary btn-sm me-1" onclick="editProduct(${p.id})"><i class="bi bi-pencil"></i> Edit</button>
            <button class="btn btn-outline-danger btn-sm" onclick="AdminApp.deleteProduct(${p.id})"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
      `).join('');
    }
  } catch (err) {
    table.innerHTML = `<tr><td colspan="7" class="text-danger text-center">Failed to load products.</td></tr>`;
  }
}

function openProductModal() {
  document.getElementById('productForm').reset();
  document.getElementById('prodId').value = '';
  document.getElementById('prodActive').checked = true;
  document.getElementById('modalTitle').textContent = 'Add Plant Product';
  new bootstrap.Modal(document.getElementById('productModal')).show();
}

function editProduct(id) {
  const p = allProductsCache.find(item => item.id == id);
  if (!p) return;

  document.getElementById('productForm').reset();
  document.getElementById('prodId').value = p.id;
  document.getElementById('prodName').value = p.name || '';
  document.getElementById('prodCategory').value = p.category_id || '';
  document.getElementById('prodPrice').value = p.price || '';
  document.getElementById('prodSalePrice').value = p.sale_price || '';
  document.getElementById('prodStock').value = p.stock_quantity || 0;
  document.getElementById('prodLight').value = p.light_requirement || '';
  document.getElementById('prodWater').value = p.water_requirement || '';
  document.getElementById('prodShortDesc').value = p.short_description || '';
  document.getElementById('prodDesc').value = p.description || '';
  document.getElementById('prodImage').value = p.main_image || '';
  document.getElementById('prodPet').checked = (p.is_pet_friendly == 1);
  document.getElementById('prodFeatured').checked = (p.is_featured == 1);
  document.getElementById('prodActive').checked = (p.is_active == 1);

  document.getElementById('modalTitle').textContent = 'Edit Plant Product';
  new bootstrap.Modal(document.getElementById('productModal')).show();
}

async function saveProduct(e) {
  e.preventDefault();
  const prodId = document.getElementById('prodId').value;
  const endpoint = prodId ? '/admin/products/update.php' : '/admin/products/index.php';
  const formData = new FormData(document.getElementById('productForm'));

  try {
    const res = await axios.post(endpoint, formData);
    if (res.data.status === 'success') {
      App.showToast(prodId ? 'Product updated successfully!' : 'Product created successfully!');
      bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
      await loadProducts();
    }
  } catch (err) {
    App.showToast(err.response?.data?.message || 'Failed to save product', 'error');
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
