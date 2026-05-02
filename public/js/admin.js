// ── Live clock ──
function updateTime() {
  const now = new Date();
  const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  let h = now.getHours();
  const ampm = h >= 12 ? 'PM' : 'AM';
  h = h % 12 || 12;
  const m = now.getMinutes().toString().padStart(2,'0');
  document.getElementById('datetime').textContent =
    `${days[now.getDay()]} | ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()} | ${h}:${m} ${ampm}`;
}
updateTime();
setInterval(updateTime, 1000);

// ── Search shortcut ──
document.addEventListener('keydown', e => {
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault();
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) searchInput.focus();
  }
});

// ── Tabs ──
document.querySelectorAll('.admin-tab').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.admin-tab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.admin-tab-panel').forEach(p => p.classList.add('hidden'));
    btn.classList.add('active');
    document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
  });
});

// ── Add / Edit Modal ──
const overlay    = document.getElementById('modalOverlay');
const modalTitle = document.getElementById('modalTitle');
const submitBtn  = document.getElementById('modalSubmit');
const userForm   = document.getElementById('userForm');
const methodField = document.getElementById('methodField');

function openAddModal() {
  modalTitle.textContent = 'Add User';
  submitBtn.textContent  = 'Add User';
  userForm.action        = userForm.dataset.storeUrl;
  methodField.innerHTML  = '';
  document.getElementById('editUserId').value = '';
  document.getElementById('fFirstName').value = '';
  document.getElementById('fLastName').value  = '';
  document.getElementById('fEmail').value     = '';
  document.getElementById('fPassword').value  = '';
  document.getElementById('fRole').value      = 'Admin';
  document.getElementById('fDept').value      = 'Executive Committee';
  document.getElementById('fStatus').value    = 'active';
  document.getElementById('passwordGroup').style.display = '';
  document.querySelector('#passwordGroup label').textContent = 'Temporary Password';
  clearFormErrors();
  overlay.classList.add('open');
}

const closeModal = () => overlay.classList.remove('open');

document.getElementById('addUserBtn').addEventListener('click', openAddModal);
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('modalCancel').addEventListener('click', closeModal);
overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });

// Edit — populate modal from data attributes on the Edit button
document.getElementById('adminBody').addEventListener('click', e => {
  const btn = e.target.closest('.action-btn.edit');
  if (!btn) return;

  const id = btn.dataset.id;
  modalTitle.textContent = 'Edit User';
  submitBtn.textContent  = 'Save Changes';
  userForm.action        = `/admin/users/${id}`;
  methodField.innerHTML  = '<input type="hidden" name="_method" value="PUT">';

  document.getElementById('editUserId').value = id;
  document.getElementById('fFirstName').value = btn.dataset.first;
  document.getElementById('fLastName').value  = btn.dataset.last;
  document.getElementById('fEmail').value     = btn.dataset.email;
  document.getElementById('fPassword').value  = '';
  document.getElementById('fRole').value      = btn.dataset.role;
  document.getElementById('fDept').value      = btn.dataset.dept;
  document.getElementById('fStatus').value    = btn.dataset.status;
  document.getElementById('passwordGroup').style.display = '';
  document.querySelector('#passwordGroup label').textContent = 'New Password';
  clearFormErrors();
  overlay.classList.add('open');
});

const fieldErrorMap = {
  first_name: 'errFirstName',
  last_name:  'errLastName',
  email:      'errEmail',
  password:   'errPassword',
};

function clearFormErrors() {
  Object.values(fieldErrorMap).forEach(id => {
    document.getElementById(id).textContent = '';
  });
  ['fFirstName','fLastName','fEmail','fPassword'].forEach(id => {
    document.getElementById(id).classList.remove('error');
  });
}

function showFormErrors(errors) {
  Object.entries(errors).forEach(([field, messages]) => {
    const spanId = fieldErrorMap[field];
    const inputId = { first_name:'fFirstName', last_name:'fLastName', email:'fEmail', password:'fPassword' }[field];
    if (spanId) document.getElementById(spanId).textContent = messages[0];
    if (inputId) document.getElementById(inputId).classList.add('error');
  });
}
userForm.addEventListener('submit', async e => {
  e.preventDefault();
  clearFormErrors();
  submitBtn.disabled = true;
  submitBtn.textContent = 'Saving…';

  const isEdit = modalTitle.textContent === 'Edit User';
  const resetBtn = () => {
    submitBtn.disabled = false;
    submitBtn.textContent = isEdit ? 'Save Changes' : 'Add User';
  };

  try {
    const res = await fetch(userForm.action, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      body: new FormData(userForm),
    });

    if (res.ok) {
      closeModal();
      showToast(isEdit ? 'User updated successfully.' : 'User added successfully.', 'success');
      setTimeout(() => location.reload(), 1200);
    } else if (res.status === 422) {
      const json = await res.json();
      showFormErrors(json.errors);
      resetBtn();
    } else {
      showToast('Something went wrong. Please try again.', 'error');
      resetBtn();
    }
  } catch {
    showToast('Network error. Please try again.', 'error');
    resetBtn();
  }
});

// ── Confirm forms (replace browser confirm()) ──
document.querySelectorAll('.confirm-form').forEach(form => {
  form.addEventListener('submit', e => {
    e.preventDefault();
    showConfirmToast('Are you sure? This cannot be undone.', () => form.submit());
  });
});

function showConfirmToast(message, onConfirm) {
  const container = document.getElementById('toast-container');
  const toast = document.createElement('div');
  toast.className = 'toast warning show';
  toast.style.cssText = 'max-width:380px;gap:12px;flex-direction:column;align-items:flex-start;';
  toast.innerHTML = `
    <div style="display:flex;align-items:center;gap:8px">
      <svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      <span>${message}</span>
    </div>
    <div style="display:flex;gap:8px;align-self:flex-end">
      <button class="confirm-no"  style="padding:5px 14px;border:1px solid #fed7aa;border-radius:5px;background:#fff;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;color:#c2410c">Cancel</button>
      <button class="confirm-yes" style="padding:5px 14px;border:none;border-radius:5px;background:#c2410c;color:#fff;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit">Confirm</button>
    </div>`;
  container.appendChild(toast);
  toast.querySelector('.confirm-yes').addEventListener('click', () => { toast.remove(); onConfirm(); });
  toast.querySelector('.confirm-no').addEventListener('click',  () => { toast.remove(); });
}

// ── Audit Log ──
let auditPage = 1;

async function loadAuditLogs(page = 1) {
  auditPage = page;
  const event  = document.getElementById('auditEventFilter').value;
  const type   = document.getElementById('auditTypeFilter').value;
  const search = document.getElementById('auditSearch').value;

  const params = new URLSearchParams({ page });
  if (event)  params.set('event',  event);
  if (type)   params.set('type',   type);
  if (search) params.set('search', search);

  const res  = await fetch(`/admin/audit-logs?${params}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  const json = await res.json();

  const tbody = document.getElementById('auditBody');
  if (!json.data.length) {
    tbody.innerHTML = '<tr><td colspan="5" class="empty-row">No audit log entries found.</td></tr>';
  } else {
    tbody.innerHTML = json.data.map(l => `
      <tr>
        <td style="white-space:nowrap;font-size:12px;color:var(--muted);text-align:center">${l.timestamp}</td>
        <td style="text-align:center"><span class="audit-event-badge ${l.event}">${l.event.replace(/_/g, ' ')}</span></td>
        <td style="text-align:center"><span class="audit-type-badge">${l.type}</span></td>
        <td style="font-size:12.5px; text-align: left;">${l.description}</td>
        <td style="font-size:12.5px;white-space:nowrap;text-align:center">${l.user}</td>
      </tr>`).join('');
  }

  document.getElementById('auditInfo').textContent =
    json.total ? `Showing page ${json.current_page} of ${json.last_page} (${json.total} entries)` : '';

  const pag = document.getElementById('auditPagination');
  pag.innerHTML = '';
  if (json.last_page > 1) {
    const prev = document.createElement('button');
    prev.className = 'page-btn';
    prev.disabled  = json.current_page === 1;
    prev.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>';
    prev.addEventListener('click', () => loadAuditLogs(auditPage - 1));
    pag.appendChild(prev);

    for (let p = 1; p <= json.last_page; p++) {
      const a = document.createElement('button');
      a.className = 'page-num' + (p === json.current_page ? ' active' : '');
      a.textContent = p;
      a.addEventListener('click', () => loadAuditLogs(p));
      pag.appendChild(a);
    }

    const next = document.createElement('button');
    next.className = 'page-btn';
    next.disabled  = json.current_page === json.last_page;
    next.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>';
    next.addEventListener('click', () => loadAuditLogs(auditPage + 1));
    pag.appendChild(next);
  }
}

document.querySelector('[data-tab="audit"]').addEventListener('click', () => loadAuditLogs(1));

let auditSearchTimer;
document.getElementById('auditSearch').addEventListener('input', () => {
  clearTimeout(auditSearchTimer);
  auditSearchTimer = setTimeout(() => loadAuditLogs(1), 350);
});
document.getElementById('auditEventFilter').addEventListener('change', () => loadAuditLogs(1));
document.getElementById('auditTypeFilter').addEventListener('change',  () => loadAuditLogs(1));


// ── All Requests (Admin role only) ──
if (document.getElementById('allReqBody')) {
  let allReqPage = 1;

  function buildPagination(containerId, currentPage, lastPage, loadFn) {
    const pag = document.getElementById(containerId);
    pag.innerHTML = '';
    if (lastPage <= 1) return;
    const prev = document.createElement('button');
    prev.className = 'page-btn';
    prev.disabled  = currentPage === 1;
    prev.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>';
    prev.addEventListener('click', () => loadFn(currentPage - 1));
    pag.appendChild(prev);
    for (let p = 1; p <= lastPage; p++) {
      const btn = document.createElement('button');
      btn.className = 'page-num' + (p === currentPage ? ' active' : '');
      btn.textContent = p;
      btn.addEventListener('click', () => loadFn(p));
      pag.appendChild(btn);
    }
    const next = document.createElement('button');
    next.className = 'page-btn';
    next.disabled  = currentPage === lastPage;
    next.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>';
    next.addEventListener('click', () => loadFn(currentPage + 1));
    pag.appendChild(next);
  }

  const priorityBadge = p => {
    const cls = { high: 'background:#fee2e2;color:#b91c1c', medium: 'background:#fef9c3;color:#854d0e', low: 'background:#f0fdf4;color:#15803d' };
    return `<span style="display:inline-block;padding:2px 9px;border-radius:4px;font-size:11px;font-weight:700;${cls[p] ?? 'background:#eef1f5;color:#475569'}">${p}</span>`;
  };

  const statusBadge = s => {
    const cls = {
      pending: 'background:#fef9c3;color:#854d0e', 'in-progress': 'background:#e0f2fe;color:#0369a1',
      fulfilled: 'background:#dcfce7;color:#15803d', cancelled: 'background:#fee2e2;color:#b91c1c',
      'on-time': 'background:#dcfce7;color:#15803d', delayed: 'background:#fee2e2;color:#b91c1c',
      returned: 'background:#fef9c3;color:#854d0e', missing: 'background:#fce7f3;color:#9d174d',
      completed: 'background:#ede9fe;color:#5b21b6',
    };
    return `<span style="display:inline-block;padding:2px 9px;border-radius:4px;font-size:11px;font-weight:700;${cls[s] ?? 'background:#eef1f5;color:#475569'}">${s}</span>`;
  };

  async function loadAllRequests(page = 1) {
    allReqPage = page;
    const status   = document.getElementById('allReqStatusFilter').value;
    const priority = document.getElementById('allReqPriorityFilter').value;
    const search   = document.getElementById('allReqSearch').value;
    const params   = new URLSearchParams({ page });
    if (status)   params.set('status',   status);
    if (priority) params.set('priority', priority);
    if (search)   params.set('search',   search);

    const res  = await fetch(`/admin/all-requests?${params}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const json = await res.json();
    const tbody = document.getElementById('allReqBody');

    tbody.innerHTML = json.data.length
      ? json.data.map(r => `<tr>
          <td style="font-size:12px;white-space:nowrap">${r.id}</td>
          <td style="font-size:13px;text-align:left">${r.title}</td>
          <td><span class="dept-badge">${r.department}</span></td>
          <td style="font-size:12.5px">${r.user}</td>
          <td>${priorityBadge(r.priority)}</td>
          <td>${statusBadge(r.status)}</td>
          <td style="font-size:12px;white-space:nowrap;color:var(--muted)">${r.created_at}</td>
        </tr>`).join('')
      : '<tr><td colspan="7" class="empty-row">No requests found.</td></tr>';

    document.getElementById('allReqInfo').textContent =
      json.total ? `Showing page ${json.current_page} of ${json.last_page} (${json.total} entries)` : '';
    buildPagination('allReqPagination', json.current_page, json.last_page, loadAllRequests);
  }

  document.querySelector('[data-tab="all-requests"]').addEventListener('click', () => loadAllRequests(1));
  document.getElementById('allReqStatusFilter').addEventListener('change', () => loadAllRequests(1));
  document.getElementById('allReqPriorityFilter').addEventListener('change', () => loadAllRequests(1));
  let allReqTimer;
  document.getElementById('allReqSearch').addEventListener('input', () => {
    clearTimeout(allReqTimer);
    allReqTimer = setTimeout(() => loadAllRequests(1), 350);
  });
}

// ── All Routes (Admin role only) ──
if (document.getElementById('allRouteBody')) {
  let allRoutePage = 1;

  async function loadAllRoutes(page = 1) {
    allRoutePage = page;
    const status   = document.getElementById('allRouteStatusFilter').value;
    const priority = document.getElementById('allRoutePriorityFilter').value;
    const search   = document.getElementById('allRouteSearch').value;
    const params   = new URLSearchParams({ page });
    if (status)   params.set('status',   status);
    if (priority) params.set('priority', priority);
    if (search)   params.set('search',   search);

    const res  = await fetch(`/admin/all-routes?${params}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const json = await res.json();
    const tbody = document.getElementById('allRouteBody');

    tbody.innerHTML = json.data.length
      ? json.data.map(r => `<tr>
          <td style="font-size:12px;white-space:nowrap">${r.id}</td>
          <td style="font-size:13px;text-align:left">${r.title}</td>
          <td><span class="dept-badge">${r.origin}</span></td>
          <td><span class="dept-badge">${r.waypoint}</span></td>
          <td style="font-size:12.5px">${r.user}</td>
          <td>${priorityBadge(r.priority)}</td>
          <td>${statusBadge(r.status)}</td>
          <td style="font-size:12px;white-space:nowrap;color:var(--muted)">${r.created_at}</td>
        </tr>`).join('')
      : '<tr><td colspan="8" class="empty-row">No routes found.</td></tr>';

    document.getElementById('allRouteInfo').textContent =
      json.total ? `Showing page ${json.current_page} of ${json.last_page} (${json.total} entries)` : '';
    buildPagination('allRoutePagination', json.current_page, json.last_page, loadAllRoutes);
  }

  document.querySelector('[data-tab="all-routes"]').addEventListener('click', () => loadAllRoutes(1));
  document.getElementById('allRouteStatusFilter').addEventListener('change', () => loadAllRoutes(1));
  document.getElementById('allRoutePriorityFilter').addEventListener('change', () => loadAllRoutes(1));
  let allRouteTimer;
  document.getElementById('allRouteSearch').addEventListener('input', () => {
    clearTimeout(allRouteTimer);
    allRouteTimer = setTimeout(() => loadAllRoutes(1), 350);
  });
}
