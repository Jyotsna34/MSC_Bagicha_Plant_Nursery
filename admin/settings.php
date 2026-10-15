<?php
/**
 * Bagicha 2.0 - Admin Website Settings Page
 * Manage store identity, customer communication, and orders & shipping parameters.
 */
require_once __DIR__ . '/../api/helpers/auth.php';
requireAdminPage();

$adminTitle = "Website Settings - Bagicha Admin";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-content">
  <!-- Breadcrumb Navigation -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/index.php" class="text-success text-decoration-none">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Settings</li>
    </ol>
  </nav>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="font-heading h2 text-primary mb-1">Website Settings</h1>
      <p class="text-muted small mb-0">Configure store branding, support contacts, and order fulfillment parameters.</p>
    </div>
  </div>

  <!-- Settings Category Navigation Tabs -->
  <ul class="nav nav-pills gap-2 mb-4" id="settingsNavTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active rounded-pill px-3 py-2 fw-semibold" id="tab-website-btn" data-bs-toggle="pill" data-bs-target="#tab-website" type="button" role="tab" aria-controls="tab-website" aria-selected="true">
        <i class="bi bi-gear me-1"></i> Website Settings
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="tab-shipping-btn" data-bs-toggle="pill" data-bs-target="#tab-shipping" type="button" role="tab" aria-controls="tab-shipping" aria-selected="false">
        <i class="bi bi-truck me-1"></i> Orders & Shipping Settings
      </button>
    </li>
    <li class="nav-item">
      <a href="<?= BASE_URL ?>/admin/products.php" class="nav-link rounded-pill px-3 py-2 text-decoration-none border border-secondary-subtle">
        <i class="bi bi-tree me-1"></i> Product Settings
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= BASE_URL ?>/admin/profile.php" class="nav-link rounded-pill px-3 py-2 text-decoration-none border border-secondary-subtle">
        <i class="bi bi-person-badge me-1"></i> Admin Profile & Security
      </a>
    </li>
  </ul>

  <!-- Main Settings Form Container -->
  <form id="settingsForm" onsubmit="saveSettings(event)">
    <div class="tab-content" id="settingsTabContent">
      
      <!-- Tab 1: Website Settings -->
      <div class="tab-pane fade show active" id="tab-website" role="tabpanel" aria-labelledby="tab-website-btn">
        <div class="row g-4">
          <div class="col-lg-8">
            <div class="admin-card p-4 mb-4">
              <h5 class="font-heading text-primary mb-3"><i class="bi bi-shop me-2"></i> Store Identity & Branding</h5>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Store Name</label>
                  <input type="text" id="set_store_name" name="store_name" class="form-control" placeholder="Bagicha" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Store Tagline</label>
                  <input type="text" id="set_store_tagline" name="store_tagline" class="form-control" placeholder="Bring Nature Home">
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">Announcement Banner Text</label>
                  <input type="text" id="set_announcement_banner" name="announcement_banner" class="form-control" placeholder="🌿 Free Eco-Friendly Delivery on all plant orders above ₹999!">
                  <div class="form-text small">Displayed prominently in the top header marquee across all storefront pages.</div>
                </div>
              </div>

              <h5 class="font-heading text-primary mb-3"><i class="bi bi-telephone me-2"></i> Customer Support Contacts</h5>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Customer Support Email</label>
                  <input type="email" id="set_contact_email" name="contact_email" class="form-control" placeholder="support@bagicha.com">
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Customer Helpline / WhatsApp</label>
                  <input type="text" id="set_contact_phone" name="contact_phone" class="form-control" placeholder="+91 98765 43210">
                </div>
              </div>

              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-admin-primary py-2 px-4 font-heading">
                  <i class="bi bi-check2-circle me-1"></i> Save Website Settings
                </button>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="admin-card p-4">
              <h6 class="font-heading text-primary mb-2"><i class="bi bi-info-circle me-2"></i> Security Notice</h6>
              <p class="small text-muted mb-3">Administrator password changes and personal credentials have been consolidated into your dedicated Admin Profile.</p>
              <a href="<?= BASE_URL ?>/admin/profile.php" class="btn btn-outline-secondary btn-sm w-100">
                <i class="bi bi-person-badge me-1"></i> Manage Admin Credentials
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab 2: Orders & Shipping Settings -->
      <div class="tab-pane fade" id="tab-shipping" role="tabpanel" aria-labelledby="tab-shipping-btn">
        <div class="row g-4">
          <div class="col-lg-8">
            <div class="admin-card p-4 mb-4">
              <h5 class="font-heading text-primary mb-3"><i class="bi bi-truck me-2"></i> Orders, Shipping & Delivery Thresholds</h5>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Free Express Shipping Threshold (₹)</label>
                  <input type="number" step="0.01" id="set_free_shipping_threshold" name="free_shipping_threshold" class="form-control" placeholder="999.00" required>
                  <div class="form-text small">Orders meeting or exceeding this amount receive zero shipping charges.</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Standard Flat Shipping Fee (₹)</label>
                  <input type="number" step="0.01" id="set_flat_shipping_rate" name="flat_shipping_rate" class="form-control" placeholder="99.00" required>
                  <div class="form-text small">Default transit fee applied to carts below the free shipping threshold.</div>
                </div>
              </div>

              <h5 class="font-heading text-primary mb-3"><i class="bi bi-box-seam me-2"></i> Fulfillment & Delivery Timeframe</h5>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Estimated Delivery Timeframe</label>
                  <input type="text" id="set_delivery_timeframe" name="delivery_timeframe" class="form-control" placeholder="3-5 Business Days">
                  <div class="form-text small">Shown to customers on the checkout and product specification pages.</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Packaging Protection Standard</label>
                  <input type="text" id="set_packaging_standard" name="packaging_standard" class="form-control" placeholder="Eco-Shield Ventilated Transit Pods">
                  <div class="form-text small">Displayed on customer invoices and live order tracking cards.</div>
                </div>
              </div>

              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-admin-primary py-2 px-4 font-heading">
                  <i class="bi bi-check2-circle me-1"></i> Save Orders & Shipping Settings
                </button>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="admin-card p-4">
              <h6 class="font-heading text-primary mb-2"><i class="bi bi-shield-check me-2"></i> 7-Day Guarantee Link</h6>
              <p class="small text-muted mb-3">All orders are backed by Bagicha's Arrive-Alive guarantee and replacement policy.</p>
              <a href="<?= BASE_URL ?>/pages/guarantee.php" target="_blank" class="btn btn-outline-secondary btn-sm w-100">
                <i class="bi bi-box-arrow-up-right me-1"></i> Preview Policy Terms
              </a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </form>
</main>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  if (!AdminApp.checkAdminAuth()) return;

  // Handle URL hash navigation for direct linking (e.g., #setShipping or #shipping)
  const hash = window.location.hash.toLowerCase();
  if (hash === '#setshipping' || hash === '#shipping' || hash === '#orders') {
    const shippingTabBtn = document.getElementById('tab-shipping-btn');
    if (shippingTabBtn) {
      bootstrap.Tab.getOrCreateInstance(shippingTabBtn).show();
    }
  }

  await loadSettings();
});

async function loadSettings() {
  try {
    const res = await axios.get('/admin/settings/index.php', { timeout: 8000 });
    if (res.data.status === 'success') {
      const s = res.data.data.settings;
      for (const key in s) {
        const input = document.getElementById('set_' + key);
        if (input) input.value = s[key];
      }
    }
  } catch (err) {
    AdminApp.showToast('Failed to load website settings', 'error');
  }
}

async function saveSettings(e) {
  e.preventDefault();
  if (!AdminApp.checkAdminAuth()) return;

  const form = document.getElementById('settingsForm');
  const formData = new FormData(form);
  const data = {};
  formData.forEach((val, key) => data[key] = val);

  try {
    const res = await axios.post('/admin/settings/index.php', data);
    if (res.data.status === 'success') {
      AdminApp.showToast(res.data.message || 'Settings saved successfully!', 'success');
    }
  } catch (err) {
    AdminApp.showToast(err.response?.data?.message || 'Failed to save settings', 'error');
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
