// ── Live clock ──
function updateTime() {
  const now = new Date();
  const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  const h = now.getHours().toString().padStart(2,'0');
  const m = now.getMinutes().toString().padStart(2,'0');
  document.getElementById('datetime').textContent =
    `${days[now.getDay()]} | ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()} | ${h}:${m}`;
}
updateTime();
setInterval(updateTime, 1000);

// ── Search shortcut ──
document.addEventListener('keydown', e => {
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault();
    document.getElementById('searchInput').focus();
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

// ── Filtering + Pagination (client-side on server-rendered rows) ──
const ROWS_PER_PAGE = 7;
let currentPage = 1;

function getAllRows() {
  return Array.from(document.querySelectorAll('#adminBody tr[data-role]'));
}

function getFilteredRows() {
  const role   = document.getElementById('roleFilter').value;
  const dept   = document.getElementById('deptFilter').value;
  const status = document.getElementById('statusFilter').value;
  const search = document.getElementById('searchInput').value.toLowerCase();

  return getAllRows().filter(row => {
    if (role   && row.dataset.role   !== role)   return false;
    if (dept   && row.dataset.dept   !== dept)   return false;
    if (status && row.dataset.status !== status) return false;
    if (search && !row.dataset.name.includes(search) && !row.dataset.email.includes(search)) return false;
    return true;
  });
}

function renderTable() {
  const filtered   = getFilteredRows();
  const totalPages = Math.max(1, Math.ceil(filtered.length / ROWS_PER_PAGE));
  if (currentPage > totalPages) currentPage = totalPages;

  const start = (currentPage - 1) * ROWS_PER_PAGE;

  getAllRows().forEach(row => row.style.display = 'none');
  filtered.slice(start, start + ROWS_PER_PAGE).forEach(row => row.style.display = '');

  const total = filtered.length;
  const from  = total ? start + 1 : 0;
  const to    = Math.min(start + ROWS_PER_PAGE, total);
  document.getElementById('paginationInfo').textContent =
    `Showing ${from} to ${to} of ${total} users`;

  const pn = document.getElementById('pageNumbers');
  pn.innerHTML = '';
  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement('button');
    btn.className = 'page-num' + (i === currentPage ? ' active' : '');
    btn.textContent = i;
    btn.addEventListener('click', () => { currentPage = i; renderTable(); });
    pn.appendChild(btn);
  }

  document.getElementById('prevBtn').disabled = currentPage === 1;
  document.getElementById('nextBtn').disabled = currentPage === totalPages;
}

['roleFilter','deptFilter','statusFilter'].forEach(id =>
  document.getElementById(id).addEventListener('change', () => { currentPage = 1; renderTable(); })
);
document.getElementById('searchInput').addEventListener('input', () => { currentPage = 1; renderTable(); });
document.getElementById('clearFilters').addEventListener('click', () => {
  document.getElementById('roleFilter').value   = '';
  document.getElementById('deptFilter').value   = '';
  document.getElementById('statusFilter').value = '';
  document.getElementById('searchInput').value  = '';
  currentPage = 1;
  renderTable();
});
document.getElementById('prevBtn').addEventListener('click', () => { currentPage--; renderTable(); });
document.getElementById('nextBtn').addEventListener('click', () => { currentPage++; renderTable(); });

renderTable();

// ── Sidebar breakdowns ──
const ROLE_COLORS = { 'Admin': '#5b21b6', 'Records Officer': '#0369a1', 'Department Head': '#854d0e', 'Staff': '#475569' };
const DEPT_COLORS = ['#1a2e4a','#2E6DA4','#2e7d32','#c62828','#e65100','#0369a1'];

function renderSidebars() {
  const rows  = getAllRows();
  const total = rows.length || 1;

  const roleCounts = {};
  const deptCounts = {};
  rows.forEach(r => {
    roleCounts[r.dataset.role] = (roleCounts[r.dataset.role] || 0) + 1;
    deptCounts[r.dataset.dept] = (deptCounts[r.dataset.dept] || 0) + 1;
  });

  document.getElementById('roleCards').innerHTML = Object.entries(roleCounts).map(([role, count]) => {
    const pct   = Math.round(count / total * 100);
    const color = ROLE_COLORS[role] || '#64748b';
    return `<div class="ft-card">
      <div class="ft-card-top">
        <span class="ft-label"><span class="ft-dot" style="background:${color}"></span>${role}</span>
        <span class="ft-pct">${count}</span>
      </div>
      <div class="ft-bar-track"><div class="ft-bar-fill" style="width:${pct}%;background:${color}"></div></div>
    </div>`;
  }).join('');

  document.getElementById('deptCards').innerHTML = Object.entries(deptCounts).map(([dept, count], i) => {
    const pct   = Math.round(count / total * 100);
    const color = DEPT_COLORS[i % DEPT_COLORS.length];
    return `<div class="ft-card">
      <div class="ft-card-top">
        <span class="ft-label"><span class="ft-dot" style="background:${color}"></span>${dept}</span>
        <span class="ft-pct">${count}</span>
      </div>
      <div class="ft-bar-track"><div class="ft-bar-fill" style="width:${pct}%;background:${color}"></div></div>
    </div>`;
  }).join('');
}

renderSidebars();

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
