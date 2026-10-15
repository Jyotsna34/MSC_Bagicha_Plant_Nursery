<?php
/**
 * Bagicha 2.0 - Contact & Support Hub
 * Direct WhatsApp hotline, physical nursery visit info, and inquiry form.
 */
$pageTitle = "Contact Us & Support - Bagicha | Get in Touch";
$pageDescription = "Have plant questions or need assistance? Reach Bagicha's support team via WhatsApp, phone, or our online inquiry desk.";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<main id="main-content" class="container py-4" tabindex="-1">
  <!-- Page Header -->
  <div class="mb-4 text-center" style="max-width: 680px; margin: 0 auto;">
    <nav aria-label="breadcrumb" class="mb-2">
      <ol class="breadcrumb justify-content-center small text-muted mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-decoration-none text-success">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
      </ol>
    </nav>
    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2 bg-success-subtle text-success small fw-semibold">
      <i class="bi bi-chat-heart-fill"></i> Plant Care & Customer Support
    </div>
    <h1 class="font-heading display-6 fw-bold text-primary mb-2">We Are Here For You & Your Plants</h1>
    <p class="text-secondary small">Whether you need help with plant care, tracking an express delivery, or planning green gifting, our team is ready to help.</p>
  </div>

  <div class="row g-4 justify-content-center">
    <!-- Contact Info Cards -->
    <div class="col-lg-5">
      <div class="d-flex flex-column gap-3">
        
        <!-- WhatsApp Support Card -->
        <div class="card border-0 p-4 rounded-4 shadow-sm text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #075E54 0%, #128C7E 100%);">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 48px; height: 48px;">
              <i class="bi bi-whatsapp fs-3 text-success"></i>
            </div>
            <div>
              <span class="text-uppercase small fw-bold tracking-wider" style="color: #A7F3D0;">Instant Assistance</span>
              <h5 class="font-heading mb-0 text-white">WhatsApp Plant Support</h5>
            </div>
          </div>
          <p class="small mb-3 text-white-50" style="line-height: 1.6;">
            Chat directly with our friendly team for same-day plant care guidance and order help.
          </p>
          <a href="https://wa.me/919876543210?text=Hi%20Bagicha%20Team,%20I%20need%20help%20with%20my%20plant!" target="_blank" class="btn btn-light fw-bold text-success btn-sm align-self-start shadow-sm">
            Chat on WhatsApp <i class="bi bi-box-arrow-up-right ms-1"></i>
          </a>
        </div>

        <!-- Nursery Location & Hours -->
        <div class="card border-0 p-4 rounded-4 shadow-sm bg-white border border-light-subtle">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px;">
              <i class="bi bi-geo-alt-fill fs-4"></i>
            </div>
            <div>
              <span class="text-uppercase small fw-bold text-success">Our Green Nursery</span>
              <h5 class="font-heading mb-0 text-dark">Nursery Headquarters</h5>
            </div>
          </div>
          <p class="small text-muted mb-2">
            <strong>Bagicha Botanical Polyhouses</strong><br>
            Survey 42, Green Valley Nursery Road, Hinjewadi Phase 3, Pune, Maharashtra 411057
          </p>
          <hr class="my-2 text-light">
          <div class="d-flex justify-content-between small text-muted mb-1">
            <span><i class="bi bi-clock me-1 text-success"></i> Visiting Hours:</span>
            <span class="fw-semibold text-dark">Mon – Sun: 8:00 AM – 7:30 PM</span>
          </div>
          <div class="d-flex justify-content-between small text-muted">
            <span><i class="bi bi-telephone me-1 text-success"></i> Phone Support:</span>
            <span class="fw-semibold text-dark">+91 98765 43210</span>
          </div>
        </div>

        <!-- Trust Guarantee Card -->
        <div class="card border-0 p-3.5 rounded-4 bg-light text-secondary small">
          <div class="d-flex align-items-center gap-2 mb-1 text-dark fw-bold">
            <i class="bi bi-shield-check text-success fs-5"></i> 7-Day Arrive-Alive Guarantee
          </div>
          If your plant arrived damaged or stressed from shipping, please include your Order ID in the form for priority replacement dispatch.
        </div>

      </div>
    </div>

    <!-- Contact Form -->
    <div class="col-lg-7">
      <div class="card border-0 p-4 p-md-5 rounded-4 shadow-sm bg-white border border-light-subtle">
        <h3 class="font-heading h4 text-primary mb-1">Send Us a Message</h3>
        <p class="small text-muted mb-4">Our support team typically replies within 2 to 4 business hours.</p>

        <div id="contactAlert" class="alert d-none py-2.5 small mb-3"></div>

        <form id="contactForm" onsubmit="handleContactSubmit(event)">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-semibold text-dark">Your Name *</label>
              <input type="text" id="contactName" class="form-control rounded-3" placeholder="Aarav Sharma" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold text-dark">Email Address *</label>
              <input type="email" id="contactEmail" class="form-control rounded-3" placeholder="aarav@example.com" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold text-dark">Phone Number (Optional)</label>
              <input type="tel" id="contactPhone" class="form-control rounded-3" placeholder="+91 98765 43210">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold text-dark">Inquiry Category *</label>
              <select id="contactCategory" class="form-select rounded-3">
                <option value="care-guidance">🌿 Botanical Care Guidance</option>
                <option value="order-query">📦 Order Delivery & Transit Status</option>
                <option value="replacement">🛡️ 7-Day Guarantee Replacement Claim</option>
                <option value="corporate-gifting">🎁 Corporate Green Gifting & Bulk</option>
                <option value="general" selected>💬 General Inquiry</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold text-dark">Subject *</label>
              <input type="text" id="contactSubject" class="form-control rounded-3" placeholder="e.g. Care advice for Monstera in north window" required>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold text-dark">Message Details *</label>
              <textarea id="contactMessage" class="form-control rounded-3" rows="4" placeholder="Describe your question or order details..." required></textarea>
            </div>
            <div class="col-12">
              <button type="submit" id="contactSubmitBtn" class="btn btn-bagicha-primary w-100 py-2.5 fw-semibold">
                Send Message <i class="bi bi-send-fill ms-1"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const orderId = params.get('order_id');
  const cat = params.get('category');
  const subj = params.get('subject');

  if (cat && document.getElementById('contactCategory')) {
    document.getElementById('contactCategory').value = cat;
  } else if (orderId && document.getElementById('contactCategory')) {
    document.getElementById('contactCategory').value = 'order-query';
  }

  if (subj && document.getElementById('contactSubject')) {
    document.getElementById('contactSubject').value = subj;
  } else if (orderId && document.getElementById('contactSubject')) {
    document.getElementById('contactSubject').value = `Inquiry regarding Order #${orderId}`;
    const msgEl = document.getElementById('contactMessage');
    if (msgEl && !msgEl.value) {
      msgEl.value = `Hello Bagicha Team,\n\nI need assistance with my Order #${orderId}: `;
    }
  }
});

async function handleContactSubmit(e) {
  e.preventDefault();
  const alertEl = document.getElementById('contactAlert');
  const btn = document.getElementById('contactSubmitBtn');
  
  const payload = {
    name: document.getElementById('contactName').value.trim(),
    email: document.getElementById('contactEmail').value.trim(),
    phone: document.getElementById('contactPhone').value.trim(),
    category: document.getElementById('contactCategory').value,
    subject: document.getElementById('contactSubject').value.trim(),
    message: document.getElementById('contactMessage').value.trim()
  };

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sending inquiry...';

  try {
    const res = await axios.post(`${App.getBaseUrl()}/api/contact/index.php`, payload);
    if (res.data.status === 'success') {
      alertEl.className = 'alert alert-success py-2.5 small mb-3';
      alertEl.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> ${res.data.message}`;
      alertEl.classList.remove('d-none');
      document.getElementById('contactForm').reset();
    } else {
      throw new Error(res.data.message || 'Submission failed');
    }
  } catch (err) {
    alertEl.className = 'alert alert-danger py-2.5 small mb-3';
    alertEl.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> ${err.response?.data?.message || err.message || 'Failed to submit inquiry'}`;
    alertEl.classList.remove('d-none');
  } finally {
    btn.disabled = false;
    btn.innerHTML = 'Send Message <i class="bi bi-send-fill ms-1"></i>';
  }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
