<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to {{ \App\Helpers\AppNameHelper::getAppName() }}</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
      color: #333;
    }
    .container {
      max-width: 600px;
      margin: 40px auto;
      background-color: #ffffff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .header {
      background-color: #4CAF50;
      color: white;
      text-align: center;
      padding: 30px 20px;
    }
    .header h1 {
      margin: 0;
      font-size: 24px;
    }
    .content {
      padding: 30px 20px;
      line-height: 1.6;
    }
    .button {
      display: inline-block;
      background-color: #4CAF50;
      color: white;
      padding: 12px 24px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      margin-top: 20px;
    }
    .footer {
      text-align: center;
      font-size: 13px;
      color: #999;
      padding: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Welcome to {{ \App\Helpers\AppNameHelper::getAppName() }}!</h1>
    </div>
    <div class="content">
      <p>Hi {{ $tenant->owner_name ?? 'there' }},</p>

      <p>We’re excited to have you on board! Your restaurant management platform has been successfully created.</p>

      <p>Here are your basic tenant details:</p>
      <ul>
        <li><strong>Store Name:</strong> {{ $tenant->name }}</li>
        <li><strong>Domain:</strong> {{ $tenant->domain }}</li>
        <li><strong>Email:</strong> {{ $tenant->email }}</li>
      </ul>

      <p>To get started, click below to log in and set up your store:</p>

      <a href="{{ $loginUrl }}" class="button" target="_blank">Access Your Dashboard</a>

      <p>If you have any questions, feel free to reach out to our support team.</p>

      <p>Cheers,<br>The {{ \App\Helpers\AppNameHelper::getAppName() }} Team</p>
    </div>
    <div class="footer">
      &copy; {{ date('Y') }} {{ \App\Helpers\AppNameHelper::getAppName() }}. All rights reserved.
    </div>
  </div>
</body>
</html>
