<?php
/**
 * Bagicha 2.0 - Admin Profile & Security Page
 * Manage administrator credentials, personal details, and security passwords.
 */
require_once __DIR__ . '/../api/helpers/auth.php';
$adminUser = requireAdminPage();

$adminTitle = "Admin Profile - Bagicha Admin";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-content">
  <!-- Breadcrumb Navigation -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/index.php" class="text-success text-decoration-none">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Admin Profile</li>
    </ol>
  </nav>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="font-heading h2 text-primary mb-1">Administrator Profile</h1>
      <p class="text-muted small mb-0">Manage your administrative credentials, personal contact, and security settings.</p>
    </div>
    <button type="button" class="btn btn-outline-danger btn-sm" onclick="AdminApp.logout()">
      <i class="bi bi-box-arrow-right me-1"></i> Sign Out
    </button>
  </div>

  <div class="row g-4">
    <!-- Left Column: Profile Card & Quick Info -->
    <div class="col-lg-4">
      <div class="admin-card p-4 text-center mb-4">
        <div class="mx-auto mb-3 bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2.2rem;">
          <i class="bi bi-shield-check"></i>
        </div>
        <h5 class="font-heading mb-1 text-dark" id="displayAdminName"><?= htmlspecialchars($adminUser['full_name'] ?? 'Administrator') ?></h5>
        <p class="text-muted small mb-2"><?= htmlspecialchars($adminUser['email'] ?? 'admin@bagicha.com') ?></p>
        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill small">Super Admin</span>
        
        <hr class="my-3 opacity-25">
        <div class="text-start small text-muted">
          <div class="d-flex justify-content-between mb-2">
            <span>Privilege Level:</span>
            <strong class="text-dark">Full System Access</strong>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Authentication:</span>
            <strong class="text-dark">JWT / Bcrypt</strong>
          </div>
          <div class="d-flex justify-content-between">
            <span>Status:</span>
            <strong class="text-success">Active & Verified</strong>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Profile Form & Password Security -->
    <div class="col-lg-8">
      <!-- Edit Personal Info Form -->
      <div class="admin-card p-4 mb-4">
        <h5 class="font-heading text-primary h6 mb-3"><i class="bi bi-person-gear me-2"></i> Update Personal Details</h5>
        <form id="adminProfileForm" onsubmit="saveAdminDetails(event)">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Full Name</label>
              <input type="text" id="adminNameInput" class="form-control" value="<?= htmlspecialchars($adminUser['full_name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Email Address</label>
              <input type="email" id="adminEmailInput" class="form-control bg-light" value="<?= htmlspecialchars($adminUser['email'] ?? '') ?>" readonly>
              <div class="form-text small">Email acts as primary login identity.</div>
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label small fw-semibold">Phone Number</label>
            <input type="tel" id="adminPhoneInput" class="form-control" value="<?= htmlspecialchars($adminUser['phone'] ?? '') ?>" placeholder="Contact phone">
          </div>
          <button type="submit" class="btn btn-admin-primary py-2 px-4" id="saveAdminBtn">
            <i class="bi bi-check2-circle me-1"></i> Save Profile Details
          </button>
        </form>
      </div>

      <!-- Change Password Card -->
      <div class="admin-card p-4">
        <h5 class="font-heading text-primary h6 mb-3"><i class="bi bi-shield-lock me-2"></i> Change Admin Password</h5>
        <form id="adminPasswordForm" onsubmit="updateAdminPassword(event)">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Current Admin Password</label>
            <input type="password" id="adminCurPwd" class="form-control" placeholder="••••••••" required>
          </div>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">New Password</label>
              <input type="password" id="adminNewPwd" class="form-control" placeholder="Minimum 6 characters" minlength="6" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Confirm New Password</label>
              <input type="password" id="adminConfPwd" class="form-control" placeholder="Re-enter new password" minlength="6" required>
            </div>
          </div>
          <button type="submit" class="btn btn-outline-danger py-2 px-4" id="updatePwdBtn">
            <i class="bi bi-key me-1"></i> Update Security Password
          </button>
        </form>
      </div>
    </div>
  </div>
</main>

<script>
async function saveAdminDetails(e) {
  e.preventDefault();
  const btn = document.getElementById('saveAdminBtn');
  btn.disabled = true;
  try {
    const res = await axios.put('/auth/profile.php', {
      full_name: document.getElementById('adminNameInput').value,
      phone: document.getElementById('adminPhoneInput').value
    });
    if (res.data.status === 'success') {
      AdminApp.showToast('Admin profile details saved successfully!', 'success');
      document.getElementById('displayAdminName').textContent = document.getElementById('adminNameInput').value;
      const user = App.getUser() || {};
      user.full_name = document.getElementById('adminNameInput').value;
      user.phone = document.getElementById('adminPhoneInput').value;
      App.setUser(user);
    }
  } catch (err) {
    AdminApp.showToast(err.response?.data?.message || 'Failed to update admin profile', 'error');
  } finally {
    btn.disabled = false;
  }
}

async function updateAdminPassword(e) {
  e.preventDefault();
  const current_password = document.getElementById('adminCurPwd').value;
  const new_password = document.getElementById('adminNewPwd').value;
  const confirm_password = document.getElementById('adminConfPwd').value;

  if (new_password !== confirm_password) {
    AdminApp.showToast('New passwords do not match.', 'error');
    return;
  }

  const btn = document.getElementById('updatePwdBtn');
  btn.disabled = true;

  try {
    const res = await axios.post('/admin/change-password.php', {
      current_password,
      new_password,
      confirm_password
    });
    if (res.data.status === 'success') {
      AdminApp.showToast('Admin password updated successfully!', 'success');
      document.getElementById('adminPasswordForm').reset();
    }
  } catch (err) {
    AdminApp.showToast(err.response?.data?.message || 'Failed to change password.', 'error');
  } finally {
    btn.disabled = false;
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
