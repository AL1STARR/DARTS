<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<!-- ── HEADER ── -->
<header class="site-header">
  <div class="header-brand">
    <img src="assets/logo.png" alt="DARTS Logo" class="logo-img">
  </div>

  <nav class="top-nav">
    <a href="index.html" class="active">
      <svg viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      Dashboard
    </a>
    <a href="myrequests.html">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      My Requests
    </a>
    <a href="assigned.html">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><polyline points="9 12 11 14 15 10"/></svg>
      Assigned Requests
    </a>
    <a href="archive.html">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
      Archive
    </a>
     <a href="routing.html">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
      Routing
    </a>
  </nav>

  <div class="header-actions">
    <button class="notif-btn" id="notifToggle" title="Notifications">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      <span class="notif-dot" id="notifDot"></span>
    </button>
    <div class="user-chip" onclick="location.href='profile.html'" style="cursor:pointer">
      <div class="user-meta">
        <div class="user-name">Juan Dela Cruz</div>
        <div class="user-role">Records Officer</div>
      </div>
      <div class="user-avatar">JD</div>
    </div>
  </div>
</header>

<!-- ── SUBBAR ── -->
<div class="subbar">
  <div class="subbar-left">
    <span class="breadcrumb">Home / <strong>Dashboard</strong></span>
  </div>
  <div class="subbar-right">
    <div class="search-bar">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="searchInput" placeholder="Search requests or documents…">
    </div>
    <div class="datetime" id="datetime"></div>
  </div>
</div>

<!-- ── PAGE CONTENT ── -->
<main class="page">

  <!-- Stat Cards -->
  <div class="stat-cards">
    <div class="stat-card" onclick="location.href='#'">
      <div class="stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
      </div>
      <div class="stat-body">
        <div class="stat-num">24</div>
        <div class="stat-label">My Requests</div>
      </div>
      <div class="stat-trend">↑ 3 this week</div>
    </div>
    <div class="stat-card" onclick="location.href='#'">
      <div class="stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><polyline points="9 12 11 14 15 10"/></svg>
      </div>
      <div class="stat-body">
        <div class="stat-num">18</div>
        <div class="stat-label">Assigned Requests</div>
      </div>
      <div class="stat-trend">2 pending action</div>
    </div>
    <div class="stat-card" onclick="location.href='archive.html'">
      <div class="stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      </div>
      <div class="stat-body">
        <div class="stat-num">126</div>
        <div class="stat-label">Department Archive</div>
      </div>
      <div class="stat-trend">↑ 12 this month</div>
    </div>
    <div class="stat-card" onclick="location.href='archive.html'">
      <div class="stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
      </div>
      <div class="stat-body">
        <div class="stat-num">67</div>
        <div class="stat-label">General Archive</div>
      </div>
      <div class="stat-trend">↑ 5 this month</div>
    </div>
  </div>

  <!-- Main grid: left panels + notifications -->
  <div class="main-grid">
    <div class="panels">

      <!-- Near Deadline -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title">
            <span class="title-bar"></span>
            Near Deadline Requests
          </div>
          <a href="#" class="see-all">View All</a>
        </div>
        <div class="deadline-strip">
          <div class="deadline-item">
            <div class="dl-left urgent-border">
              <span class="dl-badge red">
                <span class="pulse-dot"></span>Due in 25 mins
              </span>
              <div class="dl-id">#REQ-025</div>
              <div class="dl-desc">Q4 financial report for 2026.</div>
              <div class="dl-meta">Accounting Dept.</div>
            </div>
            <a href="#" class="dl-action">Open Task</a>
          </div>
          <div class="deadline-item">
            <div class="dl-left warning-border">
              <span class="dl-badge orange">Due in 4 hours</span>
              <div class="dl-id">#REQ-030</div>
              <div class="dl-desc">Document logs of different departments for the month of August.</div>
              <div class="dl-meta">Commission on Audit</div>
            </div>
            <a href="#" class="dl-action">Open Task</a>
          </div>
        </div>
      </div>

      <!-- Assigned Requests Table -->
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title">
            <span class="title-bar"></span>
            My Assigned Requests
          </div>
          <a href="#" class="see-all">View All</a>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>Request ID</th>
              <th>Document Title</th>
              <th>Status</th>
              <th>Priority</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><span class="req-id">REQ-025</span></td>
              <td>Q4 financial report for 2026.</td>
              <td><span class="badge-status received">Received</span></td>
              <td><span class="badge-priority high">High</span></td>
              <td><button class="row-action">View</button></td>
            </tr>
            <tr>
              <td><span class="req-id">REQ-030</span></td>
              <td>Document logs of different departments for the month of August.</td>
              <td><span class="badge-status received">Received</span></td>
              <td><span class="badge-priority high">High</span></td>
              <td><button class="row-action">View</button></td>
            </tr>
            <tr>
              <td><span class="req-id">REQ-012</span></td>
              <td>Organizational Accomplishment Report for 2026.</td>
              <td><span class="badge-status received">Received</span></td>
              <td><span class="badge-priority high">High</span></td>
              <td><button class="row-action">View</button></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>

    <!-- Notifications -->
    <div class="notif-panel" id="notifPanel">
      <div class="panel-header">
        <div class="panel-title">
          <span class="title-bar"></span>
          Notifications
          <span class="notif-count" id="notifBadge">4</span>
        </div>
        <button class="clear-btn" id="clearAll">Clear all</button>
      </div>
      <div class="notif-list" id="notifList"></div>
    </div>
  </div>

</main>

<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
