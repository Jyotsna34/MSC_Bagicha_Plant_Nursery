<?php

/**
 * Bagicha 2.0 - Product Detail Page
 * Features plant care specifications, pincode estimator, bundle save, and customer reviews.
 */
$pageTitle = "Plant Details - Bagicha";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<main id="main-content" class="container py-4" tabindex="-1">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-success text-decoration-none">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/products.php" class="text-success text-decoration-none">Shop</a></li>
      <li class="breadcrumb-item active" aria-current="page" id="breadcrumbProductName">Product</li>
    </ol>
  </nav>

  <!-- Product Container (Rendered dynamically by product-detail.js) -->
  <div id="productDetailContainer">
    <div class="text-center py-5">
      <div class="spinner-border text-success" role="status"></div>
      <p class="text-muted small mt-2">Loading botanical specifications...</p>
    </div>
  </div>

  <!-- Related Products Carousel / Grid -->
  <div class="mt-5 pt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="font-heading text-primary h4 mb-0">You May Also Like</h3>
      <a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-link text-success text-decoration-none fw-semibold small">
        Explore Catalog <i class="bi bi-arrow-right"></i>
      </a>
    </div>
    <div class="row g-3" id="relatedProductsGrid"></div>
  </div>
</main>

<!-- Dedicated Product Detail JavaScript Module -->
<script src="<?= BASE_URL ?>/assets/js/product-detail.js?v=<?= time() ?>"></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
