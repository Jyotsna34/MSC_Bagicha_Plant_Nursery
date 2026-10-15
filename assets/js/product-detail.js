/**
 * Bagicha 2.0 - Product Detail Module
 * Handles gallery thumbnails, pincode estimator, reviews, bundles, and mobile action bar.
 */
let selectedStarRating = 5;

document.addEventListener('DOMContentLoaded', async () => {
  const urlParams = new URLSearchParams(window.location.search);
  const slug = urlParams.get('slug');
  const container = document.getElementById('productDetailContainer');
  const relatedGrid = document.getElementById('relatedProductsGrid');

  if (!slug) {
    container.innerHTML = '<div class="alert alert-warning">Product not found. <a href="' + App.getBaseUrl() + '/pages/products.php">Return to Shop</a></div>';
    return;
  }

  try {
    const res = await axios.get('/products/detail.php?slug=' + encodeURIComponent(slug));
    if (res.data.status === 'success') {
      const { product, gallery, related, reviews } = res.data.data;
      document.title = `${product.name} - Bagicha`;
      const bEl = document.getElementById('breadcrumbProductName');
      if (bEl) bEl.textContent = product.name;
      renderProductDetail(product, gallery, reviews);

      if (related && related.length > 0 && relatedGrid) {
        relatedGrid.innerHTML = related.map(r => ProductsModule.renderProductCard(r)).join('');
      }
      if (window.WishlistModule) WishlistModule.updateCardIcons();
    }
  } catch (err) {
    container.innerHTML = '<div class="alert alert-danger">Failed to load product details.</div>';
  }

  window.addEventListener('scroll', () => {
    const bar = document.getElementById('mobileBuyBar');
    if (bar && window.innerWidth < 992) bar.classList.toggle('d-none', window.scrollY <= 380);
  }, { passive: true });
});

function renderProductDetail(product, gallery, reviews) {
  const container = document.getElementById('productDetailContainer');
  const mainImg = gallery[0] || product.main_image;
  const galleryThumbs = gallery.map((img, idx) => `
    <img src="${img}" class="img-thumbnail rounded-3 me-2 mb-2 ${idx === 0 ? 'border-success' : ''}" 
         style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;" onclick="changeMainImg(this, '${img}')">`).join('');

  const priceHtml = product.sale_price
    ? `<span class="display-6 fw-bold text-success me-2">${App.formatCurrency(product.sale_price)}</span> <span class="fs-5 text-decoration-line-through text-muted">${App.formatCurrency(product.price)}</span>`
    : `<span class="display-6 fw-bold text-success">${App.formatCurrency(product.price)}</span>`;

  const bundleTotal = (parseFloat(product.sale_price || product.price) + 449) * 0.9;
  const bundleOriginal = parseFloat(product.sale_price || product.price) + 449;

  container.innerHTML = `
    <div class="row g-4">
      <div class="col-lg-6">
        <div class="card border-0 bg-white p-3 rounded-4 shadow-sm sticky-top" style="top: 90px;">
          <img id="detailMainImg" src="${mainImg}" class="img-fluid rounded-4 mb-3" style="width: 100%; max-height: 440px; object-fit: cover;">
          <div class="d-flex flex-wrap">${galleryThumbs}</div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="ps-lg-3">
          <span class="badge badge-green badge-tag mb-2">${product.category_name}</span>
          <h1 class="font-heading text-primary h2 mb-2">${product.name}</h1>
          <div class="d-flex align-items-center gap-2 mb-3">
            <div class="text-warning"><i class="bi bi-star-fill"></i> ${product.rating_avg}</div>
            <span class="text-muted small">(${product.rating_count} reviews) |</span>
            <span class="badge ${product.stock_quantity > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'} px-2 py-1">
              ${product.stock_quantity > 0 ? 'In Stock (' + product.stock_quantity + ' available)' : 'Out of Stock'}
            </span>
          </div>
          <div class="mb-3">${priceHtml}</div>
          <p class="text-secondary mb-4">${product.description || product.short_description}</p>
          <div class="p-3 rounded-4 bg-white border mb-4 shadow-sm">
            <h6 class="font-heading text-primary mb-3"><i class="bi bi-heart-pulse me-1"></i> Plant Care Specifications</h6>
            <div class="row g-3 text-center">
              <div class="col-6 col-sm-3"><div class="p-2 rounded-3 bg-light"><i class="bi bi-sun fs-4 text-warning"></i><div class="small fw-semibold mt-1">Light</div><div class="small text-muted">${product.light_requirement || 'Indirect'}</div></div></div>
              <div class="col-6 col-sm-3"><div class="p-2 rounded-3 bg-light"><i class="bi bi-droplet fs-4 text-primary"></i><div class="small fw-semibold mt-1">Water</div><div class="small text-muted">${product.water_requirement || 'Weekly'}</div></div></div>
              <div class="col-6 col-sm-3"><div class="p-2 rounded-3 bg-light"><i class="bi bi-gear fs-4 text-success"></i><div class="small fw-semibold mt-1">Maintenance</div><div class="small text-muted">${product.maintenance_level || 'Low'}</div></div></div>
              <div class="col-6 col-sm-3"><div class="p-2 rounded-3 bg-light"><i class="bi bi-shield-check fs-4" style="color: var(--color-accent);"></i><div class="small fw-semibold mt-1">Pet Safe</div><div class="small text-muted">${product.is_pet_friendly == 1 ? 'Yes' : 'No'}</div></div></div>
            </div>
          </div>
          <div class="d-flex align-items-stretch gap-3 mb-4">
            <div class="input-group" style="width: 130px; height: 52px;">
              <button class="btn btn-outline-secondary rounded-start-3" type="button" onclick="adjustQty(-1)">-</button>
              <input type="number" id="buyQty" class="form-control text-center fw-bold" value="1" min="1" max="${product.stock_quantity}">
              <button class="btn btn-outline-secondary rounded-end-3" type="button" onclick="adjustQty(1)">+</button>
            </div>
            <button class="btn btn-bagicha-primary btn-lg flex-grow-1 d-flex align-items-center justify-content-center gap-2" onclick="CartModule.addToCart(${product.id}, parseInt(document.getElementById('buyQty').value))" style="height: 52px;">
              <i class="bi bi-bag-plus fs-5"></i> Add to Cart
            </button>
            <button class="btn btn-detail-wishlist ${(typeof WishlistModule !== 'undefined' && WishlistModule.isSaved && WishlistModule.isSaved(product.id)) ? 'active-saved' : ''}" data-product-id="${product.id}" onclick="WishlistModule.toggleWishlist(${product.id}, this)">
              <i class="${(typeof WishlistModule !== 'undefined' && WishlistModule.isSaved && WishlistModule.isSaved(product.id)) ? 'bi bi-heart-fill text-danger' : 'bi bi-heart text-muted'}"></i>
            </button>
          </div>
          <div class="p-3 rounded-3 bg-white border mb-3 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="small fw-bold text-dark"><i class="bi bi-geo-alt-fill text-success me-1"></i> Delivery Estimates</span>
              <span class="small text-muted">Express Courier Check</span>
            </div>
            <div class="input-group input-group-sm">
              <input type="text" id="deliveryPincodeInput" class="form-control rounded-start-3" placeholder="Enter 6-digit Pincode" maxlength="6">
              <button class="btn btn-outline-success rounded-end-3" type="button" onclick="checkDeliveryPincode()">Check</button>
            </div>
            <div id="pincodeResult" class="small mt-2 d-none"></div>
          </div>
          <div class="d-flex justify-content-between text-muted small py-2 px-1 border-top border-bottom mb-4">
            <span><i class="bi bi-shield-check text-success me-1"></i> 7-Day Guarantee</span>
            <span><i class="bi bi-box-seam text-success me-1"></i> Ventilated Pods</span>
            <span><i class="bi bi-chat-heart text-success me-1"></i> Free Plant Care Tips</span>
          </div>
          <div class="bundle-box mb-4">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <h6 class="font-heading text-primary mb-0"><i class="bi bi-box2-heart text-success me-1"></i> Frequently Potted Together</h6>
              <span class="badge bg-warning text-dark small fw-bold">Save 10% Bundle</span>
            </div>
            <p class="small text-muted mb-3">Pair this plant with an artisanal ceramic planter and organic potting mix.</p>
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="fw-bold text-success font-heading fs-5">${App.formatCurrency(bundleTotal)}</span>
                <small class="text-decoration-line-through text-muted ms-1">${App.formatCurrency(bundleOriginal)}</small>
              </div>
              <button class="btn btn-bagicha-primary btn-sm px-3" onclick="CartModule.addToCart(${product.id}); App.showToast('🎉 Plant & Planter Bundle Added to Bag!');">Add Bundle to Bag</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="mobileBuyBar" class="mobile-action-bar d-none">
      <div class="overflow-hidden me-2">
        <strong class="d-block text-truncate small font-heading text-dark">${product.name}</strong>
        <span class="fw-bold text-success font-heading">${App.formatCurrency(product.sale_price || product.price)}</span>
      </div>
      <button class="btn btn-bagicha-primary btn-sm px-3 py-2 fw-semibold text-nowrap" onclick="CartModule.addToCart(${product.id})">Add to Cart <i class="bi bi-bag-plus ms-1"></i></button>
    </div>
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mt-5">
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
          <h4 class="font-heading text-primary mb-1"><i class="bi bi-chat-square-quote me-2"></i> Customer Reviews & Ratings</h4>
          <div class="d-flex align-items-center gap-2">
            <span class="fs-4 fw-bold text-warning">${product.rating_avg || '5.0'}</span>
            <div class="text-warning">${'★'.repeat(Math.round(product.rating_avg || 5))}${'☆'.repeat(5 - Math.round(product.rating_avg || 5))}</div>
            <span class="text-muted small">Based on ${reviews ? reviews.length : 0} verified reviews</span>
          </div>
        </div>
        <button class="btn btn-bagicha-outline btn-sm" onclick="document.getElementById('writeReviewCard').scrollIntoView({behavior: 'smooth'})">
          <i class="bi bi-pencil-square me-1"></i> Write a Review
        </button>
      </div>
      <div class="reviews-list mb-4">
        ${reviews && reviews.length > 0 ? reviews.map(r => `
          <div class="p-3 mb-3 rounded-3 bg-light border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="fw-bold text-dark">${r.reviewer_name}</span>
              <small class="text-muted">${new Date(r.created_at).toLocaleDateString()}</small>
            </div>
            <div class="text-warning small mb-2">${'★'.repeat(r.rating)}${'☆'.repeat(5 - r.rating)}</div>
            <p class="text-secondary mb-0 small">${r.comment || '<i>No written comment provided.</i>'}</p>
          </div>
        `).join('') : '<div class="alert alert-info small mb-0">No customer reviews yet. Be the first to share your experience!</div>'}
      </div>
      <div class="card border p-3 rounded-4 bg-light-subtle" id="writeReviewCard">
        <h6 class="font-heading text-primary mb-3"><i class="bi bi-star-fill text-warning me-1"></i> Submit Your Product Review</h6>
        <form id="reviewForm" onsubmit="submitProductReview(${product.id}, event)">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Your Rating</label>
            <div class="star-rating-selector fs-3 cursor-pointer">
              ${[1,2,3,4,5].map(i => `<i class="bi bi-star-fill text-warning me-1" id="star-${i}" onclick="selectRatingStar(${i})"></i>`).join('')}
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Your Review & Experience</label>
            <textarea id="reviewCommentInput" class="form-control" rows="3" placeholder="Tell us how your plant is thriving, packaging quality, or care instructions..." style="border-radius: 10px;" required></textarea>
          </div>
          <button type="submit" class="btn btn-bagicha-primary">Submit Review</button>
        </form>
      </div>
    </div>
  `;
}

function checkDeliveryPincode() {
  const pin = document.getElementById('deliveryPincodeInput').value.trim();
  const res = document.getElementById('pincodeResult');
  if (!pin || pin.length < 6 || !/^\d{6}$/.test(pin)) {
    res.className = 'small mt-2 text-danger';
    res.textContent = 'Please enter a valid 6-digit Indian pincode.';
    res.classList.remove('d-none');
    return;
  }
  const deliveryDate = new Date();
  deliveryDate.setDate(deliveryDate.getDate() + 3);
  const dateStr = deliveryDate.toLocaleDateString('en-IN', { weekday: 'short', month: 'short', day: 'numeric' });
  res.className = 'small mt-2 text-success fw-semibold';
  res.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> Delivery available to ${pin} by <strong>${dateStr}</strong>. Express live-plant packaging included!`;
  res.classList.remove('d-none');
}

function changeMainImg(el, src) {
  document.getElementById('detailMainImg').src = src;
  document.querySelectorAll('.img-thumbnail').forEach(t => t.classList.remove('border-success'));
  el.classList.add('border-success');
}

function adjustQty(amount) {
  const input = document.getElementById('buyQty');
  if (!input) return;
  let val = parseInt(input.value) + amount;
  if (val >= 1 && val <= parseInt(input.max || 99)) input.value = val;
}

function selectRatingStar(val) {
  selectedStarRating = val;
  for (let i = 1; i <= 5; i++) {
    const star = document.getElementById(`star-${i}`);
    if (star) star.className = (i <= val) ? 'bi bi-star-fill text-warning me-1' : 'bi bi-star text-muted me-1';
  }
}

async function submitProductReview(productId, e) {
  e.preventDefault();
  if (!App.getToken()) {
    App.showToast('Please login to submit a review', 'warning');
    new bootstrap.Modal(document.getElementById('authModal')).show();
    return;
  }
  const comment = document.getElementById('reviewCommentInput')?.value.trim();
  try {
    const res = await axios.post('/reviews/create.php', {
      product_id: productId,
      rating: selectedStarRating,
      comment: comment
    });
    if (res.data.status === 'success') {
      App.showToast(res.data.message || 'Review submitted successfully!');
      document.getElementById('reviewForm')?.reset();
      selectRatingStar(5);
      setTimeout(() => window.location.reload(), 1200);
    }
  } catch (err) {
    App.showToast(err.response?.data?.message || 'Failed to submit review', 'error');
  }
}
