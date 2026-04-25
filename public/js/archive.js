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

// ── Archive data ──
const allDocuments = [
  { id: '026-0005', title: 'Internal Audit Summary Q2',             ext: 'pdf',  dept: 'Audit Committee',     deptKey: 'audit',      category: 'letter',     uploaded: 'March 30, 2026', tab: 'general' },
  { id: '026-0004', title: 'Resource Allocation Plan',              ext: 'docs', dept: 'Research Department', deptKey: 'research',   category: 'memorandum', uploaded: 'March 30, 2026', tab: 'general' },
  { id: '026-0003', title: 'Project Orion Proposal',                ext: 'docs', dept: 'Research Department', deptKey: 'research',   category: 'memorandum', uploaded: 'March 30, 2026', tab: 'general' },
  { id: '026-0002', title: 'Strategic Planning Brief',              ext: 'docs', dept: 'Research Department', deptKey: 'research',   category: 'memorandum', uploaded: 'March 30, 2026', tab: 'general' },
  { id: '026-0001', title: 'Procurement Request Form PR-2217',      ext: 'pdf',  dept: 'Assets Management',   deptKey: 'assets',     category: 'letter',     uploaded: 'March 30, 2026', tab: 'general' },
  { id: '026-0010', title: 'Q1 Budget Utilization Report',          ext: 'xlsx', dept: 'Accounting',          deptKey: 'accounting', category: 'minutes',    uploaded: 'March 28, 2026', tab: 'general' },
  { id: '026-0009', title: 'Annual Inventory Report 2025',          ext: 'pdf',  dept: 'Assets Management',   deptKey: 'assets',     category: 'letter',     uploaded: 'March 27, 2026', tab: 'general' },
  { id: '026-0008', title: 'COA Compliance Checklist',              ext: 'docs', dept: 'Commission on Audit', deptKey: 'coa',        category: 'notice',     uploaded: 'March 25, 2026', tab: 'general' },
  { id: '026-0007', title: 'HR Policy Manual 2026 Edition',         ext: 'pdf',  dept: 'Research Department', deptKey: 'research',   category: 'memorandum', uploaded: 'March 22, 2026', tab: 'general' },
  { id: '026-0006', title: 'Office Supplies Requisition Form',      ext: 'docs', dept: 'Assets Management',   deptKey: 'assets',     category: 'notice',     uploaded: 'March 20, 2026', tab: 'general' },
  { id: '026-D005', title: 'Department Performance Report Q1',      ext: 'xlsx', dept: 'Accounting',          deptKey: 'accounting', category: 'minutes',    uploaded: 'March 30, 2026', tab: 'department', uploader: 'Juan Dela Cruz',    uploaderInitials: 'JD' },
  { id: '026-D004', title: 'Research Division Work Plan 2026',      ext: 'docs', dept: 'Research Department', deptKey: 'research',   category: 'memorandum', uploaded: 'March 29, 2026', tab: 'department', uploader: 'Maria Santos',      uploaderInitials: 'MS' },
  { id: '026-D003', title: 'Audit Committee Meeting Minutes',       ext: 'pdf',  dept: 'Audit Committee',     deptKey: 'audit',      category: 'minutes',    uploaded: 'March 28, 2026', tab: 'department', uploader: 'Roberto Reyes',     uploaderInitials: 'RR' },
  { id: '026-D002', title: 'Assets Disposal Request Form',          ext: 'docs', dept: 'Assets Management',   deptKey: 'assets',     category: 'letter',     uploaded: 'March 27, 2026', tab: 'department', uploader: 'Ana Lim',           uploaderInitials: 'AL' },
  { id: '026-D001', title: 'COA Audit Findings Response Letter',    ext: 'pdf',  dept: 'Commission on Audit', deptKey: 'coa',        category: 'notice',     uploaded: 'March 26, 2026', tab: 'department', uploader: 'Carlos Mendoza',    uploaderInitials: 'CM' },
];

const ROWS_PER_PAGE = 5;
let currentTab  = 'general';
let currentPage = 1;
let filteredDocs = [];

// ── Filtering ──
function getFiltered() {
  const fileType = document.getElementById('fileTypeFilter').value;
  const dept     = document.getElementById('deptFilter').value;
  const category = document.getElementById('categoryFilter').value;
  const search   = document.getElementById('searchInput').value.toLowerCase();

  return allDocuments.filter(d => {
    if (d.tab !== currentTab) return false;
    if (fileType && d.ext !== fileType) return false;
    if (dept && d.deptKey !== dept) return false;
    if (category && d.category !== category) return false;
    if (search && !d.title.toLowerCase().includes(search) && !d.id.toLowerCase().includes(search)) return false;
    return true;
  });
}

// ── Distribution sidebar (dept or uploader) ──
function renderDist() {
  const tabDocs = allDocuments.filter(d => d.tab === currentTab);
  const total   = tabDocs.length;
  const isDept  = currentTab === 'general';

  document.getElementById('distTitle').textContent = isDept ? 'Upload by Department' : 'Upload by Person';
  document.getElementById('distSub').textContent   = isDept ? 'General Archive' : 'Department Archive';

  // Build frequency map
  const map = {};
  tabDocs.forEach(d => {
    const key = isDept ? d.dept : d.uploader;
    map[key] = (map[key] || 0) + 1;
  });

  // Sort descending
  const entries = Object.entries(map).sort((a, b) => b[1] - a[1]);

  const container = document.getElementById('distCards');
  container.innerHTML = entries.map(([label, count], i) => {
    const pct      = total ? Math.round((count / total) * 100) : 0;
    const initials = label.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
    const colors   = ['#1a2e4a','#2E6DA4','#2e7d32','#6a1b9a','#c62828','#e65100'];
    const color    = colors[i % colors.length];
    return `
      <div class="ft-card">
        <div class="ft-card-top">
          <span class="ft-label" style="gap:8px">
            ${!isDept
              ? `<span class="dist-avatar" style="background:${color}">${initials}</span>`
              : `<span class="dist-dot" style="background:${color}"></span>`
            }
            <span style="font-size:11.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:110px" title="${label}">${label}</span>
          </span>
          <span class="ft-pct">${pct}%</span>
        </div>
      </div>`;
  }).join('');

  // Remove stale dist-total elements
  document.querySelectorAll('.dist-total').forEach(el => el.remove());
}

// ── File type stats sidebar ──
const FILE_TYPES = [
  { ext: 'pdf',  label: 'PDF'  },
  { ext: 'docs', label: 'DOCS' },
  { ext: 'xlsx', label: 'XLSX' },
  { ext: 'pptx', label: 'PPTX' },
];

function renderStats() {
  const tabDocs = allDocuments.filter(d => d.tab === currentTab);
  const total   = tabDocs.length;

  document.getElementById('statsSub').textContent =
    currentTab === 'general' ? 'General Archive' : 'Department Archive';

  const container = document.getElementById('filetypeCards');
  container.innerHTML = FILE_TYPES.map(ft => {
    const count = tabDocs.filter(d => d.ext === ft.ext).length;
    const pct   = total ? Math.round((count / total) * 100) : 0;
    if (!count) return '';
    return `
      <div class="ft-card">
        <div class="ft-card-top">
          <span class="ft-label">
            <span class="ft-dot ${ft.ext}"></span>${ft.label}
          </span>
          <span class="ft-pct">${pct}%</span>
        </div>
      </div>`;
  }).join('');
}

// ── Render table ──
const docIconSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>`;

function render() {
  filteredDocs = getFiltered();
  const totalPages = Math.max(1, Math.ceil(filteredDocs.length / ROWS_PER_PAGE));
  if (currentPage > totalPages) currentPage = totalPages;

  const start = (currentPage - 1) * ROWS_PER_PAGE;
  const page  = filteredDocs.slice(start, start + ROWS_PER_PAGE);

  const isDept = currentTab === 'department';

  // Swap column header
  document.getElementById('thDeptUploader').textContent = isDept ? 'Uploader' : 'Department';

  const tbody = document.getElementById('archiveBody');
  if (!page.length) {
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--muted);font-size:13px;">No documents found.</td></tr>`;
  } else {
    tbody.innerHTML = page.map(d => {
      const thirdCol = isDept
        ? `<div class="uploader-cell">
            <div class="uploader-avatar">${d.uploaderInitials}</div>
            <span class="uploader-name">${d.uploader}</span>
           </div>`
        : `<span class="dept-badge">${d.dept}</span>`;
      return `
      <tr>
        <td><span class="doc-id-cell">${d.id}</span></td>
        <td>
          <div class="doc-name-cell">
            <div class="doc-type-icon ${d.ext}">${docIconSvg}</div>
            <div>
              <div class="doc-title">${d.title}</div>
              <div class="doc-ext">${d.ext}</div>
            </div>
          </div>
        </td>
        <td>${thirdCol}</td>
        <td><span class="upload-date">${d.uploaded}</span></td>
        <td>
          <div class="action-btns">
            <button class="action-btn" title="View">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
            <button class="action-btn" title="Download">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            </button>
            <button class="action-btn" title="Print">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            </button>
          </div>
        </td>
      </tr>`;
    }).join('');
  }

  // Pagination info
  const total = filteredDocs.length;
  const from  = total ? start + 1 : 0;
  const to    = Math.min(start + ROWS_PER_PAGE, total);
  document.getElementById('paginationInfo').textContent =
    `Showing ${from} to ${to} of ${total} archived item${total !== 1 ? 's' : ''}`;

  // Page number buttons
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

// ── Tabs ──
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentTab  = btn.dataset.tab;
    currentPage = 1;
    render();
    renderStats();
    renderDist();
  });
});

// ── Pagination ──
document.getElementById('prevBtn').addEventListener('click', () => { currentPage--; render(); });
document.getElementById('nextBtn').addEventListener('click', () => { currentPage++; render(); });

// ── Filters ──
document.getElementById('fileTypeFilter').addEventListener('change', () => { currentPage = 1; render(); });
document.getElementById('categoryFilter').addEventListener('change',  () => { currentPage = 1; render(); });
document.getElementById('deptFilter').addEventListener('change',      () => { currentPage = 1; render(); });
document.getElementById('searchInput').addEventListener('input',     () => { currentPage = 1; render(); });

document.getElementById('clearFilters').addEventListener('click', () => {
  document.getElementById('fileTypeFilter').value = '';
  document.getElementById('categoryFilter').value  = '';
  document.getElementById('deptFilter').value      = '';
  document.getElementById('searchInput').value     = '';
  currentPage = 1;
  render();
});

// ── Upload modal ──
const overlay     = document.getElementById('modalOverlay');
const openModal   = () => overlay.classList.add('open');
const closeModal  = () => overlay.classList.remove('open');

document.getElementById('uploadBtn').addEventListener('click', openModal);
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('modalCancel').addEventListener('click', closeModal);
overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });

// Drag & drop visual
const dropZone  = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
  e.preventDefault();
  dropZone.classList.remove('dragover');
  if (e.dataTransfer.files.length) fileInput.files = e.dataTransfer.files;
});
dropZone.addEventListener('click', () => fileInput.click());

// ── Initial render ──
render();
renderStats();
renderDist();
