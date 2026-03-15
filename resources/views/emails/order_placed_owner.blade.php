<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>New Order Notification - {{ \App\Helpers\AppNameHelper::getAppName() }}</title>
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
    .details {
      background-color: #f9fafb;
      padding: 15px;
      border-radius: 6px;
      margin: 20px 0;
      font-size: 15px;
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
      <h1>New Order Received!</h1>
    </div>

    <div class="content">
      <p>Hello {{ $owner->name ?? 'Store Owner' }},</p>

      <p>A new order has just been placed by
         <strong>{{ $customer->name ?? 'Unknown Customer' }}</strong>
         on your store.</p>

      <div class="details">
        <p><strong>Order Number:</strong> {{ $order->order_number ?? 'N/A' }}</p>
        <p><strong>Total Amount:</strong> {{ number_format($order->total_amount ?? 0, 2) }} {{ $order->currency_symbol ?? '' }}</p>
        <p><strong>Payment Method:</strong> {{ $order->payment_method ?? 'Not specified' }}</p>
        <p><strong>Order Date:</strong> {{ optional($order->created_at)->format('F j, Y - H:i A') ?? now()->format('F j, Y - H:i A') }}</p>
      </div>

      {{-- <a href="{{ $orderUrl ?? '#' }}" class="button" target="_blank">View Order in Dashboard</a> --}}

      <p>Please take necessary action to fulfill this order promptly.</p>

      <p>Thanks,<br>The {{ $businessName ?? \App\Helpers\AppNameHelper::getAppName() }} Team</p>
    </div>

    <div class="footer">
      &copy; {{ date('Y') }} {{ $businessName ?? \App\Helpers\AppNameHelper::getAppName() }}. All rights reserved.
    </div>
  </div>
</body>
</html>
