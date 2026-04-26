<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – My Assigned Requests</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/assigned.css') }}">
</head>
<body>

@include('partials.nav')

<!-- ── SUBBAR ── -->
<div class="subbar">
  <div class="subbar-left">
    <span class="breadcrumb">Home / <strong>Assigned Requests</strong></span>
  </div>
  <div class="subbar-right">
    <div class="search-bar">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="searchInput" placeholder="Search assigned requests…">
    </div>
    <div class="datetime" id="datetime"></div>
  </div>
</div>

<!-- ── PAGE ── -->
<main class="page">

  <div class="assigned-heading">
    <h1 class="page-h1">My Assigned Requests</h1>
    <p class="page-sub">Manage your document clearance workflows. Acknowledge new assignments and track the progress of document releases through the processing lifecycle</p>
  </div>

  <div class="assigned-panel">

    <!-- Filters -->
    <div class="filters-row">
      <div class="filter-group">
        <label class="filter-label">STATUS</label>
        <div class="select-wrap">
          <select id="statusFilter">
            <option value="">All Statuses</option>
            <option value="received">Received</option>
            <option value="assigned">Assigned</option>
            <option value="completed">Completed</option>
          </select>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>
      <div class="filter-group">
        <label class="filter-label">PRIORITY</label>
        <div class="select-wrap">
          <select id="priorityFilter">
            <option value="">All Priorities</option>
            <option value="high">High</option>
            <option value="moderate">Moderate</option>
            <option value="low">Low</option>
          </select>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>
      <div class="filter-group">
        <label class="filter-label">CATEGORY</label>
        <div class="select-wrap">
          <select id="categoryFilter">
            <option value="">All Categories</option>
            <option value="letters">Letters</option>
            <option value="memorandum">Memorandum</option>
            <option value="minutes">Minutes of the Meeting</option>
            <option value="notice">Notice of the Meeting</option>
          </select>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>
      <button class="clear-filters-btn" id="clearFilters">Clear All Filters</button>
    </div>

    <!-- Table -->
    <div class="assigned-table-wrap">
      <table class="assigned-table">
        <thead>
          <tr>
            <th>REQUEST ID</th>
            <th>DOCUMENT NAME</th>
            <th>CATEGORY</th>
            <th>REQUESTOR</th>
            <th>STATUS</th>
            <th>PRIORITY</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody id="assignedBody">
          <tr data-status="received" data-priority="high" data-category="memorandum" data-search="req-025 q4 financial report" data-desc="Quarterly financial summary for the fourth quarter of 2026.">
            <td class="req-id-cell">REQ-025</td>
            <td class="doc-name-td">Q4 financial report for 2026.</td>
            <td><span class="category-badge">Memorandum</span></td>
            <td><div class="requestor"><span class="requestor-avatar">CS</span>Chloe S.</div></td>
            <td><span class="badge-status received">Received</span></td>
            <td><span class="badge-priority high">High</span></td>
            <td><button class="view-btn">View</button></td>
          </tr>
          <tr data-status="completed" data-priority="high" data-category="letters" data-search="req-030 document logs" data-desc="Document logs of different departments for the month of August.">
            <td class="req-id-cell">REQ-030</td>
            <td class="doc-name-td">Document logs of different departments for the month of August.</td>
            <td><span class="category-badge">Letters</span></td>
            <td><div class="requestor"><span class="requestor-avatar">CS</span>Chloe S.</div></td>
            <td><span class="badge-status completed">Completed</span></td>
            <td><span class="badge-priority high">High</span></td>
            <td><button class="view-btn">View</button></td>
          </tr>
          <tr data-status="assigned" data-priority="moderate" data-category="minutes" data-search="req-012 organizational accomplishment" data-desc="Annual organizational accomplishment report for 2026.">
            <td class="req-id-cell">REQ-012</td>
            <td class="doc-name-td">Organizational Accomplishment Report for 2026</td>
            <td><span class="category-badge">Minutes of the Meeting</span></td>
            <td><div class="requestor"><span class="requestor-avatar">CS</span>Chloe S.</div></td>
            <td><span class="badge-status assigned">Assigned</span></td>
            <td><span class="badge-priority moderate">Moderate</span></td>
            <td><button class="view-btn">View</button></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-bar">
      <span class="pagination-info" id="paginationInfo">Showing 3 of 3 assigned document requests</span>
      <div class="pagination-controls">
        <button class="page-btn" id="prevBtn" disabled>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <div class="page-numbers" id="pageNumbers">
          <button class="page-num active">1</button>
        </div>
        <button class="page-btn" id="nextBtn" disabled>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
      </div>
    </div>

  </div>
</main>

<!-- Detail Drawer -->
<div class="detail-overlay" id="detailOverlay"></div>
<div class="detail-drawer" id="detailDrawer">
  <div class="detail-drawer-header">
    <div class="detail-drawer-breadcrumb">
      <button class="back-btn" id="drawerClose">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Assigned Requests
      </button>
      <span class="breadcrumb-sep">›</span>
      <span id="drawerReqId" class="drawer-req-id"></span>
    </div>
  </div>
  <div class="detail-drawer-body">
    <div class="drawer-title-row">
      <div>
        <h2 class="drawer-h2" id="drawerTitle"></h2>
        <p class="drawer-sub" id="drawerSub"></p>
      </div>
      <span class="drawer-status-badge" id="drawerStatus"></span>
    </div>
    <div class="drawer-info-grid">
      <div class="drawer-info-item">
        <span class="drawer-info-label">REQUEST ID</span>
        <span class="drawer-info-value" id="dInfoId"></span>
      </div>
      <div class="drawer-info-item">
        <span class="drawer-info-label">CATEGORY</span>
        <span class="drawer-info-value" id="dInfoCategory"></span>
      </div>
      <div class="drawer-info-item">
        <span class="drawer-info-label">PRIORITY</span>
        <span class="drawer-info-value" id="dInfoPriority"></span>
      </div>
      <div class="drawer-info-item">
        <span class="drawer-info-label">REQUESTOR</span>
        <span class="drawer-info-value" id="dInfoRequestor"></span>
      </div>
      <div class="drawer-info-item">
        <span class="drawer-info-label">STATUS</span>
        <span class="drawer-info-value" id="dInfoStatus"></span>
      </div>
    </div>
    <div class="drawer-desc-card">
      <div class="drawer-desc-label">DESCRIPTION</div>
      <p class="drawer-desc-text" id="dInfoDesc"></p>
    </div>
    <div class="drawer-attachments-card">
      <div class="drawer-desc-label">ATTACHMENTS</div>
      <div id="dInfoAttachments" class="drawer-attachments-list"></div>
    </div>
    <div class="mgmt-panel">
      <div class="mgmt-title">MANAGEMENT ACTIONS</div>
      <button class="mgmt-btn mgmt-received" id="mgmtPrimary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        MARK AS RECEIVED
      </button>
      <button class="mgmt-btn mgmt-flag" id="mgmtFlag">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        REJECTED
      </button>
    </div>
  </div>
</div>

<script src="{{ asset('js/assigned.js') }}"></script>
</body>
</html>
