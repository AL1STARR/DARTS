<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – My Requests</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/myrequests.css') }}">
</head>
<body>

@include('partials.nav')

<!-- ── SUBBAR ── -->
<div class="subbar">
  <div class="subbar-left">
    <span class="breadcrumb">Home / <strong>My Requests</strong></span>
  </div>
  <div class="subbar-right">
    <div class="search-bar">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="searchInput" placeholder="Search requests…">
    </div>
    <div class="datetime" id="datetime"></div>
  </div>
</div>

<!-- ── PAGE ── -->
<main class="page">

  <!-- Heading -->
  <div class="mr-heading">
    <div>
      <h1 class="page-h1">My Requests</h1>
      <p class="page-sub">Track and manage all document requests you have submitted</p>
    </div>
    <button class="new-request-btn" id="newRequestBtn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Request
    </button>
  </div>

  <!-- Panel -->
  <div class="mr-panel">

    <!-- Filters -->
    <div class="filters-row">
      <div class="filter-group">
        <label class="filter-label">STATUS</label>
        <div class="select-wrap">
          <select id="statusFilter">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="in-review">In Review</option>
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
            <option value="medium">Medium</option>
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
    <div class="mr-table-wrap">
      <table class="mr-table">
        <thead>
          <tr>
            <th>Request ID</th>
            <th>Document Title</th>
            <th>Category</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Date Submitted</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="mrBody"></tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-bar">
      <span class="pagination-info" id="paginationInfo"></span>
      <div class="pagination-controls">
        <button class="page-btn" id="prevBtn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <div class="page-numbers" id="pageNumbers"></div>
        <button class="page-btn" id="nextBtn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
      </div>
    </div>

  </div>
</main>

<!-- New Request Modal -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal">
    <div class="modal-header">
      <h3>New Request</h3>
      <button class="modal-close" id="modalClose">✕</button>
    </div>
    <div class="modal-body">
      <div class="field-group">
        <label>Document Title</label>
        <input type="text" id="fTitle" placeholder="Enter document title">
        <span class="field-error" id="errTitle"></span>
      </div>
      <div class="field-row">
        <div class="field-group">
          <label>Category</label>
          <div class="select-wrap">
            <select id="fCategory">
              <option value="">Select category</option>
              <option value="letters">Letters</option>
              <option value="memorandum">Memorandum</option>
              <option value="minutes">Minutes of the Meeting</option>
              <option value="notice">Notice of the Meeting</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
          <span class="field-error" id="errCategory"></span>
        </div>
        <div class="field-group">
          <label>Priority</label>
          <div class="select-wrap">
            <select id="fPriority">
              <option value="">Select priority</option>
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
          <span class="field-error" id="errPriority"></span>
        </div>
      </div>
      <div class="field-group">
        <label>Department</label>
        <div class="select-wrap">
          <select id="fDept">
            <option value="">Select department</option>
            <option>Executive Committee</option>
            <option>Internal Affairs</option>
            <option>External Affairs</option>
            <option>Secretariat</option>
            <option>Finance</option>
            <option>Audit</option>
          </select>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <span class="field-error" id="errDept"></span>
      </div>
      <div class="field-group">
        <label>Description</label>
        <textarea id="fDesc" placeholder="Briefly describe the request…" rows="3"></textarea>
      </div>
      <div class="field-group">
        <label>Attach Supporting Documents</label>
        <div class="drop-zone" id="dropZone">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
          <p>Drag &amp; drop files here or <span class="browse-link" id="browseLink">browse</span></p>
          <span>PDF, DOCS, XLSX, PPTX supported</span>
          <input type="file" id="fAttachments" multiple accept=".pdf,.doc,.docx,.xlsx,.pptx" hidden>
        </div>
        <div id="fileList" class="file-list"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" id="modalCancel">Cancel</button>
      <button class="btn-submit" id="modalSubmit">Submit Request</button>
    </div>
  </div>
</div>

<!-- Detail Drawer -->
<div class="detail-overlay" id="detailOverlay"></div>
<div class="detail-drawer" id="detailDrawer">
  <div class="detail-drawer-header">
    <div class="detail-drawer-breadcrumb">
      <button class="back-btn" id="drawerClose">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        My Requests
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
        <span class="drawer-info-label">DEPARTMENT</span>
        <span class="drawer-info-value" id="dInfoDept"></span>
      </div>
      <div class="drawer-info-item">
        <span class="drawer-info-label">DATE SUBMITTED</span>
        <span class="drawer-info-value" id="dInfoDate"></span>
      </div>
      <div class="drawer-info-item">
        <span class="drawer-info-label">SUBMITTED BY</span>
        <span class="drawer-info-value" id="dInfoBy"></span>
      </div>
    </div>
    <div class="drawer-desc-card">
      <div class="drawer-desc-label">DESCRIPTION</div>
      <p class="drawer-desc-text" id="dInfoDesc"></p>
    </div>
  </div>
</div>

<script src="{{ asset('js/myrequests.js') }}"></script>
</body>
</html>
