<?php
/**
 * Bagicha 2.0 - Admin Footer Component
 */
?>
</div> <!-- End admin-wrapper -->

<!-- Admin Custom Confirmation Modal -->
<div class="modal fade" id="adminConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title font-heading text-primary" id="adminConfirmModalTitle">Confirm Action</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <div class="mb-3 text-warning fs-1">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div class="fs-6 text-secondary mb-4" id="adminConfirmModalBody">Are you sure you want to proceed?</div>
        <div class="d-flex gap-2 justify-content-center">
          <button type="button" class="btn btn-outline-secondary px-4 font-heading" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="adminConfirmModalOkBtn" class="btn btn-danger px-4 font-heading">Confirm</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<script src="<?= BASE_URL ?>/assets/js/password-toggle.js"></script>
<script src="<?= BASE_URL ?>/admin/assets/js/admin-charts.js"></script>
<script src="<?= BASE_URL ?>/admin/assets/js/admin.js"></script>
<script src="<?= BASE_URL ?>/admin/assets/js/admin-ui.js"></script>
</body>
</html>
