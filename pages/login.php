<?php
/**
 * Bagicha 2.0 - Standalone Login & Register Page
 */
$pageTitle = "Login & Register - Bagicha";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-5" style="max-width: 540px;">
  <div class="card border-0 shadow-lg p-4 rounded-4 bg-white">
    <h2 class="font-heading text-primary text-center mb-4">Welcome to Bagicha</h2>

    <?php if (isset($_GET['logged_out'])): ?>
      <div class="alert alert-success d-flex align-items-center gap-2 py-2 small mb-4" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <span>You have been logged out securely. Please sign in to continue.</span>
      </div>
    <?php endif; ?>

    <ul class="nav nav-pills nav-justified mb-4" id="pageAuthTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active font-heading fw-bold" id="page-login-tab" data-bs-toggle="pill" data-bs-target="#pageLoginPane">Login</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link font-heading fw-bold" id="page-register-tab" data-bs-toggle="pill" data-bs-target="#pageRegisterPane">Register</button>
      </li>
    </ul>

    <div class="tab-content">
      <!-- Login -->
      <div class="tab-pane fade show active" id="pageLoginPane">
        <div id="pageLoginError" class="alert alert-danger d-none py-2 small"></div>
        <form id="loginForm">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Email Address</label>
            <input type="email" id="loginEmail" class="form-control" placeholder="your.email@bagicha.com" required style="border-radius: 10px;">
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label class="form-label small fw-semibold mb-0">Password</label>
              <a href="#" class="small text-success text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a>
            </div>
            <input type="password" id="loginPassword" class="form-control" placeholder="••••••••" required style="border-radius: 10px;">
          </div>
          <button type="submit" class="btn btn-bagicha-primary w-100 py-2 mt-2">Login to Account</button>
        </form>
      </div>

      <!-- Register -->
      <div class="tab-pane fade" id="pageRegisterPane">
        <div id="pageRegisterError" class="alert alert-danger d-none py-2 small"></div>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
