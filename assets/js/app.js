/**
 * Bagicha 2.0 - Core JS & State Management
 */

const getAppPrefix = () => {
  const path = window.location.pathname;
  const match = path.match(/^(\/[^\/]*bagicha[^\/]*)/i);
  return match ? match[1] : '';
};

const API_BASE_URL = window.location.origin + getAppPrefix() + '/api';

const App = {
  getBaseUrl() {
    return window.location.origin + getAppPrefix();
  },

  getToken() {

    return localStorage.getItem('bagicha_token');
  },

  setToken(token) {
    localStorage.setItem('bagicha_token', token);
    document.cookie = `bagicha_token=${token}; path=/; max-age=604800; SameSite=Lax`;
  },

  removeToken() {
    localStorage.removeItem('bagicha_token');
    localStorage.removeItem('bagicha_user');
    document.cookie = 'bagicha_token=; path=/; max-age=0; SameSite=Lax';
    if (this.eventSource) {
      this.eventSource.close();
      this.eventSource = null;
    }
  },

  getUser() {
    const raw = localStorage.getItem('bagicha_user');
    return raw ? JSON.parse(raw) : null;
  },

  setUser(user) {
    localStorage.setItem('bagicha_user', JSON.stringify(user));
  },

  initAxios() {
    if (typeof axios === 'undefined') return;
    axios.defaults.baseURL = '';
    axios.interceptors.request.handlers = [];

    axios.interceptors.request.use((config) => {
      if (config.url && !config.url.startsWith('http://') && !config.url.startsWith('https://')) {
        const cleanPath = config.url.startsWith('/') ? config.url : '/' + config.url;
        config.url = API_BASE_URL + cleanPath;
      }
      return config;
    });

    const token = this.getToken();
    if (token) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }
  },

  showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;

    const bgClass = type === 'success' ? 'bg-success' : (type === 'error' ? 'bg-danger' : 'bg-warning');
    const toastHtml = `
      <div class="toast align-items-center text-white ${bgClass} border-0 mb-2 show" role="alert">
        <div class="d-flex">
          <div class="toast-body">${message}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
    `;
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    setTimeout(() => {
      const first = toastContainer.querySelector('.toast');
      if (first) first.remove();
    }, 4000);
  },

  updateAuthUI() {
    if (window.location.pathname.includes('/admin/')) return;
    const user = this.getUser();
    const guestNav = document.getElementById('guestNav');
    const userNav = document.getElementById('userNav');
    const userNameSpan = document.getElementById('userNameDisplay');
    const adminLinkItem = document.getElementById('adminNavDropdownItem');
    const notifWrap = document.getElementById('userNotificationWrap');

    if (user) {
      if (guestNav) guestNav.classList.add('d-none');
      if (userNav) userNav.classList.remove('d-none');
      if (userNameSpan) userNameSpan.textContent = user.full_name.split(' ')[0];

      if (user.role === 'admin' && adminLinkItem) {
        adminLinkItem.classList.remove('d-none');
      }

      if (notifWrap) notifWrap.classList.remove('d-none');
      this.loadNotifications();
      this.initNotificationStream();
    } else {
      if (guestNav) guestNav.classList.remove('d-none');
      if (userNav) userNav.classList.add('d-none');
      if (notifWrap) notifWrap.classList.add('d-none');
      if (this.eventSource) {
        this.eventSource.close();
        this.eventSource = null;
      }
    }

    if (window.WishlistModule) {
      window.WishlistModule.syncWishlistState();
    }
  },

  formatCurrency(amount) {
    return '₹' + parseFloat(amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 });
  },

  confirmModal(options = {}) {
    return new Promise((resolve) => {
      const modalEl = document.getElementById('globalConfirmModal');
      if (!modalEl) {
        resolve(true);
        return;
      }
      document.getElementById('confirmModalTitle').textContent = options.title || 'Confirm Action';
      document.getElementById('confirmModalBody').innerHTML = options.message || 'Are you sure you want to proceed?';
      const btnOk = document.getElementById('confirmModalOkBtn');
      btnOk.textContent = options.confirmText || 'Confirm';
      btnOk.className = `btn ${options.confirmClass || 'btn-bagicha-primary'} px-4 font-heading`;

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
      osc.frequency.setValueAtTime(523.25, ctx.currentTime); // C5
      gain.gain.setValueAtTime(0.1, ctx.currentTime);
      osc.start();
      
      osc.frequency.setValueAtTime(659.25, ctx.currentTime + 0.1); // E5
      osc.frequency.setValueAtTime(783.99, ctx.currentTime + 0.2); // G5
      
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
      osc.stop(ctx.currentTime + 0.4);
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
    const listEl = document.getElementById('userNotifList');
    const badgeEl = document.getElementById('userNotifBadge');
    if (!listEl) return;

    if (!notifications || notifications.length === 0) {
      listEl.innerHTML = '<li class="p-3 text-center text-muted">No notifications yet</li>';
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
      const bgClass = n.is_read ? '' : 'bg-success-subtle fw-semibold';
      const timeStr = new Date(n.created_at).toLocaleString();
      let icon = 'bi-bell-fill text-success';
      if (n.type === 'order') icon = 'bi-box-seam text-primary';
      if (n.type === 'stock') icon = 'bi-exclamation-triangle text-warning';
      if (n.type === 'review') icon = 'bi-star text-info';

      return `
        <li class="dropdown-item p-3 border-bottom d-flex align-items-start gap-2 ${bgClass}" 
            style="white-space: normal; cursor: pointer; transition: background 0.2s;" 
            onclick="App.markNotificationRead(${n.id}, event)">
          <i class="bi ${icon} fs-5 mt-1"></i>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <span class="fw-bold text-dark" style="font-size: 0.9rem;">${n.title}</span>
              <span class="text-muted" style="font-size: 0.75rem;">${timeStr}</span>
            </div>
            <p class="mb-0 text-muted" style="font-size: 0.85rem; line-height: 1.3;">${n.message}</p>
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

  initNotificationStream() {
    if (this.eventSource) {
      this.eventSource.close();
    }

    const token = this.getToken();
    if (!token) return;

    const streamUrl = `${this.getBaseUrl()}/api/notifications/stream.php?token=${encodeURIComponent(token)}`;
    this.eventSource = new EventSource(streamUrl);

    this.eventSource.onmessage = (event) => {
      try {
        const notif = JSON.parse(event.data);
        this.playNotificationSound();
        this.showToast(`🔔 ${notif.title}: ${notif.message}`, 'success');
        this.loadNotifications();
      } catch (err) {
        console.error('Failed to parse SSE notification', err);
      }
    };

    this.eventSource.onerror = (err) => {
      console.warn('Notification stream connection error, reconnecting...', err);
    };
  }
};

// Initialize Axios defaults immediately when script loads so inline event handlers have valid headers
App.initAxios();

document.addEventListener('DOMContentLoaded', () => {
  App.initAxios();
  App.updateAuthUI();

  // Mark all notifications read listener
  const markAllBtn = document.getElementById('userMarkAllReadBtn');
  if (markAllBtn) {
    markAllBtn.addEventListener('click', (e) => {
      e.preventDefault();
      App.markAllNotificationsRead();
    });
  }

  // Floating Back to Top Button
  const backToTopBtn = document.getElementById('backToTopBtn');
  if (backToTopBtn) {
    const handleScroll = () => {
      if (window.scrollY > 300) {
        backToTopBtn.classList.add('visible');
      } else {
        backToTopBtn.classList.remove('visible');
      }
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
    backToTopBtn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // Smooth scroll for internal hash links
  document.querySelectorAll('a[href*="#"]').forEach(link => {
    link.addEventListener('click', (e) => {
      const href = link.getAttribute('href');
      if (!href) return;
      const hashIndex = href.indexOf('#');
      if (hashIndex !== -1) {
        const hash = href.substring(hashIndex);
        const path = href.substring(0, hashIndex);
        if (path === '' || window.location.pathname.endsWith('/index.php') || window.location.pathname === '/' || window.location.pathname.endsWith('/bagicha/')) {
          const target = document.querySelector(hash);
          if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
            history.pushState(null, '', hash);
          }
        }
      }
    });
  });

  // Dynamic active navbar state for Home & About section
  const aboutSec = document.getElementById('about');
  const navAboutLink = document.getElementById('navAboutLink');
  const navHomeLink = document.querySelector('.navbar-nav a[href$="/index.php"], .navbar-nav a[href$="bagicha/"]');
  if (aboutSec && navAboutLink) {
    window.addEventListener('scroll', () => {
      const rect = aboutSec.getBoundingClientRect();
      if (rect.top <= 140 && rect.bottom >= 140) {
        navAboutLink.classList.add('active');
        if (navHomeLink) navHomeLink.classList.remove('active');
      } else if (rect.top > 140 && window.scrollY < 400) {
        navAboutLink.classList.remove('active');
        if (navHomeLink) navHomeLink.classList.add('active');
      }
    }, { passive: true });
  }

  // Live Search Autocomplete with Thumbnails & Shortcuts
  const searchInput = document.getElementById('navbarSearchInput');
  const searchDropdown = document.getElementById('searchAutocompleteDropdown');
  let searchDebounceTimer = null;

  if (searchInput && searchDropdown) {
    searchInput.addEventListener('input', (e) => {
      clearTimeout(searchDebounceTimer);
      const query = e.target.value.trim();
      if (query.length < 2) {
        searchDropdown.style.display = 'none';
        searchDropdown.innerHTML = '';
        return;
      }

      searchDebounceTimer = setTimeout(async () => {
        try {
          const res = await axios.get(`${App.getBaseUrl()}/api/products/search.php?q=${encodeURIComponent(query)}`);
          const results = res.data.data.results || [];
          if (results.length === 0) {
            searchDropdown.innerHTML = '<div class="p-3 text-center text-muted small">No botanical matches found</div>';
            searchDropdown.style.display = 'block';
            return;
          }

          searchDropdown.innerHTML = results.map(item => `
            <a href="${App.getBaseUrl()}/pages/product-detail.php?slug=${item.slug}" class="search-autocomplete-item">
              <img src="${item.main_image}" alt="${item.name}" class="search-autocomplete-thumb">
              <div class="flex-grow-1 overflow-hidden">
                <span class="badge bg-success-subtle text-success small mb-0" style="font-size: 0.72rem;">${item.category_name}</span>
                <div class="small fw-semibold text-truncate text-dark">${item.name}</div>
                <div class="small fw-bold text-success">${App.formatCurrency(item.sale_price || item.price)}</div>
              </div>
            </a>
          `).join('') + `
            <a href="${App.getBaseUrl()}/pages/products.php?search=${encodeURIComponent(query)}" class="p-2.5 text-center d-block bg-light text-success fw-bold small text-decoration-none">
              View all results for "${query}" <i class="bi bi-arrow-right"></i>
            </a>
          `;
          searchDropdown.style.display = 'block';
        } catch (err) {
          searchDropdown.style.display = 'none';
        }
      }, 220);
    });

    document.addEventListener('click', (e) => {
      if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
        searchDropdown.style.display = 'none';
      }
    });

    // ⌘K or Ctrl+K shortcut to focus search
    document.addEventListener('keydown', (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        searchInput.focus();
      }
    });
  }
});
