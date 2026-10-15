/**
 * Bagicha 2.0 - Wishlist JS Module with Real-Time Synchronization
 */

const WishlistModule = {
  savedProductIds: new Set(),
  syncPromise: null,

  isSaved(productId) {
    return this.savedProductIds.has(Number(productId));
  },

  async syncWishlistState() {
    const token = App.getToken();

    if (!token) {
      this.savedProductIds.clear();
      this.updateAllCounters(0);
      this.updateCardIcons();
      return;
    }

    if (this.syncPromise) {
      return this.syncPromise;
    }

    this.syncPromise = (async () => {
      try {
        const res = await axios.get('/wishlist/index.php');
        if (res.data && res.data.status === 'success') {
          const items = res.data.data.items || [];
          this.savedProductIds = new Set(items.map(item => Number(item.product_id)));
          this.updateAllCounters(items.length);
          this.updateCardIcons();
        } else {
          this.savedProductIds.clear();
          this.updateAllCounters(0);
          this.updateCardIcons();
        }
      } catch (err) {
        console.error('Wishlist sync error:', err);
        if (err.response && err.response.status === 401) {
          this.savedProductIds.clear();
          this.updateAllCounters(0);
          this.updateCardIcons();
        }
      } finally {
        this.syncPromise = null;
      }
    })();

    return this.syncPromise;
  },

  updateAllCounters(count) {
    // 1. Navbar badge
    const badge = document.getElementById('wishlistCountBadge');
    if (badge) {
      badge.textContent = count;
      badge.classList.toggle('d-none', count === 0);
    }

    // 2. Navbar icon (Keep empty heart at all times per requirement A)
    const navIcon = document.getElementById('navbarWishlistIcon');
    if (navIcon) {
      navIcon.className = 'bi bi-heart';
    }

    // 3. Wishlist page header counter
    const wishlistPageCount = document.getElementById('wishlistItemCount');
    if (wishlistPageCount) {
      wishlistPageCount.textContent = count;
    }

    // 4. Profile page dashboard counter & icon
    const profileCount = document.getElementById('wishlistCount');
    if (profileCount) {
      profileCount.textContent = count;
    }
    const profileIcon = document.getElementById('profileWishlistIcon');
    if (profileIcon) {
      profileIcon.className = count > 0 ? 'bi bi-heart-fill text-danger' : 'bi bi-heart';
    }
  },

  updateCardIcons() {
    const buttons = document.querySelectorAll('.btn-card-wishlist, .btn-detail-wishlist, button[onclick*="WishlistModule.toggleWishlist"]');
    buttons.forEach(btn => {
      let prodId = btn.getAttribute('data-product-id');
      if (!prodId) {
        const onclickAttr = btn.getAttribute('onclick') || '';
        const match = onclickAttr.match(/toggleWishlist\s*\(\s*(\d+)/);
        if (match) {
          prodId = match[1];
        }
      }

      if (prodId) {
        const saved = this.isSaved(prodId);
        const icon = btn.querySelector('i') || btn;
        if (icon) {
          if (saved) {
            icon.className = 'bi bi-heart-fill text-danger';
          } else {
            icon.className = 'bi bi-heart text-muted';
          }
        }
        if (btn.classList.contains('btn-detail-wishlist')) {
          btn.classList.toggle('active-saved', saved);
        }
        btn.setAttribute('title', saved ? 'Remove from Wishlist' : 'Add to Wishlist');
        btn.setAttribute('aria-label', saved ? 'Remove from Wishlist' : 'Add to Wishlist');
      }
    });
  },

  async toggleWishlist(productId, btnElement) {
    if (!App.getToken()) {
      App.showToast('Please login to add items to your wishlist', 'warning');
      const authModalEl = document.getElementById('authModal');
      if (authModalEl) {
        const authModal = bootstrap.Modal.getOrCreateInstance(authModalEl);
        authModal.show();
      }
      return;
    }

    const numProdId = Number(productId);
    if (btnElement) {
      btnElement.disabled = true;
    }

    try {
      const res = await axios.post('/wishlist/toggle.php', { product_id: numProdId });
      if (res.data && res.data.status === 'success') {
        const inWishlist = res.data.data ? res.data.data.in_wishlist : false;
        
        if (inWishlist) {
          this.savedProductIds.add(numProdId);
          App.showToast(res.data.message || 'Added to wishlist');
        } else {
          this.savedProductIds.delete(numProdId);
          App.showToast(res.data.message || 'Removed from wishlist');
        }

        this.updateAllCounters(this.savedProductIds.size);
        this.updateCardIcons();

        const wishlistGrid = document.getElementById('wishlistPageGrid');
        if (wishlistGrid) {
          await this.loadWishlistPage('wishlistPageGrid');
        }
      }
    } catch (err) {
      console.error('Wishlist Toggle Error:', err);
      const msg = err.response?.data?.message || 'Wishlist action failed';
      App.showToast(msg, 'error');
    } finally {
      if (btnElement) {
        btnElement.disabled = false;
      }
    }
  },

  async loadWishlistPage(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (!App.getToken()) {
      container.innerHTML = `
        <div class="col-12"><div class="portal-empty-state">
          <span class="portal-empty-icon wishlist-empty-icon"><i class="bi bi-heart"></i></span>
          <h2 class="h4 font-heading mt-3">Log in to view your Wishlist</h2>
          <p>Sign in to your account to view saved items and add new plants to your wishlist.</p>
          <button class="btn btn-bagicha-primary" data-bs-toggle="modal" data-bs-target="#authModal" data-tab="login">
            <i class="bi bi-box-arrow-in-right me-2"></i>Log In / Register
          </button>
        </div></div>
      `;
      this.updateAllCounters(0);
      return;
    }

    try {
      const res = await axios.get('/wishlist/index.php');
      if (res.data && res.data.status === 'success') {
        const items = res.data.data.items || [];
        this.savedProductIds = new Set(items.map(item => Number(item.product_id)));
        this.updateAllCounters(items.length);

        if (items.length === 0) {
          container.innerHTML = `
            <div class="col-12"><div class="portal-empty-state">
              <span class="portal-empty-icon wishlist-empty-icon"><i class="bi bi-heart"></i></span>
              <h2 class="h4 font-heading mt-3">Save plants for later</h2>
              <p>Your wishlist is empty right now. Explore the collection and save the plants that catch your eye.</p>
              <a href="${App.getBaseUrl()}/pages/products.php" class="btn btn-bagicha-primary"><i class="bi bi-search me-2"></i>Explore plants</a>
            </div></div>
          `;
          return;
        }

        container.innerHTML = items.map(item => `
          <div class="col-sm-6 col-lg-4 col-xl-3">
            <article class="card card-bagicha wishlist-product-card h-100 position-relative">
              <button class="btn btn-sm btn-light rounded-circle position-absolute top-0 end-0 m-3 shadow-sm btn-card-wishlist" 
                      data-product-id="${item.product_id}"
                      onclick="WishlistModule.toggleWishlist(${item.product_id}, this)" 
                      title="Remove from Wishlist" 
                      aria-label="Remove from Wishlist">
                <i class="bi bi-heart-fill text-danger"></i>
              </button>

              <div class="wishlist-image-wrap">
                <a href="${App.getBaseUrl()}/pages/product-detail.php?slug=${item.slug}" class="d-block overflow-hidden">
                  <img src="${item.main_image}" class="card-img-top card-img-top-custom" alt="${item.name}">
                </a>
              </div>

              <div class="card-body d-flex flex-column justify-content-between p-3 p-lg-4">
                <div>
                  <span class="badge badge-green badge-tag mb-1">${item.category_name || 'Plant'}</span>
                  <h2 class="card-title font-heading text-dark h5 mb-2">
                    <a href="${App.getBaseUrl()}/pages/product-detail.php?slug=${item.slug}" class="text-dark text-decoration-none hover-primary">${item.name}</a>
                  </h2>
                  <div class="wishlist-price mb-3">${App.formatCurrency(item.sale_price || item.price)}</div>
                </div>
                <div class="d-grid gap-2">
                  <button class="btn btn-bagicha-primary btn-sm" onclick="CartModule.addToCart(${item.product_id})">
                    <i class="bi bi-bag-plus me-1"></i> Add to cart
                  </button>
                  <button class="btn btn-wishlist-remove btn-sm" onclick="WishlistModule.removeFromWishlist(${item.product_id}, '${containerId}', this)">
                    <i class="bi bi-heartbreak me-1"></i> Remove
                  </button>
                </div>
              </div>
            </article>
          </div>
        `).join('');
      }
    } catch (err) {
      container.innerHTML = `<div class="col-12 text-center text-danger py-5">Failed to load wishlist.</div>`;
    }
  },

  async removeFromWishlist(productId, containerId, button) {
    await this.toggleWishlist(productId, button);
  }
};

document.addEventListener('DOMContentLoaded', () => {
  WishlistModule.syncWishlistState();
});
