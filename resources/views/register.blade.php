<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – Request Access</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body class="reg-body">

  <div class="reg-top-bar">
    <img src="{{ asset('assets/logo.png') }}" alt="DARTS Logo" class="reg-logo">
  </div>

  <main class="reg-main">
    <div class="reg-card">

      <!-- Left accent panel -->
      <div class="reg-accent">
        <div class="reg-accent-content">
          <div class="reg-accent-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <h2 class="reg-accent-title">Request System Access</h2>
          <p class="reg-accent-desc">Submit your details for review. Access is granted by a system administrator after verification.</p>
          <ul class="reg-accent-list">
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              Secure document archiving
            </li>
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              Request tracking & routing
            </li>
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              Role-based access control
            </li>
          </ul>
          <div class="reg-accent-footer">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
          </div>
        </div>
      </div>

      <!-- Right: form panel -->
      <div class="reg-form-panel">
        <h1 class="reg-title">Create your account</h1>
        <p class="reg-subtitle">Fill in your information to request access to DARTS</p>

        <form id="registerForm" action="/register" method="POST" novalidate>
          @csrf
          @if ($errors->any())
            <div class="field-error" style="margin-bottom:1rem">{{ $errors->first() }}</div>
          @endif

          <div class="reg-field-row">
            <div class="field-group">
              <label for="firstName">First Name</label>
              <input type="text" id="firstName" name="first_name" placeholder="Juan" autocomplete="given-name">
              <span class="field-error" id="firstNameError"></span>
            </div>
            <div class="field-group">
              <label for="lastName">Last Name</label>
              <input type="text" id="lastName" name="last_name" placeholder="Dela Cruz" autocomplete="family-name">
              <span class="field-error" id="lastNameError"></span>
            </div>
          </div>

          <div class="field-group">
            <label for="regEmail">Email Address</label>
            <input type="email" id="regEmail" name="email" placeholder="name@example.com" autocomplete="email">
            <span class="field-error" id="regEmailError"></span>
          </div>

          <div class="reg-field-row">
            <div class="field-group">
              <label for="department">Department</label>
              <select id="department" name="department">
                <option value="" disabled selected>Select department</option>
                <option>Records Management</option>
                <option>Accounting</option>
                <option>Human Resources</option>
                <option>Commission on Audit</option>
                <option>Information Technology</option>
                <option>Legal</option>
                <option>Other</option>
              </select>
              <span class="field-error" id="departmentError"></span>
            </div>
            <div class="field-group">
              <label for="role">Assigned Role</label>
              <select id="role" name="role">
                <option value="" disabled selected>Select role</option>
                <option>Records Officer</option>
                <option>Department Staff</option>
                <option>Auditor</option>
                <option>Viewer</option>
              </select>
              <span class="field-error" id="roleError"></span>
            </div>
          </div>

          <div class="reg-field-row">
            <div class="field-group">
              <label for="regPassword">Password</label>
              <div class="password-wrap">
                <input type="password" id="regPassword" name="password" placeholder="••••••••" autocomplete="new-password">
                <button type="button" class="toggle-pw" id="togglePw1" aria-label="Show password">
                  <svg id="eyeIcon1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </div>
              <span class="field-error" id="regPasswordError"></span>
            </div>
            <div class="field-group">
              <label for="confirmPassword">Confirm Password</label>
              <div class="password-wrap">
                <input type="password" id="confirmPassword" name="password_confirmation" placeholder="••••••••" autocomplete="new-password">
                <button type="button" class="toggle-pw" id="togglePw2" aria-label="Show password">
                  <svg id="eyeIcon2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </div>
              <span class="field-error" id="confirmPasswordError"></span>
            </div>
          </div>

          <div class="reg-password-hint">
            <span id="hint-len" class="hint">8+ characters</span>
            <span id="hint-upper" class="hint">Uppercase</span>
            <span id="hint-num" class="hint">Number</span>
          </div>

          <div class="reg-terms">
            <input type="checkbox" id="terms" name="terms">
            <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
          </div>
          <span class="field-error" id="termsError"></span>

          <button type="submit" class="btn-register">Submit Access Request →</button>

        </form>
      </div>

    </div>
  </main>

  <footer class="reg-page-footer">
    <span>© 2026 DARTS Intelligence. All rights reserved.</span>
    <div class="footer-links">
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
      <a href="#">Documentation</a>
    </div>
  </footer>

  <script src="{{ asset('js/register.js') }}"></script>
</body>
</html>
