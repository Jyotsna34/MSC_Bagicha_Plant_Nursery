<?php
/**
 * Bagicha 2.0 - Admin Support & Inquiries Management Page
 */
require_once __DIR__ . '/../api/helpers/auth.php';
requireAdminPage();

$adminTitle = "Support Inquiries - Bagicha Admin";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-content">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h1 class="font-heading h2 text-primary mb-1">Customer Support Inquiries</h1>
      <p class="text-muted small mb-0">Manage incoming inquiries, order delivery questions, and replacement claims.</p>
    </div>
    <div class="d-flex gap-2">
      <select id="messageStatusFilter" class="form-select form-select-sm" onchange="loadMessages()" style="width: 160px;">
        <option value="">All Statuses</option>
        <option value="unread" selected>Unread Only</option>
        <option value="replied">Replied</option>
        <option value="archived">Archived</option>
      </select>
    </div>
  </div>

  <div class="admin-card p-4">
    <div class="table-responsive">
      <table class="table table-admin-custom align-middle mb-0">
        <thead class="small">
          <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Category</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="adminMessagesTable">
          <tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-success"></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- View Message Modal -->
<div class="modal fade" id="viewMessageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-bottom p-3">
        <h5 class="modal-title font-heading text-primary" id="viewMsgSubject">Inquiry Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <strong id="viewMsgName" class="d-block text-dark"></strong>
            <small id="viewMsgEmail" class="text-muted"></small>
            <small id="viewMsgPhone" class="text-muted ms-2"></small>
          </div>
          <span id="viewMsgCategory" class="badge bg-success-subtle text-success"></span>
        </div>
        <div class="p-3 bg-light rounded-3 mb-3" style="max-height: 240px; overflow-y: auto;">
          <p id="viewMsgBody" class="small mb-0 text-dark" style="white-space: pre-wrap; line-height: 1.6;"></p>
        </div>
        <div class="d-flex justify-content-between align-items-center pt-2">
          <a id="viewMsgReplyMailto" href="#" class="btn btn-bagicha-primary btn-sm">
            <i class="bi bi-envelope-reply me-1"></i> Reply via Email
          </a>
          <button type="button" id="viewMsgMarkRepliedBtn" class="btn btn-outline-success btn-sm" onclick="markCurrentAsReplied()">
            <i class="bi bi-check2-circle me-1"></i> Mark as Replied
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let currentViewingMsgId = 0;
let messagesList = [];

async function loadMessages() {
  const table = document.getElementById('adminMessagesTable');
  const status = document.getElementById('messageStatusFilter').value;
  table.innerHTML = '<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-success"></div></td></tr>';

  try {
    const res = await axios.get(`${App.getBaseUrl()}/api/contact/index.php?status=${encodeURIComponent(status)}`);
    messagesList = res.data.data.messages || [];
    renderMessagesTable(messagesList);
  } catch (err) {
    table.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Failed to load inquiries.</td></tr>';
  }
}

function renderMessagesTable(list) {
  const table = document.getElementById('adminMessagesTable');
  if (!list || list.length === 0) {
    table.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No inquiries found matching criteria.</td></tr>';
    return;
  }

  table.innerHTML = list.map(m => {
    let catBadge = 'bg-secondary';
    if (m.category === 'care-guidance') catBadge = 'bg-success';
    else if (m.category === 'replacement') catBadge = 'bg-danger';
    else if (m.category === 'order-query') catBadge = 'bg-primary';
    else if (m.category === 'corporate-gifting') catBadge = 'bg-warning text-dark';

    let statusBadge = m.status === 'unread' ? '<span class="badge bg-danger">Unread</span>' : (m.status === 'replied' ? '<span class="badge bg-success">Replied</span>' : '<span class="badge bg-secondary">Archived</span>');

    return `
      <tr>
        <td>#${m.id}</td>
        <td class="small text-muted">${new Date(m.created_at).toLocaleDateString()}</td>
        <td>
          <strong class="d-block text-dark small">${m.name}</strong>
          <small class="text-muted">${m.email}</small>
        </td>
        <td><span class="badge ${catBadge}">${m.category}</span></td>
        <td class="text-truncate" style="max-width: 200px;">${m.subject}</td>
        <td>${statusBadge}</td>
        <td>
          <button class="btn btn-sm btn-outline-primary p-1 px-2" onclick="openViewModal(${m.id})" title="View Inquiry"><i class="bi bi-eye"></i></button>
          <button class="btn btn-sm btn-outline-danger p-1 px-2" onclick="deleteMessage(${m.id})" title="Delete"><i class="bi bi-trash"></i></button>
        </td>
      </tr>
    `;
  }).join('');
}

function openViewModal(id) {
  const m = messagesList.find(item => item.id == id);
  if (!m) return;
  currentViewingMsgId = m.id;

  document.getElementById('viewMsgSubject').textContent = m.subject;
  document.getElementById('viewMsgName').textContent = m.name;
  document.getElementById('viewMsgEmail').textContent = m.email;
  document.getElementById('viewMsgPhone').textContent = m.phone ? `• ${m.phone}` : '';
  document.getElementById('viewMsgCategory').textContent = m.category;
  document.getElementById('viewMsgBody').textContent = m.message;
  document.getElementById('viewMsgReplyMailto').href = `mailto:${m.email}?subject=Re: ${encodeURIComponent(m.subject)}`;

  const modal = new bootstrap.Modal(document.getElementById('viewMessageModal'));
  modal.show();
}

async function markCurrentAsReplied() {
  if (!currentViewingMsgId) return;
  try {
    await axios.post(`${App.getBaseUrl()}/api/contact/index.php`, {
      action: 'update_status',
      id: currentViewingMsgId,
      status: 'replied'
    });
    bootstrap.Modal.getInstance(document.getElementById('viewMessageModal')).hide();
    App.showToast('Inquiry marked as replied');
    loadMessages();
  } catch (e) {
    App.showToast('Failed to update status', 'error');
  }
}

async function deleteMessage(id) {
  const ok = await App.confirmModal({ title: 'Delete Inquiry', message: 'Are you sure you want to delete this message?' });
  if (!ok) return;
  try {
    await axios.delete(`${App.getBaseUrl()}/api/contact/index.php?id=${id}`);
    App.showToast('Inquiry deleted');
    loadMessages();
  } catch (e) {
    App.showToast('Failed to delete inquiry', 'error');
  }
}

document.addEventListener('DOMContentLoaded', loadMessages);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
