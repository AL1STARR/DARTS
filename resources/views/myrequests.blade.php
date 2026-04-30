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
      <input type="text" id="searchInput" placeholder="Search" value="{{ request('search') }}">
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
    <form method="GET" action="{{ route('myrequests') }}" id="filterForm">
      <div class="filters-row">
        <div class="filter-group">
          <label class="filter-label">STATUS</label>
          <div class="select-wrap">
            <select name="status" id="statusFilter" onchange="document.getElementById('filterForm').submit()">
              <option value="">All Statuses</option>
              <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
              <option value="approved"  {{ request('status') === 'approved'  ? 'selected' : '' }}>Approved</option>
              <option value="rejected"  {{ request('status') === 'rejected'  ? 'selected' : '' }}>Rejected</option>
              <option value="in-review" {{ request('status') === 'in-review' ? 'selected' : '' }}>In Review</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
        </div>
        <div class="filter-group">
          <label class="filter-label">PRIORITY</label>
          <div class="select-wrap">
            <select name="priority" id="priorityFilter" onchange="document.getElementById('filterForm').submit()">
              <option value="">All Priorities</option>
              @foreach($priorities as $p)
                <option value="{{ strtolower($p) }}" {{ request('priority') === strtolower($p) ? 'selected' : '' }}>{{ $p }}</option>
              @endforeach
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
        </div>
        <div class="filter-group">
          <label class="filter-label">CATEGORY</label>
          <div class="select-wrap">
            <select name="category" id="categoryFilter" onchange="document.getElementById('filterForm').submit()">
              <option value="">All Categories</option>
              @foreach($categories as $cat)
                <option value="{{ strtolower($cat) }}" {{ request('category') === strtolower($cat) ? 'selected' : '' }}>{{ $cat }}</option>
              @endforeach
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
        </div>
        <a href="{{ route('myrequests') }}" class="clear-filters-btn">Clear All Filters</a>
      </div>
    </form>

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
        <tbody id="mrBody">
          @forelse($requests as $req)
          <tr>
            <td><span class="mr-req-id">{{ $req->formattedId() }}</span></td>
            <td>
              <div class="mr-doc-cell">
                <div class="mr-doc-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <span class="mr-doc-title">{{ $req->title }}</span>
              </div>
            </td>
            <td><span class="category-badge">{{ ucfirst($req->category) }}</span></td>
            <td>
              <span class="priority-badge {{ $req->priority }}">
                @if($req->priority === 'high')
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                @elseif($req->priority === 'medium')
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                @else
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                @endif
                {{ ucfirst($req->priority) }}
              </span>
            </td>
            <td><span class="status-badge {{ $req->status }}">{{ ucfirst(str_replace('-', ' ', $req->status)) }}</span></td>
            <td>{{ $req->created_at->format('M d, Y') }}</td>
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
                data-deadline="{{ $req->deadline ? $req->deadline->format('Y-m-d\TH:i') : '' }}"
                data-deadline-display="{{ $req->deadline ? $req->deadline->format('M d, Y g:i A') : 'No deadline' }}"
                data-assigned="{{ $req->assignedTo ? $req->assignedTo->first_name . ' ' . $req->assignedTo->last_name : 'Unassigned' }}"
                data-fulfilled-doc="{{ $req->fulfilledBy ? $req->fulfilledBy->title : '' }}"
                data-fulfilled-url="{{ $req->fulfilledBy ? route('archive.download', $req->fulfilledBy) : '' }}"
                data-attachments="{{ $req->attachments->map(fn($a) => ['name' => $a->filename, 'size' => round($a->size / 1024, 1) . ' KB', 'url' => route('attachments.view', $a)])->toJson() }}"
                data-formatted-id="{{ $req->formattedId() }}"
                data-delete-url="{{ route('myrequests.destroy', $req) }}"
                data-update-url="{{ route('myrequests.update', $req) }}">
                View
              </button>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="empty-row">No requests found.</td></tr>
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

<!-- New Request Modal -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal">
    <div class="modal-header">
      <h3>New Request</h3>
      <button class="modal-close" id="modalClose">✕</button>
    </div>
    <form id="requestForm" enctype="multipart/form-data" novalidate data-action="{{ route('myrequests.store') }}" data-store-action="{{ route('myrequests.store') }}">
      @csrf
      <div class="modal-body">
        <div class="field-group">
          <label>Document Title</label>
          <input type="text" name="title" id="fTitle" placeholder="Enter document title">
          <span class="field-error" id="errTitle"></span>
        </div>
        <div class="field-row">
          <div class="field-group">
            <label>Category</label>
            <div class="select-wrap">
              <select name="category" id="fCategory">
                <option value="">Select category</option>
                @foreach($categories as $cat)
                  <option value="{{ strtolower($cat) }}">{{ $cat }}</option>
                @endforeach
              </select>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <span class="field-error" id="errCategory"></span>
          </div>
          <div class="field-group">
            <label>Priority</label>
            <div class="select-wrap">
              <select name="priority" id="fPriority">
                <option value="">Select priority</option>
                @foreach($priorities as $p)
                  <option value="{{ strtolower($p) }}">{{ $p }}</option>
                @endforeach
              </select>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <span class="field-error" id="errPriority"></span>
          </div>
        </div>
        <div class="field-group">
          <label>Department</label>
          <div class="select-wrap">
            <select name="department" id="fDept" data-user-dept="{{ auth()->user()->department }}">
              <option value="">Select department</option>
              @foreach($departments as $dept)
                <option value="{{ $dept }}" @if($dept === auth()->user()->department) disabled @endif>{{ $dept }}{{ $dept === auth()->user()->department ? ' (Your Department)' : '' }}</option>
              @endforeach
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
          <span class="field-error" id="errDept"></span>
        </div>
        <div class="field-row">
          <div class="field-group">
            <label>Assign To</label>
            <div class="select-wrap">
              <select name="assigned_to" id="fAssignTo" disabled>
                <option value="">Select department first…</option>
              </select>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <span class="field-error" id="errAssignTo"></span>
          </div>
          <div class="field-group">
            <label>Deadline</label>
            <input type="datetime-local" name="deadline" id="fDeadline">
            <span class="field-error" id="errDeadline"></span>
          </div>
        </div>
        <div class="field-group">
          <label>Description</label>
          <textarea name="description" id="fDesc" placeholder="Briefly describe the request…" rows="3"></textarea>
        </div>
        <div class="field-group">
          <label>Attachment</label>
          <div class="drop-zone" id="dropZone">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
            <p>Drag &amp; drop files here or <span class="browse-link" id="browseLink">browse</span></p>
            <span>PDF files only</span>
            <input type="file" name="attachments[]" id="fAttachments" multiple accept=".pdf" hidden>
          </div>
          <div id="fileList" class="file-list"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" id="modalCancel">Cancel</button>
        <button type="submit" class="btn-submit" id="modalSubmit">Submit Request</button>
      </div>
    </form>
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
        <span class="drawer-info-label">ASSIGNED TO</span>
        <span class="drawer-info-value" id="dInfoAssigned"></span>
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
        <span class="drawer-info-label">DEADLINE</span>
        <span class="drawer-info-value" id="dInfoDeadline"></span>
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
    <div class="drawer-attachments-card" id="fulfilledDocCard" style="display:none">
      <div class="drawer-desc-label">FULFILLED DOCUMENT</div>
      <div id="dInfoFulfilledDoc" class="drawer-attachments-list"></div>
    </div>
  </div>
  <div class="detail-drawer-footer">
    <button class="edit-request-btn" id="editRequestBtn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Edit Request
    </button>
    <button class="delete-request-btn" id="deleteRequestBtn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
      Delete Request
    </button>
  </div>
</div>

<script src="{{ asset('js/myrequests.js') }}"></script>
</body>
</html>
