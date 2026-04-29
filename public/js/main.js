// Live clock
function updateTime() {
  const now = new Date();
  const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  const h = now.getHours().toString().padStart(2,'0');
  const m = now.getMinutes().toString().padStart(2,'0');
  const datetimeElement = document.getElementById('datetime');
  if (datetimeElement) {
    datetimeElement.textContent =
      `${days[now.getDay()]} | ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()} | ${h}:${m}`;
  }
}
updateTime();
setInterval(updateTime, 1000);

// Notifications — fetch unread count for the bell dot only
async function fetchNotifications() {
  try {
    const response = await fetch('/notifications/data');
    const data = await response.json();
    const dot = document.getElementById('notifDot');
    if (dot) dot.classList.toggle('visible', data.unreadCount > 0);
    renderDashboardNotifs(data.notifications || []);
  } catch (error) {
    console.error('Failed to fetch notifications:', error);
  }
}

function renderDashboardNotifs(notifications) {
  const list = document.getElementById('dashboardNotifList');
  if (!list) return;
  if (!notifications.length) {
    list.innerHTML = '<div class="notif-empty">No notifications</div>';
    return;
  }
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
  await fetch(`/notifications/${id}/read`, {
    method: 'PATCH',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
  });
  fetchNotifications();
}

async function dashDismiss(e, id) {
  e.stopPropagation();
  await fetch(`/notifications/${id}/dismiss`, {
    method: 'PATCH',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
  });
  fetchNotifications();
}

async function dashClearAll() {
  await fetch('/notifications/clear-all', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
  });
  fetchNotifications();
}

const dashboardClearAllBtn = document.getElementById('dashboardClearAll');
if (dashboardClearAllBtn) dashboardClearAllBtn.addEventListener('click', dashClearAll);

// Initial load
fetchNotifications();
setInterval(fetchNotifications, 30000);

// Search shortcut
document.addEventListener('keydown', e => {
  if ((e.ctrlKey || e.metaKey) && e.key === 's') {
    e.preventDefault();
    document.getElementById('searchInput').focus();
  }
});
