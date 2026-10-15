/**
 * Bagicha 2.0 - Dynamic Reviews & Testimonials Carousel JS Module
 */

const ReviewsModule = {
  async loadTestimonials(trackId) {
    const track = document.getElementById(trackId);
    if (!track) return;

    try {
      const res = await axios.get('/reviews/index.php?limit=10');
      if (res.data.status === 'success' && res.data.data.reviews.length > 0) {
        const reviews = res.data.data.reviews;
        track.innerHTML = reviews.map(r => this.renderTestimonialCard(r)).join('');
      } else {
        track.innerHTML = `<div class="text-center py-4 text-muted w-100">No reviews published yet.</div>`;
      }
    } catch (err) {
      console.error('Failed to load testimonials', err);
      track.innerHTML = `<div class="text-center py-4 text-danger w-100">Unable to load reviews.</div>`;
    }
  },

  renderTestimonialCard(r) {
    const stars = Array.from({ length: 5 }, (_, i) => 
      `<i class="bi ${i < r.rating ? 'bi-star-fill text-warning' : 'bi-star text-muted'}"></i>`
    ).join('');

    const initial = r.reviewer_name ? r.reviewer_name.trim().charAt(0).toUpperCase() : 'P';
    
    // Biophilic gradient palettes
    const bgGradients = [
      'linear-gradient(135deg, #033B20 0%, #0F683A 100%)',
      'linear-gradient(135deg, #15803D 0%, #166534 100%)',
      'linear-gradient(135deg, #047857 0%, #065F46 100%)',
      'linear-gradient(135deg, #0284C7 0%, #0369A1 100%)',
      'linear-gradient(135deg, #D97706 0%, #B45309 100%)'
    ];
    const gradIndex = (r.id || 0) % bgGradients.length;
    const grad = bgGradients[gradIndex];

    const avatarHtml = `
      <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" 
           style="width: 44px; height: 44px; font-size: 1.15rem; background: ${grad}; border: 2px solid rgba(255,255,255,0.9); font-family: var(--font-heading); line-height: 1;">
        <span style="display: block; line-height: 1;">${initial}</span>
      </div>
    `;

    return `
      <div class="testimonial-item-card">
        <div class="card border-0 bg-white p-4 shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between">
          <div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="small fs-6">${stars}</div>
              <span class="badge bg-success-subtle text-success small fw-semibold px-2.5 py-1 rounded-pill">
                <i class="bi bi-patch-check-fill me-1"></i> Verified Buyer
              </span>
            </div>
            <p class="text-secondary mb-4" style="line-height: 1.6; font-size: 0.92rem;">
              "${r.comment || 'Excellent healthy plants and fast express delivery!'}"
            </p>
          </div>

          <div class="d-flex align-items-center gap-3 pt-3 border-top border-light-subtle">
            ${avatarHtml}
            <div class="overflow-hidden me-auto">
              <strong class="d-block text-dark font-heading text-truncate mb-0" style="font-size: 0.95rem;">${r.reviewer_name}</strong>
              <small class="text-muted text-truncate d-block" style="font-size: 0.78rem;">
                Purchased <a href="${App.getBaseUrl()}/pages/product-detail.php?slug=${r.product_slug}" class="text-success text-decoration-none fw-semibold">${r.product_name}</a>
              </small>
            </div>
          </div>
        </div>
      </div>
    `;
  },

  scrollTrack(trackId, direction = 'next') {
    const track = document.getElementById(trackId);
    if (!track) return;

    const scrollAmount = track.clientWidth * 0.75;
    if (direction === 'next') {
      track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    } else {
      track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    }
  }
};
