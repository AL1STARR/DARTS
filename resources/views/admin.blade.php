<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – Admin</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>

@include('partials.nav')

<!-- ── SUBBAR ── -->
<div class="subbar">
  <div class="subbar-left">
    <span class="breadcrumb">Home / <strong>Admin</strong></span>
  </div>
  <div class="subbar-right">
    <form method="GET" action="{{ route('admin') }}" class="search-bar">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" name="search" placeholder="Search" value="{{ request('search') }}" onkeydown="if(event.key==='Enter') this.form.submit()">
      <input type="hidden" name="role" value="{{ request('role') }}">
      <input type="hidden" name="department" value="{{ request('department') }}">
      <input type="hidden" name="status" value="{{ request('status') }}">
    </form>
    <div class="datetime" id="datetime"></div>
  </div>
</div>

<!-- ── PAGE ── -->
<main class="page">

@if(session('success'))
  <script>document.addEventListener('DOMContentLoaded', () => showToast('{{ session('success') }}', 'success'));</script>
@endif

  <!-- Page heading -->
  <div class="admin-heading">
    <div>
      <h1 class="page-h1">Admin Control Panel</h1>
      <p class="page-sub">Manage users, access requests, and system settings</p>
    </div>
    <button class="add-user-btn" id="addUserBtn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add User
    </button>
  </div>

  <!-- Stat cards -->
  <div class="admin-stat-cards">
    <div class="admin-stat-card">
      <div class="asc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
      <div class="asc-num">{{ $users->count() }}</div>
      <div class="asc-label">Total Users</div>
    </div>
    <div class="admin-stat-card">
      <div class="asc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
      <div class="asc-num">{{ $users->where('status', 'active')->count() }}</div>
      <div class="asc-label">Active Users</div>
    </div>
    <div class="admin-stat-card">
      <div class="asc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/><line x1="12" y1="12" x2="12" y2="22"/><line x1="8" y1="16" x2="16" y2="16"/></svg></div>
      <div class="asc-num">{{ $users->where('role', 'Admin')->count() }}</div>
      <div class="asc-label">Admins</div>
    </div>
    <div class="admin-stat-card pending-card">
      <div class="asc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
      <div class="asc-num">{{ $requests->count() }}</div>
      <div class="asc-label">Pending Requests</div>
    </div>
  </div>

  <!-- Tabs -->
  <div class="admin-tabs">
    <button class="admin-tab active" data-tab="users">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      Users
    </button>
    <button class="admin-tab" data-tab="requests">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Access Requests
      @if($requests->count() > 0)
        <span class="tab-badge">{{ $requests->count() }}</span>
      @endif
    </button>
    <button class="admin-tab" data-tab="settings">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      System Settings
    </button>
  </div>

  <!-- Content layout -->
  <div class="admin-layout">

    <!-- ── USERS TAB ── -->
    <div id="tab-users" class="admin-tab-panel">
      <div class="admin-panel">

        <!-- Filters -->
        <form method="GET" action="{{ route('admin') }}" id="filterForm" class="filters-row">
          <div class="filter-group">
            <label class="filter-label">ROLE</label>
            <div class="select-wrap">
              <select name="role" id="roleFilter" onchange="document.getElementById('filterForm').submit()">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                  <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>{{ $role }}</option>
                @endforeach
              </select>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
          </div>
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
          <div class="filter-group">
            <label class="filter-label">STATUS</label>
            <div class="select-wrap">
              <select name="status" id="statusFilter" onchange="document.getElementById('filterForm').submit()">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
              </select>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
          </div>
          <a href="{{ route('admin') }}" class="clear-filters-btn" id="clearFilters">Clear All Filters</a>
        </form>

        <!-- Table -->
        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>User</th>
                <th>Role</th>
                <th>Department</th>
                <th>Status</th>
                <th>Date Added</th>
                <th>Admin Privileges</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="adminBody">
              @forelse($users as $user)
              <tr
                data-role="{{ $user->role }}"
                data-dept="{{ $user->department }}"
                data-status="{{ $user->status }}"
                data-name="{{ strtolower($user->first_name . ' ' . $user->last_name) }}"
                data-email="{{ strtolower($user->email) }}"
              >
                <td>
                  <div class="user-cell">
                    <div class="user-cell-avatar">{{ strtoupper(substr($user->first_name,0,1).substr($user->last_name,0,1)) }}</div>
                    <div>
                      <div class="user-cell-name">{{ $user->first_name }} {{ $user->last_name }}</div>
                      <div class="user-cell-email">{{ $user->email }}</div>
                    </div>
                  </div>
                </td>
                <td><span class="role-badge {{ Str::slug($user->role) }}">{{ $user->role }}</span></td>
                <td><span class="dept-badge">{{ $user->department }}</span></td>
                <td><span class="status-badge {{ $user->status }}">{{ ucfirst($user->status) }}</span></td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
                <td>
                  @if($user->role === 'Admin')
                    <button class="admin-toggle on" disabled title="Cannot change privileges for Admin role">
                      <span class="admin-toggle-knob"></span>
                    </button>
                  @else
                    <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="inline-form">
                      @csrf
                      <button type="submit" class="admin-toggle {{ $user->is_admin ? 'on' : '' }}" title="{{ $user->is_admin ? 'Remove admin privileges' : 'Grant admin privileges' }}">
                        <span class="admin-toggle-knob"></span>
                      </button>
                    </form>
                  @endif
                </td>
                <td>
                  <div class="action-btns">
                    <button class="action-btn edit"
                      data-id="{{ $user->id }}"
                      data-first="{{ $user->first_name }}"
                      data-last="{{ $user->last_name }}"
                      data-email="{{ $user->email }}"
                      data-role="{{ $user->role }}"
                      data-dept="{{ $user->department }}"
                      data-status="{{ $user->status }}">Edit</button>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-form confirm-form">
                      @csrf @method('DELETE')
                      <button type="submit" class="action-btn remove">Remove</button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr><td colspan="7" class="empty-row">No users found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="pagination-bar">
          <span class="pagination-info" id="paginationInfo">
            @if($users->count() > 0)
              Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
            @else
              No users found
            @endif
          </span>
          <div class="pagination-controls">
            @if($users->onFirstPage())
              <button class="page-btn" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
              </button>
            @else
              <a href="{{ $users->previousPageUrl() }}" class="page-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
              </a>
            @endif
            <div class="page-numbers" id="pageNumbers">
              @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="page-num {{ $page == $users->currentPage() ? 'active' : '' }}">{{ $page }}</a>
              @endforeach
            </div>
            @if($users->hasMorePages())
              <a href="{{ $users->nextPageUrl() }}" class="page-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
              </a>
            @else
              <button class="page-btn" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
              </button>
            @endif
          </div>
        </div>

      </div>
    </div>

    <!-- ── ACCESS REQUESTS TAB ── -->
    <div id="tab-requests" class="admin-tab-panel hidden">
      <div class="admin-panel">
        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Applicant</th>
                <th>Role Requested</th>
                <th>Department</th>
                <th>Submitted</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($requests as $req)
              <tr>
                <td>
                  <div class="user-cell">
                    <div class="user-cell-avatar">{{ strtoupper(substr($req->first_name,0,1).substr($req->last_name,0,1)) }}</div>
                    <div>
                      <div class="user-cell-name">{{ $req->first_name }} {{ $req->last_name }}</div>
                      <div class="user-cell-email">{{ $req->email }}</div>
                    </div>
                  </div>
                </td>
                <td><span class="role-badge {{ Str::slug($req->role) }}">{{ $req->role }}</span></td>
                <td><span class="dept-badge">{{ $req->department }}</span></td>
                <td>{{ $req->created_at->format('M d, Y') }}</td>
                <td>
                  <div class="action-btns">
                    <form method="POST" action="{{ route('admin.requests.approve', $req) }}" class="inline-form">
                      @csrf
                      <button type="submit" class="action-btn approve">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.requests.reject', $req) }}" class="inline-form confirm-form">
                      @csrf @method('DELETE')
                      <button type="submit" class="action-btn remove">Reject</button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="empty-row">No pending access requests.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ── SYSTEM SETTINGS TAB ── -->
    <div id="tab-settings" class="admin-tab-panel hidden">
      <div class="settings-grid">

        @php
          $groups = [
            'departments' => 'Departments',
            'roles'       => 'Roles',
            'categories'  => 'Document Categories',
            'priorities'  => 'Priorities',
          ];
        @endphp

        @foreach($groups as $groupKey => $groupLabel)
        <div class="settings-card">
          <div class="settings-card-header">
            <span class="settings-card-title">{{ $groupLabel }}</span>
            <span class="settings-card-count">{{ isset($settings[$groupKey]) ? $settings[$groupKey]->count() : 0 }} options</span>
          </div>
          <div class="settings-card-body">
            @if(isset($settings[$groupKey]) && $settings[$groupKey]->count())
              @foreach($settings[$groupKey] as $item)
              <div class="settings-item">
                <span class="settings-item-value">{{ $item->value }}</span>
                <form method="POST" action="{{ route('admin.settings.destroy', $item) }}" class="inline-form confirm-form">
                  @csrf @method('DELETE')
                  <button type="submit" class="settings-remove-btn" title="Remove">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  </button>
                </form>
              </div>
              @endforeach
            @else
              <p class="settings-empty">No options yet.</p>
            @endif
          </div>
          <form method="POST" action="{{ route('admin.settings.store', $groupKey) }}" class="settings-add-form">
            @csrf
            <input type="text" name="value" placeholder="Add new {{ strtolower($groupLabel) }}…" class="settings-add-input">
            <button type="submit" class="settings-add-btn">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </button>
          </form>
        </div>
        @endforeach

      </div>
    </div>

    <!-- Sidebar -->
    <div class="admin-sidebar-col">
      <aside class="stats-sidebar">
        <div class="stats-sidebar-header">
          <span class="title-bar"></span>
          Role Breakdown
        </div>
        <div class="filetype-cards" id="roleCards">
          @php
            $total = $allFilteredUsers->count() ?: 1;
            $roleColors = ['Admin' => '#5b21b6', 'Records Officer' => '#0369a1', 'Department Head' => '#854d0e', 'Staff' => '#475569'];
          @endphp
          @foreach($roleCounts as $role => $count)
            @php
              $pct = round(($count / $total) * 100);
              $color = $roleColors[$role] ?? '#64748b';
            @endphp
            <div class="ft-card">
              <div class="ft-card-top">
                <span class="ft-label"><span class="ft-dot" style="background:{{ $color }}"></span>{{ $role }}</span>
                <span class="ft-pct">{{ $count }}</span>
              </div>
              <div class="ft-bar-track"><div class="ft-bar-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div></div>
            </div>
          @endforeach
        </div>
      </aside>
      <aside class="stats-sidebar">
        <div class="stats-sidebar-header">
          <span class="title-bar"></span>
          By Department
        </div>
        <div class="filetype-cards" id="deptCards">
          @php
            $deptColors = ['#1a2e4a','#2E6DA4','#2e7d32','#c62828','#e65100','#0369a1'];
          @endphp
          @foreach($deptCounts as $dept => $count)
            @php
              $pct = round(($count / $total) * 100);
              $colorIndex = array_search($dept, $deptCounts->keys()->toArray()) % count($deptColors);
              $color = $deptColors[$colorIndex];
            @endphp
            <div class="ft-card">
              <div class="ft-card-top">
                <span class="ft-label"><span class="ft-dot" style="background:{{ $color }}"></span>{{ $dept }}</span>
                <span class="ft-pct">{{ $count }}</span>
              </div>
              <div class="ft-bar-track"><div class="ft-bar-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div></div>
            </div>
          @endforeach
        </div>
      </aside>
    </div>

  </div>
</main>

<!-- Add / Edit User Modal -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modalTitle">Add User</h3>
      <button class="modal-close" id="modalClose">✕</button>
    </div>
    <form id="userForm" method="POST" action="{{ route('admin.users.store') }}" data-store-url="{{ route('admin.users.store') }}" novalidate>
      @csrf
      <span id="methodField"></span>
      <div class="modal-body">
        <input type="hidden" id="editUserId" name="edit_user_id">
        <div class="field-row">
          <div class="field-group">
            <label>First Name</label>
            <input type="text" id="fFirstName" name="first_name" placeholder="First name">
            <span class="field-error" id="errFirstName"></span>
          </div>
          <div class="field-group">
            <label>Last Name</label>
            <input type="text" id="fLastName" name="last_name" placeholder="Last name">
            <span class="field-error" id="errLastName"></span>
          </div>
        </div>
        <div class="field-group">
          <label>Email</label>
          <input type="email" id="fEmail" name="email" placeholder="email@example.com">
          <span class="field-error" id="errEmail"></span>
        </div>
        <div class="field-group" id="passwordGroup">
          <label>Temporary Password</label>
          <input type="text" id="fPassword" name="password" placeholder="Enter temporary password" autocomplete="off">
          <span class="field-error" id="errPassword"></span>
        </div>
        <div class="field-row">
          <div class="field-group">
            <label>Role</label>
            <div class="select-wrap">
              <select id="fRole" name="role">
                @foreach($roles as $role)
                  <option value="{{ $role }}">{{ $role }}</option>
                @endforeach
              </select>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
          </div>
          <div class="field-group">
            <label>Department</label>
            <div class="select-wrap">
              <select id="fDept" name="department">
                @foreach($departments as $dept)
                  <option value="{{ $dept }}">{{ $dept }}</option>
                @endforeach
              </select>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
          </div>
        </div>
        <div class="field-group">
          <label>Status</label>
          <div class="select-wrap">
            <select id="fStatus" name="status">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" id="modalCancel">Cancel</button>
        <button type="submit" class="btn-upload" id="modalSubmit">Add User</button>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
