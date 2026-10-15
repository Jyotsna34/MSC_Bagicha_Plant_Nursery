/**
 * Bagicha - Product Catalog & Rendering JS
 */

const ProductsModule = {
  recordRecentlyViewed(product) {
    if (!product?.id || !product?.slug) return;
    const key = 'bagicha_recently_viewed';
    const existing = this.getRecentlyViewed().filter(item => Number(item.id) !== Number(product.id));
    const item = { id: product.id, slug: product.slug, name: product.name, main_image: product.main_image, price: product.price, sale_price: product.sale_price };
    localStorage.setItem(key, JSON.stringify([item, ...existing].slice(0, 6)));
  },

  getRecentlyViewed() {
    try {
      const items = JSON.parse(localStorage.getItem('bagicha_recently_viewed') || '[]');
      return Array.isArray(items) ? items : [];
    } catch (err) {
      return [];
    }
  },

  renderProductCard(p) {
    const isSaved = (typeof WishlistModule !== 'undefined' && WishlistModule.isSaved) ? WishlistModule.isSaved(p.id) : false;
    const heartIcon = isSaved ? 'bi bi-heart-fill text-danger' : 'bi bi-heart text-muted';
    const heartTitle = isSaved ? 'Remove from Wishlist' : 'Add to Wishlist';

    const isSale = p.sale_price && Number(p.sale_price) < Number(p.price);
    const discountPct = isSale ? Math.round(((p.price - p.sale_price) / p.price) * 100) : 0;
    
    const saleTag = isSale 
      ? `<span class="badge badge-accent badge-tag position-absolute top-0 start-0 m-3 shadow-sm"><i class="bi bi-tag-fill me-1"></i> SAVE ${discountPct}%</span>` 
      : (p.featured ? `<span class="badge badge-green badge-tag position-absolute top-0 start-0 m-3 shadow-sm"><i class="bi bi-star-fill me-1"></i> POPULAR</span>` : '');

    const priceHtml = isSale 
      ? `<span class="fw-bold text-success fs-5">${App.formatCurrency(p.sale_price)}</span> <small class="text-decoration-line-through text-muted ms-1">${App.formatCurrency(p.price)}</small>`
      : `<span class="fw-bold text-success fs-5">${App.formatCurrency(p.price)}</span>`;

    const lightIcon = p.light_requirement ? `<span class="care-pill" title="Light Requirement"><i class="bi bi-sun"></i> ${p.light_requirement}</span>` : '';
    const waterIcon = p.water_requirement ? `<span class="care-pill" title="Watering Needs"><i class="bi bi-droplet"></i> ${p.water_requirement}</span>` : '';

    return `
      <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
        <div class="card card-bagicha position-relative h-100">
          ${saleTag}
          <button class="btn btn-sm btn-light rounded-circle position-absolute top-0 end-0 m-3 shadow-sm btn-card-wishlist" 
                  data-product-id="${p.id}"
                  onclick="WishlistModule.toggleWishlist(${p.id}, this)" 
                  title="${heartTitle}" 
                  aria-label="${heartTitle}">
            <i class="${heartIcon}"></i>
          </button>

          <a href="${App.getBaseUrl()}/pages/product-detail.php?slug=${p.slug}" class="text-decoration-none card-img-wrapper d-block overflow-hidden">
            <img src="${p.main_image}" class="card-img-top card-img-top-custom" alt="${p.name}" loading="lazy">
          </a>

          <div class="card-body d-flex flex-column justify-content-between p-3">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="badge badge-green badge-tag">${p.category_name || 'Plant'}</span>
                <span class="badge bg-warning-subtle text-dark border border-warning-subtle small fw-semibold px-2 py-1">
                  <i class="bi bi-star-fill text-warning"></i> ${p.rating_avg || '4.8'}
                </span>
              </div>
              <h6 class="card-title font-heading text-dark mb-2 text-truncate" title="${p.name}">
                <a href="${App.getBaseUrl()}/pages/product-detail.php?slug=${p.slug}" class="text-dark text-decoration-none hover-primary">${p.name}</a>
              </h6>
              <div class="d-flex gap-1 flex-wrap mb-3">
                ${lightIcon}
                ${waterIcon}
              </div>
            </div>

            <div>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>${priceHtml}</div>
                <small class="text-muted">${(p.stock_quantity !== undefined && p.stock_quantity !== null && Number(p.stock_quantity) === 0) ? '<span class="text-danger small fw-semibold">Out of Stock</span>' : '<span class="text-success small fw-semibold">In Stock</span>'}</small>
              </div>
              <div class="d-grid gap-2">
                <a href="${App.getBaseUrl()}/pages/product-detail.php?slug=${p.slug}" class="btn btn-bagicha-primary btn-sm d-flex align-items-center justify-content-center gap-1">
                  <i class="bi bi-eye"></i> View Details
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  },

  async loadFeaturedProducts(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const isTrack = container.classList.contains('horizontal-scroll-track');

    try {
      const res = await axios.get('/products/index.php?featured=1&limit=10');
      if (res.data.status === 'success' && res.data.data.products.length > 0) {
        if (isTrack) {
          container.innerHTML = res.data.data.products.map(p => `
            <div class="product-item-card">
              ${this.renderProductCard(p).replace(/class="col-[^"]*"/, 'class="h-100"')}
            </div>
          `).join('');
        } else {
          container.innerHTML = res.data.data.products.map(p => this.renderProductCard(p)).join('');
        }
        if (window.WishlistModule) WishlistModule.updateCardIcons();
      } else {
        container.innerHTML = `<div class="text-center py-4 text-muted w-100">No featured products available.</div>`;
      }
    } catch (err) {
      console.error('Failed to load featured products', err);
      container.innerHTML = `<div class="text-center py-4 text-danger w-100">Failed to load products.</div>`;
    }
  },

  async loadCategories(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const isTrack = container.classList.contains('horizontal-scroll-track');

    try {
      const res = await axios.get('/categories/index.php');
      if (res.data.status === 'success') {
        container.innerHTML = res.data.data.categories.map(c => {
          const cardHtml = `
            <a href="${App.getBaseUrl()}/pages/products.php?category=${c.slug}" class="text-decoration-none d-block h-100">
              <div class="card card-bagicha text-center p-3 h-100 category-card-hover border-0 shadow-sm rounded-4">
                <div class="mx-auto mb-2 rounded-circle overflow-hidden p-1 bg-light border shadow-sm" style="width: 76px; height: 76px;">
                  <img src="${c.image || 'https://images.unsplash.com/photo-1545241047-6083a3684587?auto=format&fit=crop&w=200&q=80'}" class="rounded-circle w-100 h-100" style="object-fit: cover;" alt="${c.name}">
                </div>
                <span class="fw-bold text-dark small font-heading d-block mb-1 text-truncate">${c.name}</span>
                <span class="badge bg-success-subtle text-success small">Explore <i class="bi bi-chevron-right"></i></span>
              </div>
            </a>
          `;

          if (isTrack) {
            return `<div class="category-item-card">${cardHtml}</div>`;
          }
          return `<div class="col-6 col-md-4 col-lg-2 mb-3">${cardHtml}</div>`;
        }).join('');
      }
    } catch (err) {
      console.error('Failed to load categories', err);
    }
  }
};

