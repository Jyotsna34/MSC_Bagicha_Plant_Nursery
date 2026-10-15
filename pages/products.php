<?php
/**
 * Bagicha 2.0 - Product Catalog Page
 */
$pageTitle = "Shop All Plants & Supplies - Bagicha";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<main id="main-content" class="container py-4" tabindex="-1">
  <!-- Breadcrumb Navigation -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-success text-decoration-none">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Shop Catalog</li>
    </ol>
  </nav>

  <!-- Catalog Header Banner -->
  <div class="card border-0 bg-white p-4 rounded-4 shadow-sm mb-4 border border-light-subtle">
    <div class="row align-items-center g-3">
      <div class="col-lg-7">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2 bg-success-subtle text-success small fw-semibold">
          <i class="bi bi-patch-check-fill"></i> Verified Healthy On Arrival
        </div>
        <h1 class="font-heading h2 text-primary mb-1" id="catalogTitle">All Garden & Plant Products</h1>
        <p class="text-muted small mb-0">Browse our hand-reared indoor foliage, exotic succulents, and organic care supplies.</p>
      </div>
      <div class="col-lg-5 text-lg-end d-flex flex-column flex-sm-row justify-content-lg-end align-items-sm-center gap-3">
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small" id="resultsCount">Loading products...</span>
        <div class="d-flex align-items-center gap-2">
          <label for="sortSelect" class="small text-muted text-nowrap fw-semibold d-none d-sm-inline">Sort:</label>
          <select id="sortSelect" class="form-select form-select-sm rounded-3 shadow-xs" style="min-width: 170px;">
            <option value="latest">Newest First</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
            <option value="rating">Top Customer Rated</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Filters Sidebar -->
    <div class="col-lg-3">
      <div class="d-flex flex-column gap-3 sticky-top" style="top: 85px; z-index: 10;">
        <!-- Categories Card -->
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
          <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom border-light">
            <h6 class="font-heading text-primary mb-0"><i class="bi bi-grid me-1.5"></i> Categories</h6>
            <button class="btn btn-link btn-sm text-muted p-0 text-decoration-none small" onclick="clearCategoryFilter()" title="Clear Category">Reset</button>
          </div>
          <div class="list-group list-group-flush" id="categoryFilterList">
            <button class="list-group-item list-group-item-action active border-0 rounded-3 mb-1" data-cat=""><i class="bi bi-grid-fill me-2 text-success"></i> All Collections</button>
            <button class="list-group-item list-group-item-action border-0 rounded-3 mb-1" data-cat="indoor-plants"><i class="bi bi-house-heart me-2 text-success"></i> Indoor Plants</button>
            <button class="list-group-item list-group-item-action border-0 rounded-3 mb-1" data-cat="outdoor-plants"><i class="bi bi-sun me-2 text-warning"></i> Outdoor Plants</button>
            <button class="list-group-item list-group-item-action border-0 rounded-3 mb-1" data-cat="flowering-plants"><i class="bi bi-flower1 me-2 text-danger"></i> Flowering Plants</button>
            <button class="list-group-item list-group-item-action border-0 rounded-3 mb-1" data-cat="succulents-cacti"><i class="bi bi-water me-2 text-info"></i> Succulents & Cacti</button>
            <button class="list-group-item list-group-item-action border-0 rounded-3 mb-1" data-cat="gardening-tools"><i class="bi bi-tools me-2 text-secondary"></i> Gardening Tools</button>
            <button class="list-group-item list-group-item-action border-0 rounded-3 mb-1" data-cat="seeds-fertilizers"><i class="bi bi-box-seam me-2 text-success"></i> Organic Soil & Seeds</button>
          </div>
        </div>

        <!-- Sunlight Filter Card -->
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
          <h6 class="font-heading text-primary mb-2.5 pb-1 border-bottom border-light"><i class="bi bi-brightness-high me-1.5"></i> Room Lighting</h6>
          <div class="d-flex flex-column gap-1" id="lightFilterGroup">
            <button class="btn btn-sm text-start py-1.5 px-2 rounded-2 btn-light border-0 fw-medium active" data-light=""><i class="bi bi-check2 text-success me-1"></i> Any Light</button>
            <button class="btn btn-sm text-start py-1.5 px-2 rounded-2 btn-light border-0 fw-medium" data-light="Low"><i class="bi bi-cloud-sun text-primary me-1"></i> Low Light / Shade</button>
            <button class="btn btn-sm text-start py-1.5 px-2 rounded-2 btn-light border-0 fw-medium" data-light="Indirect"><i class="bi bi-sun text-warning me-1"></i> Filtered Indirect</button>
            <button class="btn btn-sm text-start py-1.5 px-2 rounded-2 btn-light border-0 fw-medium" data-light="Full"><i class="bi bi-brightness-alt-high text-danger me-1"></i> Direct Sunlight</button>
          </div>
        </div>

        <!-- Arrive-Alive Guarantee Mini-Card -->
        <div class="card border-0 p-3 rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #034E2B 0%, #064E2B 100%) !important;">
          <div class="d-flex align-items-center gap-2 mb-1.5">
            <i class="bi bi-shield-check fs-4 text-warning"></i>
            <h6 class="font-heading mb-0 text-white">7-Day Guarantee</h6>
          </div>
          <p class="small text-white-50 mb-2.5" style="font-size: 0.82rem;">Every plant is backed by our unconditional Arrive-Alive free replacement policy.</p>
          <a href="<?= BASE_URL ?>/pages/guarantee.php" class="btn btn-light btn-sm text-success fw-bold w-100 py-1.5">
            View Guarantee <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Product Grid & Main Content -->
    <div class="col-lg-9">
      <!-- Active Filter Tags Row -->
      <div id="activeFilterBadges" class="d-flex flex-wrap gap-2 align-items-center mb-3 d-none"></div>

      <!-- Quick Feature Filter Pills Bar -->
      <div class="filter-pills-bar mb-3" id="quickPillsBar">
        <button class="filter-pill active" data-filter="" data-val=""><i class="bi bi-grid-fill me-1"></i> All Catalog</button>
        <button class="filter-pill" data-filter="pet_friendly" data-val="1"><i class="bi bi-shield-check text-success me-1"></i> Pet-Safe</button>
        <button class="filter-pill" data-filter="light" data-val="Low"><i class="bi bi-cloud-sun text-primary me-1"></i> Low-Light Survivors</button>
        <button class="filter-pill" data-filter="maintenance" data-val="Low"><i class="bi bi-battery-charging text-warning me-1"></i> Low Maintenance</button>
        <button class="filter-pill" data-filter="featured" data-val="1"><i class="bi bi-stars text-warning me-1"></i> Bestsellers</button>
      </div>

      <!-- Catalog Product Grid -->
      <div class="row" id="catalogProductGrid">
        <div class="col-12 text-center py-5">
          <div class="spinner-border text-success" role="status"></div>
        </div>
      </div>

      <!-- Pagination -->
      <nav class="d-flex justify-content-center mt-4">
        <ul class="pagination pagination-sm" id="catalogPagination"></ul>
      </nav>
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  let currentCategory = urlParams.get('category') || '';
  let currentSearch = urlParams.get('search') || '';
  let currentLight = urlParams.get('light') || '';
  let currentFilterKey = '';
  let currentFilterVal = '';
  let currentSort = 'latest';
  let currentPage = 1;

  const catalogTitle = document.getElementById('catalogTitle');
  const resultsCount = document.getElementById('resultsCount');
  const sortSelect = document.getElementById('sortSelect');
  const activeFilterBadges = document.getElementById('activeFilterBadges');

  function updateActiveFilterTags() {
    let tags = [];
    if (currentSearch) {
      tags.push(`<span class="badge bg-light text-dark border px-2 py-1.5 rounded-pill">Search: "${currentSearch}" <i class="bi bi-x-circle-fill text-muted ms-1 cursor-pointer" onclick="clearSearchFilter()"></i></span>`);
    }
    if (currentCategory) {
      tags.push(`<span class="badge bg-light text-dark border px-2 py-1.5 rounded-pill">Category: ${currentCategory} <i class="bi bi-x-circle-fill text-muted ms-1 cursor-pointer" onclick="clearCategoryFilter()"></i></span>`);
    }
    if (currentLight) {
      tags.push(`<span class="badge bg-light text-dark border px-2 py-1.5 rounded-pill">Light: ${currentLight} <i class="bi bi-x-circle-fill text-muted ms-1 cursor-pointer" onclick="clearLightFilter()"></i></span>`);
    }
    if (currentFilterKey && currentFilterVal) {
      tags.push(`<span class="badge bg-light text-dark border px-2 py-1.5 rounded-pill">Filter: ${currentFilterKey} <i class="bi bi-x-circle-fill text-muted ms-1 cursor-pointer" onclick="clearPillFilter()"></i></span>`);
    }

    if (tags.length > 0) {
      activeFilterBadges.innerHTML = '<span class="small text-muted me-1">Active:</span>' + tags.join('') + ' <button class="btn btn-link btn-sm text-danger p-0 ms-1 text-decoration-none" onclick="clearAllFilters()">Reset All</button>';
      activeFilterBadges.classList.remove('d-none');
    } else {
      activeFilterBadges.innerHTML = '';
      activeFilterBadges.classList.add('d-none');
    }
  }

  window.clearSearchFilter = () => { currentSearch = ''; currentPage = 1; fetchCatalog(); };
  window.clearCategoryFilter = () => {
    currentCategory = '';
    document.querySelectorAll('#categoryFilterList button').forEach(b => b.classList.toggle('active', b.dataset.cat === ''));
    currentPage = 1; fetchCatalog();
  };
  window.clearLightFilter = () => {
    currentLight = '';
    document.querySelectorAll('#lightFilterGroup button').forEach(b => b.classList.toggle('active', b.dataset.light === ''));
    currentPage = 1; fetchCatalog();
  };
  window.clearPillFilter = () => {
    currentFilterKey = ''; currentFilterVal = '';
    document.querySelectorAll('#quickPillsBar .filter-pill').forEach(p => p.classList.toggle('active', p.dataset.filter === ''));
    currentPage = 1; fetchCatalog();
  };
  window.clearAllFilters = () => {
    currentSearch = ''; currentCategory = ''; currentLight = ''; currentFilterKey = ''; currentFilterVal = '';
    document.querySelectorAll('#categoryFilterList button').forEach(b => b.classList.toggle('active', b.dataset.cat === ''));
    document.querySelectorAll('#lightFilterGroup button').forEach(b => b.classList.toggle('active', b.dataset.light === ''));
    document.querySelectorAll('#quickPillsBar .filter-pill').forEach(p => p.classList.toggle('active', p.dataset.filter === ''));
    currentPage = 1; fetchCatalog();
  };

  if (currentCategory) {
    document.querySelectorAll('#categoryFilterList button').forEach(btn => btn.classList.toggle('active', btn.dataset.cat === currentCategory));
  }

  // Sunlight filter listener
  document.querySelectorAll('#lightFilterGroup button').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('#lightFilterGroup button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentLight = btn.dataset.light;
      currentPage = 1;
      fetchCatalog();
    });
  });

  // Quick filter pills listener
  document.querySelectorAll('#quickPillsBar .filter-pill').forEach(pill => {
    pill.addEventListener('click', () => {
      document.querySelectorAll('#quickPillsBar .filter-pill').forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
      currentFilterKey = pill.dataset.filter;
      currentFilterVal = pill.dataset.val;
      currentPage = 1;
      fetchCatalog();
    });
  });

  async function fetchCatalog() {
    const grid = document.getElementById('catalogProductGrid');
    grid.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-success" role="status"></div></div>';
    updateActiveFilterTags();

    if (currentSearch) {
      catalogTitle.textContent = `Search Results for "${currentSearch}"`;
    } else if (currentCategory) {
      catalogTitle.textContent = 'Category: ' + currentCategory.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    } else {
      catalogTitle.textContent = 'All Garden & Plant Products';
    }

    let query = `/products/index.php?page=${currentPage}&limit=9&sort=${currentSort}`;
    if (currentCategory) query += `&category=${encodeURIComponent(currentCategory)}`;
    if (currentSearch) query += `&search=${encodeURIComponent(currentSearch)}`;
    if (currentLight) query += `&light=${encodeURIComponent(currentLight)}`;
    if (currentFilterKey && currentFilterVal) query += `&${currentFilterKey}=${encodeURIComponent(currentFilterVal)}`;

    try {
      const res = await axios.get(query);
      if (res.data.status === 'success') {
        const { products, pagination } = res.data.data;
        resultsCount.textContent = `Showing ${products.length} of ${pagination.total} species`;

        if (products.length > 0) {
          grid.innerHTML = products.map(p => ProductsModule.renderProductCard(p)).join('');
        } else {
          grid.innerHTML = `
            <div class="col-12 text-center py-5">
              <i class="bi bi-tree fs-1 text-muted"></i>
              <h5 class="mt-3 text-muted">No plants found matching criteria</h5>
              <button class="btn btn-bagicha-outline btn-sm mt-2" onclick="clearAllFilters()">Clear All Filters</button>
            </div>
          `;
        }
        renderPagination(pagination);
        if (window.WishlistModule) WishlistModule.updateCardIcons();
      }
    } catch (err) {
      grid.innerHTML = '<div class="col-12 text-center py-5 text-danger">Failed to load catalog products.</div>';
    }
  }

  function renderPagination(pg) {
    const nav = document.getElementById('catalogPagination');
    if (pg.total_pages <= 1) {
      nav.innerHTML = '';
      return;
    }
    let html = '';
    for (let i = 1; i <= pg.total_pages; i++) {
      html += `<li class="page-item ${i === pg.page ? 'active' : ''}"><button class="page-link" onclick="changePage(${i})">${i}</button></li>`;
    }
    nav.innerHTML = html;
  }

  window.changePage = function(p) {
    currentPage = p;
    fetchCatalog();
  };

  document.querySelectorAll('#categoryFilterList button').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('#categoryFilterList button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentCategory = btn.dataset.cat;
      currentPage = 1;
      fetchCatalog();
    });
  });

  sortSelect.addEventListener('change', (e) => {
    currentSort = e.target.value;
    currentPage = 1;
    fetchCatalog();
  });

  fetchCatalog();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
