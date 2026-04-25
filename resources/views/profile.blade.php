<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – Profile</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/profile.css">
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
        <div class="profile-avatar" id="profileAvatar">JD</div>
        <button class="avatar-edit-btn" id="avatarEditBtn" title="Change avatar color">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
        </button>
      </div>
      <div class="profile-name" id="profileNameDisplay">Juan Dela Cruz</div>
      <div class="profile-role">Records Officer</div>
      <div class="profile-dept">Document Management Division</div>

      <div class="profile-meta">
        <div class="meta-row">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <span id="profileEmailDisplay">juan.delacruz@gov.ph</span>
        </div>
        <div class="meta-row">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <span>Member since January 2024</span>
        </div>
        <div class="meta-row">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span>Manila, Philippines</span>
        </div>
      </div>

      <div class="profile-badge-row">
        <span class="profile-badge">Active</span>
        <span class="profile-badge outline">Level 2 Access</span>
      </div>
    </aside>

    <!-- Right: edit forms -->
    <div class="profile-forms">

      <!-- Personal Information -->
      <div class="form-card">
        <div class="form-card-header">
          <div class="form-card-title">
            <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Personal Information
          </div>
        </div>
        <div class="form-card-body">
          <div class="field-row-2">
            <div class="field-group">
              <label>First Name</label>
              <input type="text" id="firstName" value="Juan">
            </div>
            <div class="field-group">
              <label>Last Name</label>
              <input type="text" id="lastName" value="Dela Cruz">
            </div>
          </div>
          <div class="field-group">
            <label>Display Name</label>
            <input type="text" id="displayName" value="Juan Dela Cruz">
          </div>
          <div class="field-group">
            <label>Position / Role</label>
            <input type="text" value="Records Officer" disabled>
            <span class="field-hint">Contact your administrator to change your role.</span>
          </div>
          <div class="form-actions">
            <button class="btn-secondary" onclick="resetPersonal()">Discard</button>
            <button class="btn-primary" onclick="savePersonal()">Save Changes</button>
          </div>
        </div>
      </div>

      <!-- Email -->
      <div class="form-card">
        <div class="form-card-header">
          <div class="form-card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Email Address
          </div>
        </div>
        <div class="form-card-body">
          <div class="field-group">
            <label>Current Email</label>
            <input type="email" id="currentEmail" value="juan.delacruz@gov.ph">
          </div>
          <div class="field-group">
            <label>New Email Address</label>
            <input type="email" id="newEmail" placeholder="Enter new email address">
          </div>
          <div class="field-group">
            <label>Confirm New Email</label>
            <input type="email" id="confirmEmail" placeholder="Re-enter new email address">
          </div>
          <div class="form-actions">
            <button class="btn-secondary" onclick="resetEmail()">Discard</button>
            <button class="btn-primary" onclick="saveEmail()">Update Email</button>
          </div>
        </div>
      </div>

      <!-- Password -->
      <div class="form-card">
        <div class="form-card-header">
          <div class="form-card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Change Password
          </div>
        </div>
        <div class="form-card-body">
          <div class="field-group">
            <label>Current Password</label>
            <div class="input-wrap">
              <input type="password" id="currentPassword" placeholder="Enter current password">
              <button class="toggle-pw" data-target="currentPassword" type="button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>
          <div class="field-group">
            <label>New Password</label>
            <div class="input-wrap">
              <input type="password" id="newPassword" placeholder="Minimum 8 characters">
              <button class="toggle-pw" data-target="newPassword" type="button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            <div class="pw-strength" id="pwStrength"></div>
          </div>
          <div class="field-group">
            <label>Confirm New Password</label>
            <div class="input-wrap">
              <input type="password" id="confirmPassword" placeholder="Re-enter new password">
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
            <button class="btn-secondary" onclick="resetPassword()">Discard</button>
            <button class="btn-primary" onclick="savePassword()">Update Password</button>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script src="js/profile.js"></script>
</body>
</html>
