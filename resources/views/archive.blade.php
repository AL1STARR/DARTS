<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – Archive</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/archive.css">
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
      <input type="text" id="searchInput" placeholder="Search through document archive…">
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
        <button class="tab-btn active" data-tab="general">General Archive</button>
        <button class="tab-btn" data-tab="department">Department Archive</button>
      </div>
      <button class="adv-filter-btn" id="advFilterBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
        Advanced Filters
      </button>
    </div>

    <!-- Filters row -->
    <div class="filters-row" id="filtersRow">
      <div class="filter-group">
        <label class="filter-label">FILE TYPE</label>
        <div class="select-wrap">
          <select id="fileTypeFilter">
            <option value="">All Formats</option>
            <option value="pdf">PDF</option>
            <option value="docs">DOCS</option>
            <option value="xlsx">XLSX</option>
            <option value="pptx">PPTX</option>
          </select>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>
      <div class="filter-group">
        <label class="filter-label">CATEGORY</label>
        <div class="select-wrap">
          <select id="categoryFilter">
            <option value="">All Categories</option>
            <option value="letter">Letters</option>
            <option value="memorandum">Memorandum</option>
            <option value="minutes">Minutes of the Meeting</option>
            <option value="notice">Notice of the Meeting</option>
          </select>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>
      <div class="filter-group">
        <label class="filter-label">DEPARTMENT</label>
        <div class="select-wrap">
          <select id="deptFilter">
            <option value="">All Committee</option>
            <option value="executive">Executive Committee</option>
            <option value="internals">Internal Affairs Division</option>
            <option value="externals">Externals Affairs Division</option>
            <option value="secre">Secretariat Committee</option>
            <option value="finance">Finance Division</option>
            <option value="audit">Audit Division</option>
            <option value="logproc">Logistics & Procurement Division</option>
            <option value="graphic">Graphic Design Division</option>
            <option value="docu">Documentation Division</option>
            <option value="social">Social Media Division</option>
          </select>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>
      <button class="clear-filters-btn" id="clearFilters">Clear All Filters</button>
    </div>

    <!-- Table -->
    <div class="archive-table-wrap">
      <table class="archive-table" id="archiveTable">
        <thead>
          <tr>
            <th>Document ID</th>
            <th>Document Name</th>
            <th id="thDeptUploader">Department</th>
            <th>Uploaded</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="archiveBody"></tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-bar">
      <span class="pagination-info" id="paginationInfo"></span>
      <div class="pagination-controls">
        <button class="page-btn" id="prevBtn" title="Previous">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <div class="page-numbers" id="pageNumbers"></div>
        <button class="page-btn" id="nextBtn" title="Next">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
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
      <div class="stats-sidebar-sub" id="statsSub">General Archive</div>
      <div class="filetype-cards" id="filetypeCards"></div>
    </aside>

    <aside class="stats-sidebar">
      <div class="stats-sidebar-header">
        <span class="title-bar"></span>
        <span id="distTitle">Upload by Department</span>
      </div>
      <div class="stats-sidebar-sub" id="distSub">General Archive</div>
      <div class="filetype-cards" id="distCards"></div>
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
    <div class="modal-body">
      <div class="drop-zone" id="dropZone">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
        <p>Drag &amp; drop file here or <label class="browse-link" for="fileInput">browse</label></p>
        <span>PDF, DOCS, XLSX, PPTX supported</span>
        <input type="file" id="fileInput" accept=".pdf,.doc,.docx,.xlsx,.pptx" hidden>
      </div>
      <div class="modal-fields">
        <div class="field-group">
          <label>Document Title</label>
          <input type="text" placeholder="Enter document title">
        </div>
        <div class="field-row">
          <div class="field-group">
            <label>Department</label>
            <div class="select-wrap">
              <select>
                <option value="">Select department</option>
                <option>Audit Committee</option>
                <option>Research Department</option>
                <option>Assets Management</option>
                <option>Commission on Audit</option>
                <option>Accounting</option>
              </select>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
          </div>
          <div class="field-group">
            <label>Archive Type</label>
            <div class="select-wrap">
              <select>
                <option>General Archive</option>
                <option>Department Archive</option>
              </select>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" id="modalCancel">Cancel</button>
      <button class="btn-upload">Upload Document</button>
    </div>
  </div>
</div>

<script src="js/archive.js"></script>
</body>
</html>
