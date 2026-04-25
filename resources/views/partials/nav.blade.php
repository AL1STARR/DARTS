<header class="site-header">
  <div class="header-brand">
    <img src="{{ asset('assets/logo.png') }}" alt="DARTS Logo" class="logo-img">
  </div>

  <nav class="top-nav">
    <a href="{{ route('dashboard') }}" {{ request()->routeIs('dasboard') ? 'class=active' : '' }}>
      <svg viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      Dashboard
    </a>
    <a href="{{ route('myrequests') }}" {{ request()->routeIs('myrequests') ? 'class=active' : '' }}>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      My Requests
    </a>
    <a href="{{ route('assigned') }}" {{ request()->routeIs('assigned') ? 'class=active' : '' }}>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><polyline points="9 12 11 14 15 10"/></svg>
      Assigned Requests
    </a>
    <a href="{{ route('archive') }}" {{ request()->routeIs('archive') ? 'class=active' : '' }}>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
      Archive
    </a>
    <a href="{{ route('routing') }}" {{ request()->routeIs('routing') ? 'class=active' : '' }}>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
      Routing
    </a>
  </nav>

  <div class="header-actions">
    <button class="notif-btn" id="notifToggle" title="Notifications">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      <span class="notif-dot" id="notifDot"></span>
    </button>
    <div class="user-chip" onclick="location.href='{{ route('profile') }}'" style="cursor:pointer">
      <div class="user-meta">
        <div class="user-name">{{ auth()->user()->name }}</div>
        <div class="user-role">Records Officer</div>
      </div>
      <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
    </div>
  </div>
</header>
