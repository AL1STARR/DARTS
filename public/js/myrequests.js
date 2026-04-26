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
let requests = [
  { id: 'REQ-001', title: 'Q4 Financial Report 2026',                   category: 'memorandum', priority: 'high',   status: 'approved',  dept: 'Finance',             date: 'Apr 10, 2026', desc: 'Quarterly financial summary for the fourth quarter of 2026.' },
  { id: 'REQ-002', title: 'Organizational Accomplishment Report',        category: 'letters',    priority: 'medium', status: 'in-review', dept: 'Executive Committee', date: 'Apr 12, 2026', desc: 'Annual accomplishment report for all departments.' },
  { id: 'REQ-003', title: 'Audit Committee Meeting Minutes – March',     category: 'minutes',    priority: 'low',    status: 'pending',   dept: 'Audit',               date: 'Apr 14, 2026', desc: 'Minutes from the March audit committee meeting.' },
  { id: 'REQ-004', title: 'Notice of General Assembly',                  category: 'notice',     priority: 'high',   status: 'approved',  dept: 'Secretariat',         date: 'Apr 15, 2026', desc: 'Official notice for the upcoming general assembly.' },
  { id: 'REQ-005', title: 'Budget Utilization Report Q1',                category: 'memorandum', priority: 'medium', status: 'rejected',  dept: 'Finance',             date: 'Apr 16, 2026', desc: 'First quarter budget utilization summary.' },
  { id: 'REQ-006', title: 'Internal Affairs Division Work Plan',         category: 'letters',    priority: 'low',    status: 'pending',   dept: 'Internal Affairs',    date: 'Apr 17, 2026', desc: 'Annual work plan for the Internal Affairs Division.' },
  { id: 'REQ-007', title: 'External Affairs Correspondence Log',         category: 'letters',    priority: 'medium', status: 'in-review', dept: 'External Affairs',    date: 'Apr 18, 2026', desc: 'Log of all external correspondence for the quarter.' },
  { id: 'REQ-008', title: 'Secretariat Committee Meeting Notice',        category: 'notice',     priority: 'low',    status: 'approved',  dept: 'Secretariat',         date: 'Apr 19, 2026', desc: 'Notice for the upcoming secretariat committee meeting.' },
  { id: 'REQ-009', title: 'Finance Division Compliance Memorandum',      category: 'memorandum', priority: 'high',   status: 'pending',   dept: 'Finance',             date: 'Apr 20, 2026', desc: 'Compliance memorandum for finance division staff.' },
  { id: 'REQ-010', title: 'Audit Findings Response Letter',              category: 'letters',    priority: 'high',   status: 'rejected',  dept: 'Audit',               date: 'Apr 21, 2026', desc: 'Formal response to the latest audit findings.' },
];

let nextId = 11;
const ROWS_PER_PAGE = 5;
let currentPage = 1;

// ── Helpers ──
const categoryLabel = { letters: 'Letters', memorandum: 'Memorandum', minutes: 'Minutes of the Meeting', notice: 'Notice of the Meeting' };
const priorityLabel  = { high: 'High', medium: 'Medium', low: 'Low' };
const statusLabel    = { pending: 'Pending', approved: 'Approved', rejected: 'Rejected', 'in-review': 'In Review' };

const upArrow   = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>`;
const dashIcon  = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>`;
const downArrow = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>`;
const priorityIcon = { high: upArrow, medium: dashIcon, low: downArrow };

const docIconSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>`;

// ── Filtering ──
function getFiltered() {
  const status   = document.getElementById('statusFilter').value;
  const priority = document.getElementById('priorityFilter').value;
  const category = document.getElementById('categoryFilter').value;
  const search   = document.getElementById('searchInput').value.toLowerCase();

  return requests.filter(r => {
    if (status   && r.status   !== status)   return false;
    if (priority && r.priority !== priority) return false;
    if (category && r.category !== category) return false;
    if (search   && !r.title.toLowerCase().includes(search) && !r.id.toLowerCase().includes(search)) return false;
    return true;
  });
}

// ── Render ──
function render() {
  const filtered   = getFiltered();
  const totalPages = Math.max(1, Math.ceil(filtered.length / ROWS_PER_PAGE));
  if (currentPage > totalPages) currentPage = totalPages;

  const start = (currentPage - 1) * ROWS_PER_PAGE;
  const page  = filtered.slice(start, start + ROWS_PER_PAGE);

  const tbody = document.getElementById('mrBody');
  tbody.innerHTML = page.length
    ? page.map(r => `
      <tr>
        <td><span class="mr-req-id">${r.id}</span></td>
        <td>
          <div class="mr-doc-cell">
            <div class="mr-doc-icon">${docIconSvg}</div>
            <span class="mr-doc-title">${r.title}</span>
          </div>
        </td>
        <td><span class="category-badge">${categoryLabel[r.category] || r.category}</span></td>
        <td><span class="priority-badge ${r.priority}">${priorityIcon[r.priority]} ${priorityLabel[r.priority]}</span></td>
        <td><span class="status-badge ${r.status}">${statusLabel[r.status]}</span></td>
        <td>${r.date}</td>
        <td><button class="view-btn" data-id="${r.id}">View</button></td>
      </tr>`).join('')
    : `<tr><td colspan="7" class="empty-row">No requests found.</td></tr>`;

  // Pagination info
  const total = filtered.length;
  const from  = total ? start + 1 : 0;
  const to    = Math.min(start + ROWS_PER_PAGE, total);
  document.getElementById('paginationInfo').textContent =
    `Showing ${from} to ${to} of ${total} request${total !== 1 ? 's' : ''}`;

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
['statusFilter','priorityFilter','categoryFilter'].forEach(id =>
  document.getElementById(id).addEventListener('change', () => { currentPage = 1; render(); })
);
document.getElementById('searchInput').addEventListener('input', () => { currentPage = 1; render(); });
document.getElementById('clearFilters').addEventListener('click', () => {
  document.getElementById('statusFilter').value   = '';
  document.getElementById('priorityFilter').value = '';
  document.getElementById('categoryFilter').value = '';
  document.getElementById('searchInput').value    = '';
  currentPage = 1;
  render();
});

document.getElementById('prevBtn').addEventListener('click', () => { currentPage--; render(); });
document.getElementById('nextBtn').addEventListener('click', () => { currentPage++; render(); });

// ── File attachment ──
let attachedFiles = [];

const dropZone    = document.getElementById('dropZone');
const fileInput   = document.getElementById('fAttachments');
const fileList    = document.getElementById('fileList');

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
    if (!attachedFiles.find(x => x.name === f.name && x.size === f.size)) {
      attachedFiles.push(f);
    }
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
const overlay   = document.getElementById('modalOverlay');
const openModal  = () => { clearErrors(); resetForm(); overlay.classList.add('open'); };
const closeModal = () => overlay.classList.remove('open');

document.getElementById('newRequestBtn').addEventListener('click', openModal);
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('modalCancel').addEventListener('click', closeModal);
overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });

function resetForm() {
  ['fTitle','fCategory','fPriority','fDept','fDesc'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = '';
  });
  attachedFiles = [];
  renderFileList();
}

function clearErrors() {
  ['errTitle','errCategory','errPriority','errDept'].forEach(id => {
    document.getElementById(id).textContent = '';
  });
  ['fTitle','fCategory','fPriority','fDept'].forEach(id => {
    document.getElementById(id)?.classList.remove('error');
  });
}

document.getElementById('modalSubmit').addEventListener('click', () => {
  clearErrors();
  const title    = document.getElementById('fTitle').value.trim();
  const category = document.getElementById('fCategory').value;
  const priority = document.getElementById('fPriority').value;
  const dept     = document.getElementById('fDept').value;
  const desc     = document.getElementById('fDesc').value.trim();

  let valid = true;
  if (!title)    { document.getElementById('errTitle').textContent    = 'Document title is required.';  document.getElementById('fTitle').classList.add('error');    valid = false; }
  if (!category) { document.getElementById('errCategory').textContent = 'Please select a category.';    document.getElementById('fCategory').classList.add('error'); valid = false; }
  if (!priority) { document.getElementById('errPriority').textContent = 'Please select a priority.';    document.getElementById('fPriority').classList.add('error'); valid = false; }
  if (!dept)     { document.getElementById('errDept').textContent     = 'Please select a department.';  document.getElementById('fDept').classList.add('error');     valid = false; }
  if (!valid) return;

  const now    = new Date();
  const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  const id     = `REQ-${String(nextId).padStart(3,'0')}`;
  nextId++;

  requests.unshift({ id, title, category, priority, status: 'pending', dept,
    date: `${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}`, desc: desc || '—' });

  closeModal();
  currentPage = 1;
  render();
  showToast(`Request ${id} submitted successfully.`, 'success');
});

// ── Detail Drawer ──
const detailOverlay = document.getElementById('detailOverlay');
const detailDrawer  = document.getElementById('detailDrawer');

const statusDrawerClasses = {
  pending:   'status-badge pending',
  approved:  'status-badge approved',
  rejected:  'status-badge rejected',
  'in-review': 'status-badge in-review',
};

function openDrawer(id) {
  const r = requests.find(r => r.id === id);
  if (!r) return;

  document.getElementById('drawerReqId').textContent  = r.id;
  document.getElementById('drawerTitle').textContent  = r.title;
  document.getElementById('drawerSub').textContent    = `Submitted on ${r.date} · ${r.dept}`;
  document.getElementById('dInfoId').textContent       = r.id;
  document.getElementById('dInfoCategory').textContent = categoryLabel[r.category] || r.category;
  document.getElementById('dInfoPriority').textContent = priorityLabel[r.priority];
  document.getElementById('dInfoDept').textContent     = r.dept;
  document.getElementById('dInfoDate').textContent     = r.date;
  document.getElementById('dInfoBy').textContent       = 'You';
  document.getElementById('dInfoDesc').textContent     = r.desc || '—';

  const statusEl = document.getElementById('drawerStatus');
  statusEl.className = statusDrawerClasses[r.status] || 'status-badge pending';
  statusEl.textContent = statusLabel[r.status];

  detailOverlay.classList.add('open');
  detailDrawer.classList.add('open');
}

function closeDrawer() {
  detailOverlay.classList.remove('open');
  detailDrawer.classList.remove('open');
}

document.getElementById('drawerClose').addEventListener('click', closeDrawer);
detailOverlay.addEventListener('click', closeDrawer);

document.getElementById('mrBody').addEventListener('click', e => {
  const btn = e.target.closest('.view-btn');
  if (!btn) return;
  openDrawer(btn.dataset.id);
});

// ── Initial render ──
render();
