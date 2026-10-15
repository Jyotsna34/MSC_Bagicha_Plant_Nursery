<?php
/**
 * Bagicha 2.0 - Admin Category Management Page
 */
require_once __DIR__ . '/../api/helpers/auth.php';
requireAdminPage();

$adminTitle = "Manage Categories - Bagicha Admin";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-content">
  <!-- Breadcrumb Navigation -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/index.php" class="text-success text-decoration-none">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Categories</li>
    </ol>
  </nav>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="font-heading h2 text-primary mb-1">Categories Management</h1>
      <p class="text-muted small mb-0">Organize plant collections into indoor, outdoor, succulents, tools & fertilizers.</p>
    </div>
    <button class="btn btn-admin-primary" onclick="openCategoryModal()">
      <i class="bi bi-plus-lg"></i> Add Category
    </button>
  </div>

  <div class="admin-card p-4">
    <div class="table-responsive">
      <table class="table table-admin-custom align-middle mb-0">
        <thead class="small">
          <tr>
            <th>Image</th>
            <th>Category Name</th>
            <th>Slug</th>
            <th>Product Count</th>
            <th>Featured</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="adminCategoriesTable">
          <tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-success"></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Add / Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-bottom p-3">
        <h5 class="modal-title font-heading text-primary" id="catModalTitle">Add Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="categoryForm" enctype="multipart/form-data" onsubmit="saveCategory(event)">
          <input type="hidden" id="catId" name="id">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Category Name</label>
            <input type="text" id="catName" name="name" class="form-control" placeholder="Tropical Ferns" required style="border-radius: 10px;">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Description</label>
            <textarea id="catDesc" name="description" class="form-control" rows="3" placeholder="Category highlights..." style="border-radius: 10px;"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Image URL</label>
            <input type="text" id="catImage" name="image" class="form-control" placeholder="https://..." style="border-radius: 10px;">
          </div>
          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="catFeatured" name="is_featured">
            <label class="form-check-label small" for="catFeatured">Display as Featured Category on Homepage</label>
          </div>
          <button type="submit" class="btn btn-bagicha-primary w-100 py-2">Save Category</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
let categoriesCache = [];

document.addEventListener('DOMContentLoaded', async () => {
  if (!AdminApp.checkAdminAuth()) return;
  await loadCategories();
});

async function loadCategories() {
  const table = document.getElementById('adminCategoriesTable');
  try {
    const res = await axios.get('/admin/categories/index.php', { timeout: 8000 });
    if (res.data.status === 'success') {
      categoriesCache = res.data.data.categories || [];
      if (categoriesCache.length === 0) {
        table.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">No categories created yet.</td></tr>`;
        return;
      }
      table.innerHTML = categoriesCache.map(c => `
        <tr>
          <td><img src="${c.image || 'https://via.placeholder.com/50'}" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;"></td>
          <td class="fw-bold">${c.name}</td>
          <td class="text-muted small">${c.slug}</td>
          <td><span class="badge badge-neutral">${c.product_count || 0} Products</span></td>
          <td><span class="badge ${c.is_featured == 1 ? 'bg-success' : 'bg-secondary'}">${c.is_featured == 1 ? 'Yes' : 'No'}</span></td>
          <td class="text-nowrap">
            <button class="btn btn-outline-primary btn-sm me-1" onclick="editCategory(${c.id})" title="Edit Category">
              <i class="bi bi-pencil me-1"></i> Edit
            </button>
            <button class="btn btn-outline-danger btn-sm" onclick="AdminApp.deleteCategory(${c.id}, ${c.product_count || 0})" title="Delete Category">
              <i class="bi bi-trash me-1"></i> Delete
            </button>
          </td>
        </tr>
      `).join('');
    }
  } catch (err) {
    table.innerHTML = `<tr><td colspan="6" class="text-danger text-center">Failed to load categories.</td></tr>`;
  }
}

function openCategoryModal() {
  document.getElementById('categoryForm').reset();
  document.getElementById('catId').value = '';
  document.getElementById('catModalTitle').textContent = 'Add Category';
  const modalEl = document.getElementById('categoryModal');
  const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
  modal.show();
}

function editCategory(id) {
  const cat = categoriesCache.find(c => c.id == id);
  if (!cat) return;

  document.getElementById('categoryForm').reset();
  document.getElementById('catId').value = cat.id;
  document.getElementById('catName').value = cat.name || '';
  document.getElementById('catDesc').value = cat.description || '';
  document.getElementById('catImage').value = cat.image || '';
  document.getElementById('catFeatured').checked = (parseInt(cat.is_featured) === 1);
  document.getElementById('catModalTitle').textContent = 'Edit Category';

  const modalEl = document.getElementById('categoryModal');
  const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
  modal.show();
}

async function saveCategory(e) {
  e.preventDefault();
  const formData = new FormData(document.getElementById('categoryForm'));
  try {
    const res = await axios.post('/admin/categories/index.php', formData);
    if (res.data.status === 'success') {
      App.showToast(res.data.message || 'Category saved!');
      const modalEl = document.getElementById('categoryModal');
      const modal = bootstrap.Modal.getInstance(modalEl);
      if (modal) modal.hide();
      await loadCategories();
    }
  } catch (err) {
    App.showToast(err.response?.data?.message || 'Failed to save category', 'error');
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
