<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice - {{ \App\Helpers\AppNameHelper::getAppName() }}</title>
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
      background-color: #d53f8c;
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
      background-color: #fef2f2;
      padding: 15px;
      border-left: 5px solid #f87171;
      border-radius: 6px;
      font-size: 15px;
      color: #991b1b;
    }
    .button {
      display: inline-block;
      background-color: #f87171;
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
      <h1>Payment Failed</h1>
    </div>

    <div class="content">
      <p>Hi {{ $user->name ?? 'there' }},</p>

      <p>Unfortunately, we were unable to process your recent payment for your subscription/order.</p>

      <div class="highlight-box">
        <strong>Reason:</strong> {{ $failureReason ?? 'Insufficient funds or payment method declined' }}<br>
        <strong>Amount:</strong> {{ $amount ?? '$0.00' }}<br>
        <strong>Reference:</strong> {{ $reference ?? 'N/A' }}
      </div>

      <p>Please update your payment method or try again to ensure uninterrupted service.</p>

      <a href="{{ $billingUrl }}" class="button" target="_blank">Update Payment Method</a>

      <p>If you believe this was a mistake or need assistance, feel free to reply to this email or contact support.</p>

      <p>Thanks,<br>
      The {{ \App\Helpers\AppNameHelper::getAppName() }} Team</p>
    </div>

    <div class="footer">
      &copy; {{ date('Y') }} {{ \App\Helpers\AppNameHelper::getAppName() }}. All rights reserved.
    </div>
  </div>
</body>
</html>
