<?php
/**
 * Bagicha 2.0 - Customer Change Password Modal Component
 */
?>
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-0 pb-0">
        <div>
          <p class="account-eyebrow mb-1">Account Security</p>
          <h2 class="modal-title font-heading h4 mb-0 text-primary" id="changePasswordModalTitle">Change Password</h2>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form id="changePasswordForm" onsubmit="handleChangePassword(event)">
          <div class="mb-3">
            <label for="custCurrentPassword" class="form-label small fw-semibold">Current Password</label>
            <input type="password" id="custCurrentPassword" class="form-control" placeholder="••••••••" required autocomplete="current-password">
          </div>
          <div class="mb-3">
            <label for="custNewPassword" class="form-label small fw-semibold">New Password</label>
            <input type="password" id="custNewPassword" class="form-control" placeholder="At least 6 characters" minlength="6" required autocomplete="new-password">
          </div>
          <div class="mb-4">
            <label for="custConfirmPassword" class="form-label small fw-semibold">Confirm New Password</label>
            <input type="password" id="custConfirmPassword" class="form-control" placeholder="Repeat new password" minlength="6" required autocomplete="new-password">
          </div>
          <button id="savePasswordBtn" type="submit" class="btn btn-bagicha-primary w-100 py-2">
            <i class="bi bi-shield-check me-1"></i> Update Password
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
