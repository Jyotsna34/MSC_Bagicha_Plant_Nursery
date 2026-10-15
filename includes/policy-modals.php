<?php
/**
 * Bagicha 2.0 - Policy & Terms Modals Component
 * Provides accessible, biophilic legal disclosures for Privacy Policy and Terms & Conditions.
 */
?>
<!-- Privacy Policy Modal -->
<div class="modal fade" id="privacyPolicyModal" tabindex="-1" aria-labelledby="privacyPolicyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-header border-bottom p-4">
        <div>
          <span class="badge bg-success-subtle text-success small mb-1">Legal & Security</span>
          <h5 class="modal-title font-heading text-primary h4 mb-0" id="privacyPolicyModalLabel">Privacy Policy</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 small" style="line-height: 1.7; color: #495057;">
        <h6 class="font-heading text-dark fw-bold mb-2">1. Information We Collect</h6>
        <p>At Bagicha, we respect your privacy. When you place an order, create an account, or contact our customer support team, we collect necessary contact information (name, delivery address, email, phone number) strictly to fulfill your orders and offer personalized assistance.</p>

        <h6 class="font-heading text-dark fw-bold mb-2 mt-4">2. How Your Data Is Protected</h6>
        <p>Payment information is processed directly by PCI-DSS compliant gateways (Razorpay). Bagicha never stores credit card or debit card credentials on our servers. User sessions use cryptographically signed tokens (JWT) and encrypted passwords (bcrypt).</p>

        <h6 class="font-heading text-dark fw-bold mb-2 mt-4">3. No Third-Party Selling</h6>
        <p>We never sell, rent, or trade your personal information to third parties. Data is shared solely with trusted courier partners to enable prompt delivery of your living plants.</p>

        <h6 class="font-heading text-dark fw-bold mb-2 mt-4">4. Customer Inquiries & Communications</h6>
        <p>Inquiries and messages submitted through our Contact Desk or support channels are utilized exclusively by our team to assist you and ensure prompt order resolution.</p>
      </div>
      <div class="modal-footer border-0 p-3 bg-light" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
        <button type="button" class="btn btn-bagicha-primary btn-sm px-4" data-bs-dismiss="modal">I Understand</button>
      </div>
    </div>
  </div>
</div>

<!-- Terms & Conditions Modal -->
<div class="modal fade" id="termsConditionsModal" tabindex="-1" aria-labelledby="termsConditionsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-header border-bottom p-4">
        <div>
          <span class="badge bg-success-subtle text-success small mb-1">Customer Agreement</span>
          <h5 class="modal-title font-heading text-primary h4 mb-0" id="termsConditionsModalLabel">Terms & Conditions</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 small" style="line-height: 1.7; color: #495057;">
        <h6 class="font-heading text-dark fw-bold mb-2">1. Living Plant Deliveries</h6>
        <p>Living plants are natural specimens; actual foliage size, leaf patterns, and variegation may naturally vary slightly from photography. Each specimen is carefully inspected, hydrated, and packed in ventilated Eco-Shield packaging before dispatch.</p>

        <h6 class="font-heading text-dark fw-bold mb-2 mt-4">2. 7-Day Arrive-Alive Guarantee & Replacement</h6>
        <p>If your plant arrives transit-damaged or in severe distress, notify Bagicha within 7 days of delivery with an unboxing photograph. We provide free replacement plants or nursery credits promptly as detailed in our Refund & Return Policy.</p>

        <h6 class="font-heading text-dark fw-bold mb-2 mt-4">3. Order Fulfillment & Delivery Timelines</h6>
        <p>Standard express shipping delivers within 3 to 5 business days across serviceable pin codes. Delays resulting from extreme weather conditions or carrier force majeure are actively communicated via SMS and email.</p>

        <h6 class="font-heading text-dark fw-bold mb-2 mt-4">4. Acceptable Platform Use</h6>
        <p>Users agree to provide accurate delivery and contact credentials. Unauthorized tampering, scraping, or disruptive automated access to the website or APIs is strictly prohibited.</p>
      </div>
      <div class="modal-footer border-0 p-3 bg-light" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
        <button type="button" class="btn btn-bagicha-primary btn-sm px-4" data-bs-dismiss="modal">Accept & Close</button>
      </div>
    </div>
  </div>
</div>
