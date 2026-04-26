<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – My Assigned Requests</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/assigned.css">
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
      <input type="text" id="searchInput" placeholder="Search through document archive">
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
    <div class="filters-section">
      <span class="filters-label">FILTERS:</span>
      <div class="select-wrap">
        <select id="statusFilter">
          <option value="">Status: All</option>
          <option value="received">Received</option>
          <option value="assigned">Assigned</option>
          <option value="completed">Completed</option>
        </select>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
      </div>
      <div class="select-wrap">
        <select id="priorityFilter">
          <option value="">Priority: All</option>
          <option value="high">High</option>
          <option value="moderate">Moderate</option>
          <option value="low">Low</option>
        </select>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
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
            <th>REQUESTOR</th>
            <th>STATUS</th>
            <th>PRIORITY</th>
          </tr>
        </thead>
        <tbody id="assignedBody">
          <tr>
            <td class="req-id-cell">REQ-025</td>
            <td>Q4 financial report for 2026.</td>
            <td><div class="requestor"><span class="requestor-avatar">CS</span>Chloe S.</div></td>
            <td><span class="badge-status received">Received</span></td>
            <td><span class="badge-priority high">High</span></td>
          </tr>
          <tr>
            <td class="req-id-cell">REQ-030</td>
            <td>Document logs of different departments for the month of August.</td>
            <td><div class="requestor"><span class="requestor-avatar">CS</span>Chloe S.</div></td>
            <td><span class="badge-status completed">Completed</span></td>
            <td><span class="badge-priority high">High</span></td>
          </tr>
          <tr>
            <td class="req-id-cell">REQ-012</td>
            <td>Organizational Accomplishment Report for 2026</td>
            <td><div class="requestor"><span class="requestor-avatar">CS</span>Chloe S.</div></td>
            <td><span class="badge-status assigned">Assigned</span></td>
            <td><span class="badge-priority moderate">Moderate</span></td>
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
          <button class="page-num">2</button>
          <button class="page-num">3</button>
        </div>
        <button class="page-btn" id="nextBtn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
      </div>
    </div>

  </div>
</main>

<script src="js/assigned.js"></script>
</body>
</html>
