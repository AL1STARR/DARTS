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

// Notifications
let notifications = [];

async function fetchNotifications() {
  try {
    const response = await fetch('/notifications');
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    notifications = data.notifications.map(n => ({
      id: n.id,
      title: n.title,
      desc: n.description,
      read: n.read,
    }));
    renderNotifs();
  } catch (error) {
    console.error('Failed to fetch notifications:', error);
    // Display fallback notification state
    notifications = [];
    renderNotifs();
  }
}

function renderNotifs() {
  const bellList = document.getElementById('notifList');
  const dashList = document.getElementById('dashboardNotifList');
  const badge = document.getElementById('notifBadge');
  const dot = document.getElementById('notifDot');
  const unread = notifications.filter(n => !n.read).length;

  badge.textContent = unread;
  badge.style.display = unread ? '' : 'none';
  dot.classList.toggle('visible', unread > 0);

  const notifHTML = !notifications.length
    ? '<div class="notif-empty">No notifications</div>'
    : notifications.map(n => `
        <div class="notif-item ${n.read ? 'read' : ''}" onclick="markRead(${n.id})">
          <div class="notif-avatar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
          </div>
          <div class="notif-body">
            <div class="title">${n.title}</div>
            <div class="desc">${n.desc}</div>
          </div>
          <button class="notif-dismiss" onclick="dismissNotif(event,${n.id})">✕</button>
        </div>`).join('');

  if (bellList) bellList.innerHTML = notifHTML;
  if (dashList) dashList.innerHTML = notifHTML;
}

async function markRead(id) {
  try {
    await fetch(`/notifications/${id}/read`, {
      method: 'PATCH',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
    await fetchNotifications();
  } catch (error) {
    console.error('Failed to mark notification as read:', error);
  }
}

async function dismissNotif(e, id) {
  e.stopPropagation();
  try {
    await fetch(`/notifications/${id}/dismiss`, {
      method: 'PATCH',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
    await fetchNotifications();
  } catch (error) {
    console.error('Failed to dismiss notification:', error);
  }
}

async function clearAll() {
  try {
    await fetch('/notifications/clear-all', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
    await fetchNotifications();
  } catch (error) {
    console.error('Failed to clear notifications:', error);
  }
}

// Setup clear all buttons
const clearAllBtn = document.getElementById('clearAll');
const dashboardClearAllBtn = document.getElementById('dashboardClearAll');
if (clearAllBtn) clearAllBtn.addEventListener('click', clearAll);
if (dashboardClearAllBtn) dashboardClearAllBtn.addEventListener('click', clearAll);

// Notification bell toggle
const notifToggle = document.getElementById('notifToggle');
const notifPanel = document.getElementById('notifPanel');

if (notifToggle && notifPanel) {
  notifToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    const currentDisplay = window.getComputedStyle(notifPanel).display;
    const isOpen = currentDisplay !== 'none';
    notifPanel.style.display = isOpen ? 'none' : 'flex';
  });
  
  document.addEventListener('click', () => {
    notifPanel.style.display = 'none';
  });

  notifPanel.addEventListener('click', (e) => e.stopPropagation());
}

// Keyboard navigation for notifications
document.addEventListener('keydown', e => {
  if (e.key === 'Escape' && notifPanel) {
    notifPanel.style.display = 'none';
  }
});

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
