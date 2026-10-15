/**
 * Bagicha 2.0 - Admin UI Enhancements
 * Collapsible/Expandable Sidebar & Universal Password Visibility Toggle
 */

const AdminUI = {
  tooltipInstances: [],

  init() {
    this.initSidebar();
    this.initPasswordToggles();
    this.initModalHooks();
    this.initKeyboardShortcuts();
  },

  // =========================================================================
  // 1. COLLAPSIBLE SIDEBAR CONTROLLER
  // =========================================================================
  initSidebar() {
    const collapseBtn = document.getElementById('sidebarCollapseBtn');
    const mobileOpenBtn = document.getElementById('sidebarMobileOpenBtn');
    const backdrop = document.getElementById('adminSidebarBackdrop');
    const sidebar = document.getElementById('adminSidebar');

    // Restore desktop collapsed state
    const isCollapsed = localStorage.getItem('bagicha_admin_sidebar_collapsed') === '1';
    if (window.innerWidth >= 992 && isCollapsed) {
      document.body.classList.add('sidebar-collapsed');
      this.updateCollapseIcon(true);
      this.setupTooltips(true);
    }

    if (collapseBtn) {
      collapseBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.toggleSidebar();
      });
    }

    if (mobileOpenBtn && sidebar && backdrop) {
      mobileOpenBtn.addEventListener('click', (e) => {
        e.preventDefault();
        sidebar.classList.add('mobile-open');
        backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
      });

      const closeMobile = () => {
        sidebar.classList.remove('mobile-open');
        backdrop.classList.remove('show');
        document.body.style.overflow = '';
      };

      backdrop.addEventListener('click', closeMobile);

      // Close mobile drawer when clicking any nav link
      sidebar.querySelectorAll('.nav-link-admin').forEach(link => {
        link.addEventListener('click', () => {
          if (window.innerWidth < 992) closeMobile();
        });
      });
    }

    window.addEventListener('resize', () => {
      if (window.innerWidth >= 992) {
        if (backdrop) backdrop.classList.remove('show');
        if (sidebar) sidebar.classList.remove('mobile-open');
        document.body.style.overflow = '';
        const collapsed = document.body.classList.contains('sidebar-collapsed');
        this.setupTooltips(collapsed);
      } else {
        this.destroyTooltips();
      }
    });
  },

  toggleSidebar() {
    const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
    localStorage.setItem('bagicha_admin_sidebar_collapsed', isCollapsed ? '1' : '0');
    this.updateCollapseIcon(isCollapsed);
    this.setupTooltips(isCollapsed);
  },

  updateCollapseIcon(isCollapsed) {
    const icon = document.getElementById('sidebarToggleIcon');
    const btn = document.getElementById('sidebarCollapseBtn');
    if (icon) {
      icon.className = isCollapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left';
    }
    if (btn) {
      btn.title = isCollapsed ? 'Expand sidebar (Ctrl+B)' : 'Collapse sidebar (Ctrl+B)';
      btn.setAttribute('aria-label', btn.title);
    }
  },

  setupTooltips(enable) {
    this.destroyTooltips();
    if (!enable || typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;

    const links = document.querySelectorAll('.admin-sidebar .nav-link-admin');
    links.forEach(el => {
      const title = el.getAttribute('data-sidebar-title') || el.querySelector('.nav-link-text')?.textContent?.trim();
      if (title) {
        el.setAttribute('data-sidebar-title', title);
        el.setAttribute('title', title);
        const tt = new bootstrap.Tooltip(el, {
          placement: 'right',
          trigger: 'hover',
          boundary: 'window'
        });
        this.tooltipInstances.push(tt);
      }
    });
  },

  destroyTooltips() {
    this.tooltipInstances.forEach(tt => tt.dispose());
    this.tooltipInstances = [];
  },

  initKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
        const activeTag = document.activeElement?.tagName?.toLowerCase();
        if (activeTag === 'input' || activeTag === 'textarea' || activeTag === 'select') return;
        e.preventDefault();
        this.toggleSidebar();
      }
      if (e.key === 'Escape') {
        const sidebar = document.getElementById('adminSidebar');
        const backdrop = document.getElementById('adminSidebarBackdrop');
        if (sidebar && sidebar.classList.contains('mobile-open')) {
          sidebar.classList.remove('mobile-open');
          if (backdrop) backdrop.classList.remove('show');
          document.body.style.overflow = '';
        }
      }
    });
  },

  // =========================================================================
  // 2. UNIVERSAL SHOW/HIDE PASSWORD CONTROLLER
  // =========================================================================
  initPasswordToggles(container = document) {
    const inputs = container.querySelectorAll('input[type="password"]');
    inputs.forEach(input => {
      if (input.dataset.pwdEnhanced) return;
      input.dataset.pwdEnhanced = 'true';

      const toggleBtn = document.createElement('button');
      toggleBtn.type = 'button';
      toggleBtn.className = 'btn btn-password-toggle';
      toggleBtn.setAttribute('tabindex', '-1');
      toggleBtn.setAttribute('aria-label', 'Show password');
      toggleBtn.setAttribute('title', 'Show password');
      toggleBtn.innerHTML = '<i class="bi bi-eye"></i>';

      toggleBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        toggleBtn.innerHTML = isHidden ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        const label = isHidden ? 'Hide password' : 'Show password';
        toggleBtn.setAttribute('aria-label', label);
        toggleBtn.setAttribute('title', label);
        input.focus();
      });

      const parentGroup = input.closest('.input-group');
      if (parentGroup) {
        parentGroup.appendChild(toggleBtn);
      } else {
        const wrapper = document.createElement('div');
        wrapper.className = 'input-group password-toggle-group';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        wrapper.appendChild(toggleBtn);
      }
    });
  },

  initModalHooks() {
    document.addEventListener('shown.bs.modal', (e) => {
      this.initPasswordToggles(e.target);
    });
  },

  async logout() {
    try {
      if (typeof axios !== 'undefined') {
        await axios.post('/auth/logout.php');
      }
    } catch (e) {
      // ignore network errors
    }
    if (typeof App !== 'undefined') {
      App.removeToken();
    }
    localStorage.removeItem('bagicha_token');
    localStorage.removeItem('bagicha_user');
    document.cookie = 'bagicha_token=; path=/; max-age=0; SameSite=Lax';
    if (typeof axios !== 'undefined') {
      delete axios.defaults.headers.common['Authorization'];
    }
    const baseUrl = (typeof App !== 'undefined') ? App.getBaseUrl() : '';
    window.location.href = (baseUrl ? baseUrl : '') + '/pages/login.php?logged_out=1';
  }
};

if (typeof AdminApp !== 'undefined') {
  AdminApp.logout = () => AdminUI.logout();
}

document.addEventListener('DOMContentLoaded', () => {
  AdminUI.init();
  if (typeof AdminApp !== 'undefined') {
    AdminApp.logout = () => AdminUI.logout();
  }
});
