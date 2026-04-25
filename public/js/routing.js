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
let routes = [
  { id: 'RT-001', doc: 'Staff Evaluation Report – Q1',       origin: 'Human Resources',    waypoint: 'Research Department',  status: 'on-time',  priority: 'low'    },
  { id: 'RT-002', doc: 'User Activity Log Report',            origin: 'IT Department',       waypoint: 'Accounting Department',status: 'delayed',  priority: 'low'    },
  { id: 'RT-003', doc: 'Document Routing Slip DRS-778',       origin: 'Research Department', waypoint: 'Executive Board',      status: 'delayed',  priority: 'high'   },
  { id: 'RT-004', doc: 'Annual Budget Proposal FY2026',       origin: 'Accounting Department',waypoint: 'Executive Board',     status: 'on-time',  priority: 'high'   },
  { id: 'RT-005', doc: 'Procurement Request Form PR-2217',    origin: 'Assets Management',   waypoint: 'Commission on Audit',  status: 'pending',  priority: 'medium' },
  { id: 'RT-006', doc: 'HR Policy Manual 2026 Edition',       origin: 'Human Resources',     waypoint: 'IT Department',        status: 'completed',priority: 'low'    },
  { id: 'RT-007', doc: 'Q1 Budget Utilization Report',        origin: 'Accounting Department',waypoint: 'Research Department', status: 'on-time',  priority: 'medium' },
  { id: 'RT-008', doc: 'COA Compliance Checklist',            origin: 'Commission on Audit', waypoint: 'Executive Board',      status: 'delayed',  priority: 'high'   },
  { id: 'RT-009', doc: 'Internal Audit Summary Q2',           origin: 'Accounting Department',waypoint: 'Human Resources',     status: 'completed',priority: 'medium' },
  { id: 'RT-010', doc: 'Office Supplies Requisition Form',    origin: 'Assets Management',   waypoint: 'Accounting Department',status: 'pending',  priority: 'low'    },
  { id: 'RT-011', doc: 'Research Division Work Plan 2026',    origin: 'Research Department', waypoint: 'Executive Board',      status: 'on-time',  priority: 'medium' },
  { id: 'RT-012', doc: 'Assets Disposal Request Form',        origin: 'Assets Management',   waypoint: 'Commission on Audit',  status: 'pending',  priority: 'high'   },
];

let nextId = 13;
const ROWS_PER_PAGE = 5;
let currentPage = 1;

// ── Filtering ──
function getFiltered() {
  const status   = document.getElementById('statusFilter').value;
  const priority = document.getElementById('priorityFilter').value;
  const search   = document.getElementById('searchInput').value.toLowerCase();

  return routes.filter(r => {
    if (status   && r.status   !== status)   return false;
    if (priority && r.priority !== priority) return false;
    if (search   && !r.doc.toLowerCase().includes(search) && !r.id.toLowerCase().includes(search)) return false;
    return true;
  });
}

// ── Priority icon ──
const upArrow   = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>`;
const downArrow = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>`;
const dashIcon  = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>`;

const priorityIcon = { high: upArrow, medium: dashIcon, low: downArrow };
const priorityLabel = { high: 'High', medium: 'Medium', low: 'Low' };
const statusLabel   = { 'on-time': 'On-time', delayed: 'Delayed', pending: 'Pending', completed: 'Completed' };

const docIconSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>`;

// ── Render ──
function render() {
  const filtered   = getFiltered();
  const totalPages = Math.max(1, Math.ceil(filtered.length / ROWS_PER_PAGE));
  if (currentPage > totalPages) currentPage = totalPages;

  const start = (currentPage - 1) * ROWS_PER_PAGE;
  const page  = filtered.slice(start, start + ROWS_PER_PAGE);

  const tbody = document.getElementById('routingBody');
  if (!page.length) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:40px;color:#64748b;font-size:13px;">No routing records found.</td></tr>`;
  } else {
    tbody.innerHTML = page.map(r => `
      <tr>
        <td><span class="rt-id">${r.id}</span></td>
        <td>
          <div class="doc-name-cell">
            <div class="doc-icon">${docIconSvg}</div>
            <span class="doc-title">${r.doc}</span>
          </div>
        </td>
        <td><span class="dept-origin">${r.origin}</span></td>
        <td><span class="dept-waypoint">${r.waypoint}</span></td>
        <td><span class="status-badge ${r.status}">${statusLabel[r.status]}</span></td>
        <td><span class="priority-badge ${r.priority}">${priorityIcon[r.priority]} ${priorityLabel[r.priority]}</span></td>
        <td><button class="view-btn">View</button></td>
      </tr>`).join('');
  }

  // Pagination info
  const total = filtered.length;
  const from  = total ? start + 1 : 0;
  const to    = Math.min(start + ROWS_PER_PAGE, total);
  document.getElementById('paginationInfo').textContent =
    `Showing ${from} to ${to} of ${total} document routing`;

  // Page numbers
  const pn = document.getElementById('pageNumbers');
  pn.innerHTML = '';
  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement('button');
    btn.className = 'page-num' + (i === currentPage ? ' active' : '');
    btn.textContent = i;
    btn.addEventListener('click', () => { currentPage = i; render(); });
    pn.appendChild(btn);
  }

  document.getElementById('prevBtn').disabled = currentPage === 1;
  document.getElementById('nextBtn').disabled = currentPage === totalPages;
}

// ── Filters ──
document.getElementById('statusFilter').addEventListener('change',   () => { currentPage = 1; render(); });
document.getElementById('priorityFilter').addEventListener('change', () => { currentPage = 1; render(); });
document.getElementById('searchInput').addEventListener('input',     () => { currentPage = 1; render(); });

document.getElementById('clearFilters').addEventListener('click', () => {
  document.getElementById('statusFilter').value   = '';
  document.getElementById('priorityFilter').value = '';
  document.getElementById('searchInput').value    = '';
  currentPage = 1;
  render();
});

// ── Pagination ──
document.getElementById('prevBtn').addEventListener('click', () => { currentPage--; render(); });
document.getElementById('nextBtn').addEventListener('click', () => { currentPage++; render(); });

// ── Modal ──
const overlay   = document.getElementById('modalOverlay');
const openModal  = () => { resetModal(); overlay.classList.add('open'); };
const closeModal = () => overlay.classList.remove('open');

document.getElementById('createBtn').addEventListener('click', openModal);
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('modalCancel').addEventListener('click', closeModal);
overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });

const DEPTS = [
  'Human Resources', 'IT Department', 'Research Department',
  'Accounting Department', 'Executive Board', 'Assets Management', 'Commission on Audit'
];

const MY_DEPT = 'Records Division';

function deptOptions(selected = '', disabled = false) {
  if (disabled) return `<div class="origin-display">${selected}</div>`;
  return `
    <select class="waypoint-select">
      <option value="">Select department</option>
      ${DEPTS.map(d => `<option value="${d}" ${d === selected ? 'selected' : ''}>${d}</option>`).join('')}
    </select>`;
}

let stages = [];

function renderStages() {
  const list = document.getElementById('stagesList');
  list.innerHTML = stages.map((s, i) => `
    <div class="stage-row" data-index="${i}">
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
        ${deptOptions(s.waypoint, false)}
      </div>
      <button class="remove-stage-btn" data-index="${i}" ${stages.length === 1 ? 'disabled style="opacity:.3;cursor:not-allowed"' : ''}>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>`).join('');

  // Waypoint change — auto-fill next stage origin
  list.querySelectorAll('.waypoint-select').forEach((sel, i) => {
    sel.addEventListener('change', () => {
      stages[i].waypoint = sel.value;
      if (stages[i + 1]) {
        stages[i + 1].origin = sel.value;
        renderStages();
      }
    });
  });

  // Remove stage
  list.querySelectorAll('.remove-stage-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const idx = parseInt(btn.dataset.index);
      stages.splice(idx, 1);
      // Re-chain origins
      for (let j = 1; j < stages.length; j++) {
        stages[j].origin = stages[j - 1].waypoint || stages[j].origin;
      }
      renderStages();
    });
  });
  // Disable Add Stage if last waypoint === first origin (route has looped back)
  const firstOrigin  = stages[0].origin;
  const lastWaypoint = stages[stages.length - 1].waypoint;
  const looped = lastWaypoint && lastWaypoint === firstOrigin;
  const addBtn = document.getElementById('addStageBtn');
  addBtn.disabled = looped;
  addBtn.style.opacity = looped ? '.4' : '1';
  addBtn.style.cursor  = looped ? 'not-allowed' : 'pointer';
  addBtn.title = looped ? 'Route has returned to the starting department' : '';
}

function addStage() {
  const prevWaypoint = stages.length ? stages[stages.length - 1].waypoint : MY_DEPT;
  stages.push({ origin: prevWaypoint || MY_DEPT, waypoint: '' });
  renderStages();
}

function resetModal() {
  stages = [{ origin: MY_DEPT, waypoint: '' }];
  document.getElementById('newDocName').value  = '';
  document.getElementById('newPriority').value = 'low';
  renderStages();
}

document.getElementById('addStageBtn').addEventListener('click', addStage);

document.getElementById('modalSubmit').addEventListener('click', () => {
  const doc      = document.getElementById('newDocName').value.trim();
  const priority = document.getElementById('newPriority').value;
  const status   = 'pending';

  if (!doc) { alert('Please enter a document name.'); return; }
  if (stages.some(s => !s.waypoint)) { alert('Please select a waypoint for all stages.'); return; }

  const origin   = stages[0].origin;
  const waypoint = stages[stages.length - 1].waypoint;
  const id = `RT-${String(nextId).padStart(3, '0')}`;
  nextId++;
  routes.unshift({ id, doc, origin, waypoint, status, priority, stages: [...stages] });

  closeModal();
  currentPage = 1;
  render();
});

// ── Initial render ──
render();

// ── Detail panel data ──
const routeDetails = {
  'RT-001': {
    owner: 'Jose Chan', submitted: 'March 17, 2026',
    originAbbr: 'HRD',
    paths: [
      { from: 'Human Resources', to: 'Research',  handler: 'Michael D.',  initials: 'MD', status: 'completed', duration: '30m'    },
      { from: 'Research',        to: 'Accounting', handler: 'Chloe S.',   initials: 'CS', status: 'completed', duration: '2h 45m' },
      { from: 'Accounting',      to: 'Budget',     handler: 'Jolina M.',  initials: 'JM', status: 'completed', duration: '7m'     },
      { from: 'Budget',          to: 'Technical',  handler: 'Juan D.',    initials: 'JD', status: 'active',    duration: '-'      },
    ],
    currentHandler: 'Juan Dela Cruz', currentInitials: 'JD',
  },
  'RT-002': {
    owner: 'Maria Santos', submitted: 'March 18, 2026',
    originAbbr: 'ITD',
    paths: [
      { from: 'IT Department', to: 'Accounting', handler: 'Ana Lim',     initials: 'AL', status: 'completed', duration: '1h 10m' },
      { from: 'Accounting',    to: 'Audit',      handler: 'Roberto R.',  initials: 'RR', status: 'active',    duration: '-'      },
    ],
    currentHandler: 'Roberto Reyes', currentInitials: 'RR',
  },
};

const statusDisplayMap = {
  'on-time':  { label: 'ON-TIME',   cls: 'on-time',   dot: '#1d4ed8' },
  'delayed':  { label: 'DELAYED',   cls: 'delayed',   dot: '#c62828' },
  'pending':  { label: 'PENDING',   cls: 'pending',   dot: '#c2410c' },
  'completed':{ label: 'COMPLETED', cls: 'completed', dot: '#15803d' },
};

// ── Open detail panel ──
function openDetail(routeId) {
  const route  = routes.find(r => r.id === routeId);
  if (!route) return;

  const detail = routeDetails[routeId] || {
    owner: 'Unknown', submitted: 'N/A', originAbbr: route.origin.split(' ').map(w => w[0]).join('').slice(0,3).toUpperCase(),
    paths: [
      { from: route.origin, to: route.waypoint, handler: 'Juan Dela Cruz', initials: 'JD', status: 'active', duration: '-' },
    ],
    currentHandler: 'Juan Dela Cruz', currentInitials: 'JD',
  };

  const sd = statusDisplayMap[route.status];

  document.getElementById('detailRtId').textContent  = route.id;
  document.getElementById('detailH2').textContent    = `Tracking Route ${route.id}`;
  document.getElementById('detailSub').textContent   = `Path for: ${route.doc}`;
  document.getElementById('detailDocId').textContent = `ID: ${route.id}`;
  document.getElementById('detailDocName').textContent = route.doc;
  document.getElementById('detailDocMeta').textContent = `Owner: ${detail.owner} • Submitted: ${detail.submitted}`;
  document.getElementById('detailOrigin').textContent  = detail.originAbbr;

  const statusEl = document.getElementById('detailStatus');
  statusEl.className = `detail-status ${sd.cls}`;
  statusEl.innerHTML = `<span class="detail-status-dot" style="background:${sd.dot}"></span>${sd.label}`;

  // Path table
  document.getElementById('pathBody').innerHTML = detail.paths.map(p => `
    <tr>
      <td><span class="path-label">${p.from} <span class="path-arrow">→</span> ${p.to}</span></td>
      <td>
        <div class="handler-cell">
          <div class="handler-chip">${p.initials}</div>
          ${p.handler}
        </div>
      </td>
      <td><span class="path-status ${p.status}">${p.status.toUpperCase()}</span></td>
      <td><span class="path-duration">${p.duration}</span></td>
    </tr>`).join('');

  document.getElementById('handlerAvatar').textContent = detail.currentInitials;
  document.getElementById('handlerName').textContent   = detail.currentHandler;

  document.getElementById('detailOverlay').classList.add('open');
  document.getElementById('detailPanel').classList.add('open');
}

function closeDetail() {
  document.getElementById('detailOverlay').classList.remove('open');
  document.getElementById('detailPanel').classList.remove('open');
}

document.getElementById('backBtn').addEventListener('click', closeDetail);
document.getElementById('detailOverlay').addEventListener('click', closeDetail);

function handleAction(type) {
  const labels = { received: 'Marked as Received', returned: 'Document Returned', flag: 'Flagged as Missing' };
  alert(labels[type]);
}

// ── Wire view buttons (delegate from tbody) ──
document.getElementById('routingBody').addEventListener('click', e => {
  const btn = e.target.closest('.view-btn');
  if (!btn) return;
  const row = btn.closest('tr');
  const id  = row.querySelector('.rt-id').textContent;
  openDetail(id);
});
