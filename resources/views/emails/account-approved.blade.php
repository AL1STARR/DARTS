<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
    .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .header { background: linear-gradient(135deg, #173651 0%, #2a5f8f 100%); padding: 36px 32px; text-align: center; border-bottom: 4px solid #a0c4e4; }
    .header h1 { color: #fff; margin: 12px 0 0; font-size: 22px; }
    .badge { display: block; background: #008000; width: fit-content; color: #fff; padding: 6px 12px; border-radius: 50px; font-size: 14px; margin: 8px auto 0; }
    .body { padding: 32px; color: #333; }
    .body p { line-height: 1.7; margin: 0 0 16px; }
    .btn { display: inline-block; background: #173651; color: white; padding: 12px 28px; border-radius: 6px; text-decoration: none; font-weight: bold; margin: 16px 0; }
    .info-box { background: #f0f4ff; border-left: 4px solid #173651; padding: 16px; border-radius: 4px; margin: 24px 0; }
    .info-box p { margin: 4px 0; font-size: 14px; }
    .footer { background: #173651; padding: 24px 32px; text-align: center; font-size: 12px; color: #a0c4e4; border-top: 4px solid #a0c4e4; }
    .footer a { color: #c8dff0; text-decoration: none; }  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <span class="badge">ACCOUNT APPROVED</span>
    </div>
    <div class="body">
      <p>Dear <strong>{{ $user->first_name }}</strong>,</p>
      <p>Great news! Your request for an account in the <strong>Document Archiving and Release Tracking System (DARTS)</strong> has been approved.</p>
      <p>You may now access the system using the details below:</p>
      <div class="info-box">
        <p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Role:</strong> {{ $user->role }}</p>
        <p><strong>Department:</strong> {{ $user->department }}</p>
      </div>
      <div class="info-box" style="border-left-color: #f59e0b; background: #fef3c7;">
        <p><strong>Temporary Password:</strong> {{ $temporaryPassword }}</p>
        <p style="font-size: 12px; color: #92400e; margin-top: 8px;"><em>Please change this password immediately after your first login for security.</em></p>
      </div>
      <p style="text-align:center;">
        <a href="{{ config('app.url') }}/login" class="btn">Log In to DARTS</a>
      </p>
      <p>If you have any issues logging in, please contact your system administrator.</p>
      <p>Welcome to DARTS.
      <p>Best regards,<br><strong>DARTS Team</strong></p>
    </div>
    <div class="footer">
      &copy; {{ date('Y') }} DARTS Intelligence. All rights reserved.
    </div>
  </div>
</body>
</html>
