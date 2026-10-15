/**
 * Bagicha 2.0 - Admin JavaScript Module & Theme Controller
 */

const AdminApp = {
  initTheme() {
    if (typeof App !== 'undefined') {
      App.initAxios();
    }

    const savedTheme = localStorage.getItem('bagicha_admin_theme') || 'dark';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
    document.body.setAttribute('data-bs-theme', savedTheme);
    this.updateThemeToggleButton(savedTheme);
  },

  toggleTheme() {
    const current = document.documentElement.getAttribute('data-bs-theme') || 'dark';
    const next = current === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-bs-theme', next);
    document.body.setAttribute('data-bs-theme', next);
    localStorage.setItem('bagicha_admin_theme', next);
    this.updateThemeToggleButton(next);

    // Re-render charts with updated theme contrast if present
    if (typeof AdminCharts !== 'undefined') {
      if (document.getElementById('revenueChartCanvas')) AdminCharts.renderRevenueChart('revenueChartCanvas');
      if (document.getElementById('categoryChartCanvas')) AdminCharts.renderCategoryChart('categoryChartCanvas');
      if (document.getElementById('reportsRevenueChartCanvas')) AdminCharts.renderRevenueChart('reportsRevenueChartCanvas');
      if (document.getElementById('reportsCategoryChartCanvas')) AdminCharts.renderCategoryChart('reportsCategoryChartCanvas');
    }
  },

  updateThemeToggleButton(theme) {
    const btnIcon = document.getElementById('themeToggleIcon');
    const btnText = document.getElementById('themeToggleText');
    if (btnIcon && btnText) {
      if (theme === 'light') {
        btnIcon.className = 'bi bi-moon-stars-fill text-primary';
        btnText.textContent = 'Dark Mode';
      } else {
        btnIcon.className = 'bi bi-sun-fill text-warning';
        btnText.textContent = 'Light Mode';
      }
    }
  },

  checkAdminAuth() {
    if (typeof App !== 'undefined') {
      App.initAxios();
    }
    const token = typeof App !== 'undefined' ? App.getToken() : localStorage.getItem('bagicha_token');
    if (token && typeof axios !== 'undefined') {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }
    const user = typeof App !== 'undefined' ? App.getUser() : (localStorage.getItem('bagicha_user') ? JSON.parse(localStorage.getItem('bagicha_user')) : null);
    if (!user || user.role !== 'admin') {
      const baseUrl = (typeof App !== 'undefined') ? App.getBaseUrl() : window.location.origin;
      window.location.href = baseUrl + '/pages/login.php';
      return false;
    }
    return true;
  },

  async loadDashboardStats() {
    if (!this.checkAdminAuth()) return;

    try {
      const res = await axios.get('/admin/stats.php');
      if (res.data.status === 'success') {
        const d = res.data.data;
        const revEl = document.getElementById('statRevenue');
        if (revEl) revEl.textContent = App.formatCurrency(d.total_revenue);
        const ordEl = document.getElementById('statOrders');
        if (ordEl) ordEl.textContent = d.total_orders;
        const prodEl = document.getElementById('statProducts');
        if (prodEl) prodEl.textContent = d.total_products;
        const custEl = document.getElementById('statCustomers');
        if (custEl) custEl.textContent = d.total_customers;
        const lowEl = document.getElementById('statLowStock');
        if (lowEl) lowEl.textContent = d.low_stock_count;

        const table = document.getElementById('recentOrdersTable');
        if (table) {
          table.innerHTML = d.recent_orders.map(o => `
            <tr>
              <td class="fw-bold text-primary font-heading">${o.order_number}</td>
              <td>${o.customer_name}</td>
              <td class="fw-bold text-success">${App.formatCurrency(o.final_amount)}</td>
              <td><span class="badge ${o.payment_status === 'paid' ? 'bg-success' : 'bg-warning'}">${o.payment_status.toUpperCase()}</span></td>
              <td><span class="badge bg-secondary">${o.order_status.toUpperCase()}</span></td>
              <td>${new Date(o.created_at).toLocaleDateString()}</td>
            </tr>
          `).join('');
        }
      }
    } catch (err) {
      console.error('Failed to load admin stats', err);
    }
  },

  confirmModal(options = {}) {
    return new Promise((resolve) => {
      const modalEl = document.getElementById('adminConfirmModal');
      if (!modalEl) {
        resolve(true);
        return;
      }
      document.getElementById('adminConfirmModalTitle').textContent = options.title || 'Confirm Action';
      document.getElementById('adminConfirmModalBody').innerHTML = options.message || 'Are you sure you want to proceed?';
      const btnOk = document.getElementById('adminConfirmModalOkBtn');
      btnOk.textContent = options.confirmText || 'Confirm';
      btnOk.className = `btn ${options.confirmClass || 'btn-danger'} px-4 font-heading`;

      const bsModal = new bootstrap.Modal(modalEl);
      let resolved = false;

      btnOk.onclick = () => {
        if (!resolved) {
          resolved = true;
          bsModal.hide();
          resolve(true);
        }
      };

      modalEl.addEventListener('hidden.bs.modal', () => {
        if (!resolved) {
          resolved = true;
          resolve(false);
        }
      }, { once: true });

      bsModal.show();
    });
  },

  async deleteProduct(id) {
    const confirmed = await this.confirmModal({
      title: 'Delete Plant Product',
      message: 'Are you sure you want to permanently delete this plant product?',
      confirmText: 'Delete Product',
      confirmClass: 'btn-danger'
    });
    if (!confirmed) return;
    try {
      const res = await axios.delete(`/admin/products/index.php?id=${id}`);
      if (res.data.status === 'success') {
        App.showToast('Product deleted');
        window.location.reload();
      }
    } catch (err) {
      App.showToast('Delete failed', 'error');
    }
  },

  async updateOrderStatus(orderId, newStatus) {
    try {
      const res = await axios.post('/admin/orders/update-status.php', {
        order_id: orderId,
        order_status: newStatus
      });
      if (res.data.status === 'success') {
        App.showToast('Order status updated!');
      }
    } catch (err) {
      App.showToast('Status update failed', 'error');
    }
  },

  async updatePaymentStatus(orderId, newPaymentStatus) {
    try {
      const res = await axios.post('/admin/orders/update-status.php', {
        order_id: orderId,
        payment_status: newPaymentStatus
      });
      if (res.data.status === 'success') {
        App.showToast('Payment status updated!');
      }
    } catch (err) {
      App.showToast('Payment status update failed', 'error');
    }
  },

  async deleteCategory(id, count = 0) {
    const warningText = count > 0 ? `<br><strong class="text-danger">Warning:</strong> Deleting this category will also permanently delete all <strong>${count}</strong> products assigned to it!` : '';
    const confirmed = await this.confirmModal({
      title: 'Delete Category',
      message: `Are you sure you want to delete this category?${warningText}`,
      confirmText: 'Delete Category',
      confirmClass: 'btn-danger'
    });
    if (!confirmed) return;
    try {
      await axios.delete(`/admin/categories/index.php?id=${id}`);
      App.showToast('Category deleted');
      window.location.reload();
    } catch (err) {
      App.showToast('Failed to delete category', 'error');
    }
  },

  async deleteCoupon(id) {
    const confirmed = await this.confirmModal({
      title: 'Delete Coupon Code',
      message: 'Are you sure you want to delete this discount coupon code?',
      confirmText: 'Delete Coupon',
      confirmClass: 'btn-danger'
    });
    if (!confirmed) return;
    try {
      await axios.delete(`/admin/coupons/index.php?id=${id}`);
      App.showToast('Coupon deleted');
      window.location.reload();
    } catch (err) {
      App.showToast('Failed to delete coupon', 'error');
    }
  },

  // Notification properties & methods
  eventSource: null,

  playNotificationSound() {
    try {
      const ctx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.connect(gain);
      gain.connect(ctx.destination);
      
      osc.type = 'sine';
      osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
      gain.gain.setValueAtTime(0.1, ctx.currentTime);
      osc.start();
      
      osc.frequency.setValueAtTime(698.46, ctx.currentTime + 0.1); // F5
      osc.frequency.setValueAtTime(880.00, ctx.currentTime + 0.2); // A5
      
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);
      osc.stop(ctx.currentTime + 0.45);
    } catch (e) {
      // AudioContext blocked
    }
  },

  async loadNotifications() {
    if (typeof axios === 'undefined') return;
    try {
      const res = await axios.get('/notifications/index.php');
      if (res.data.status === 'success') {
        this.renderNotifications(res.data.data);
      }
    } catch (err) {
      console.error('Failed to load notifications', err);
    }
  },

  renderNotifications(notifications) {
    const listEl = document.getElementById('adminNotifList');
    const badgeEl = document.getElementById('adminNotifBadge');
    if (!listEl) return;

    if (!notifications || notifications.length === 0) {
      listEl.innerHTML = '<li class="p-3 text-center text-muted">No new notifications</li>';
      if (badgeEl) badgeEl.classList.add('d-none');
      return;
    }

    const unreadCount = notifications.filter(n => !n.is_read).length;
    if (badgeEl) {
      if (unreadCount > 0) {
        badgeEl.textContent = unreadCount;
        badgeEl.classList.remove('d-none');
      } else {
        badgeEl.classList.add('d-none');
      }
    }

    listEl.innerHTML = notifications.map(n => {
      const bgClass = n.is_read ? 'opacity-75' : 'fw-bold bg-dark-subtle border-start border-3 border-emerald';
      const timeStr = new Date(n.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      let icon = 'bi-bell-fill text-warning';
      if (n.type === 'order') icon = 'bi-box-seam text-primary';
      if (n.type === 'stock') icon = 'bi-exclamation-triangle text-danger';
      if (n.type === 'review') icon = 'bi-star text-info';

      return `
        <li class="dropdown-item p-2 border-bottom border-secondary d-flex align-items-start gap-2 ${bgClass}" 
            style="white-space: normal; cursor: pointer; transition: background 0.2s;" 
            onclick="AdminApp.markNotificationRead(${n.id}, event)">
          <i class="bi ${icon} mt-1"></i>
          <div class="flex-grow-1 text-light">
            <div class="d-flex justify-content-between">
              <span class="fw-bold" style="font-size: 0.85rem;">${n.title}</span>
              <span class="text-muted" style="font-size: 0.7rem;">${timeStr}</span>
            </div>
            <p class="mb-0 text-muted" style="font-size: 0.78rem; line-height: 1.3;">${n.message}</p>
          </div>
        </li>
      `;
    }).join('');
  },

  async markNotificationRead(id, event) {
    if (event) event.stopPropagation();
    try {
      await axios.post('/notifications/index.php', { notification_id: id });
      this.loadNotifications();
    } catch (err) {
      console.error('Failed to mark notification read', err);
    }
  },

  async markAllNotificationsRead() {
    try {
      await axios.post('/notifications/index.php', { mark_all: true });
      this.loadNotifications();
    } catch (err) {
      console.error('Failed to mark all notifications read', err);
    }
  },

  notifPollTimer: null,

  initNotificationStream() {
    if (this.eventSource) {
      this.eventSource.close();
      this.eventSource = null;
    }

    if (this.notifPollTimer) {
      clearInterval(this.notifPollTimer);
    }

    const token = typeof App !== 'undefined' ? App.getToken() : localStorage.getItem('bagicha_token');
    if (!token) return;

    // Periodically sync notifications every 10 seconds without blocking Apache threads
    this.notifPollTimer = setInterval(() => {
      this.loadNotifications();
    }, 10000);
  }
};

// Initialize theme & axios settings immediately when script executes
if (typeof axios !== 'undefined') {
  AdminApp.initTheme();
}

document.addEventListener('DOMContentLoaded', () => {
  AdminApp.initTheme();

  if (AdminApp.checkAdminAuth()) {
    AdminApp.loadNotifications();
    AdminApp.initNotificationStream();
  }

  // Register Mark all read click listener for admin side
  const markAllBtn = document.getElementById('adminMarkAllReadBtn');
  if (markAllBtn) {
    markAllBtn.addEventListener('click', (e) => {
      e.preventDefault();
      AdminApp.markAllNotificationsRead();
    });
  }
});
