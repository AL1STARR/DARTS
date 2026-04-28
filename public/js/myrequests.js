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

// ── Search on enter ──
document.getElementById('searchInput').addEventListener('keydown', e => {
  if (e.key === 'Enter') {
    const url = new URL(window.location.href);
    url.searchParams.set('search', e.target.value);
    url.searchParams.delete('page');
    window.location.href = url.toString();
  }
});

// ── Assign To: load users when department changes ──
document.getElementById('fDept').addEventListener('change', async function () {
  const dept = this.value;
  const assignSelect = document.getElementById('fAssignTo');
  if (!dept) {
    assignSelect.innerHTML = '<option value="">Select department first…</option>';
    assignSelect.disabled = true;
    return;
  }
  assignSelect.innerHTML = '<option value="">Loading…</option>';
  assignSelect.disabled = true;
  try {
    const res = await fetch(`/assigned/department-users?department=${encodeURIComponent(dept)}`);
    const users = await res.json();
    assignSelect.innerHTML = '<option value="">Select person (optional)…</option>' +
      users.map(u => `<option value="${u.id}">${u.first_name} ${u.last_name} — ${u.role}</option>`).join('');
    assignSelect.disabled = false;
  } catch {
    assignSelect.innerHTML = '<option value="">Failed to load users</option>';
  }
});

// ── File attachment ──
let attachedFiles = [];

const dropZone  = document.getElementById('dropZone');
const fileInput = document.getElementById('fAttachments');
const fileList  = document.getElementById('fileList');

document.getElementById('browseLink').addEventListener('click', e => { e.stopPropagation(); fileInput.click(); });
dropZone.addEventListener('click', () => fileInput.click());
dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
  e.preventDefault();
  dropZone.classList.remove('dragover');
  addFiles(Array.from(e.dataTransfer.files));
});
fileInput.addEventListener('change', () => {
  addFiles(Array.from(fileInput.files));
  fileInput.value = '';
});

function addFiles(files) {
  files.forEach(f => {
    if (!attachedFiles.find(x => x.name === f.name && x.size === f.size)) attachedFiles.push(f);
  });
  renderFileList();
}

function renderFileList() {
  const docSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>`;
  fileList.innerHTML = attachedFiles.map((f, i) => `
    <div class="file-item">
      <div class="file-item-name">${docSvg}<span>${f.name}</span></div>
      <span class="file-item-size">${(f.size / 1024).toFixed(1)} KB</span>
      <button class="file-remove-btn" data-index="${i}" type="button">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>`).join('');

  fileList.querySelectorAll('.file-remove-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      attachedFiles.splice(parseInt(btn.dataset.index), 1);
      renderFileList();
    });
  });
}

// ── New Request Modal ──
const overlay    = document.getElementById('modalOverlay');
const submitBtn  = document.getElementById('modalSubmit');
const requestForm = document.getElementById('requestForm');

const openModal  = () => { clearErrors(); resetForm(); overlay.classList.add('open'); };
const closeModal = () => {
  overlay.classList.remove('open');
  requestForm.dataset.action = requestForm.dataset.storeAction;
  delete requestForm.dataset.method;
  document.getElementById('modalSubmit').textContent = 'Submit Request';
  document.querySelector('.modal-header h3').textContent = 'New Request';
};

document.getElementById('newRequestBtn').addEventListener('click', openModal);
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('modalCancel').addEventListener('click', closeModal);
overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });

function resetForm() {
  requestForm.reset();
  attachedFiles = [];
  renderFileList();
  document.getElementById('fDeadline').value = '';
  const assignSelect = document.getElementById('fAssignTo');
  assignSelect.innerHTML = '<option value="">Select department first…</option>';
  assignSelect.disabled = true;
}

function clearErrors() {
  ['errTitle','errCategory','errPriority','errDept','errAssignTo'].forEach(id => {
    document.getElementById(id).textContent = '';
  });
  ['fTitle','fCategory','fPriority','fDept','fAssignTo'].forEach(id => {
    document.getElementById(id)?.classList.remove('error');
  });
}

function showFormErrors(errors) {
  const map = { title: 'errTitle', category: 'errCategory', priority: 'errPriority', department: 'errDept', assigned_to: 'errAssignTo' };
  const inputMap = { title: 'fTitle', category: 'fCategory', priority: 'fPriority', department: 'fDept', assigned_to: 'fAssignTo' };
  Object.entries(errors).forEach(([field, messages]) => {
    if (map[field])      document.getElementById(map[field]).textContent = messages[0];
    if (inputMap[field]) document.getElementById(inputMap[field]).classList.add('error');
  });
}

requestForm.addEventListener('submit', async e => {
  e.preventDefault();
  clearErrors();
  submitBtn.disabled = true;
  submitBtn.textContent = 'Submitting…';

  const isEdit = requestForm.dataset.method === 'PATCH';
  const formData = new FormData(requestForm);
  if (isEdit) formData.append('_method', 'PATCH');
  attachedFiles.forEach(f => formData.append('attachments[]', f));

  try {
    const res = await fetch(requestForm.dataset.action, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      body: formData,
    });

    if (res.ok) {
      closeModal();
      showToast(isEdit ? 'Request updated successfully.' : 'Request submitted successfully.', 'success');
      setTimeout(() => location.reload(), 1200);
    } else if (res.status === 422) {
      const json = await res.json();
      showFormErrors(json.errors);
    } else {
      showToast('Something went wrong. Please try again.', 'error');
    }
  } catch {
    showToast('Network error. Please try again.', 'error');
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = 'Submit Request';
  }
});

// ── Detail Drawer ──
const detailOverlay = document.getElementById('detailOverlay');
const detailDrawer  = document.getElementById('detailDrawer');
let currentDeleteUrl = null;
let currentUpdateUrl = null;
let currentEditData  = null;

const statusDrawerClasses = {
  pending:    'status-badge pending',
  approved:   'status-badge approved',
  rejected:   'status-badge rejected',
  'in-review':'status-badge in-review',
};

const statusLabels = {
  pending: 'Pending', approved: 'Approved', rejected: 'Rejected', 'in-review': 'In Review',
};

document.getElementById('mrBody').addEventListener('click', e => {
  const btn = e.target.closest('.view-btn');
  if (!btn) return;

  const id          = btn.dataset.id;
  const formattedId = btn.dataset.formattedId || `REQ-${String(id).padStart(3,'0')}`;
  const title       = btn.dataset.title;
  const category    = btn.dataset.category;
  const priority    = btn.dataset.priority;
  const status      = btn.dataset.status;
  const dept        = btn.dataset.dept;
  const date        = btn.dataset.date;
  const desc        = btn.dataset.desc || 'No description provided.';
  const attachments = JSON.parse(btn.dataset.attachments || '[]');
  currentDeleteUrl  = btn.dataset.deleteUrl;
  currentUpdateUrl  = btn.dataset.updateUrl;
  currentEditData   = { title: btn.dataset.title, category: btn.dataset.category.toLowerCase(), priority: btn.dataset.priority.toLowerCase(), dept: btn.dataset.dept, desc: btn.dataset.desc, deadline: btn.dataset.deadline };

  // Edit button: only active when pending
  const editBtn = document.getElementById('editRequestBtn');
  const isPending = status === 'pending';
  editBtn.disabled = !isPending;
  editBtn.style.opacity = isPending ? '1' : '.4';
  editBtn.style.cursor  = isPending ? 'pointer' : 'not-allowed';
  editBtn.title = isPending ? '' : 'Cannot edit — request is no longer pending';

  document.getElementById('drawerReqId').textContent  = formattedId;
  document.getElementById('drawerTitle').textContent  = title;
  document.getElementById('drawerSub').textContent    = `Submitted on ${date} · ${dept}`;
  document.getElementById('dInfoId').textContent      = formattedId;
  document.getElementById('dInfoCategory').textContent = category;
  document.getElementById('dInfoPriority').textContent = priority;
  document.getElementById('dInfoAssigned').textContent  = btn.dataset.assigned;
  document.getElementById('dInfoDept').textContent     = dept;
  document.getElementById('dInfoDate').textContent     = date;
  document.getElementById('dInfoDesc').textContent     = desc;

  const statusEl = document.getElementById('drawerStatus');
  statusEl.className   = statusDrawerClasses[status] || 'status-badge pending';
  statusEl.textContent = statusLabels[status] || status;

  const docSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>`;
  const attachEl = document.getElementById('dInfoAttachments');
  attachEl.innerHTML = attachments.length
    ? attachments.map(f => `
        <a class="file-item" href="${f.url}" target="_blank" rel="noopener">
          <div class="file-item-name">${docSvg}<span>${f.name}</span></div>
          <span class="file-item-size">${f.size}</span>
          <svg class="file-view-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </a>`).join('')
    : '<p class="drawer-no-attachments">No attachments.</p>';

  const fulfilledDoc  = btn.dataset.fulfilledDoc;
  const fulfilledUrl  = btn.dataset.fulfilledUrl;

  const fulfilledCard = document.getElementById('fulfilledDocCard');
  const fulfilledEl   = document.getElementById('dInfoFulfilledDoc');
  if (fulfilledDoc && fulfilledUrl) {
    const docSvgF = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>`;
    fulfilledEl.innerHTML = `
      <a class="file-item" href="${fulfilledUrl}" download>
        <div class="file-item-name">${docSvgF}<span>${fulfilledDoc}</span></div>
        <svg class="file-view-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      </a>`;
    fulfilledCard.style.display = 'block';
  } else {
    fulfilledCard.style.display = 'none';
  }

  detailOverlay.classList.add('open');
  detailDrawer.classList.add('open');
});

function closeDrawer() {
  detailOverlay.classList.remove('open');
  detailDrawer.classList.remove('open');
}

document.getElementById('drawerClose').addEventListener('click', closeDrawer);
detailOverlay.addEventListener('click', closeDrawer);

// ── Edit ──
document.getElementById('editRequestBtn').addEventListener('click', () => {
  if (!currentEditData) return;
  closeDrawer();

  // Pre-fill the modal
  document.getElementById('fTitle').value    = currentEditData.title;
  document.getElementById('fCategory').value = currentEditData.category;
  document.getElementById('fPriority').value = currentEditData.priority;
  document.getElementById('fDesc').value     = currentEditData.desc;
  document.getElementById('fDeadline').value = currentEditData.deadline || '';

  // Switch dept and reload assign-to users
  const deptSelect = document.getElementById('fDept');
  deptSelect.value = currentEditData.dept;
  deptSelect.dispatchEvent(new Event('change'));

  // Switch modal to edit mode
  requestForm.dataset.action = currentUpdateUrl;
  requestForm.dataset.method = 'PATCH';
  document.getElementById('modalSubmit').textContent = 'Save Changes';
  document.querySelector('.modal-header h3').textContent = 'Edit Request';
  overlay.classList.add('open');
});

// ── Delete ──
document.getElementById('deleteRequestBtn').addEventListener('click', () => {
  showConfirmToast('Delete this request? This cannot be undone.', async () => {
    try {
      const res = await fetch(currentDeleteUrl, {
        method: 'DELETE',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            || document.querySelector('input[name="_token"]')?.value,
        },
      });
      if (res.ok) {
        closeDrawer();
        showToast('Request deleted.', 'success');
        setTimeout(() => location.reload(), 1200);
      } else {
        showToast('Could not delete request.', 'error');
      }
    } catch {
      showToast('Network error.', 'error');
    }
  });
});

// ── Confirm toast ──
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
  toast.querySelector('.confirm-no').addEventListener('click',  () => toast.remove());
}
