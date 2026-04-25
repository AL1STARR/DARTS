<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DARTS – Login</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="css/login.css">
</head>
<body class="login-body">

  <div class="login-top-bar">
    <img src="assets/logo.png" alt="DARTS Logo" class="login-logo">
  </div>

  <main class="login-main">
    <div class="login-card">

      <!-- Left: image panel -->
      <div class="login-image">
        <div class="login-image-overlay">
          <p class="login-image-tag">Records Management</p>
          <h2 class="login-image-headline">Every document.<br>Exactly where it belongs.</h2>
          <p class="login-image-sub">Archive • Release • Track</p>
        </div>
      </div>

      <!-- Right: form panel -->
      <div class="login-form-panel">
        <h1 class="login-title">Login to DARTS</h1>
        <p class="login-subtitle">Enter your credentials to access the secure terminal</p>

        <form action="/login" method="POST" id="loginForm" novalidate>
          @csrf
          @if ($errors->any())
            <div class="field-error" style="margin-bottom:1rem">
              {{ $errors->first() }}
            </div>
          @endif
          <div class="field-group">
            <label for="email">Email Address</label>
            <input name="email" type="email" id="email" placeholder="name@example.com" autocomplete="email">
            <span class="field-error" id="emailError"></span>
          </div>

          <div class="field-group">
            <label for="password">Password</label>
            <div class="password-wrap">
              <input name="password" type="password" id="password" placeholder="••••••••" autocomplete="current-password">
              <button type="button" class="toggle-pw" id="togglePw" aria-label="Show password">
                <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            <span class="field-error" id="passwordError"></span>
          </div>

          <div class="forgot-row">
            <a href="#" class="forgot-link">Forgot Password?</a>
          </div>

          <button type="submit" class="btn-login">Login →</button>

          <div class="divider"><span>OR CONTINUE WITH</span></div>

          <button type="button" class="btn-google">
            <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.08 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-3.59-13.46-8.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
            Sign in with Google
          </button>
        </form>

        <div class="login-footer-row">
          <span>New personnel?</span>
          <a href="register.html" class="request-access">Request access</a>
        </div>
      </div>

    </div>
  </main>

  <footer class="login-page-footer">
    <span>© 2026 DARTS Intelligence. All rights reserved.</span>
    <div class="footer-links">
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
      <a href="#">Documentation</a>
    </div>
  </footer>

  <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
