/**
 * Bagicha 2.0 - Checkout & Order Processing JS
 */

const CheckoutModule = {
  selectedAddressId: null,
  appliedCoupon: null,

  async initCheckout() {
    if (!App.getToken()) {
      window.location.href = App.getBaseUrl() + '/pages/login.php';
      return;
    }

    await this.loadAddresses();
    await this.loadOrderSummary();
  },

  async loadAddresses() {
    const list = document.getElementById('checkoutAddressList');
    if (!list) return;

    try {
      const res = await axios.get('/addresses/index.php');
      if (res.data.status === 'success') {
        const addresses = res.data.data.addresses;
        if (addresses.length === 0) {
          list.innerHTML = `<div class="alert alert-info small mb-3">No saved addresses found. Please add one below.</div>`;
          return;
        }

        list.innerHTML = addresses.map((addr, idx) => {
          if (idx === 0 || addr.is_default) this.selectedAddressId = addr.id;
          return `
            <div class="card p-3 mb-2 rounded-3 cursor-pointer border ${this.selectedAddressId === addr.id ? 'border-success bg-light-subtle' : ''}" 
                 onclick="CheckoutModule.selectAddress(${addr.id}, this)">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold text-dark mb-1">${addr.address_line1} ${addr.address_line2 || ''}</div>
                  <div class="small text-muted">${addr.city}, ${addr.state} - ${addr.pincode}</div>
                </div>
                ${addr.is_default ? '<span class="badge bg-success small">Default</span>' : ''}
              </div>
            </div>
          `;
        }).join('');
      }
    } catch (err) {
      console.error('Failed to load addresses', err);
    }
  },

  selectAddress(id, cardEl) {
    this.selectedAddressId = id;
    document.querySelectorAll('#checkoutAddressList .card').forEach(c => c.classList.remove('border-success', 'bg-light-subtle'));
    if (cardEl) cardEl.classList.add('border-success', 'bg-light-subtle');
  },

  async saveNewAddress(e) {
    e.preventDefault();
    const data = {
      address_line1: document.getElementById('addrLine1').value,
      address_line2: document.getElementById('addrLine2').value,
      city: document.getElementById('addrCity').value,
      state: document.getElementById('addrState').value,
      pincode: document.getElementById('addrPincode').value,
      is_default: document.getElementById('addrDefault').checked ? 1 : 0
    };

    try {
      const res = await axios.post('/addresses/index.php', data);
      if (res.data.status === 'success') {
        App.showToast('Address saved!');
        const modal = bootstrap.Modal.getInstance(document.getElementById('addAddressModal'));
        if (modal) modal.hide();
        await this.loadAddresses();
      }
    } catch (err) {
      App.showToast(err.response?.data?.message || 'Failed to save address', 'error');
    }
  },

  async applyCoupon() {
    const code = document.getElementById('couponCodeInput')?.value.trim();
    if (!code) {
      App.showToast('Please enter a coupon code', 'warning');
      return;
    }

    try {
      const cartRes = await axios.get('/cart/index.php');
      const subtotal = cartRes.data.data.summary.subtotal;

      const res = await axios.post('/coupons/apply.php', { code, subtotal });
      if (res.data.status === 'success') {
        this.appliedCoupon = res.data.data;
        App.showToast(res.data.message);
        this.loadOrderSummary();
      }
    } catch (err) {
      App.showToast(err.response?.data?.message || 'Coupon validation failed', 'error');
    }
  },

  async loadOrderSummary() {
    const summaryContainer = document.getElementById('checkoutSummaryBox');
    if (!summaryContainer) return;

    try {
      const res = await axios.get('/cart/index.php');
      const { items, summary } = res.data.data;

      if (items.length === 0) {
        window.location.href = App.getBaseUrl() + '/pages/cart.php';
        return;
      }

      let discount = this.appliedCoupon ? this.appliedCoupon.calculated_discount : 0;
      let finalTotal = Math.max(0, summary.total - discount);

      summaryContainer.innerHTML = `
        <h5 class="font-heading text-primary mb-3">Order Summary</h5>
        <div class="mb-3 max-h-200 overflow-auto">
          ${items.map(i => `
            <div class="d-flex justify-content-between align-items-center mb-2 small">
              <span class="text-truncate" style="max-width: 180px;">${i.name} x ${i.quantity}</span>
              <span class="fw-semibold">${App.formatCurrency(i.item_subtotal)}</span>
            </div>
          `).join('')}
        </div>
        <hr>
        <div class="d-flex justify-content-between small mb-2">
          <span>Subtotal</span><span>${App.formatCurrency(summary.subtotal)}</span>
        </div>
        <div class="d-flex justify-content-between small mb-2">
          <span>Shipping Fee</span><span>${summary.shipping_fee === 0 ? 'FREE' : App.formatCurrency(summary.shipping_fee)}</span>
        </div>
        ${discount > 0 ? `
          <div class="d-flex justify-content-between small text-success mb-2">
            <span>Coupon Discount (${this.appliedCoupon.code})</span><span>-${App.formatCurrency(discount)}</span>
          </div>
        ` : ''}
        <hr>
        <div class="d-flex justify-content-between fw-bold fs-5 mb-4 text-primary">
          <span>Total Amount</span><span>${App.formatCurrency(finalTotal)}</span>
        </div>
        <button class="btn btn-bagicha-accent btn-lg w-100 py-3 font-heading" onclick="CheckoutModule.placeOrder()">
          <i class="bi bi-shield-lock me-2"></i> Pay Now with Razorpay
        </button>
      `;
    } catch (err) {
      summaryContainer.innerHTML = `<div class="text-danger">Failed to load order summary.</div>`;
    }
  },

  async placeOrder() {
    if (!this.selectedAddressId) {
      App.showToast('Please select or add a shipping address', 'warning');
      return;
    }

    try {
      const selectedAddrCard = document.querySelector('#checkoutAddressList .border-success');
      const addressText = selectedAddrCard ? selectedAddrCard.innerText.replace('\n', ', ') : 'Default Address';

      const orderRes = await axios.post('/orders/create.php', {
        shipping_address: addressText,
        coupon_code: this.appliedCoupon ? this.appliedCoupon.code : '',
        discount_amount: this.appliedCoupon ? this.appliedCoupon.calculated_discount : 0
      });

      if (orderRes.data.status === 'success') {
        const orderData = orderRes.data.data;
        this.openRazorpayModal(orderData);
      }
    } catch (err) {
      App.showToast(err.response?.data?.message || 'Order creation failed', 'error');
    }
  },

  openRazorpayModal(orderData) {
    const user = App.getUser();
    
    const razorpayKeyRegex = /^rzp_(test|live)_[a-zA-Z0-9]{14,}$/;
    const isRealRazorpayKey = orderData.key_id && razorpayKeyRegex.test(orderData.key_id) && !orderData.key_id.includes('Bagicha2026Secret');

    if (typeof Razorpay !== 'undefined' && isRealRazorpayKey) {
      try {
        const options = {
          key: orderData.key_id,
          amount: Math.round(orderData.final_amount * 100),
          currency: "INR",
          name: "Bagicha 2.0",
          description: "Plant Order #" + orderData.order_number,
          order_id: orderData.razorpay_order_id,
          prefill: {
            name: user ? user.full_name : '',
            email: user ? user.email : '',
            contact: (user && user.phone) ? user.phone : ''
          },
          theme: { color: "#034324" },
          handler: async function (response) {
            await CheckoutModule.verifyPayment(orderData.order_id, response.razorpay_payment_id || 'pay_test_12345', response.razorpay_signature || '');
          },
          modal: {
            ondismiss: function() {
              App.showToast('Payment window closed', 'info');
            }
          }
        };
        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function (response) {
          App.showToast(response.error?.description || 'Payment Failed', 'error');
        });
        rzp.open();
      } catch (err) {
        console.warn('Razorpay SDK error, falling back to simulation', err);
        this.simulateTestPayment(orderData);
      }
    } else {
      this.simulateTestPayment(orderData);
    }
  },

  async simulateTestPayment(orderData) {
    const confirmed = await App.confirmModal({
      title: 'Simulate Payment (Test Mode)',
      message: `Simulate Razorpay payment for Order <strong>#${orderData.order_number}</strong> (${App.formatCurrency(orderData.final_amount)})?`,
      confirmText: 'Pay Now',
      confirmClass: 'btn-bagicha-primary'
    });
    if (confirmed) {
      this.verifyPayment(orderData.order_id, 'pay_simulated_' + Date.now(), '');
    }
  },

  async verifyPayment(orderId, paymentId, signature = '') {
    try {
      const res = await axios.post('/orders/verify.php', {
        order_id: orderId,
        razorpay_payment_id: paymentId,
        razorpay_signature: signature
      });
      if (res.data.status === 'success') {
        App.showToast('Payment Successful!');
        window.location.href = App.getBaseUrl() + `/pages/order-success.php?order_id=${orderId}`;
      }
    } catch (err) {
      App.showToast(err.response?.data?.message || 'Payment verification failed', 'error');
    }
  }
};
