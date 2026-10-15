<?php
/**
 * Bagicha 2.0 - 7-Day Arrive-Alive Guarantee & Shipping Policy
 * Details packaging engineering, claim procedure, and replacement promise.
 */
$pageTitle = "7-Day Plant Guarantee & Shipping Policy - Bagicha";
$pageDescription = "Learn about Bagicha's 100% Arrive-Alive guarantee, custom ventilated plant shipping pods, and our zero-hassle 7-day free replacement policy.";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<main id="main-content" class="container py-4" tabindex="-1">
  <!-- Breadcrumb Navigation -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-success text-decoration-none">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">7-Day Guarantee</li>
    </ol>
  </nav>

  <!-- Page Header -->
  <div class="text-center mb-5" style="max-width: 680px; margin: 0 auto;">
    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background: rgba(6, 78, 43, 0.08); color: var(--color-primary); font-weight: 600; font-size: 0.86rem;">
      <i class="bi bi-shield-check text-success"></i> 100% Arrive-Alive Protection
    </div>
    <h1 class="font-heading display-6 fw-bold text-primary mb-2">Our 7-Day Plant Guarantee</h1>
    <p class="text-secondary small">We know shipping live plants requires meticulous care. Every specimen is backed by our unconditional 7-Day Free Replacement Guarantee.</p>
  </div>

  <!-- The 3-Step Simple Claim Process -->
  <section class="mb-5">
    <div class="text-center mb-4">
      <span class="text-uppercase tracking-wider text-success small fw-bold">Zero Hassle Process</span>
      <h3 class="font-heading h2 text-primary mb-2">How Our Guarantee Works</h3>
      <p class="text-muted mx-auto small" style="max-width: 500px;">If your plant arrives stressed, damaged, or fails within 7 days, we make it right immediately.</p>
    </div>

    <div class="row g-3">
      <div class="col-md-4">
        <div class="card h-100 border-0 p-4 rounded-4 shadow-sm bg-white border border-light-subtle text-center">
          <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 54px; height: 54px;">
            <i class="bi bi-camera-fill fs-4"></i>
          </div>
          <h5 class="font-heading h6 text-dark mb-2">1. Snap a Photo</h5>
          <p class="small text-muted mb-0">Take a clear picture of the plant, stem, or packaging within 7 days of delivery.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card h-100 border-0 p-4 rounded-4 shadow-sm bg-white border border-light-subtle text-center">
          <div class="rounded-circle bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 54px; height: 54px;">
            <i class="bi bi-chat-dots-fill fs-4"></i>
          </div>
          <h5 class="font-heading h6 text-dark mb-2">2. Send to Our Team</h5>
          <p class="small text-muted mb-0">Send the photo via WhatsApp (+91 98765 43210) or submit a claim via our Contact desk with your Order ID.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card h-100 border-0 p-4 rounded-4 shadow-sm bg-white border border-light-subtle text-center">
          <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 54px; height: 54px;">
            <i class="bi bi-arrow-repeat fs-4"></i>
          </div>
          <h5 class="font-heading h6 text-dark mb-2">3. Free Replacement Dispatched</h5>
          <p class="small text-muted mb-0">We dispatch a fresh healthy plant immediately, or issue a 100% store credit/refund. No return shipping required!</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Eco-Shield Packaging Engineering -->
  <section class="mb-5">
    <div class="card border-0 p-4 p-md-5 rounded-4 shadow-sm text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #033B20 0%, #064E2B 60%, #0F683A 100%);">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <span class="badge bg-warning text-dark px-3 py-1.5 fw-bold text-uppercase mb-3">Packaging Engineering</span>
          <h3 class="font-heading text-white display-6 mb-3">How Do Live Plants Survive Shipping?</h3>
          <p class="text-white-50 small mb-4" style="line-height: 1.7;">
            We spent two years engineering our custom <strong>Eco-Shield Transit Pods</strong>. Plants are potted in lightweight sterilized coco peat with organic hydration gel, then secured with root collar locks that prevent soil shifting even if the box is inverted during transit.
          </p>
          <div class="row g-3">
            <div class="col-sm-6">
              <div class="d-flex align-items-center gap-2 text-white small">
                <i class="bi bi-wind text-warning fs-5"></i>
                <span>Ventilated Air-Circulation Ports</span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="d-flex align-items-center gap-2 text-white small">
                <i class="bi bi-shield-lock text-warning fs-5"></i>
                <span>Stem-Lock Anti-Crush Columns</span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="d-flex align-items-center gap-2 text-white small">
                <i class="bi bi-droplet-fill text-warning fs-5"></i>
                <span>Moisture-Retentive Organic Gel</span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="d-flex align-items-center gap-2 text-white small">
                <i class="bi bi-recycle text-warning fs-5"></i>
                <span>100% Recyclable Corrugated Pod</span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5 text-center d-none d-lg-block">
          <img src="https://images.unsplash.com/photo-1592150621744-aca64f48394a?auto=format&fit=crop&w=600&q=80" 
               alt="Eco-Shield Plant Box" 
               class="img-fluid rounded-4 shadow-lg border border-2 border-white border-opacity-25" 
               style="max-height: 320px; width: 100%; object-fit: cover; border-radius: 20px !important;">
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Banner -->
  <section class="text-center py-4">
    <h4 class="font-heading text-primary mb-2">Have an Existing Order to Claim?</h4>
    <p class="small text-muted mb-3">Our support team is ready to assist you right now.</p>
    <div class="d-flex justify-content-center gap-3">
      <a href="<?= BASE_URL ?>/pages/contact.php" class="btn btn-bagicha-primary px-4 fw-semibold">
        Submit Replacement Claim <i class="bi bi-arrow-right ms-1"></i>
      </a>
      <a href="<?= BASE_URL ?>/pages/track-order.php" class="btn btn-outline-secondary px-4 fw-semibold">
        Track Your Package
      </a>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
