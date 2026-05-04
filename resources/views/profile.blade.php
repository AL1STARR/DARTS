<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – Profile</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>
<body>

@include('partials.nav')

<!-- ── CONTEXT BAR ── -->
<div class="context-bar">
  <div class="context-left">
    <span class="page-title">Profile</span>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-cur">Account Settings</span>
  </div>
  <div class="context-right">
    <div class="datetime" id="datetime"></div>
  </div>
</div>

<!-- ── PAGE ── -->
<main class="page">
  <div class="profile-layout">

    <!-- Left: identity card -->
    <aside class="profile-card">
      <div class="avatar-wrap">
        <div class="profile-avatar" id="profileAvatar">{{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}</div>
      </div>
      <div class="profile-name" id="profileNameDisplay">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
      <div class="profile-role">{{ auth()->user()->role }}</div>

      <div class="profile-meta">
        <div class="meta-row">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <span id="profileEmailDisplay">{{ auth()->user()->email }}</span>
        </div>
        <div class="meta-row">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <span>Member since {{ auth()->user()->created_at->format('F Y') }}</span>
        </div>
        <div class="meta-row">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span>{{ auth()->user()->department }}</span>
        </div>
      </div>

      <div class="profile-badge-row">
        <span class="profile-badge">{{ ucfirst(auth()->user()->status) }}</span>
        @if(auth()->user()->isFullAdmin())
          <span class="profile-badge level-access level-3">Level 3 Access</span>
        @elseif(auth()->user()->isDeptAdmin())
          <span class="profile-badge level-access level-2">Level 2 Access</span>
        @else
          <span class="profile-badge level-access level-1">Level 1 Access</span>
        @endif
      </div>

      <form method="POST" action="/logout" style="margin-top:1.5rem;width:100%">
        @csrf
        <button type="submit" class="btn-logout">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Logout
        </button>
      </form>
    </aside>

    <!-- Right: edit forms -->
    <div class="profile-forms">

      @if (session('status') === 'profile-information-updated')
        <div class="alert-success">Personal information updated successfully.</div>
      @endif
      @if (session('status') === 'password-updated')
        <div class="alert-success">Password updated successfully.</div>
      @endif

      <!-- Personal Information -->
      <div class="form-card">
        <div class="form-card-header">
          <div class="form-card-title">
            <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Personal Information
          </div>
        </div>
        <form method="POST" action="/user/profile-information" class="form-card-body">
          @csrf
          @method('PUT')
          @if ($errors->updateProfileInformation->any())
            <div class="field-error">{{ $errors->updateProfileInformation->first() }}</div>
          @endif
          <div class="field-row-2">
            <div class="field-group">
              <label>First Name</label>
              <input type="text" name="first_name" id="firstName" value="{{ auth()->user()->first_name }}">
            </div>
            <div class="field-group">
              <label>Last Name</label>
              <input type="text" name="last_name" id="lastName" value="{{ auth()->user()->last_name }}">
            </div>
          </div>
          <div class="field-group">
            <label>Email Address</label>
            <input type="email" name="email" value="{{ auth()->user()->email }}">
          </div>
          <div class="field-group">
            <label>Position / Role</label>
            <input type="text" value="{{ auth()->user()->role }}" disabled>
            <span class="field-hint">Contact your administrator to change your role.</span>
          </div>
          <div class="form-actions">
            <button type="reset" class="btn-secondary">Discard</button>
            <button type="submit" class="btn-primary">Save Changes</button>
          </div>
        </form>
      </div>

      <!-- Password -->
      <div class="form-card">
        <div class="form-card-header">
          <div class="form-card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Change Password
          </div>
        </div>
        <form method="POST" action="/user/password" class="form-card-body" id="passwordForm">
          @csrf
          @method('PUT')
          <div class="field-group">
            <label>Current Password</label>
            <div class="input-wrap">
              <input type="password" name="current_password" id="currentPassword" placeholder="Enter current password"
                class="{{ $errors->updatePassword->has('current_password') ? 'input-error' : '' }}">
              <button class="toggle-pw" data-target="currentPassword" type="button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            @error('current_password', 'updatePassword')
              <span class="field-error">{{ $message }}</span>
            @enderror
          </div>
          <div class="field-group">
            <label>New Password</label>
            <div class="input-wrap">
              <input type="password" name="password" id="newPassword" placeholder="Minimum 8 characters"
                class="{{ $errors->updatePassword->has('password') ? 'input-error' : '' }}">
              <button class="toggle-pw" data-target="newPassword" type="button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            @error('password', 'updatePassword')
              <span class="field-error">{{ $message }}</span>
            @enderror
            <div class="pw-strength" id="pwStrength"></div>
          </div>
          <div class="field-group">
            <label>Confirm New Password</label>
            <div class="input-wrap">
              <input type="password" name="password_confirmation" id="confirmPassword" placeholder="Re-enter new password"
                class="{{ $errors->updatePassword->has('password_confirmation') ? 'input-error' : '' }}">
              <button class="toggle-pw" data-target="confirmPassword" type="button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>
          <div class="pw-rules">
            <div class="pw-rule" id="rule-len">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              At least 8 characters
            </div>
            <div class="pw-rule" id="rule-upper">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              One uppercase letter
            </div>
            <div class="pw-rule" id="rule-num">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              One number
            </div>
            <div class="pw-rule" id="rule-special">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              One special character
            </div>
          </div>
          <div class="form-actions">
            <button type="reset" class="btn-secondary">Discard</button>
            <button type="submit" class="btn-primary">Update Password</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</main>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script src="{{ asset('js/profile.js') }}"></script>
</body>
</html>
