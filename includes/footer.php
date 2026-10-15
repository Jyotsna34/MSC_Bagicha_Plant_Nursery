<?php

/**
 * Bagicha 2.0 - Footer Component
 */
?>
<footer class="bagicha-footer">
  <div class="container">
    <div class="row g-4 mb-5">
      <div class="col-lg-4 col-md-6">
        <a class="brand-logo mb-3 d-inline-block text-decoration-none" href="<?= BASE_URL ?>/index.php">
          <img src="<?= BASE_URL ?>/img/logo-3.png" alt="Bagicha Logo" class="brand-logo-img footer-logo-img">
        </a>
        <p class="text-secondary-light me-lg-4" style="color: #A3C2AE; line-height: 1.6;">
          Bring Nature Home. We carefully nurture and deliver healthy, high-quality indoor & outdoor plants, stylish planters, and organic gardening supplies right to your doorstep with our Arrive-Alive guarantee.
        </p>
        <div class="d-flex gap-3 fs-5 mt-4">
          <a href="#" class="footer-social-icon" title="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" class="footer-social-icon" title="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" class="footer-social-icon" title="Pinterest"><i class="bi bi-pinterest"></i></a>
          <a href="#" class="footer-social-icon" title="YouTube"><i class="bi bi-youtube"></i></a>
        </div>
      </div>

      <div class="col-lg-2 col-md-6">
        <h5 class="font-heading text-white mb-3">Shop & Explore</h5>
        <ul class="list-unstyled footer-links">
          <li class="mb-2.5"><a href="<?= BASE_URL ?>/index.php"><i class="bi bi-chevron-right me-1 small"></i> Home</a></li>
          <li class="mb-2.5"><a href="<?= BASE_URL ?>/pages/products.php"><i class="bi bi-chevron-right me-1 small"></i> Shop All Plants</a></li>
          <li class="mb-2.5"><a href="<?= BASE_URL ?>/index.php#about"><i class="bi bi-chevron-right me-1 small"></i> About Us</a></li>
        </ul>
      </div>

      <div class="col-lg-3 col-md-6">
        <h5 class="font-heading text-white mb-3">Help & Policies</h5>
        <ul class="list-unstyled footer-links">
          <li class="mb-2.5"><a href="<?= BASE_URL ?>/pages/contact.php"><i class="bi bi-chat-heart me-1 small"></i> Contact Us</a></li>
          <li class="mb-2.5"><a href="<?= BASE_URL ?>/pages/guarantee.php"><i class="bi bi-shield-check me-1 text-warning small"></i> Refund / Return Policy</a></li>
          <li class="mb-2.5"><a href="#" data-bs-toggle="modal" data-bs-target="#privacyPolicyModal"><i class="bi bi-file-earmark-lock me-1 small"></i> Privacy Policy</a></li>
          <li class="mb-2.5"><a href="#" data-bs-toggle="modal" data-bs-target="#termsConditionsModal"><i class="bi bi-file-earmark-text me-1 small"></i> Terms & Conditions</a></li>
          <li class="mb-2.5"><a href="<?= BASE_URL ?>/pages/track-order.php"><i class="bi bi-truck me-1 text-success small"></i> Track Order</a></li>
        </ul>
      </div>

      <div class="col-lg-3 col-md-6">
        <h5 class="font-heading text-white mb-3">Join Green Club</h5>
        <p class="small text-secondary-light mb-3" style="color: #A3C2AE;">Subscribe for monthly plant care tips & 10% off your first plant order.</p>
        <form class="d-flex flex-column gap-2" onsubmit="event.preventDefault(); App.showToast('🎉 Subscribed to Bagicha Green Club! Check your inbox for 10% off code.'); this.reset();">
          <input type="email" class="form-control" placeholder="Enter your email address" required style="border-radius: 12px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); color: #fff;">
          <button type="submit" class="btn btn-bagicha-accent py-2 fw-semibold">
            Subscribe & Get 10% OFF <i class="bi bi-send-fill ms-1"></i>
          </button>
        </form>
      </div>
    </div>

    <hr style="border-color: rgba(255,255,255,0.15);">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-3 small" style="color: #8CA496;">
      <p class="mb-0">&copy; <?= date('Y') ?> Bagicha Nursery. All Rights Reserved. Crafted with passion for urban greenery.</p>
      <div class="d-flex align-items-center gap-3 mt-3 mt-md-0 text-white-50">
        <span class="small">Secured Payments by:</span>
        <i class="bi bi-credit-card fs-5" title="Cards"></i>
        <i class="bi bi-qr-code-scan fs-5" title="UPI"></i>
        <i class="bi bi-shield-check fs-5" title="Razorpay Secure"></i>
      </div>
    </div>
  </div>
</footer>

<!-- Authentication Modal (Login / Register Tabs) -->
<div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-body p-4">
        <button type="button" class="btn-close float-end" data-bs-dismiss="modal"></button>

        <ul class="nav nav-pills nav-justified mb-4" id="authTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active font-heading fw-bold" id="login-tab" data-bs-toggle="pill" data-bs-target="#loginPane">Login</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link font-heading fw-bold" id="register-tab" data-bs-toggle="pill" data-bs-target="#registerPane">Register</button>
          </li>
        </ul>

        <div class="tab-content">
          <!-- Login Form -->
          <div class="tab-pane fade show active" id="loginPane">
            <div id="loginError" class="alert alert-danger d-none py-2 small"></div>
            <form id="loginForm">
              <div class="mb-3">
                <label class="form-label small fw-semibold">Email Address</label>
                <input type="email" id="loginEmail" class="form-control" placeholder="your.email@bagicha.com" required style="border-radius: 10px;">
              </div>
              <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <label class="form-label small fw-semibold">Password</label>
                  <a href="#" class="small text-success text-decoration-none" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a>
                </div>
                <input type="password" id="loginPassword" class="form-control" placeholder="••••••••" required style="border-radius: 10px;">
              </div>
              <button type="submit" class="btn btn-bagicha-primary w-100 py-2 mt-2">Login to Account</button>
            </form>
          </div>

          <!-- Register Form -->
          <div class="tab-pane fade" id="registerPane">
            <div id="registerError" class="alert alert-danger d-none py-2 small"></div>
            <form id="registerForm">
              <div class="mb-3">
                <label class="form-label small fw-semibold">Full Name</label>
                <input type="text" id="regName" class="form-control" placeholder="Full Name" required style="border-radius: 10px;">
              </div>
              <div class="mb-3">
                <label class="form-label small fw-semibold">Email Address</label>
                <input type="email" id="regEmail" class="form-control" placeholder="your.email@bagicha.com" required style="border-radius: 10px;">
              </div>
              <div class="mb-3">
                <label class="form-label small fw-semibold">Phone Number</label>
                <input type="tel" id="regPhone" class="form-control" placeholder="10-digit Phone Number" style="border-radius: 10px;">
              </div>
              <div class="mb-3">
                <label class="form-label small fw-semibold">Password</label>
                <input type="password" id="regPassword" class="form-control" placeholder="At least 6 characters" minlength="6" required style="border-radius: 10px;">
              </div>
              <button type="submit" class="btn btn-bagicha-accent w-100 py-2 mt-2">Create Account</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Global Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title font-heading text-primary">Reset Your Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div id="forgotMsg" class="alert d-none py-2 small"></div>
        <form id="forgotForm" onsubmit="handleForgotPassword(event)">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Registered Email Address</label>
            <input type="email" id="forgotEmail" class="form-control" placeholder="your.email@bagicha.com" required style="border-radius: 10px;">
          </div>
          <button type="submit" class="btn btn-bagicha-primary w-100 py-2">Request Reset Token</button>
        </form>

        <form id="resetForm" class="d-none mt-3 border-top pt-3" onsubmit="handleResetPassword(event)">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Reset Token</label>
            <input type="text" id="resetToken" class="form-control" placeholder="Enter token received" required style="border-radius: 10px;">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">New Password</label>
            <input type="password" id="resetNewPassword" class="form-control" placeholder="At least 6 characters" minlength="6" required style="border-radius: 10px;">
          </div>
          <button type="submit" class="btn btn-bagicha-accent w-100 py-2">Set New Password</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
async function handleForgotPassword(e) {
  e.preventDefault();
  const email = document.getElementById('forgotEmail').value.trim();
  const msg = document.getElementById('forgotMsg');
  try {
    const res = await axios.post('/auth/forgot-password.php', { email });
    if (res.data.status === 'success') {
      msg.className = 'alert alert-success py-2 small';
      msg.textContent = res.data.message;
      msg.classList.remove('d-none');
      if (res.data.data?.reset_token) {
        document.getElementById('resetToken').value = res.data.data.reset_token;
      }
      document.getElementById('resetForm').classList.remove('d-none');
    }
  } catch (err) {
    msg.className = 'alert alert-danger py-2 small';
    msg.textContent = err.response?.data?.message || 'Failed to request reset';
    msg.classList.remove('d-none');
  }
}

async function handleResetPassword(e) {
  e.preventDefault();
  const email = document.getElementById('forgotEmail').value.trim();
  const token = document.getElementById('resetToken').value.trim();
  const password = document.getElementById('resetNewPassword').value.trim();
  const msg = document.getElementById('forgotMsg');
  try {
    const res = await axios.post('/auth/reset-password.php', { email, token, password });
    if (res.data.status === 'success') {
      msg.className = 'alert alert-success py-2 small';
      msg.textContent = res.data.message;
      msg.classList.remove('d-none');
      setTimeout(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
        if (modal) modal.hide();
      }, 1500);
    }
  } catch (err) {
    msg.className = 'alert alert-danger py-2 small';
    msg.textContent = err.response?.data?.message || 'Failed to reset password';
    msg.classList.remove('d-none');
  }
}
</script>

<!-- Global Custom Confirmation Modal -->
<div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title font-heading text-primary" id="confirmModalTitle">Confirm Action</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <div class="mb-3 text-primary fs-1">
          <i class="bi bi-question-circle"></i>
        </div>
        <div class="fs-6 text-secondary mb-4" id="confirmModalBody">Are you sure you want to proceed?</div>
        <div class="d-flex gap-2 justify-content-center">
          <button type="button" class="btn btn-outline-secondary px-4 font-heading" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="confirmModalOkBtn" class="btn btn-bagicha-primary px-4 font-heading">Confirm</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Floating Back to Top Button -->
<button type="button" id="backToTopBtn" class="back-to-top-btn" aria-label="Back to top" title="Back to top">
  <i class="bi bi-arrow-up"></i>
</button>



<?php require_once __DIR__ . '/policy-modals.php'; ?>

<!-- JS Dependencies & Razorpay -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/auth.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/password-toggle.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/products.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/cart.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/wishlist.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/checkout.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/reviews.js?v=<?= time() ?>"></script>
</body>

</html>