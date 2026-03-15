<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to {{ \App\Helpers\AppNameHelper::getAppName() }}</title>
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
      background-color: #1e3a8a; /* dark professional blue */
      color: #ffffff;
      padding: 30px 25px;
      text-align: center;
    }
    .header h1 {
      margin: 0;
      font-size: 24px;
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
    .button {
      display: inline-block;
      background-color: #2563eb; /* professional blue */
      color: #ffffff;
      padding: 12px 20px;
      border-radius: 6px;
      font-weight: 600;
      text-decoration: none;
      margin-top: 20px;
    }
    .details {
      background-color: #f9fafb;
      padding: 15px;
      border-radius: 6px;
      margin: 20px 0;
      font-size: 15px;
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
      <h1>Welcome, {{ $user->name }} 👋</h1>
    </div>

    <div class="content">
      <p>Hello {{ $user->name }},</p>

      <p>We're excited to have you on board at <strong>{{ $tenant->name }}</strong>, hosted on the <strong>{{ \App\Helpers\AppNameHelper::getAppName() }}</strong> platform.</p>

      <div class="details">
        <p><strong>Email:</strong> {{ $user->email }}</p>
        @if(isset($plainPassword))
        <p><strong>Temporary Password:</strong> {{ $plainPassword }}</p>
        @endif
        <p><strong>Store Domain:</strong> {{ $tenant->domain }}</p>
      </div>

      <p>Click the button below to log in and explore your dashboard:</p>

      <a href="{{ $loginUrl }}" class="button" target="_blank">Access Your Account</a>

      <p>If you have any questions, feel free to contact support or your store administrator.</p>

      <p>Welcome aboard!<br><strong>{{ \App\Helpers\AppNameHelper::getAppName() }} Team</strong></p>
    </div>

    <div class="footer">
      &copy; {{ date('Y') }} {{ \App\Helpers\AppNameHelper::getAppName() }}. All rights reserved.
    </div>
  </div>
</body>
</html>
