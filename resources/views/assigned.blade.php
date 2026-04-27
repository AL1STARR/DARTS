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
      <input type="text" id="searchInput" placeholder="Search assigned requests…" value="{{ request('search') }}">
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
    <form method="GET" action="{{ route('assigned') }}" id="filterForm">
      <div class="filters-row">
        <div class="filter-group">
          <label class="filter-label">STATUS</label>
          <div class="select-wrap">
            <select name="status" id="statusFilter" onchange="document.getElementById('filterForm').submit()">
              <option value="">All Statuses</option>
              <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
              <option value="in-review" {{ request('status') === 'in-review' ? 'selected' : '' }}>In Review</option>
              <option value="approved"  {{ request('status') === 'approved'  ? 'selected' : '' }}>Approved</option>
              <option value="rejected"  {{ request('status') === 'rejected'  ? 'selected' : '' }}>Rejected</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
        </div>
        <div class="filter-group">
          <label class="filter-label">PRIORITY</label>
          <div class="select-wrap">
            <select name="priority" id="priorityFilter" onchange="document.getElementById('filterForm').submit()">
              <option value="">All Priorities</option>
              <option value="high"   {{ request('priority') === 'high'   ? 'selected' : '' }}>High</option>
              <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
              <option value="low"    {{ request('priority') === 'low'    ? 'selected' : '' }}>Low</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
        </div>
        <div class="filter-group">
          <label class="filter-label">CATEGORY</label>
          <div class="select-wrap">
            <select name="category" id="categoryFilter" onchange="document.getElementById('filterForm').submit()">
              <option value="">All Categories</option>
              <option value="letters"    {{ request('category') === 'letters'    ? 'selected' : '' }}>Letters</option>
              <option value="memorandum" {{ request('category') === 'memorandum' ? 'selected' : '' }}>Memorandum</option>
              <option value="minutes"    {{ request('category') === 'minutes'    ? 'selected' : '' }}>Minutes of the Meeting</option>
              <option value="notice"     {{ request('category') === 'notice'     ? 'selected' : '' }}>Notice of the Meeting</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
        </div>
        <a href="{{ route('assigned') }}" class="clear-filters-btn">Clear All Filters</a>
      </div>
    </form>

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
          @forelse($requests as $req)
          <tr>
            <td class="req-id-cell">REQ-{{ str_pad($req->id, 3, '0', STR_PAD_LEFT) }}</td>
            <td class="doc-name-td">{{ $req->title }}</td>
            <td><span class="category-badge">{{ ucfirst($req->category) }}</span></td>
            <td>
              <div class="requestor">
                <span class="requestor-avatar">{{ strtoupper(substr($req->user->first_name, 0, 1) . substr($req->user->last_name, 0, 1)) }}</span>
                {{ $req->user->first_name }} {{ substr($req->user->last_name, 0, 1) }}.
              </div>
            </td>
            <td><span class="badge-status {{ $req->status }}">{{ ucfirst(str_replace('-', ' ', $req->status)) }}</span></td>
            <td><span class="badge-priority {{ $req->priority }}">{{ ucfirst($req->priority) }}</span></td>
            <td>
              <button class="view-btn"
                data-id="{{ $req->id }}"
                data-title="{{ $req->title }}"
                data-category="{{ ucfirst($req->category) }}"
                data-priority="{{ ucfirst($req->priority) }}"
                data-status="{{ $req->status }}"
                data-dept="{{ $req->department }}"
                data-date="{{ $req->created_at->format('M d, Y') }}"
                data-desc="{{ $req->description ?? '' }}"
                data-requestor="{{ $req->user->first_name }} {{ $req->user->last_name }}"
                data-attachments="{{ $req->attachments->map(fn($a) => ['name' => $a->filename, 'size' => round($a->size / 1024, 1) . ' KB', 'url' => route('attachments.view', $a)])->toJson() }}"
                data-status-url="{{ route('assigned.status', $req) }}"
                data-transfer-url="{{ route('assigned.transfer', $req) }}"
                data-department-users-url="{{ route('assigned.department-users') }}"
                data-department-docs-url="{{ route('archive.department-docs') }}">
                View
              </button>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="empty-row">No assigned requests found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-bar">
      <span class="pagination-info">
        Showing {{ $requests->firstItem() ?? 0 }} to {{ $requests->lastItem() ?? 0 }} of {{ $requests->total() }} request{{ $requests->total() !== 1 ? 's' : '' }}
      </span>
      <div class="pagination-controls">
        <a href="{{ $requests->previousPageUrl() ?? '#' }}" class="page-btn {{ $requests->onFirstPage() ? 'disabled' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <div class="page-numbers">
          @for($i = 1; $i <= $requests->lastPage(); $i++)
            <a href="{{ $requests->url($i) }}" class="page-num {{ $requests->currentPage() === $i ? 'active' : '' }}">{{ $i }}</a>
          @endfor
        </div>
        <a href="{{ $requests->nextPageUrl() ?? '#' }}" class="page-btn {{ !$requests->hasMorePages() ? 'disabled' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
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
        <span class="drawer-info-label">DEPARTMENT</span>
        <span class="drawer-info-value" id="dInfoDept"></span>
      </div>
      <div class="drawer-info-item">
        <span class="drawer-info-label">DATE SUBMITTED</span>
        <span class="drawer-info-value" id="dInfoDate"></span>
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
      <button class="mgmt-btn mgmt-received" id="mgmtPrimary"></button>

      <!-- Document picker (shown only when marking as received) -->
      <div id="docPickerWrap" style="display:none">
        <div class="mgmt-title" style="margin-top:4px">ATTACH DOCUMENT FROM ARCHIVE</div>
        <div class="select-wrap transfer-select-wrap">
          <select id="docPickerSelect">
            <option value="">Loading department documents…</option>
          </select>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>

      <button class="mgmt-btn mgmt-flag" id="mgmtReject">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        MARK AS REJECTED
      </button>
      <div class="mgmt-divider"></div>
      <div class="mgmt-transfer">
        <div class="mgmt-title">TRANSFER REQUEST</div>
        <div class="transfer-row">
          <div class="select-wrap transfer-select-wrap">
            <select id="transferSelect">
              <option value="">Select person to transfer to…</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
          <button class="mgmt-btn mgmt-transfer-btn" id="mgmtTransfer">Transfer</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/assigned.js') }}"></script>
</body>
</html>
