// Live clock
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

// Notifications
let notifications = [
  { id: 1, title: 'Request Approved', desc: 'Document #REQ-056 has been approved by Human Resources.', time: '28 mins ago', read: false },
  { id: 2, title: 'New Assignment', desc: 'You have been assigned to review #REQ-030.', time: '1 hour ago', read: false },
  { id: 3, title: 'Deadline Reminder', desc: '#REQ-025 is due in less than 30 minutes.', time: '2 hours ago', read: false },
  { id: 4, title: 'Request Approved', desc: 'Document #REQ-012 has been approved by COA.', time: '3 hours ago', read: false },
];

function renderNotifs() {
  const list = document.getElementById('notifList');
  const badge = document.getElementById('notifBadge');
  const dot = document.getElementById('notifDot');
  const unread = notifications.filter(n => !n.read).length;

  badge.textContent = unread;
  badge.style.display = unread ? '' : 'none';
  dot.classList.toggle('visible', unread > 0);

  if (!notifications.length) {
    list.innerHTML = '<div class="notif-empty">No notifications</div>';
    return;
  }

  list.innerHTML = notifications.map(n => `
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
        <div class="time">${n.time}</div>
      </div>
      <button class="notif-dismiss" onclick="dismissNotif(event,${n.id})">✕</button>
    </div>`).join('');
}

function markRead(id) {
  notifications = notifications.map(n => n.id === id ? {...n, read: true} : n);
  renderNotifs();
}

function dismissNotif(e, id) {
  e.stopPropagation();
  notifications = notifications.filter(n => n.id !== id);
  renderNotifs();
}

document.getElementById('clearAll').addEventListener('click', () => {
  notifications = [];
  renderNotifs();
});

renderNotifs();

// Search shortcut
document.addEventListener('keydown', e => {
  if ((e.ctrlKey || e.metaKey) && e.key === 's') {
    e.preventDefault();
    document.getElementById('searchInput').focus();
  }
});
