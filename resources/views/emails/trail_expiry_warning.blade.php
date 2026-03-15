<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Trial is Ending Soon - {{ config('app.name') }}</title>
  <style>
    body {
      font-family: 'Segoe UI', Roboto, sans-serif;
      background-color: #f2f4f8;
      margin: 0;
      padding: 0;
      color: #2c3e50;
    }
    .container {
      max-width: 640px;
      margin: 40px auto;
      background-color: #ffffff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }
    .header {
      background-color: #1e3a8a;
      color: #ffffff;
      padding: 30px 25px;
      text-align: center;
    }
    .header h1 {
      margin: 0;
      font-size: 22px;
      font-weight: 600;
    }
    .content {
      padding: 30px 25px;
      font-size: 16px;
      line-height: 1.7;
    }
    .content p {
      margin-bottom: 16px;
    }
    .highlight-box {
      background-color: #fff7ed;
      padding: 15px;
      border-left: 5px solid #f97316;
      border-radius: 6px;
      font-size: 15px;
      color: #92400e;
    }
    .button {
      display: inline-block;
      background-color: #2563eb;
      color: #ffffff;
      padding: 12px 20px;
      border-radius: 6px;
      font-weight: 600;
      text-decoration: none;
      margin-top: 20px;
    }
    .footer {
      text-align: center;
      font-size: 13px;
      color: #7b8794;
      padding: 20px;
      background-color: #f1f5f9;
      border-top: 1px solid #e5e7eb;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Your Free Trial is Almost Over</h1>
    </div>

    <div class="content">
      <p>Hi {{ $tenant->owner_name ?? 'there' }},</p>

      <p>Just a quick reminder — your trial on <strong>{{ config('app.name') }}</strong> will expire in <strong>3 days</strong>.</p>

      <div class="highlight-box">
        Your trial ends on <strong>{{ \Carbon\Carbon::parse($tenant->trial_ends_at)->format('F j, Y') }}</strong>.
      </div>

      <p>To keep your store running without interruptions, please upgrade to a paid plan before your trial ends.</p>

      <a href="{{ $upgradeUrl }}" class="button" target="_blank">Upgrade Now</a>

      <p>Need help choosing a plan? Just reply to this email or contact our support team — we’re here to help!</p>

      <p>Thanks for trying {{ config('app.name') }}!<br>
      The {{ config('app.name') }} Team</p>
    </div>

    <div class="footer">
      &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
  </div>
</body>
</html>