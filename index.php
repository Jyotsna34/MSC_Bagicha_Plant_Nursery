<?php

/**
 * Bagicha 2.0 - Homepage
 */
$pageTitle = "Bagicha - Bring Nature Home | Online Plant Nursery";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content" class="container" tabindex="-1">
  <!-- Biophilic Hero Banner -->
  <section class="hero-banner d-flex align-items-center position-relative overflow-hidden mb-4">
    <div class="row align-items-center w-100 g-5">
      <div class="col-lg-7">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3 shadow-xs" style="background: rgba(6, 78, 43, 0.08); color: var(--color-primary); font-weight: 600; font-size: 0.86rem;">
          <i class="bi bi-patch-check-fill text-success"></i> India's Most Loved Urban Plant Nursery
        </div>
        <h1 class="hero-title font-heading display-4 fw-bold text-primary mb-3">
          Transform Your Living Space Into A Botanical Sanctuary
        </h1>
        <p class="lead text-secondary mb-4" style="max-width: 560px; line-height: 1.6;">
          Hand-reared, air-purifying indoor plants, exotic succulents, and organic plant care supplies delivered straight from our nurseries to your home with guaranteed health on arrival.
        </p>
        <div class="d-flex flex-wrap gap-3 align-items-center">
          <a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-bagicha-primary btn-lg shadow-sm">
            Explore Full Catalog <i class="bi bi-arrow-right ms-2"></i>
          </a>
          <a href="<?= BASE_URL ?>/pages/products.php?featured=1" class="btn btn-bagicha-outline btn-lg">
            <i class="bi bi-stars text-warning me-1"></i> Bestseller Plants
          </a>
        </div>

        <!-- Quick Trending Discovery Tags -->
        <div class="d-flex align-items-center gap-2 mt-3 pt-1 flex-wrap">
          <span class="small text-muted fw-semibold">Popular:</span>
          <a href="<?= BASE_URL ?>/pages/products.php?search=Monstera" class="badge bg-white text-secondary border rounded-pill text-decoration-none px-2.5 py-1.5 shadow-sm">Monstera</a>
          <a href="<?= BASE_URL ?>/pages/products.php?search=Snake" class="badge bg-white text-secondary border rounded-pill text-decoration-none px-2.5 py-1.5 shadow-sm">Snake Plant</a>
          <a href="<?= BASE_URL ?>/pages/products.php?category=succulents-cacti" class="badge bg-white text-secondary border rounded-pill text-decoration-none px-2.5 py-1.5 shadow-sm">Succulents</a>
          <a href="<?= BASE_URL ?>/pages/products.php?category=indoor-plants" class="badge bg-white text-secondary border rounded-pill text-decoration-none px-2.5 py-1.5 shadow-sm">Indoor Plants</a>
        </div>

        <!-- Trust Ratings Strip in Hero -->
        <div class="d-flex align-items-center gap-4 mt-4 pt-3 border-top border-light-subtle text-muted small">
          <div class="d-flex align-items-center gap-1.5">
            <i class="bi bi-star-fill text-warning fs-6"></i>
            <span class="fw-bold text-dark">4.8/5 Rating</span> (12k+ Reviews)
          </div>
          <div class="d-flex align-items-center gap-1.5">
            <i class="bi bi-shield-check text-success fs-6"></i>
            <span class="fw-bold text-dark">7-Day Free</span> Replacement
          </div>
        </div>
      </div>

      <div class="col-lg-5 position-relative">
        <div class="hero-image-wrap position-relative text-center">
          <img src="https://images.unsplash.com/photo-1614594975525-e45190c55d0b?auto=format&fit=crop&w=800&q=80"
            alt="Bagicha Monstera Plant"
            class="img-fluid rounded-4 shadow-lg hero-img"
            style="max-height: 460px; width: 100%; object-fit: cover; border-radius: 28px !important;">

          <!-- Floating Glass Badge 1 -->
          <div class="hero-float-card glass-card p-3 rounded-4 shadow-lg text-start position-absolute bottom-0 start-0 translate-middle-y ms-n3 d-none d-sm-block">
            <div class="d-flex align-items-center gap-2">
              <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                <i class="bi bi-heart-pulse-fill"></i>
              </div>
              <div>
                <strong class="d-block text-dark small font-heading">Monstera Deliciosa</strong>
                <span class="text-success small fw-bold">₹799 • Bestseller</span>
              </div>
            </div>
          </div>

          <!-- Floating Glass Badge 2 -->
          <div class="hero-float-card glass-card p-2.5 px-3 rounded-pill shadow-lg position-absolute top-0 end-0 translate-middle-x mt-3 d-none d-sm-block">
            <span class="badge bg-warning text-dark me-1"><i class="bi bi-lightning-charge-fill"></i> Fast Express</span>
            <small class="fw-bold text-dark">Packaging Sealed</small>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Unified Key Metrics & Nursery Assurances Bar -->
  <section class="metrics-bar p-4 rounded-4 mb-5 shadow-sm bg-white border border-light-subtle">
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3 border-end border-light">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
          <i class="bi bi-box-seam text-success fs-4"></i>
          <h3 class="font-heading display-6 fw-bold text-success mb-0">50k+</h3>
        </div>
        <p class="small text-muted mb-0 fw-medium">Plants Safely Shipped</p>
      </div>
      <div class="col-6 col-md-3 border-end-md border-light">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
          <i class="bi bi-heart-pulse text-success fs-4"></i>
          <h3 class="font-heading display-6 fw-bold text-success mb-0">99.4%</h3>
        </div>
        <p class="small text-muted mb-0 fw-medium">Arrival Survival Rate</p>
      </div>
      <div class="col-6 col-md-3 border-end border-light">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
          <i class="bi bi-flower1 text-success fs-4"></i>
          <h3 class="font-heading display-6 fw-bold text-success mb-0">150+</h3>
        </div>
        <p class="small text-muted mb-0 fw-medium">Acclimatized Species</p>
      </div>
      <div class="col-6 col-md-3">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
          <i class="bi bi-shield-check text-success fs-4"></i>
          <h3 class="font-heading display-6 fw-bold text-success mb-0">7 Days</h3>
        </div>
        <p class="small text-muted mb-0 fw-medium">Free Replacement Guarantee</p>
      </div>
    </div>
  </section>

  <!-- Featured Collections & Categories (Discovery First) -->
  <section class="mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <span class="text-uppercase tracking-wider text-success small fw-bold"><i class="bi bi-grid-fill me-1"></i> Botanical Collections</span>
        <h2 class="font-heading h3 text-primary mb-0">Shop By Nursery Category</h2>
      </div>
      <a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-link text-success text-decoration-none fw-semibold">View All Collections <i class="bi bi-arrow-right ms-1"></i></a>
    </div>

    <div class="horizontal-scroll-wrapper">
      <div class="horizontal-scroll-track" id="categoriesGrid">
        <div class="text-center py-4 w-100">
          <div class="spinner-border text-success" role="status"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Top Featured Best-Selling Plants -->
  <section class="mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <span class="text-uppercase tracking-wider text-success small fw-bold"><i class="bi bi-stars me-1"></i> Handpicked Selection</span>
        <h2 class="font-heading h3 text-primary mb-0">Best-Selling Plants & Supplies</h2>
      </div>
      <a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-link text-success text-decoration-none fw-semibold">View Full Catalog <i class="bi bi-arrow-right ms-1"></i></a>
    </div>

    <div class="horizontal-scroll-wrapper">
      <div class="horizontal-scroll-track" id="featuredProductsGrid">
        <div class="text-center py-4 w-100">
          <div class="spinner-border text-success" role="status"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Botanical Nursery Promise Gateway -->
  <section class="mb-5 py-2">
    <div class="text-center mb-4">
      <span class="text-uppercase tracking-wider text-success small fw-bold">The Bagicha Assurance</span>
      <h2 class="font-heading h3 text-primary mb-1">Nursery-Grown Quality for Every Home</h2>
      <p class="text-muted small mx-auto" style="max-width: 540px;">Every plant is hand-selected and nurtured under optimal conditions so you receive healthy, thriving greenery.</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card border-0 p-4 rounded-4 shadow-sm h-100 bg-white border border-light-subtle d-flex flex-column justify-content-between feature-card-hover" style="border-top: 4px solid var(--color-primary) !important;">
          <div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="feature-icon-circle text-success fs-4"><i class="bi bi-tree"></i></div>
              <span class="badge bg-success-subtle text-success small px-2 py-1">Farm Fresh</span>
            </div>
            <h5 class="font-heading text-primary mb-2">Acclimatized Species</h5>
            <p class="small text-muted mb-4">Cultivated with vigorous root systems in 100% peat-free organic soils under ambient nursery shade for seamless indoor transition.</p>
          </div>
          <a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-bagicha-primary btn-sm w-100 py-2">
            Explore Plants <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 p-4 rounded-4 shadow-sm h-100 bg-white border border-light-subtle d-flex flex-column justify-content-between feature-card-hover" style="border-top: 4px solid #059669 !important;">
          <div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="feature-icon-circle text-success fs-4"><i class="bi bi-box-seam"></i></div>
              <span class="badge bg-success-subtle text-success small px-2 py-1">Eco-Shield</span>
            </div>
            <h5 class="font-heading text-primary mb-2">Safe Transit Packaging</h5>
            <p class="small text-muted mb-4">Custom ventilated shock-absorbing packaging keeps soil locked in place and foliage protected throughout express courier transit.</p>
          </div>
          <a href="<?= BASE_URL ?>/index.php#about" class="btn btn-bagicha-outline btn-sm w-100 py-2">
            Learn More <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 p-4 rounded-4 shadow-sm h-100 bg-white border border-light-subtle d-flex flex-column justify-content-between feature-card-hover" style="border-top: 4px solid #D97706 !important;">
          <div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="feature-icon-circle text-warning fs-4"><i class="bi bi-shield-check"></i></div>
              <span class="badge bg-warning-subtle text-warning-emphasis small px-2 py-1">Zero Risk</span>
            </div>
            <h5 class="font-heading text-primary mb-2">7-Day Arrive-Alive</h5>
            <p class="small text-muted mb-4">Engineered shock-absorbing ventilated pods protect roots during transit. If your plant arrives stressed, we replace it free.</p>
          </div>
          <a href="<?= BASE_URL ?>/pages/guarantee.php" class="btn btn-bagicha-outline btn-sm w-100 py-2">
            View Guarantee <i class="bi bi-patch-check ms-1"></i>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- About Bagicha Section -->
  <?php require_once __DIR__ . '/includes/about-section.php'; ?>

  <!-- Dynamic Customer Testimonials Carousel -->
  <section class="mb-5 py-4">
    <div class="text-center mb-4">
      <span class="text-uppercase tracking-wider text-success small fw-bold">Plant Parent Testimonials</span>
      <h2 class="font-heading h2 text-primary mb-0">Loved By Over 12,000 Green Lovers</h2>
    </div>

    <div class="horizontal-scroll-wrapper position-relative px-4 px-md-5">
      <button class="testimonial-nav-btn testimonial-nav-prev" onclick="ReviewsModule.scrollTrack('testimonialsTrack', 'prev')" aria-label="Previous Testimonial" title="Previous">
        <i class="bi bi-chevron-left"></i>
      </button>

      <div class="horizontal-scroll-track" id="testimonialsTrack">
        <div class="text-center py-4 w-100">
          <div class="spinner-border text-success" role="status"></div>
        </div>
      </div>

      <button class="testimonial-nav-btn testimonial-nav-next" onclick="ReviewsModule.scrollTrack('testimonialsTrack', 'next')" aria-label="Next Testimonial" title="Next">
        <i class="bi bi-chevron-right"></i>
      </button>
    </div>
  </section>
</main>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    ProductsModule.loadCategories('categoriesGrid');
    ProductsModule.loadFeaturedProducts('featuredProductsGrid');
    ReviewsModule.loadTestimonials('testimonialsTrack');
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>