// Live clock
function updateTime() {
  const now = new Date();
  const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  const h = now.getHours().toString().padStart(2,'0');
  const m = now.getMinutes().toString().padStart(2,'0');
  const el = document.getElementById('datetime');
  if (el) el.textContent = `${days[now.getDay()]} | ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()} | ${h}:${m}`;
}
updateTime();
setInterval(updateTime, 1000);

// ── Notifications ──
async function fetchNotifications() {
  try {
    const data = await fetch('/notifications/data').then(r => r.json());
    const dot = document.getElementById('notifDot');
    if (dot) dot.classList.toggle('visible', data.unreadCount > 0);
    renderDashboardNotifs(data.notifications || []);
  } catch {}
}

function renderDashboardNotifs(notifications) {
  const list = document.getElementById('dashboardNotifList');
  if (!list) return;
  if (!notifications.length) { list.innerHTML = '<div class="notif-empty">No notifications</div>'; return; }
  list.innerHTML = notifications.map(n => `
    <div class="notif-item ${n.read ? 'read' : ''}" onclick="dashMarkRead(${n.id})">
      <div class="notif-avatar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
          <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
      </div>
      <div class="notif-body">
        <div class="title">${n.title}</div>
        <div class="desc">${n.description}</div>
      </div>
      <button class="notif-dismiss" onclick="dashDismiss(event,${n.id})">✕</button>
    </div>`).join('');
}

async function dashMarkRead(id) {
  await fetch(`/notifications/${id}/read`, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
  fetchNotifications();
}
async function dashDismiss(e, id) {
  e.stopPropagation();
  await fetch(`/notifications/${id}/dismiss`, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
  fetchNotifications();
}
async function dashClearAll() {
  await fetch('/notifications/clear-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
  fetchNotifications();
}

fetchNotifications();
setInterval(fetchNotifications, 30000);

// ── Dashboard Search — init after DOM is ready ──
document.addEventListener('DOMContentLoaded', function () {

  const clearAllBtn = document.getElementById('dashboardClearAll');
  if (clearAllBtn) clearAllBtn.addEventListener('click', dashClearAll);

  const searchInput = document.getElementById('searchInput');
  if (!searchInput) return; // not on dashboard, skip

  // Append dropdown to body to avoid subbar overflow clipping
  const dropdown = document.createElement('div');
  dropdown.style.cssText = 'position:fixed;background:#fff;border:1px solid #d1d9e0;border-radius:8px;box-shadow:0 8px 32px rgba(0,0,0,0.15);z-index:99999;max-height:360px;overflow-y:auto;display:none;';
  document.body.appendChild(dropdown);

  function positionDropdown() {
    const r = searchInput.getBoundingClientRect();
    dropdown.style.top   = (r.bottom + 4) + 'px';
    dropdown.style.left  = r.left + 'px';
    dropdown.style.width = r.width + 'px';
  }

  function hideDropdown() { dropdown.style.display = 'none'; }
  function showDropdown()  { positionDropdown(); dropdown.style.display = 'block'; }

  let timer;
  searchInput.addEventListener('input', function () {
    clearTimeout(timer);
    const q = this.value.trim();
    if (!q) { hideDropdown(); return; }
    timer = setTimeout(() => doSearch(q), 300);
  });

  searchInput.addEventListener('keydown', e => {
    if (e.key === 'Escape') { hideDropdown(); searchInput.value = ''; }
  });

  document.addEventListener('click', e => {
    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) hideDropdown();
  });

  window.addEventListener('resize', () => {
    if (dropdown.style.display !== 'none') positionDropdown();
  });

  async function doSearch(q) {
    dropdown.innerHTML = '<div style="padding:12px 16px;font-size:12px;color:#6b7280;">Searching…</div>';
    showDropdown();
    try {
      const results = await fetch('/dashboard/search?q=' + encodeURIComponent(q)).then(r => r.json());
      renderResults(results);
    } catch {
      dropdown.innerHTML = '<div style="padding:12px 16px;font-size:12px;color:#c62828;">Search failed.</div>';
    }
  }

  function renderResults(results) {
    if (!results.length) {
      dropdown.innerHTML = '<div style="padding:14px 16px;font-size:12px;color:#6b7280;">No results found.</div>';
      showDropdown();
      return;
    }

    const typeColor  = { document: '#0369a1', request: '#7c3aed', route: '#b45309' };
    const typeLabel  = { document: 'Archive',  request: 'Request',  route: 'Routing' };
    const statusColor = {
      pending: '#ca8a04', approved: '#15803d', rejected: '#dc2626',
      'in-review': '#0369a1', 'on-time': '#1d4ed8', delayed: '#c62828',
      completed: '#15803d', returned: '#7c3aed', missing: '#c2410c',
    };

    dropdown.innerHTML = results.map((d, i) => {
      const color  = typeColor[d.type] || '#64748b';
      const label  = typeLabel[d.type] || d.type;
      const border = i < results.length - 1 ? 'border-bottom:1px solid #f0f2f5;' : '';
      const statusHtml = d.status
        ? ` · <span style="color:${statusColor[d.status] || '#64748b'};font-weight:600;">${d.status.replace('-', ' ')}</span>`
        : '';
      const ftHtml = d.file_type
        ? `<span style="background:#e2e8f0;color:#475569;font-size:10px;font-weight:700;padding:1px 5px;border-radius:3px;margin-left:6px;">${d.file_type.toUpperCase()}</span>`
        : '';
      return (
        `<div class="dash-result" data-idx="${i}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;cursor:pointer;${border}">` +
          `<span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:4px;background:${color}20;color:${color};white-space:nowrap;flex-shrink:0;">${label}</span>` +
          `<div style="flex:1;min-width:0;">` +
            `<div style="font-size:13px;font-weight:600;color:#1a2e4a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${d.title}${ftHtml}</div>` +
            `<div style="font-size:11px;color:#6b7280;margin-top:1px;">${d.formatted_id}${statusHtml}</div>` +
          `</div>` +
          `<svg style="width:14px;height:14px;color:#9ca3af;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>` +
        `</div>`
      );
    }).join('');

    dropdown.querySelectorAll('.dash-result').forEach((el, i) => {
      const d = results[i];
      el.addEventListener('mouseenter', () => el.style.background = '#f8fafc');
      el.addEventListener('mouseleave', () => el.style.background = '');
      el.addEventListener('click', () => {
        hideDropdown();
        searchInput.value = '';
        if (d.type === 'document' && d.view_url) window.open(d.view_url, '_blank');
        else window.location.href = d.page_url;
      });
    });

    showDropdown();
  }
});
