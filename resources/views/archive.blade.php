<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – Archive</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/archive.css') }}">
</head>
<body>

@include('partials.nav')

<!-- ── SUBBAR ── -->
<div class="subbar">
  <div class="subbar-left">
    <span class="breadcrumb">Home / <strong>Archive</strong></span>
  </div>
  <div class="subbar-right">
    <div class="search-bar">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="searchInput" placeholder="Search through document archive…" value="{{ request('search') }}">
    </div>
    <div class="datetime" id="datetime"></div>
  </div>
</div>

<!-- ── PAGE ── -->
<main class="page">

  <!-- Page heading -->
  <div class="archive-heading">
    <div>
      <h1 class="page-h1">Archive</h1>
      <p class="page-sub">Central repository for all documentation</p>
    </div>
    <button class="upload-btn" id="uploadBtn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
      Upload Document
    </button>
  </div>

  <!-- Content grid: archive panel + stats sidebar -->
  <div class="archive-layout">
  <div class="archive-panel">

    <!-- Tabs + Advanced Filters -->
    <div class="archive-toolbar">
      <div class="archive-tabs">
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'general', 'page' => 1]) }}" class="tab-btn {{ request('tab', 'general') === 'general' ? 'active' : '' }}">General Archive</a>
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'department', 'page' => 1]) }}" class="tab-btn {{ request('tab') === 'department' ? 'active' : '' }}">Department Archive</a>
      </div>
    </div>

    <!-- Filters row -->
    <form method="GET" action="{{ route('archive') }}" id="filterForm">
      <input type="hidden" name="tab" value="{{ request('tab', 'general') }}">
      <div class="filters-row" id="filtersRow">
        <div class="filter-group">
          <label class="filter-label">FILE TYPE</label>
          <div class="select-wrap">
            <select name="file_type" id="fileTypeFilter" onchange="document.getElementById('filterForm').submit()">
              <option value="">All Formats</option>
              <option value="pdf"  {{ request('file_type') === 'pdf'  ? 'selected' : '' }}>PDF</option>
              <option value="docs" {{ request('file_type') === 'docs' ? 'selected' : '' }}>DOCS</option>
              <option value="xlsx" {{ request('file_type') === 'xlsx' ? 'selected' : '' }}>XLSX</option>
              <option value="pptx" {{ request('file_type') === 'pptx' ? 'selected' : '' }}>PPTX</option>
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
        @if(request('tab', 'general') === 'general')
        <div class="filter-group">
          <label class="filter-label">DEPARTMENT</label>
          <div class="select-wrap">
            <select name="department" id="deptFilter" onchange="document.getElementById('filterForm').submit()">
              <option value="">All Departments</option>
              @foreach($departments as $dept)
                <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
              @endforeach
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
        </div>
        @endif
        <a href="{{ route('archive', ['tab' => request('tab', 'general')]) }}" class="clear-filters-btn">Clear All Filters</a>
      </div>
    </form>

    <!-- Table -->
    <div class="archive-table-wrap">
      <table class="archive-table" id="archiveTable">
        <thead>
          <tr>
            <th>Document ID</th>
            <th>Document Name</th>
            <th>Category</th>
            <th>{{ $tab === 'department' ? 'Uploader' : 'Department' }}</th>
            <th>Uploaded</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="archiveBody">
          @forelse($documents as $doc)
          <tr>
            <td><span class="doc-id-cell">DOC-{{ str_pad($doc->id, 4, '0', STR_PAD_LEFT) }}</span></td>
            <td>
              <div class="doc-name-cell">
                <div class="doc-type-icon {{ $doc->file_type }}">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div>
                  <div class="doc-title">{{ $doc->title }}</div>
                  <div class="doc-ext">{{ strtoupper($doc->file_type) }}</div>
                </div>
              </div>
            </td>
            <td><span class="category-badge">{{ ucfirst($doc->category) }}</span></td>
            <td id="thDeptUploaderVal">
              @if($doc->archive_type === 'department')
                <div class="uploader-cell">
                  <div class="uploader-avatar">{{ strtoupper(substr($doc->uploader->first_name,0,1).substr($doc->uploader->last_name,0,1)) }}</div>
                  <span class="uploader-name">{{ $doc->uploader->first_name }} {{ $doc->uploader->last_name }}</span>
                </div>
              @else
                <span class="dept-badge">{{ $doc->department }}</span>
              @endif
            </td>
            <td><span class="upload-date">{{ $doc->created_at->format('M d, Y') }}</span></td>
            <td>
              <div class="action-btns">
                <a href="{{ route('archive.view', $doc) }}" target="_blank" class="action-btn" title="Preview">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </a>
                <a href="{{ route('archive.download', $doc) }}" class="action-btn" title="Download">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </a>
                <button class="action-btn print-btn" title="Print" data-url="{{ route('archive.view', $doc) }}">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                </button>
                @if($doc->uploaded_by === auth()->id())
                <button class="action-btn edit-doc-btn" title="Edit"
                  data-id="{{ $doc->id }}"
                  data-title="{{ $doc->title }}"
                  data-description="{{ $doc->description ?? '' }}"
                  data-category="{{ $doc->category }}"
                  data-archive-type="{{ $doc->archive_type }}"
                  data-url="{{ route('archive.update', $doc) }}">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                @endif
                <button class="action-btn delete-doc-btn" title="Delete" data-url="{{ route('archive.destroy', $doc) }}">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                </button>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="empty-row">No documents found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-bar">
      <span class="pagination-info">
        Showing {{ $documents->firstItem() ?? 0 }} to {{ $documents->lastItem() ?? 0 }} of {{ $documents->total() }} document{{ $documents->total() !== 1 ? 's' : '' }}
      </span>
      <div class="pagination-controls">
        <a href="{{ $documents->previousPageUrl() ?? '#' }}" class="page-btn {{ $documents->onFirstPage() ? 'disabled' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <div class="page-numbers">
          @for($i = 1; $i <= $documents->lastPage(); $i++)
            <a href="{{ $documents->url($i) }}" class="page-num {{ $documents->currentPage() === $i ? 'active' : '' }}">{{ $i }}</a>
          @endfor
        </div>
        <a href="{{ $documents->nextPageUrl() ?? '#' }}" class="page-btn {{ !$documents->hasMorePages() ? 'disabled' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
      </div>
    </div>

  </div>

  <!-- File Type Stats Sidebar -->
  <!-- Stats sidebar column -->
  <div class="stats-col">
    <aside class="stats-sidebar">
      <div class="stats-sidebar-header">
        <span class="title-bar"></span>
        File Type Breakdown
      </div>
      <div class="stats-sidebar-sub">{{ $tab === 'general' ? 'General Archive' : 'Department Archive' }}</div>
      <div class="filetype-cards">
        @php $totalDocs = $fileTypeStats->sum(); @endphp
        @forelse($fileTypeStats->sortDesc() as $type => $count)
          @php $pct = $totalDocs ? round(($count / $totalDocs) * 100) : 0; @endphp
          <div class="ft-card">
            <div class="ft-card-top">
              <span class="ft-label">
                <span class="ft-dot {{ $type }}"></span>
                {{ strtoupper($type) }}
              </span>
              <span class="ft-pct">{{ $pct }}%</span>
            </div>
          </div>
        @empty
          <p style="font-size:12px;color:var(--muted);padding:10px 6px">No documents yet.</p>
        @endforelse
      </div>
    </aside>

    <aside class="stats-sidebar">
      <div class="stats-sidebar-header">
        <span class="title-bar"></span>
        {{ $tab === 'general' ? 'Upload by Department' : 'Upload by Person' }}
      </div>
      <div class="stats-sidebar-sub">{{ $tab === 'general' ? 'General Archive' : 'Department Archive' }}</div>
      <div class="filetype-cards">
        @php
          $totalDist = $distStats->sum();
          $colors = ['#1a2e4a','#2E6DA4','#2e7d32','#6a1b9a','#c62828','#e65100'];
          $ci = 0;
        @endphp
        @forelse($distStats->sortDesc() as $label => $count)
          @php $pct = $totalDist ? round(($count / $totalDist) * 100) : 0; $color = $colors[$ci++ % count($colors)]; @endphp
          <div class="ft-card">
            <div class="ft-card-top">
              <span class="ft-label" style="gap:8px">
                @if($tab === 'department')
                  @php $initials = collect(explode(' ', $label))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join(''); @endphp
                  <span class="dist-avatar" style="background:{{ $color }}">{{ $initials }}</span>
                @else
                  <span class="dist-dot" style="background:{{ $color }}"></span>
                @endif
                <span style="font-size:11.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:110px" title="{{ $label }}">{{ $label }}</span>
              </span>
              <span class="ft-pct">{{ $pct }}%</span>
            </div>
          </div>
        @empty
          <p style="font-size:12px;color:var(--muted);padding:10px 6px">No documents yet.</p>
        @endforelse
      </div>
    </aside>
  </div>

  </div><!-- end archive-layout -->

<!-- Upload Modal -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal">
    <div class="modal-header">
      <h3>Upload Document</h3>
      <button class="modal-close" id="modalClose">✕</button>
    </div>
    <form id="uploadForm" novalidate data-action="{{ route('archive.store') }}">
      @csrf
      <div class="modal-body">
        <div class="drop-zone" id="dropZone">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
          <p>Drag &amp; drop file here or <label class="browse-link" for="fileInput">browse</label></p>
          <span>PDF, DOCS, XLSX, PPTX supported</span>
          <input type="file" id="fileInput" name="file" accept=".pdf,.doc,.docx,.xlsx,.pptx" hidden>
        </div>
        <div id="selectedFile" style="display:none" class="file-selected-info"></div>
        <span class="field-error" id="errFile"></span>
        <div class="modal-fields">
          <div class="field-group">
            <label>Document Title</label>
            <input type="text" name="title" id="fTitle" placeholder="Enter document title">
            <span class="field-error" id="errTitle"></span>
          </div>
          <div class="field-group">
            <label>Description <span style="font-weight:400;color:var(--muted)">(optional)</span></label>
            <textarea name="description" id="fDesc" placeholder="Briefly describe the document…" rows="2" style="border:1px solid var(--border);border-radius:6px;padding:8px 12px;font-size:13px;font-family:inherit;color:var(--navy);outline:none;resize:vertical;"></textarea>
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
              <label>Archive Type</label>
              <div class="select-wrap">
                <select name="archive_type" id="fArchiveType">
                  <option value="general">General Archive</option>
                  <option value="department">Department Archive</option>
                </select>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" id="modalCancel">Cancel</button>
        <button type="submit" class="btn-upload" id="uploadSubmit">Upload Document</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModalOverlay">
  <div class="modal">
    <div class="modal-header">
      <h3>Edit Document</h3>
      <button class="modal-close" id="editModalClose">✕</button>
    </div>
    <form id="editForm" novalidate>
      @csrf
      <div class="modal-body">
        <div class="modal-fields">
          <div class="field-group">
            <label>Document Title</label>
            <input type="text" name="title" id="eFTitle" placeholder="Enter document title">
            <span class="field-error" id="eErrTitle"></span>
          </div>
          <div class="field-group">
            <label>Description <span style="font-weight:400;color:var(--muted)">(optional)</span></label>
            <textarea name="description" id="eFDesc" placeholder="Briefly describe the document…" rows="2" style="border:1px solid var(--border);border-radius:6px;padding:8px 12px;font-size:13px;font-family:inherit;color:var(--navy);outline:none;resize:vertical;"></textarea>
          </div>
          <div class="field-row">
            <div class="field-group">
              <label>Category</label>
              <div class="select-wrap">
                <select name="category" id="eFCategory">
                  @foreach($categories as $cat)
                    <option value="{{ strtolower($cat) }}">{{ $cat }}</option>
                  @endforeach
                </select>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
              </div>
              <span class="field-error" id="eErrCategory"></span>
            </div>
            <div class="field-group">
              <label>Archive Type</label>
              <div class="select-wrap">
                <select name="archive_type" id="eFArchiveType">
                  <option value="general">General Archive</option>
                  <option value="department">Department Archive</option>
                </select>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" id="editModalCancel">Cancel</button>
        <button type="submit" class="btn-upload" id="editSubmit">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/archive.js') }}"></script>
</body>
</html>
