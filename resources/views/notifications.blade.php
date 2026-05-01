<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – Notifications</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
</head>
<body>

@include('partials.nav')

<div class="subbar">
  <div class="subbar-left">
    <span class="breadcrumb">Home / <strong>Notifications</strong></span>
  </div>
  <div class="subbar-right">
    <div class="datetime" id="datetime"></div>
  </div>
</div>

<main class="page">
  <div class="notif-page-heading">
    <div>
      <h1 class="page-h1">Notifications</h1>
      <p class="page-sub">Stay updated on your document requests, routes, and assignments</p>
    </div>
  </div>

  <div class="notif-page-panel" id="notifPagePanel">
    <div class="notif-page-list" id="notifPageList">
      <div class="notif-page-empty">Loading notifications…</div>
    </div>
  </div>
</main>

<script>
async function loadNotifications() {
  try {
    const res  = await fetch('/notifications/archive');
    const data = await res.json();
    renderNotifications(data.notifications);
  } catch (e) {
    document.getElementById('notifPageList').innerHTML =
      '<div class="notif-page-empty">Failed to load notifications.</div>';
  }
}

function timeAgo(dateStr) {
  const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
  if (diff < 60)     return 'Just now';
  if (diff < 3600)   return Math.floor(diff / 60) + 'm ago';
  if (diff < 86400)  return Math.floor(diff / 3600) + 'h ago';
  if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
  const d = new Date(dateStr);
  return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}

function renderNotifications(notifications) {
  const list = document.getElementById('notifPageList');
  if (!notifications.length) {
    list.innerHTML = '<div class="notif-page-empty">You have no notifications.</div>';
    return;
  }
  list.innerHTML = notifications.map(n => `
    <div class="notif-page-item ${n.read ? 'read' : 'unread'}" data-id="${n.id}">
      <div class="notif-page-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
          <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
      </div>
      <div class="notif-page-body">
        <div class="notif-page-title">${n.title}</div>
        <div class="notif-page-desc">${n.description}</div>
        <div class="notif-page-time">${timeAgo(n.created_at)}</div>
      </div>
      <div class="notif-page-actions">
        ${!n.read ? `<button class="notif-read-btn" onclick="markRead(${n.id})">Mark as read</button>` : '<span class="notif-read-label">Read</span>'}
      </div>
    </div>`).join('');
}

async function markRead(id) {
  await fetch(`/notifications/${id}/read`, {
    method: 'PATCH',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
  });
  loadNotifications();
}

loadNotifications();
</script>
</body>
</html>
