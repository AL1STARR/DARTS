<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
    .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .header { background: #a0c4e4; padding: 32px; text-align: center; }
    .header img { height: 64px; }
    .header h1 { color: #90EE90; margin: 12px 0 0; font-size: 22px; }
    .body { padding: 32px; color: #333; }
    .body p { line-height: 1.7; margin: 0 0 16px; }
    .btn { display: inline-block; background: #4f6ef7; color: #fff; padding: 12px 28px; border-radius: 6px; text-decoration: none; font-weight: bold; margin: 16px 0; }
    .info-box { background: #f0f4ff; border-left: 4px solid #4f6ef7; padding: 16px; border-radius: 4px; margin: 24px 0; }
    .info-box p { margin: 4px 0; font-size: 14px; }
    .footer { background: #f9f9f9; padding: 20px 32px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="{{ asset('images/darts-logo.png') }}" alt="DARTS Logo">
      <h1>Account Approved</h1>
    </div>
    <div class="body">
      <p>Hi <strong>{{ $user->first_name }}</strong>,</p>
      <p>Great news! Your access request to <strong>DARTS (Document and Records Tracking System)</strong> has been approved. You can now log in and start using the system.</p>
      <div class="info-box">
        <p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Role:</strong> {{ $user->role }}</p>
        <p><strong>Department:</strong> {{ $user->department }}</p>
      </div>
      <p style="text-align:center;">
        <a href="{{ config('app.url') }}/login" class="btn">Log In to DARTS</a>
      </p>
      <p>If you have any issues logging in, please contact your system administrator.</p>
      <p>Welcome aboard,<br><strong>DARTS Team</strong></p>
    </div>
    <div class="footer">
      &copy; {{ date('Y') }} DARTS. All rights reserved.
    </div>
  </div>
</body>
</html>
