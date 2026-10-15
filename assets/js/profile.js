/**
 * Bagicha 2.0 - Customer Dashboard & Profile Management Module
 */

let profileUser = null;
let profileAddresses = [];
let editingAddressId = null;

document.addEventListener('DOMContentLoaded', async () => {
  let user = App.getUser();
  if (!user || !App.getToken()) {
    window.location.href = App.getBaseUrl() + '/pages/login.php';
    return;
  }

  try {
    const meRes = await axios.get('/auth/me.php');
    if (meRes.data.status === 'success' && meRes.data.data.user) {
      user = meRes.data.data.user;
      App.setUser(user);
    }
  } catch (err) {}

  profileUser = user;
  renderProfile(user);

  await Promise.all([loadProfileAddresses(), loadAccountOverview(), loadPlantDashboard()]);
  renderRecentlyViewedPlants();
});

async function loadPlantDashboard() {
  const careContainer = document.getElementById('careReminders');
  const recommendationsContainer = document.getElementById('personalRecommendations');
  try {
    const res = await axios.get('/customer/dashboard.php');
    if (res.data.status !== 'success') return;
    const { care_items: careItems, recommendations } = res.data.data;
    careContainer.innerHTML = careItems.length ? careItems.map(item => `
      <a href="${App.getBaseUrl()}/pages/product-detail.php?slug=${item.slug}" class="care-reminder-item">
        <img src="${item.main_image}" alt="${item.name}"><div><strong>${item.name}</strong><span><i class="bi bi-droplet"></i> ${item.water_requirement || 'Check soil moisture'} · <i class="bi bi-sun"></i> ${item.light_requirement || 'Bright indirect light'}</span></div><i class="bi bi-chevron-right"></i>
      </a>`).join('') : `<div class="portal-inline-empty"><i class="bi bi-flower1"></i><div><strong>Your care plan will appear here.</strong><p class="mb-0">Place your first plant order to get tailored care tips.</p></div></div>`;
    recommendationsContainer.innerHTML = recommendations.length ? recommendations.map(item => `
      <div class="col-sm-6"><a href="${App.getBaseUrl()}/pages/product-detail.php?slug=${item.slug}" class="recommendation-card"><img src="${item.main_image}" alt="${item.name}"><div><span>${item.category_name}</span><strong>${item.name}</strong><small>${App.formatCurrency(item.sale_price || item.price)}</small></div></a></div>`).join('') : `<div class="col-12"><div class="portal-inline-empty">More tailored picks will appear as your collection grows.</div></div>`;
  } catch (err) {
    if (careContainer) careContainer.innerHTML = `<div class="portal-inline-empty">Care reminders are unavailable right now.</div>`;
    if (recommendationsContainer) recommendationsContainer.innerHTML = '';
  }
}

function renderRecentlyViewedPlants() {
  const container = document.getElementById('recentlyViewedPlants');
  if (!container) return;
  const items = ProductsModule.getRecentlyViewed();
  if (!items.length) {
    container.innerHTML = `<div class="portal-inline-empty"><i class="bi bi-eye"></i><div><strong>Nothing viewed recently.</strong><p class="mb-0">Browse plants and we’ll keep your recent finds here.</p></div></div>`;
    return;
  }
  container.innerHTML = items.map(item => `<a href="${App.getBaseUrl()}/pages/product-detail.php?slug=${item.slug}" class="recent-plant-card"><img src="${item.main_image}" alt="${item.name}"><div><strong>${item.name}</strong><span>${App.formatCurrency(item.sale_price || item.price)}</span></div></a>`).join('');
}

function renderProfile(user) {
  const firstName = (user.full_name || 'Plant parent').trim().split(/\s+/)[0];
  const welcomeName = document.getElementById('welcomeName');
  if (welcomeName) welcomeName.textContent = firstName;
  if (document.getElementById('profName')) document.getElementById('profName').textContent = user.full_name || 'Not added';
  if (document.getElementById('profEmail')) document.getElementById('profEmail').textContent = user.email || 'Not added';
  if (document.getElementById('profPhone')) document.getElementById('profPhone').textContent = user.phone || 'Not added';
  if (document.getElementById('profRole')) document.getElementById('profRole').textContent = user.role || 'customer';
  if (document.getElementById('editProfName')) document.getElementById('editProfName').value = user.full_name || '';
  if (document.getElementById('editProfEmail')) document.getElementById('editProfEmail').value = user.email || '';
  if (document.getElementById('editProfPhone')) document.getElementById('editProfPhone').value = user.phone || '';
}

async function saveProfile(event) {
  event.preventDefault();
  const button = document.getElementById('saveProfileBtn');
  button.disabled = true;
  button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

  try {
    const res = await axios.put('/auth/profile.php', {
      full_name: document.getElementById('editProfName').value,
      phone: document.getElementById('editProfPhone').value
    });
    if (res.data.status === 'success') {
      profileUser = res.data.data.user;
      App.setUser(profileUser);
      App.updateAuthUI();
      renderProfile(profileUser);
      bootstrap.Modal.getOrCreateInstance(document.getElementById('profileEditModal')).hide();
      App.showToast('Your profile has been updated.');
    }
  } catch (err) {
    App.showToast(err.response?.data?.message || 'Unable to update your profile.', 'error');
  } finally {
    button.disabled = false;
    button.innerHTML = '<i class="bi bi-check2 me-1"></i>Save changes';
  }
}

async function loadAccountOverview() {
  try {
    const [ordersRes, wishlistRes] = await Promise.all([axios.get('/orders/index.php'), axios.get('/wishlist/index.php')]);
    if (ordersRes.data.status === 'success' && document.getElementById('ordersCount')) {
      document.getElementById('ordersCount').textContent = ordersRes.data.data.orders.length;
    }
    if (wishlistRes.data.status === 'success' && document.getElementById('wishlistCount')) {
      document.getElementById('wishlistCount').textContent = wishlistRes.data.data.items.length;
    }
  } catch (err) {
    if (document.getElementById('ordersCount')) document.getElementById('ordersCount').textContent = '0';
    if (document.getElementById('wishlistCount')) document.getElementById('wishlistCount').textContent = '0';
  }
}

async function loadProfileAddresses() {
  const container = document.getElementById('profileAddressContainer');
  if (!container) return;
  try {
    const res = await axios.get('/addresses/index.php');
    if (res.data.status === 'success') {
      const addresses = res.data.data.addresses;
      profileAddresses = addresses;
      if (addresses.length === 0) {
        if (document.getElementById('addressCount')) document.getElementById('addressCount').textContent = '0';
        container.innerHTML = `<div class="empty-address-state"><span><i class="bi bi-geo-alt"></i></span><div><strong>No delivery address yet</strong><p class="mb-0">Add one now for a faster checkout next time.</p></div><button class="btn btn-bagicha-outline btn-sm" type="button" onclick="openAddressForm()">Add address</button></div>`;
        return;
      }

      if (document.getElementById('addressCount')) document.getElementById('addressCount').textContent = addresses.length;

      container.innerHTML = addresses.map(a => `
        <div class="saved-address">
          <span class="saved-address-icon"><i class="bi bi-house-door"></i></span>
          <div class="flex-grow-1"><div class="d-flex align-items-center flex-wrap gap-2"><strong>${a.address_line1} ${a.address_line2 || ''}</strong>${Number(a.is_default) === 1 ? '<span class="address-default-badge"><i class="bi bi-check2"></i> Default</span>' : ''}</div><p class="mb-0">${a.city}, ${a.state} · ${a.pincode}</p></div>
          <div class="address-actions">${Number(a.is_default) !== 1 ? `<button class="btn btn-address-action" onclick="setDefaultAddress(${a.id})">Make default</button>` : ''}<button class="btn btn-address-icon" onclick="openAddressFormById(${a.id})" aria-label="Edit this address" title="Edit address"><i class="bi bi-pencil"></i></button><button class="btn btn-address-delete" onclick="deleteAddress(${a.id})" aria-label="Delete this address" title="Delete address"><i class="bi bi-trash3"></i></button></div>
        </div>
      `).join('');
    }
  } catch (err) {
    container.innerHTML = `<div class="alert alert-danger">Failed to load addresses.</div>`;
  }
}

function openAddressFormById(id) {
  openAddressForm(profileAddresses.find(address => Number(address.id) === Number(id)) || null);
}

function openAddressForm(address = null) {
  editingAddressId = address?.id || null;
  document.getElementById('addressModalTitle').textContent = editingAddressId ? 'Edit Shipping Address' : 'Add Shipping Address';
  document.getElementById('saveAddressBtn').textContent = editingAddressId ? 'Save Changes' : 'Save Address';
  document.getElementById('pAddr1').value = address?.address_line1 || '';
  document.getElementById('pAddr2').value = address?.address_line2 || '';
  document.getElementById('pCity').value = address?.city || '';
  document.getElementById('pState').value = address?.state || '';
  document.getElementById('pPincode').value = address?.pincode || '';
  document.getElementById('pAddrDefault').checked = Number(address?.is_default) === 1;
  bootstrap.Modal.getOrCreateInstance(document.getElementById('profileAddrModal')).show();
}

async function saveProfileAddress(e) {
  e.preventDefault();
  const data = {
    address_line1: document.getElementById('pAddr1').value,
    address_line2: document.getElementById('pAddr2').value,
    city: document.getElementById('pCity').value,
    state: document.getElementById('pState').value,
    pincode: document.getElementById('pPincode').value,
    is_default: document.getElementById('pAddrDefault').checked
  };
  const saveButton = document.getElementById('saveAddressBtn');
  const wasEditing = Boolean(editingAddressId);
  saveButton.disabled = true;
  saveButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

  try {
    const res = wasEditing
      ? await axios.put('/addresses/index.php', { ...data, id: editingAddressId })
      : await axios.post('/addresses/index.php', data);
    if (res.data.status === 'success') {
      App.showToast(wasEditing ? 'Address updated!' : 'Address added!');
      const modal = bootstrap.Modal.getInstance(document.getElementById('profileAddrModal'));
      if (modal) modal.hide();
      editingAddressId = null;
      await loadProfileAddresses();
    }
  } catch (err) {
    App.showToast(err.response?.data?.message || 'Failed to save address', 'error');
  } finally {
    saveButton.disabled = false;
    saveButton.textContent = wasEditing ? 'Save Changes' : 'Save Address';
  }
}

async function setDefaultAddress(id) {
  try {
    const res = await axios.patch('/addresses/index.php', { id });
    if (res.data.status === 'success') {
      App.showToast('Default delivery address updated.');
      await loadProfileAddresses();
    }
  } catch (err) {
    App.showToast(err.response?.data?.message || 'Failed to update default address', 'error');
  }
}

async function deleteAddress(id) {
  const confirmed = await App.confirmModal({
    title: 'Delete Address',
    message: 'Are you sure you want to delete this saved delivery address?',
    confirmText: 'Delete',
    confirmClass: 'btn-danger'
  });
  if (confirmed) {
    try {
      await axios.delete(`/addresses/index.php?id=${id}`);
      App.showToast('Address deleted');
      await loadProfileAddresses();
    } catch (err) {
      App.showToast('Failed to delete address', 'error');
    }
  }
}
