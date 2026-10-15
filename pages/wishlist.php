<?php
/**
 * Bagicha 2.0 - Customer Wishlist Page
 */
$pageTitle = "My Saved Wishlist - Bagicha";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<main id="main-content" class="container customer-dashboard py-4 py-lg-5" tabindex="-1">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-success text-decoration-none">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/profile.php" class="text-success text-decoration-none">My Garden</a></li>
      <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
    </ol>
  </nav>

  <section class="portal-page-header portal-wishlist-header mb-4" aria-labelledby="wishlistTitle">
    <div class="portal-page-icon"><i class="bi bi-heart"></i></div>
    <div>
      <p class="account-eyebrow mb-1">Your saved collection</p>
      <h1 id="wishlistTitle" class="font-heading mb-1">Wishlist <span id="wishlistItemCount" class="portal-count">0</span></h1>
      <p class="mb-0">Keep the plants you love close and move them to your cart when you’re ready.</p>
    </div>
    <a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-bagicha-primary ms-md-auto"><i class="bi bi-search me-2"></i>Discover plants</a>
  </section>

  <div class="row g-4" id="wishlistPageGrid" aria-live="polite">
    <div class="col-12"><div class="portal-loading"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading wishlist</span></div><span>Gathering your saved plants…</span></div></div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  WishlistModule.loadWishlistPage('wishlistPageGrid');
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
