<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
    .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .header { background: #a0c4e4; padding: 32px; text-align: center; }
    .header img { height: 64px; }
    .header h1 { color: #fff; margin: 12px 0 0; font-size: 22px; }
    .body { padding: 32px; color: #333; }
    .body p { line-height: 1.7; margin: 0 0 16px; }
    .info-box { background: #f0f4ff; border-left: 4px solid #173651; padding: 16px; border-radius: 4px; margin: 24px 0; }
    .info-box p { margin: 4px 0; font-size: 14px; }
    .footer { background: #f9f9f9; padding: 20px 32px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="{{ asset('images/darts-logo.png') }}" alt="DARTS Logo">
      <h1>Access Request Received</h1>
    </div>
    <div class="body">
      <p>Dear <strong>{{ $user->first_name }}</strong>,</p>
      <p>This is to confirm that your request for an account in the <strong>Document Archiving and Release Tracking System (DARTS)</strong> has been successfully received. Your request  is currently under review by our administrators.</p>
      <div class="info-box">
        <p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
        <p><strong>Email Address:</strong> {{ $user->email }}</p>
        <p><strong>Role:</strong> {{ $user->role }}</p>
        <p><strong>Department:</strong> {{ $user->department }}</p>
      </div>
      <p>If you did not initiate this request, please disregard this email or report it to the system administrator immediately.</p>
      <p>Thank you,<br><strong>DARTS Team</strong></p>
    </div>
    <div class="footer">
      &copy; {{ date('Y') }} DARTS. All rights reserved.
    </div>
  </div>
</body>
</html>
