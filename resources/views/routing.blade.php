<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – Routing</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/routing.css') }}">
</head>
<body data-user-department="{{ auth()->user()->department ?? 'Records Division' }}" data-user-id="{{ auth()->user()->id }}">

@include('partials.nav')

<!-- ── SUBBAR ── -->
<div class="subbar">
  <div class="subbar-left">
    <span class="breadcrumb">Home / <strong>Routing</strong></span>
  </div>
  <div class="subbar-right">
    <div class="search-bar">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="searchInput" placeholder="Search routing records…">
    </div>
    <div class="datetime" id="datetime"></div>
  </div>
</div>

<!-- ── PAGE ── -->
<main class="page">

  <!-- Page heading -->
  <div class="routing-heading">
    <div>
      <h1 class="page-h1">Document Routing</h1>
      <p class="page-sub">Monitor and manage cross-departmental documents</p>
    </div>
    <button class="create-btn" id="createBtn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
      Create New Route
    </button>
  </div>

  <!-- Routing panel -->
  <div class="routing-panel">

    <!-- Filters -->
    <div class="filters-row">
      <div class="filter-group">
        <label class="filter-label">STATUS</label>
        <div class="select-wrap">
          <select id="statusFilter">
            <option value="">All Statuses</option>
            <option value="on-time">On-time</option>
            <option value="delayed">Delayed</option>
            <option value="pending">Pending</option>
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
            @foreach($priorities as $p)
              <option value="{{ strtolower($p) }}">{{ $p }}</option>
            @endforeach
          </select>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>
      <button class="clear-filters-btn" id="clearFilters">Clear All Filters</button>
    </div>

    <!-- Table -->
    <div class="routing-table-wrap">
      <table class="routing-table">
        <thead>
          <tr>
            <th>Routing ID</th>
            <th>Document Name</th>
            <th>Origin</th>
            <th>Waypoint</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="routingBody"></tbody>
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

<!-- Create Route Modal -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal modal-wide">
    <div class="modal-header">
      <h3>Create New Route</h3>
      <button class="modal-close" id="modalClose">✕</button>
    </div>
    <div class="modal-body">
      <div class="field-group">
        <label>Document Name</label>
        <input type="text" id="newDocName" placeholder="Enter document name">
      </div>
      <div class="field-group">
        <label>Priority</label>
        <div class="select-wrap">
          <select id="newPriority">
            @foreach($priorities as $p)
              <option value="{{ strtolower($p) }}">{{ $p }}</option>
            @endforeach
          </select>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>
      <div class="field-group">
        <label>Deadline</label>
        <input type="datetime-local" id="newDeadline">
      </div>

      <!-- Routing stages -->
      <div class="stages-label">
        <span>ROUTING PATH</span>
        <span class="stages-hint">Each stage's origin is auto-filled from the previous waypoint</span>
      </div>
      <div class="stages-list" id="stagesList"></div>
      <button class="add-stage-btn" id="addStageBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Stage
      </button>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" id="modalCancel">Cancel</button>
      <button class="btn-create" id="modalSubmit">Create Route</button>
    </div>
  </div>
</div>

<!-- Detail View -->
<div class="detail-overlay" id="detailOverlay"></div>
<div class="detail-panel" id="detailPanel">

  <div class="detail-topbar">
    <div class="detail-breadcrumb">
      <button class="back-btn" id="backBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Routing
      </button>
      <span class="breadcrumb-sep">›</span>
      <span class="detail-rt-id" id="detailRtId"></span>
    </div>
  </div>

  <div class="detail-body">
    <div class="detail-title-row">
      <div>
        <h2 class="detail-h2" id="detailH2"></h2>
        <p class="detail-sub" id="detailSub"></p>
      </div>
    </div>

    <!-- Document info card -->
    <div class="detail-info-card">
      <div class="detail-doc-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      </div>
      <div class="detail-doc-info">
        <div class="detail-doc-id" id="detailDocId"></div>
        <div class="detail-doc-name" id="detailDocName"></div>
        <div class="detail-doc-meta" id="detailDocMeta"></div>
      </div>
      <div class="detail-stat-block">
        <div class="detail-stat-label">STATUS</div>
        <div class="detail-status" id="detailStatus"></div>
      </div>
      <div class="detail-divider-v"></div>
      <div class="detail-stat-block">
        <div class="detail-stat-label">ORIGIN</div>
        <div class="detail-origin" id="detailOrigin"></div>
      </div>
    </div>

    <!-- Path table + actions -->  
    <div class="detail-grid">
      <div class="detail-path-panel">
        <table class="path-table">
          <thead>
            <tr>
              <th>Path</th>
              <th>Handler</th>
              <th>Status</th>
              <th>Duration</th>
            </tr>
          </thead>
          <tbody id="pathBody"></tbody>
        </table>
      </div>

      <div class="mgmt-panel">
        <div class="mgmt-title">MANAGEMENT ACTIONS</div>
        <button class="mgmt-btn received" onclick="handleAction('received')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          RECEIVED
        </button>
        <button class="mgmt-btn returned" onclick="handleAction('returned')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 14 4 9 9 4"/><path d="M20 20v-7a4 4 0 0 0-4-4H4"/></svg>
          RETURNED
        </button>
        <button class="mgmt-btn flag" onclick="handleAction('flag')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
          FLAG AS MISSING
        </button>
        <div class="mgmt-divider"></div>
        <!-- Remarks card (shown when status is returned) -->
        <div class="mgmt-remarks" id="mgmtRemarks" style="display:none">
          <div class="mgmt-remarks-label">RETURN REMARKS</div>
          <p class="mgmt-remarks-text" id="mgmtRemarksText"></p>
          <button class="mgmt-btn republish" id="republishBtn" onclick="handleRepublish()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
            RE-PUBLISH ROUTE
          </button>
        </div>
        <div class="mgmt-divider"></div>
        <div class="current-handler">
          <div class="handler-avatar" id="handlerAvatar"></div>
          <div>
            <div class="handler-label">CURRENT HANDLER</div>
            <div class="handler-name" id="handlerName"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Remarks Prompt -->
<div class="remarks-overlay" id="remarksOverlay">
  <div class="remarks-card">
    <div class="remarks-card-header">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 14 4 9 9 4"/><path d="M20 20v-7a4 4 0 0 0-4-4H4"/></svg>
      <h4>Return Document</h4>
    </div>
    <p class="remarks-card-sub">Please provide a reason for returning this document.</p>
    <textarea id="remarksInput" placeholder="Enter remarks…" rows="4"></textarea>
    <div class="remarks-card-footer">
      <button class="btn-cancel" id="remarksCancelBtn">Cancel</button>
      <button class="btn-create" id="remarksConfirmBtn">Confirm Return</button>
    </div>
  </div>
</div>

<script src="{{ asset('js/routing.js') }}"></script>
</body>
</html>
