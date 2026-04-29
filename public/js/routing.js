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

// ── Data ──
let routes = [];
let currentPage = 1;
let lastPage = 1;
let totalRoutes = 0;
const ROWS_PER_PAGE = 5;

const MY_DEPT    = document.body.dataset.userDepartment || '';
const MY_USER_ID = parseInt(document.body.dataset.userId) || 0;

// ── Fetch departments on modal open (lazy) ──
let deptsLoaded = false;
async function fetchDepartments() {
  if (deptsLoaded) return;
  try {
    const res  = await fetch('/routing/departments');
    const data = await res.json();
    DEPTS = data.departments || [];
    deptsLoaded = true;
  } catch (err) {
    console.error('Failed to fetch departments:', err);
    showToast('Failed to load departments.', 'error');
  }
}

// ── Helpers ──
function escapeHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function safeRouteId(id) {
  return /^[A-Z0-9-]+$/.test(String(id)) ? String(id) : '';
}

// ── CSRF ──
function csrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.content : '';
}

// ── Fetch routes ──
async function fetchRoutes() {
  const status   = document.getElementById('statusFilter').value;
  const priority = document.getElementById('priorityFilter').value;
  const search   = document.getElementById('searchInput').value;

  const params = new URLSearchParams();
  params.append('page', currentPage);
  if (status)   params.append('status', status);
  if (priority) params.append('priority', priority);
  if (search)   params.append('search', search);

  try {
    const res = await fetch(`/routing/list?${params.toString()}`);
    const data = await res.json();
    routes = data.data || [];
    currentPage = data.current_page || 1;
    lastPage = data.last_page || 1;
    totalRoutes = data.total || 0;
    render();
  } catch (err) {
    console.error('Failed to fetch routes:', err);
    showToast('Failed to load routing records.', 'error');
  }
}

// ── Priority icon ──
const upArrow   = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>`;
const downArrow = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>`;
const dashIcon  = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>`;

const priorityIcon  = { high: upArrow, medium: dashIcon, low: downArrow };
const priorityLabel = { high: 'High', medium: 'Medium', low: 'Low' };
const statusLabel = { 'on-time': 'On-time', delayed: 'Delayed', pending: 'Pending', returned: 'Returned', missing: 'Missing', completed: 'Completed' };

const docIconSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>`;

// ── Render ──
function render() {
  const tbody = document.getElementById('routingBody');

  if (!routes.length) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:40px;color:#64748b;font-size:13px;">No routing records found.</td></tr>`;
  } else {
    tbody.innerHTML = routes.map(r => `
      <tr>
        <td><span class="rt-id">${escapeHtml(r.id)}</span></td>
        <td>
          <div class="doc-name-cell">
            <div class="doc-icon">${docIconSvg}</div>
            <span class="doc-title">${escapeHtml(r.doc)}</span>
          </div>
        </td>
        <td><span class="dept-origin">${escapeHtml(r.origin)}</span></td>
        <td><span class="dept-waypoint">${escapeHtml(r.waypoint)}</span></td>
        <td><span class="status-badge ${escapeHtml(r.status)}">${statusLabel[r.status] || escapeHtml(r.status)}</span></td>
        <td><span class="priority-badge ${escapeHtml(r.priority)}">${priorityIcon[r.priority]} ${priorityLabel[r.priority] || escapeHtml(r.priority)}</span></td>
        <td><button class="view-btn">View</button></td>
      </tr>`).join('');
  }

  const from = totalRoutes ? ((currentPage - 1) * ROWS_PER_PAGE) + 1 : 0;
  const to   = Math.min(currentPage * ROWS_PER_PAGE, totalRoutes);
  document.getElementById('paginationInfo').textContent =
    `Showing ${from} to ${to} of ${totalRoutes} document routing`;

  const pn = document.getElementById('pageNumbers');
  pn.innerHTML = '';
  for (let i = 1; i <= lastPage; i++) {
    const btn = document.createElement('button');
    btn.className = 'page-num' + (i === currentPage ? ' active' : '');
    btn.textContent = i;
    btn.addEventListener('click', () => { currentPage = i; fetchRoutes(); });
    pn.appendChild(btn);
  }

  document.getElementById('prevBtn').disabled = currentPage === 1;
  document.getElementById('nextBtn').disabled = currentPage === lastPage;
}

// ── Filters ──
document.getElementById('statusFilter').addEventListener('change',   () => { currentPage = 1; fetchRoutes(); });
document.getElementById('priorityFilter').addEventListener('change', () => { currentPage = 1; fetchRoutes(); });
document.getElementById('searchInput').addEventListener('input',     debounce(() => { currentPage = 1; fetchRoutes(); }, 300));

document.getElementById('clearFilters').addEventListener('click', () => {
  document.getElementById('statusFilter').value   = '';
  document.getElementById('priorityFilter').value = '';
  document.getElementById('searchInput').value    = '';
  currentPage = 1;
  fetchRoutes();
});

function debounce(fn, ms) {
  let t;
  return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}

// ── Pagination ──
document.getElementById('prevBtn').addEventListener('click', () => { if (currentPage > 1)        { currentPage--; fetchRoutes(); } });
document.getElementById('nextBtn').addEventListener('click', () => { if (currentPage < lastPage) { currentPage++; fetchRoutes(); } });

// ── Create Modal ──
const overlay    = document.getElementById('modalOverlay');
const openModal  = () => { fetchDepartments().then(() => { resetModal(); overlay.classList.add('open'); }); };
const closeModal = () => overlay.classList.remove('open');

document.getElementById('createBtn').addEventListener('click', openModal);
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('modalCancel').addEventListener('click', closeModal);
overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });

let DEPTS = [];

function deptOptions(selected = '', disabled = false, disableOption = '') {
  if (disabled) return `<div class="origin-display">${selected}</div>`;
  return `
    <select class="waypoint-select">
      <option value="">Select department</option>
      ${DEPTS.map(d => `<option value="${d}" ${d === selected ? 'selected' : ''} ${d === disableOption ? 'disabled' : ''}>${d}</option>`).join('')}
    </select>`;
}

let stages = [];

function renderStages() {
  const list = document.getElementById('stagesList');
  list.innerHTML = stages.map((s, i) => `
    <div class="stage-card" data-index="${i}">
    <div class="stage-row">
      <div class="stage-num">${i + 1}</div>
      <div class="stage-field">
        <label>Origin</label>
        ${deptOptions(s.origin, true)}
      </div>
      <div class="stage-arrow">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
      </div>
      <div class="stage-field">
        <label>Waypoint</label>
        ${deptOptions(s.waypoint, false, s.origin)}
      </div>
      <div class="stage-field">
        <label>Assigned To</label>
        <select class="handler-select" data-index="${i}" ${!s.waypoint ? 'disabled' : ''}>
          <option value="">${!s.waypoint ? 'Select waypoint first' : s.loadingUsers ? 'Loading...' : 'Select person (optional)'}</option>
          ${(s.users || []).map(u => `<option value="${u.id}" ${u.id == s.handler_id ? 'selected' : ''}>${u.first_name} ${u.last_name} — ${u.role}</option>`).join('')}
        </select>
      </div>
      <button class="remove-stage-btn" data-index="${i}" ${stages.length === 1 ? 'disabled style="opacity:.3;cursor:not-allowed"' : ''}>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="stage-instructions-row">
      <label>Request Instructions <span style="color:#ef4444">*</span></label>
      <textarea class="instructions-input" data-index="${i}" rows="2" placeholder="Enter instructions for this stage…">${s.instructions || ''}</textarea>
    </div>
    </div>`).join('');

  // Waypoint change — load users and chain next origin
  list.querySelectorAll('.waypoint-select').forEach((sel, i) => {
    sel.addEventListener('change', async () => {
      stages[i].waypoint   = sel.value;
      stages[i].handler_id = '';
      stages[i].users      = [];
      if (stages[i + 1]) stages[i + 1].origin = sel.value;
      if (sel.value) await loadUsersForStage(i, sel.value);
      renderStages();
    });
  });

  // Handler change
  list.querySelectorAll('.handler-select').forEach(sel => {
    sel.addEventListener('change', () => {
      stages[parseInt(sel.dataset.index)].handler_id = sel.value;
    });
  });

  // Instructions change
  list.querySelectorAll('.instructions-input').forEach(ta => {
    ta.addEventListener('input', () => {
      stages[parseInt(ta.dataset.index)].instructions = ta.value;
    });
  });

  // Remove stage
  list.querySelectorAll('.remove-stage-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      stages.splice(parseInt(btn.dataset.index), 1);
      for (let j = 1; j < stages.length; j++) {
        stages[j].origin = stages[j - 1].waypoint || stages[j].origin;
      }
      renderStages();
    });
  });

  // Disable Add Stage if looped back
  const looped = stages[stages.length - 1].waypoint && stages[stages.length - 1].waypoint === stages[0].origin;
  const addBtn = document.getElementById('addStageBtn');
  addBtn.disabled      = looped;
  addBtn.style.opacity = looped ? '.4' : '1';
  addBtn.style.cursor  = looped ? 'not-allowed' : 'pointer';
  addBtn.title         = looped ? 'Route has returned to the starting department' : '';
}

async function loadUsersForStage(index, department) {
  stages[index].loadingUsers = true;
  try {
    const res = await fetch(`/assigned/department-users?department=${encodeURIComponent(String(department).slice(0, 255))}`);
    stages[index].users = await res.json();
  } catch {
    stages[index].users = [];
  }
  stages[index].loadingUsers = false;
}

function addStage() {
  const prevWaypoint = stages.length ? stages[stages.length - 1].waypoint : MY_DEPT;
  stages.push({ origin: prevWaypoint || MY_DEPT, waypoint: '', handler_id: '', instructions: '', users: [] });
  renderStages();
}

function resetModal() {
  stages = [{ origin: MY_DEPT, waypoint: '', handler_id: '', instructions: '', users: [] }];
  document.getElementById('newDocName').value  = '';
  document.getElementById('newPriority').value = 'low';
  document.getElementById('newDeadline').value = '';
  renderStages();
}

document.getElementById('addStageBtn').addEventListener('click', addStage);

document.getElementById('modalSubmit').addEventListener('click', async () => {
  const doc      = document.getElementById('newDocName').value.trim();
  const priority = document.getElementById('newPriority').value;
  const deadline = document.getElementById('newDeadline').value || null;

  if (!doc) { showToast('Please enter a document name.', 'error'); return; }
  if (stages.some(s => !s.waypoint)) { showToast('Please select a waypoint for all stages.', 'error'); return; }
  if (stages.some(s => !s.instructions || !s.instructions.trim())) { showToast('Please enter instructions for all stages.', 'error'); return; }

  try {
    const res = await fetch('/routing/store', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        title:    doc,
        priority: priority,
        deadline: deadline,
        stages:   stages.map(s => ({ origin: s.origin, waypoint: s.waypoint, handler_id: s.handler_id || null, instructions: s.instructions })),
      }),
    });

    if (!res.ok) {
      const err = await res.json();
      showToast(err.message || 'Failed to create route.', 'error');
      return;
    }

    showToast('Route created successfully.', 'success');
    closeModal();
    currentPage = 1;
    fetchRoutes();
  } catch (err) {
    console.error(err);
    showToast('Failed to create route.', 'error');
  }
});

// ── Detail panel ──
const statusDisplayMap = {
  'on-time':  { label: 'ON-TIME',   cls: 'on-time',   dot: '#1d4ed8' },
  'delayed':  { label: 'DELAYED',   cls: 'delayed',   dot: '#c62828' },
  'pending':  { label: 'PENDING',   cls: 'pending',   dot: '#ca8a04' },
  'returned': { label: 'RETURNED',  cls: 'returned',  dot: '#7c3aed' },
  'missing':  { label: 'MISSING',   cls: 'missing',   dot: '#c2410c' },
  'completed':{ label: 'COMPLETED', cls: 'completed', dot: '#15803d' },
};

let currentDetailRouteId = null;

async function openDetail(routeId) {
  const safeId = safeRouteId(routeId);
  if (!safeId) { showToast('Invalid route ID.', 'error'); return; }
  currentDetailRouteId = safeId;
  try {
    const res = await fetch(`/routing/${safeId}/detail`);
    if (!res.ok) throw new Error('Failed to fetch detail');
    const detail = await res.json();

    const sd = statusDisplayMap[detail.status] || statusDisplayMap['pending'];

    document.getElementById('detailRtId').textContent    = detail.id;
    document.getElementById('detailH2').textContent      = `Tracking Route ${detail.id}`;
    document.getElementById('detailSub').textContent     = `Path for: ${detail.title}`;
    document.getElementById('detailDocId').textContent   = `ID: ${detail.id}`;
    document.getElementById('detailDocName').textContent = detail.title;
    document.getElementById('detailDocMeta').textContent =
      `Owner: ${detail.owner} • Submitted: ${detail.submitted}${detail.deadline ? ' • Deadline: ' + detail.deadline : ''}`;
    document.getElementById('detailOrigin').textContent  = detail.originAbbr;

    const statusEl = document.getElementById('detailStatus');
    statusEl.className = `detail-status ${sd.cls}`;
    statusEl.innerHTML = `<span class="detail-status-dot" style="background:${sd.dot}"></span>${sd.label}`;

    document.getElementById('pathBody').innerHTML = detail.paths.map(p => `
      <tr>
        <td><span class="path-label">${escapeHtml(p.from)} <span class="path-arrow">→</span> ${escapeHtml(p.to)}</span>${p.instructions ? `<div style="font-size:11px;color:#64748b;margin-top:3px">${escapeHtml(p.instructions)}</div>` : ''}</td>
        <td>
          <div class="handler-cell">
            <div class="handler-chip">${escapeHtml(p.initials)}</div>
            ${escapeHtml(p.handler)}
          </div>
        </td>
        <td><span class="path-status ${escapeHtml(p.status)}">${escapeHtml(p.status.toUpperCase())}</span></td>
        <td><span class="path-duration">${escapeHtml(p.duration)}</span></td>
      </tr>`).join('');

    document.getElementById('handlerAvatar').textContent = detail.currentInitials;
    document.getElementById('handlerName').textContent   = detail.currentHandler;

    // Enable management actions based on handler assignment
    const isCompleted = detail.status === 'completed';
    const isReturned  = detail.status === 'returned';
    const isMissing   = detail.status === 'missing';
    const isLocked    = isCompleted || isReturned || isMissing;
    const hasHandler  = !!detail.activeHandlerId;
    const canAct = !isLocked && detail.activeWaypoint && (
      hasHandler
        ? MY_USER_ID === detail.activeHandlerId
        : MY_DEPT === detail.activeWaypoint
    );
    // All action buttons except republish/delete follow canAct
    document.querySelectorAll('.mgmt-btn:not(.republish):not(.delete):not(.accomplished)').forEach(btn => {
      btn.disabled      = !canAct;
      btn.style.opacity = canAct ? '1' : '0.35';
      btn.style.cursor  = canAct ? 'pointer' : 'not-allowed';
    });
    // ACCOMPLISHED only enabled after RECEIVED has been marked on the active stage
    const accomplishedBtn = document.querySelector('.mgmt-btn.accomplished');
    const canAccomplish   = canAct && detail.activeStageReceived;
    accomplishedBtn.disabled      = !canAccomplish;
    accomplishedBtn.style.opacity = canAccomplish ? '1' : '0.35';
    accomplishedBtn.style.cursor  = canAccomplish ? 'pointer' : 'not-allowed';

    // Remarks block (returned) + missing notice + republish
    const remarksBlock = document.getElementById('mgmtRemarks');
    const republishBtn = document.getElementById('republishBtn');
    const canRepublish = MY_DEPT === detail.activeStageOrigin;

    if (isReturned) {
      document.getElementById('mgmtRemarksText').textContent = detail.remarks || '';
      remarksBlock.style.display = 'block';
      republishBtn.style.display = canRepublish ? 'flex' : 'none';
      republishBtn.disabled      = !canRepublish;
    } else if (isMissing) {
      document.getElementById('mgmtRemarksText').textContent = 'This document has been flagged as missing.';
      remarksBlock.style.display = 'block';
      republishBtn.style.display = canRepublish ? 'flex' : 'none';
      republishBtn.disabled      = !canRepublish;
    } else {
      remarksBlock.style.display = 'none';
      republishBtn.style.display = 'none';
    }

    // Delete button — only the route owner, not while actively circulating
    const deleteBtn = document.getElementById('deleteRouteBtn');
    const canDelete = MY_USER_ID === detail.ownerId && detail.status !== 'on-time';
    deleteBtn.style.display = canDelete ? 'flex' : 'none';

    document.getElementById('detailOverlay').classList.add('open');
    document.getElementById('detailPanel').classList.add('open');
  } catch (err) {
    console.error(err);
    showToast('Failed to load route details.', 'error');
  }
}

function closeDetail() {
  currentDetailRouteId = null;
  document.getElementById('detailOverlay').classList.remove('open');
  document.getElementById('detailPanel').classList.remove('open');
}

document.getElementById('backBtn').addEventListener('click', closeDetail);
document.getElementById('detailOverlay').addEventListener('click', closeDetail);
document.getElementById('republishBtn').addEventListener('click', handleRepublish);
document.getElementById('deleteRouteBtn').addEventListener('click', () => {
  if (!currentDetailRouteId) return;
  showConfirmToast('Delete this route? This cannot be undone.', async () => {
    try {
      const res = await fetch(`/routing/${safeRouteId(currentDetailRouteId)}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
      });
      if (!res.ok) {
        const err = await res.json();
        showToast(err.message || 'Failed to delete route.', 'error');
        return;
      }
      showToast('Route deleted successfully.', 'success');
      closeDetail();
      fetchRoutes();
    } catch (err) {
      console.error(err);
      showToast('Failed to delete route.', 'error');
    }
  });
});

// ── Remarks prompt ──
const remarksOverlay = document.getElementById('remarksOverlay');
let pendingReturnRouteId = null;

function openRemarksPrompt(routeId) {
  pendingReturnRouteId = routeId;
  document.getElementById('remarksInput').value = '';
  remarksOverlay.classList.add('open');
}

function closeRemarksPrompt() {
  remarksOverlay.classList.remove('open');
  pendingReturnRouteId = null;
}

document.getElementById('remarksCancelBtn').addEventListener('click', closeRemarksPrompt);
remarksOverlay.addEventListener('click', e => { if (e.target === remarksOverlay) closeRemarksPrompt(); });

document.getElementById('remarksConfirmBtn').addEventListener('click', async () => {
  const remarks = document.getElementById('remarksInput').value.trim();
  if (!remarks) { showToast('Please enter remarks before confirming.', 'error'); return; }
  const routeId = pendingReturnRouteId;
  closeRemarksPrompt();
  await submitAction('returned', routeId, remarks);
});

// ── Handle action ──
async function handleAction(type) {
  if (!currentDetailRouteId) return;
  if (type === 'returned') {
    openRemarksPrompt(currentDetailRouteId);
    return;
  }
  await submitAction(type, currentDetailRouteId, null);
}

async function submitAction(type, routeId, remarks) {
  try {
    const res = await fetch(`/routing/${safeRouteId(currentDetailRouteId)}/status`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
        'Accept': 'application/json',
      },
      body: JSON.stringify({ action: type, remarks: remarks }),
    });

    if (!res.ok) {
      const err = await res.json();
      showToast(err.message || 'Action failed.', 'error');
      return;
    }

    const labels = { received: 'Marked as Received', accomplished: 'Stage Accomplished', returned: 'Document Returned', flag: 'Flagged as Missing' };
    const types  = { received: 'success', accomplished: 'success', returned: 'info', flag: 'warning' };
    showToast(labels[type], types[type]);

    openDetail(routeId);
    fetchRoutes();
  } catch (err) {
    console.error(err);
    showToast('Action failed.', 'error');
  }
}

async function handleRepublish() {
  if (!currentDetailRouteId) return;
  try {
    const res = await fetch(`/routing/${safeRouteId(currentDetailRouteId)}/republish`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
      body: JSON.stringify({}),
    });
    if (!res.ok) {
      const err = await res.json();
      showToast(err.message || 'Failed to republish.', 'error');
      return;
    }
    showToast('Route republished successfully.', 'success');
    openDetail(currentDetailRouteId);
    fetchRoutes();
  } catch (err) {
    console.error(err);
    showToast('Failed to republish.', 'error');
  }
}

// ── Confirm toast ──
function showConfirmToast(message, onConfirm) {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }
  const toast = document.createElement('div');
  toast.className = 'toast warning show';
  toast.style.cssText = 'max-width:380px;gap:12px;flex-direction:column;align-items:flex-start;pointer-events:all;';

  const msgRow = document.createElement('div');
  msgRow.style.cssText = 'display:flex;align-items:center;gap:8px';
  msgRow.innerHTML = `<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`;
  const msgText = document.createElement('span');
  msgText.textContent = message;
  msgRow.appendChild(msgText);

  const btnRow = document.createElement('div');
  btnRow.style.cssText = 'display:flex;gap:8px;align-self:flex-end';

  const noBtn = document.createElement('button');
  noBtn.textContent = 'Cancel';
  noBtn.style.cssText = 'padding:5px 14px;border:1px solid #fed7aa;border-radius:5px;background:#fff;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;color:#c2410c';

  const yesBtn = document.createElement('button');
  yesBtn.textContent = 'Confirm';
  yesBtn.style.cssText = 'padding:5px 14px;border:none;border-radius:5px;background:#c2410c;color:#fff;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit';

  btnRow.appendChild(noBtn);
  btnRow.appendChild(yesBtn);
  toast.appendChild(msgRow);
  toast.appendChild(btnRow);
  container.appendChild(toast);

  yesBtn.addEventListener('click', () => { toast.remove(); onConfirm(); });
  noBtn.addEventListener('click',  () => toast.remove());
}

// ── Wire view buttons ──
document.getElementById('routingBody').addEventListener('click', e => {
  const btn = e.target.closest('.view-btn');
  if (!btn) return;
  openDetail(btn.closest('tr').querySelector('.rt-id').textContent);
});

// ── Initial load ──
fetchRoutes();
