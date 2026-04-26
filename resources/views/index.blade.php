<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

@include('partials.nav')

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
    <div class="stat-card" onclick="location.href='{{ route('myrequests') }}'">
      <div class="stat-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path fill="currentColor" d="M16.519 16.501c.175-.136.334-.295.651-.612l3.957-3.958c.096-.095.052-.26-.075-.305a4.3 4.3 0 0 1-1.644-1.034a4.3 4.3 0 0 1-1.034-1.644c-.045-.127-.21-.171-.305-.075L14.11 12.83c-.317.317-.476.476-.612.651q-.243.311-.412.666c-.095.2-.166.414-.308.84l-.184.55l-.292.875l-.273.82a.584.584 0 0 0 .738.738l.82-.273l.875-.292l.55-.184c.426-.142.64-.212.84-.308q.355-.17.666-.412m5.849-5.809a2.163 2.163 0 1 0-3.06-3.059l-.126.128a.52.52 0 0 0-.148.465c.02.107.055.265.12.452c.13.375.376.867.839 1.33s.955.709 1.33.839c.188.065.345.1.452.12a.53.53 0 0 0 .465-.148z"/><path fill="currentColor" fill-rule="evenodd" d="M4.172 3.172C3 4.343 3 6.229 3 10v4c0 3.771 0 5.657 1.172 6.828S7.229 22 11 22h2c3.771 0 5.657 0 6.828-1.172C20.981 19.676 21 17.832 21 14.18l-2.818 2.818c-.27.27-.491.491-.74.686a5 5 0 0 1-.944.583a8 8 0 0 1-.944.355l-2.312.771a2.083 2.083 0 0 1-2.635-2.635l.274-.82l.475-1.426l.021-.066c.121-.362.22-.658.356-.944q.24-.504.583-.943c.195-.25.416-.47.686-.74l4.006-4.007L18.12 6.7l.127-.127A3.65 3.65 0 0 1 20.838 5.5c-.151-1.03-.444-1.763-1.01-2.328C18.657 2 16.771 2 13 2h-2C7.229 2 5.343 2 4.172 3.172M7.25 9A.75.75 0 0 1 8 8.25h6.5a.75.75 0 0 1 0 1.5H8A.75.75 0 0 1 7.25 9m0 4a.75.75 0 0 1 .75-.75h2.5a.75.75 0 0 1 0 1.5H8a.75.75 0 0 1-.75-.75m0 4a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5H8a.75.75 0 0 1-.75-.75" clip-rule="evenodd"/></svg>
      </div>
      <div class="stat-body">
        <div class="stat-num">{{ $totalRequests }}</div>
        <div class="stat-label">My Requests</div>
      </div>
      <div class="stat-trend">Total submitted</div>
    </div>
    <div class="stat-card" onclick="location.href='{{ route('assigned') }}'">
      <div class="stat-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M13.5 3l5.5 5.5v11.5c0 0.55 -0.45 1 -1 1h-12c-0.55 0 -1 -0.45 -1 -1v-16c0 -0.55 0.45 -1 1 -1Z"/><path d="M14.83 15.83c-1.56 1.56 -4.1 1.56 -5.66 0c-1.56 -1.56 -1.56 -4.1 0 -5.66c1.56 -1.56 4.1 -1.56 5.66 0c1.56 1.56 1.56 4.1 0 5.66l4.67 4.67"/></g></svg>
      </div>
      <div class="stat-body">
        <div class="stat-num">{{ $assignedRequests }}</div>
        <div class="stat-label">Assigned Requests</div>
      </div>
      <div class="stat-trend">Assigned to you</div>
    </div>
    <div class="stat-card" onclick="location.href='{{ route('archive') }}'">
      <div class="stat-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path fill="currentColor" d="M5.5 0h-4A1.5 1.5 0 0 0 0 1.5v21A1.5 1.5 0 0 0 1.5 24h4A1.5 1.5 0 0 0 7 22.5v-21A1.5 1.5 0 0 0 5.5 0m-1 7.75a.25.25 0 0 1 .25.25v7a1.25 1.25 0 0 1-2.5 0V8a.25.25 0 0 1 .25-.25Zm-2-2a.25.25 0 0 1-.25-.25V4a1.25 1.25 0 0 1 2.5 0v1.5a.25.25 0 0 1-.25.25Zm1 12.75A1.5 1.5 0 1 1 2 20a1.5 1.5 0 0 1 1.5-1.5M14 0h-4a1.5 1.5 0 0 0-1.5 1.5v21A1.5 1.5 0 0 0 10 24h4a1.5 1.5 0 0 0 1.5-1.5v-21A1.5 1.5 0 0 0 14 0m-1 7.75a.25.25 0 0 1 .25.25v7a1.25 1.25 0 0 1-2.5 0V8a.25.25 0 0 1 .25-.25Zm-2-2a.25.25 0 0 1-.25-.25V4a1.25 1.25 0 0 1 2.5 0v1.5a.25.25 0 0 1-.25.25Zm1 12.75a1.5 1.5 0 1 1-1.5 1.5a1.5 1.5 0 0 1 1.5-1.5M22.5 0h-4A1.5 1.5 0 0 0 17 1.5v21a1.5 1.5 0 0 0 1.5 1.5h4a1.5 1.5 0 0 0 1.5-1.5v-21A1.5 1.5 0 0 0 22.5 0m-1 7.75a.25.25 0 0 1 .25.25v7a1.25 1.25 0 0 1-2.5 0V8a.25.25 0 0 1 .25-.25Zm-2-2a.25.25 0 0 1-.25-.25V4a1.25 1.25 0 0 1 2.5 0v1.5a.25.25 0 0 1-.25.25Zm1 12.75A1.5 1.5 0 1 1 19 20a1.5 1.5 0 0 1 1.5-1.5"/></svg>
      </div>
      <div class="stat-body">
        <div class="stat-num">{{ $departmentArchive }}</div>
        <div class="stat-label">Department Archive</div>
      </div>
      <div class="stat-trend">Documents archived</div>
    </div>
    <div class="stat-card" onclick="location.href='{{ route('archive') }}'">
      <div class="stat-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><g fill="none"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M3 19h1V6.36a1.5 1.5 0 0 1 1.026-1.423l8-2.666A1.5 1.5 0 0 1 15 3.694V19h1V9.99a.5.5 0 0 1 .598-.49l2.196.44A1.5 1.5 0 0 1 20 11.41V19h1a1 1 0 1 1 0 2H3a1 1 0 1 1 0-2"/></g></svg>
      </div>
      <div class="stat-body">
        <div class="stat-num">{{ $generalArchive }}</div>
        <div class="stat-label">General Archive</div>
      </div>
      <div class="stat-trend">Documents archived</div>
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
            @forelse($recentRequests as $req)
            <tr>
              <td><span class="req-id">REQ-{{ str_pad($req->id, 3, '0', STR_PAD_LEFT) }}</span></td>
              <td>{{ $req->title }}</td>
              <td><span class="badge-status {{ $req->status }}">{{ ucfirst(str_replace('-', ' ', $req->status)) }}</span></td>
              <td><span class="badge-priority {{ $req->priority }}">{{ ucfirst($req->priority) }}</span></td>
              <td><a href="{{ route('myrequests') }}" class="row-action">View</a></td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--muted);font-size:13px;">No requests yet.</td></tr>
            @endforelse
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
