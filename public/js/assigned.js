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

// ── Filtering ──
const allRows = Array.from(document.querySelectorAll('#assignedBody tr'));

function applyFilters() {
  const status   = document.getElementById('statusFilter').value;
  const priority = document.getElementById('priorityFilter').value;
  const category = document.getElementById('categoryFilter').value;
  const search   = document.getElementById('searchInput').value.toLowerCase();

  allRows.forEach(row => {
    const matchStatus   = !status   || row.dataset.status   === status;
    const matchPriority = !priority || row.dataset.priority === priority;
    const matchCategory = !category || row.dataset.category === category;
    const matchSearch   = !search   || row.dataset.search.includes(search);
    row.style.display = (matchStatus && matchPriority && matchCategory && matchSearch) ? '' : 'none';
  });
}

['statusFilter','priorityFilter','categoryFilter'].forEach(id =>
  document.getElementById(id).addEventListener('change', applyFilters)
);
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('clearFilters').addEventListener('click', () => {
  document.getElementById('statusFilter').value   = '';
  document.getElementById('priorityFilter').value = '';
  document.getElementById('categoryFilter').value = '';
  document.getElementById('searchInput').value    = '';
  applyFilters();
});

// ── Detail Drawer ──
const detailOverlay = document.getElementById('detailOverlay');
const detailDrawer  = document.getElementById('detailDrawer');

const statusDrawerClasses = {
  received:  'badge-status received',
  completed: 'badge-status completed',
  assigned:  'badge-status assigned',
};

function openDrawer(row) {
  const id        = row.querySelector('.req-id-cell').textContent.trim();
  const title     = row.querySelector('.doc-name-td').textContent.trim();
  const category  = row.querySelector('.category-badge').textContent.trim();
  const requestor = row.querySelector('.requestor').textContent.trim();
  const status    = row.querySelector('.badge-status').textContent.trim();
  const priority  = row.querySelector('.badge-priority').textContent.trim();
  const desc      = row.dataset.desc || 'No description provided.';
  const attachments = row.dataset.attachments ? JSON.parse(row.dataset.attachments) : [];

  document.getElementById('drawerReqId').textContent    = id;
  document.getElementById('drawerTitle').textContent    = title;
  document.getElementById('drawerSub').textContent      = `Assigned to you · ${category}`;
  document.getElementById('dInfoId').textContent        = id;
  document.getElementById('dInfoCategory').textContent  = category;
  document.getElementById('dInfoPriority').textContent  = priority;
  document.getElementById('dInfoRequestor').textContent = requestor;
  document.getElementById('dInfoStatus').textContent    = status;
  document.getElementById('dInfoDesc').textContent      = desc;

  const docSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>`;
  const attachEl = document.getElementById('dInfoAttachments');
  attachEl.innerHTML = attachments.length
    ? attachments.map(f => `
        <div class="file-item">
          <div class="file-item-name">${docSvg}<span>${f.name}</span></div>
          <span class="file-item-size">${f.size}</span>
        </div>`).join('')
    : '<p class="drawer-no-attachments">No attachments.</p>';

  const statusEl = document.getElementById('drawerStatus');
  statusEl.className = statusDrawerClasses[row.dataset.status] || 'badge-status assigned';
  statusEl.textContent = status;

  detailOverlay.classList.add('open');
  detailDrawer.classList.add('open');
}

function closeDrawer() {
  detailOverlay.classList.remove('open');
  detailDrawer.classList.remove('open');
}

document.getElementById('drawerClose').addEventListener('click', closeDrawer);
detailOverlay.addEventListener('click', closeDrawer);

// ── Management actions ──
let currentRow = null;
let isReceived = false;

const primaryBtn = document.getElementById('mgmtPrimary');

const receivedSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>`;
const approvedSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>`;

function updateBadge(row, statusCls, statusLabel, statusData) {
  const badge = row.querySelector('.badge-status');
  badge.className = statusCls;
  badge.textContent = statusLabel;
  row.dataset.status = statusData;
  document.getElementById('drawerStatus').className = statusCls;
  document.getElementById('drawerStatus').textContent = statusLabel;
  document.getElementById('dInfoStatus').textContent = statusLabel;
}

primaryBtn.addEventListener('click', () => {
  if (!currentRow) return;
  if (!isReceived) {
    // First click: mark as received, switch to Approve button
    updateBadge(currentRow, 'badge-status received', 'Received', 'received');
    primaryBtn.className = 'mgmt-btn mgmt-approved';
    primaryBtn.innerHTML = approvedSvg + ' MARK AS APPROVED';
    isReceived = true;
    showToast('Marked as Received.', 'info');
  } else {
    // Second click: mark as approved
    updateBadge(currentRow, 'badge-status completed', 'Approved', 'completed');
    primaryBtn.className = 'mgmt-btn mgmt-approved';
    primaryBtn.innerHTML = approvedSvg + ' APPROVED';
    primaryBtn.disabled = true;
    primaryBtn.style.opacity = '.5';
    primaryBtn.style.cursor = 'not-allowed';
    showToast('Request approved.', 'success');
  }
});

document.getElementById('mgmtFlag').addEventListener('click', () => {
  if (!currentRow) return;
  updateBadge(currentRow, 'badge-status assigned', 'Rejected', 'assigned');
  showToast('Request rejected.', 'warning');
});

document.getElementById('assignedBody').addEventListener('click', e => {
  const btn = e.target.closest('.view-btn');
  if (!btn) return;
  currentRow = btn.closest('tr');
  isReceived = false;
  primaryBtn.className = 'mgmt-btn mgmt-received';
  primaryBtn.innerHTML = receivedSvg + ' MARK AS RECEIVED';
  primaryBtn.disabled = false;
  primaryBtn.style.opacity = '1';
  primaryBtn.style.cursor = 'pointer';
  openDrawer(currentRow);
});
