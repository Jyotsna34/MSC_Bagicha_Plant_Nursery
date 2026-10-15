/**
 * Bagicha 2.0 - Cart Drawer & State Manager JS
 */

const CartModule = {
  cartState: {
    items: [],
    summary: { subtotal: 0, shipping_fee: 0, total: 0, total_items: 0 }
  },

  async fetchCart() {
    if (!App.getToken()) {
      this.renderCartUI();
      return;
    }

    try {
      const res = await axios.get('/cart/index.php');
      if (res.data.status === 'success') {
        this.cartState.items = res.data.data.items;
        this.cartState.summary = res.data.data.summary;
        this.updateBadgeCounter(res.data.data.summary.total_items);
        this.renderCartUI();
      }
    } catch (err) {
      console.error('Fetch cart error', err);
    }
  },

  updateBadgeCounter(count) {
    document.querySelectorAll('.cart-badge, .icon-badge:not(#userNotifBadge)').forEach(badge => {
      badge.textContent = count || 0;
    });
  },

  async addToCart(productId, quantity = 1) {
    if (!App.getToken()) {
      App.showToast('Please login to add items to your cart', 'warning');
      const authModal = new bootstrap.Modal(document.getElementById('authModal'));
      authModal.show();
      return;
    }

    try {
      const res = await axios.post('/cart/add.php', { product_id: productId, quantity: quantity });
      if (res.data.status === 'success') {
        App.showToast('Item added to cart!');
        await this.fetchCart();
        this.openCartDrawer();
      }
    } catch (err) {
      const msg = err.response?.data?.message || 'Failed to add item to cart.';
      App.showToast(msg, 'error');
    }
  },

  async updateQuantity(cartId, quantity) {
    try {
      const res = await axios.post('/cart/update.php', { cart_id: cartId, quantity: quantity });
      if (res.data.status === 'success') {
        await this.fetchCart();
      }
    } catch (err) {
      App.showToast(err.response?.data?.message || 'Update failed', 'error');
    }
  },

  async removeItem(cartId) {
    try {
      const res = await axios.post('/cart/remove.php', { cart_id: cartId });
      if (res.data.status === 'success') {
        App.showToast('Item removed');
        await this.fetchCart();
      }
    } catch (err) {
      App.showToast('Failed to remove item', 'error');
    }
  },

  openCartDrawer() {
    const el = document.getElementById('cartDrawer');
    if (el) {
      const bsOffcanvas = bootstrap.Offcanvas.getInstance(el) || new bootstrap.Offcanvas(el);
      bsOffcanvas.show();
    }
  },

  renderCartUI() {
    const drawerList = document.getElementById('cartDrawerItems');
    const drawerSubtotal = document.getElementById('cartDrawerSubtotal');
    const fullCartList = document.getElementById('fullCartItems');

    if (!drawerList) return;

    if (this.cartState.items.length === 0) {
      drawerList.innerHTML = `
        <div class="text-center py-5 text-muted">
          <i class="bi bi-bag-x fs-1 opacity-50"></i>
          <p class="mt-2 mb-0">Your shopping cart is empty.</p>
        </div>
      `;
      if (drawerSubtotal) drawerSubtotal.textContent = App.formatCurrency(0);
      if (fullCartList) {
        fullCartList.innerHTML = `
          <tr><td colspan="5" class="text-center py-5 text-muted">Your cart is empty. <a href="${App.getBaseUrl()}/pages/products.php">Start Shopping</a></td></tr>
        `;
      }
      return;
    }

    drawerList.innerHTML = this.cartState.items.map(item => `
      <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
        <img src="${item.main_image}" class="rounded-3" style="width: 60px; height: 60px; object-fit: cover;">
        <div class="flex-grow-1 min-w-0">
          <h6 class="mb-1 text-truncate font-heading text-dark small">${item.name}</h6>
          <div class="text-success fw-bold small">${App.formatCurrency(item.effective_price)}</div>
          <div class="d-flex align-items-center gap-2 mt-1">
            <div class="btn-group btn-group-sm">
              <button class="btn btn-outline-secondary btn-sm py-0" onclick="CartModule.updateQuantity(${item.cart_id}, ${item.quantity - 1})">-</button>
              <span class="btn btn-light btn-sm disabled py-0 px-2 text-dark">${item.quantity}</span>
              <button class="btn btn-outline-secondary btn-sm py-0" onclick="CartModule.updateQuantity(${item.cart_id}, ${item.quantity + 1})">+</button>
            </div>
            <button class="btn btn-link text-danger p-0 ms-auto" onclick="CartModule.removeItem(${item.cart_id})"><i class="bi bi-trash"></i></button>
          </div>
        </div>
      </div>
    `).join('');

    if (drawerSubtotal) drawerSubtotal.textContent = App.formatCurrency(this.cartState.summary.subtotal);

    if (fullCartList) {
      fullCartList.innerHTML = this.cartState.items.map(item => `
        <tr>
          <td>
            <div class="d-flex align-items-center gap-3">
              <img src="${item.main_image}" class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;">
              <div>
                <h6 class="font-heading mb-1 text-dark">${item.name}</h6>
                <small class="text-muted">${item.category_name}</small>
              </div>
            </div>
          </td>
          <td>${App.formatCurrency(item.effective_price)}</td>
          <td>
            <div class="input-group input-group-sm" style="width: 110px;">
              <button class="btn btn-outline-secondary" onclick="CartModule.updateQuantity(${item.cart_id}, ${item.quantity - 1})">-</button>
              <input type="text" class="form-control text-center bg-white" value="${item.quantity}" readonly>
              <button class="btn btn-outline-secondary" onclick="CartModule.updateQuantity(${item.cart_id}, ${item.quantity + 1})">+</button>
            </div>
          </td>
          <td class="fw-bold text-success">${App.formatCurrency(item.item_subtotal)}</td>
          <td>
            <button class="btn btn-outline-danger btn-sm" onclick="CartModule.removeItem(${item.cart_id})"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
      `).join('');
    }
  }
};

document.addEventListener('DOMContentLoaded', () => {
  CartModule.fetchCart();
});
