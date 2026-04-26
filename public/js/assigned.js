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

// ── Detail Drawer ──
const detailOverlay = document.getElementById('detailOverlay');
const detailDrawer  = document.getElementById('detailDrawer');

let currentStatusUrl   = null;
let currentTransferUrl = null;
let currentDeptUsersUrl = null;
let currentStatus      = null;

const statusClasses = {
  'pending':   'badge-status pending',
  'in-review': 'badge-status in-review',
  'approved':  'badge-status approved',
  'rejected':  'badge-status rejected',
};

const statusLabels = {
  'pending':   'Pending',
  'in-review': 'In Review',
  'approved':  'Approved',
  'rejected':  'Rejected',
};

function renderPrimaryBtn(status) {
  const btn = document.getElementById('mgmtPrimary');
  const rejectBtn = document.getElementById('mgmtReject');

  if (status === 'pending') {
    btn.className = 'mgmt-btn mgmt-received';
    btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg> MARK AS RECEIVED`;
    btn.disabled = false;
    rejectBtn.disabled = false;
    rejectBtn.style.opacity = '1';
  } else if (status === 'in-review') {
    btn.className = 'mgmt-btn mgmt-approved';
    btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> MARK AS APPROVED`;
    btn.disabled = false;
    rejectBtn.disabled = false;
    rejectBtn.style.opacity = '1';
  } else {
    btn.className = 'mgmt-btn mgmt-approved';
    btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> ${statusLabels[status] || status}`;
    btn.disabled = true;
    btn.style.opacity = '.5';
    rejectBtn.disabled = true;
    rejectBtn.style.opacity = '.5';
  }
}

async function loadDepartmentUsers(url, department) {
  const select = document.getElementById('transferSelect');
  select.innerHTML = '<option value="">Loading…</option>';
  try {
    const res = await fetch(`${url}?department=${encodeURIComponent(department)}`);
    const users = await res.json();
    select.innerHTML = '<option value="">Select person to transfer to…</option>' +
      users.map(u => `<option value="${u.id}">${u.first_name} ${u.last_name} — ${u.role}</option>`).join('');
  } catch {
    select.innerHTML = '<option value="">Failed to load users</option>';
  }
}

document.getElementById('assignedBody').addEventListener('click', e => {
  const btn = e.target.closest('.view-btn');
  if (!btn) return;

  const id          = btn.dataset.id;
  const title       = btn.dataset.title;
  const category    = btn.dataset.category;
  const priority    = btn.dataset.priority;
  const status      = btn.dataset.status;
  const dept        = btn.dataset.dept;
  const date        = btn.dataset.date;
  const desc        = btn.dataset.desc || 'No description provided.';
  const requestor   = btn.dataset.requestor;
  const attachments = JSON.parse(btn.dataset.attachments || '[]');

  currentStatusUrl    = btn.dataset.statusUrl;
  currentTransferUrl  = btn.dataset.transferUrl;
  currentDeptUsersUrl = btn.dataset.departmentUsersUrl;
  currentStatus       = status;

  document.getElementById('drawerReqId').textContent    = `REQ-${String(id).padStart(3,'0')}`;
  document.getElementById('drawerTitle').textContent    = title;
  document.getElementById('drawerSub').textContent      = `Submitted on ${date} · ${dept}`;
  document.getElementById('dInfoId').textContent        = `REQ-${String(id).padStart(3,'0')}`;
  document.getElementById('dInfoCategory').textContent  = category;
  document.getElementById('dInfoPriority').textContent  = priority;
  document.getElementById('dInfoRequestor').textContent = requestor;
  document.getElementById('dInfoDept').textContent      = dept;
  document.getElementById('dInfoDate').textContent      = date;
  document.getElementById('dInfoDesc').textContent      = desc;

  const statusEl = document.getElementById('drawerStatus');
  statusEl.className   = statusClasses[status] || 'badge-status pending';
  statusEl.textContent = statusLabels[status] || status;

  const docSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>`;
  const attachEl = document.getElementById('dInfoAttachments');
  attachEl.innerHTML = attachments.length
    ? attachments.map(f => `
        <div class="file-item">
          <div class="file-item-name">${docSvg}<span>${f.name}</span></div>
          <span class="file-item-size">${f.size}</span>
        </div>`).join('')
    : '<p class="drawer-no-attachments">No attachments.</p>';

  renderPrimaryBtn(status);
  loadDepartmentUsers(currentDeptUsersUrl, dept);

  detailOverlay.classList.add('open');
  detailDrawer.classList.add('open');
});

function closeDrawer() {
  detailOverlay.classList.remove('open');
  detailDrawer.classList.remove('open');
}

document.getElementById('drawerClose').addEventListener('click', closeDrawer);
detailOverlay.addEventListener('click', closeDrawer);

// ── Status update helper ──
const csrfToken = () =>
  document.querySelector('meta[name="csrf-token"]')?.content ||
  document.querySelector('input[name="_token"]')?.value;

async function patchStatus(newStatus) {
  const res = await fetch(currentStatusUrl, {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
    },
    body: JSON.stringify({ status: newStatus }),
  });
  return res;
}

// ── Primary action button ──
document.getElementById('mgmtPrimary').addEventListener('click', async () => {
  const nextStatus = currentStatus === 'pending' ? 'in-review' : 'approved';
  try {
    const res = await patchStatus(nextStatus);
    if (res.ok) {
      currentStatus = nextStatus;
      const statusEl = document.getElementById('drawerStatus');
      statusEl.className   = statusClasses[nextStatus];
      statusEl.textContent = statusLabels[nextStatus];
      renderPrimaryBtn(nextStatus);
      showToast(nextStatus === 'in-review' ? 'Marked as In Review.' : 'Request approved.', 'success');
      setTimeout(() => location.reload(), 1200);
    } else {
      showToast('Could not update status.', 'error');
    }
  } catch {
    showToast('Network error.', 'error');
  }
});

// ── Reject button ──
document.getElementById('mgmtReject').addEventListener('click', () => {
  showConfirmToast('Reject this request? This cannot be undone.', async () => {
    try {
      const res = await patchStatus('rejected');
      if (res.ok) {
        currentStatus = 'rejected';
        const statusEl = document.getElementById('drawerStatus');
        statusEl.className   = statusClasses['rejected'];
        statusEl.textContent = statusLabels['rejected'];
        renderPrimaryBtn('rejected');
        showToast('Request rejected.', 'warning');
        setTimeout(() => location.reload(), 1200);
      } else {
        showToast('Could not reject request.', 'error');
      }
    } catch {
      showToast('Network error.', 'error');
    }
  });
});

// ── Transfer button ──
document.getElementById('mgmtTransfer').addEventListener('click', async () => {
  const select = document.getElementById('transferSelect');
  const assignedTo = select.value;
  if (!assignedTo) { showToast('Please select a person to transfer to.', 'warning'); return; }

  showConfirmToast('Transfer this request to the selected person?', async () => {
    try {
      const res = await fetch(currentTransferUrl, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify({ assigned_to: assignedTo }),
      });
      if (res.ok) {
        closeDrawer();
        showToast('Request transferred successfully.', 'success');
        setTimeout(() => location.reload(), 1200);
      } else {
        showToast('Could not transfer request.', 'error');
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
