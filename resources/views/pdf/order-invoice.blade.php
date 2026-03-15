<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice #{{ $order->order_number }}</title>
  <style>
    body { font-family: 'DejaVu Sans', sans-serif; color: #333; }
    .container { width: 100%; max-width: 800px; margin: auto; }
    .header { text-align: center; margin-bottom: 20px; }
    .header h1 { margin: 0; font-size: 22px; }
    .info { margin-bottom: 20px; }
    .info p { margin: 4px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    .total { text-align: right; font-size: 16px; font-weight: bold; margin-top: 20px; }
    .footer { text-align: center; font-size: 12px; margin-top: 30px; color: #666; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>{{ $tenant->name ?? config('app.name') }}</h1>
      <h2>Invoice</h2>
    </div>

    <div class="info">
      <p><strong>Invoice #:</strong> {{ $order->order_number }}</p>
      <p><strong>Date:</strong> {{ $order->created_at->format('F j, Y') }}</p>
      <p><strong>Customer:</strong> {{ $customer->name }}</p>
      <p><strong>Email:</strong> {{ $customer->email }}</p>
      <p><strong>Phone:</strong> {{ $customer->phone }}</p>
      <p><strong>Address:</strong> {{ $order->delivery_address }}</p>
      <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'N/A') }}</p>
      <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
    </div>

    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Qty</th>
          <th>Price</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        @forelse($order->orderDetails as $detail)
          <tr>
            <td>{{ $detail->product->name ?? 'N/A' }}</td>
            <td>{{ $detail->quantity }}</td>
            <td>{{ number_format($detail->price, 2) }}</td>
            <td>{{ number_format($detail->quantity * $detail->price, 2) }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4">No order details available</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <p class="total">Grand Total: {{ number_format($order->total_amount, 2) }} {{ $order->currency_symbol ?? '' }}</p>

    <div class="footer">
      <p>Thank you for your order!</p>
    </div>
  </div>
</body>
</html>
