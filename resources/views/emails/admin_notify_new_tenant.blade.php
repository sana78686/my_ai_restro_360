<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>New Tenant Registration Alert</title>
  <style>
    :root {
      --color-primary: #d32f2f;
      --color-primary-dark: #b71c1c;
      --color-accent-bg: #ffebee;
      --color-surface: #ffffff;
      --color-background: #f9f9f9;
      --color-text: #333333;
      --color-muted: #777777;
      --color-border: #f0f0f0;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--color-background);
      color: var(--color-text);
      margin: 0;
      padding: 0;
    }

    .email {
      max-width: 600px;
      margin: 40px auto;
      background-color: var(--color-surface);
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .email__header {
      background-color: var(--color-primary);
      color: #ffffff;
      padding: 24px;
      text-align: center;
    }

    .email__header h2 {
      margin: 0;
      font-size: 22px;
      font-weight: 600;
    }

    .email__body {
      padding: 28px 24px;
    }

    .email__body h3 {
      margin: 0 0 16px;
      color: var(--color-primary-dark);
      font-size: 18px;
      font-weight: 600;
    }

    .email__body p {
      font-size: 15px;
      line-height: 1.6;
      margin: 12px 0;
    }

    .email__table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 16px;
      background-color: var(--color-accent-bg);
      border-radius: 6px;
      overflow: hidden;
    }

    .email__table td {
      padding: 10px 8px;
      border-bottom: 1px solid #ffcdd2;
      font-size: 14px;
    }

    .email__table td:first-child {
      font-weight: 600;
      width: 40%;
      color: var(--color-primary-dark);
    }

    .email__table tr:last-child td {
      border-bottom: none;
    }

    .email__cta {
      margin-top: 24px;
      text-align: center;
    }

    .email__cta a {
      display: inline-block;
      background-color: var(--color-primary);
      color: #fff !important;
      text-decoration: none;
      padding: 12px 28px;
      border-radius: 6px;
      font-weight: 500;
      transition: background 0.2s ease-in-out;
    }

    .email__cta a:hover {
      background-color: var(--color-primary-dark);
    }

    .email__footer {
      background-color: #fff5f5;
      border-top: 1px solid #ffcdd2;
      text-align: center;
      font-size: 13px;
      color: var(--color-muted);
      padding: 18px;
    }

    .email__footer a {
      color: var(--color-primary);
      text-decoration: none;
    }

    @media (max-width: 600px) {
      .email {
        margin: 10px;
      }

      .email__body {
        padding: 20px;
      }

      .email__cta a {
        width: 100%;
      }
    }
  </style>
</head>

<body>
  <div class="email">
    <div class="email__header">
      <h2>🏢 New Tenant Registered</h2>
    </div>

    <div class="email__body">
      <p>Hello <strong>Admin</strong>,</p>

      <p>A new tenant has successfully registered on <strong>{{ config('app.name') }}</strong>. Below are the details:
      </p>

      <table class="email__table">
        <tr>
          <td>Store Name:</td>
          <td>{{ $tenant->name }}</td>
        </tr>
        <tr>
          <td>Domain:</td>
          <td>{{ $tenant->domain }}</td>
        </tr>
        <tr>
          <td>Email:</td>
          <td>{{ $tenant->email }}</td>
        </tr>
        <tr>
          <td>Phone:</td>
          <td>{{ $tenant->phone ?? 'N/A' }}</td>
        </tr>
        <tr>
          <td>Owner Name:</td>
          <td>{{ $tenant->owner_name ?? 'N/A' }}</td>
        </tr>
        <tr>
          <td>Registered At:</td>
          <td>{{ $tenant->created_at->format('F d, Y H:i A') }}</td>
        </tr>
      </table>

      <div class="email__cta">
        <a href="{{ config('app.url') }}/tenants/{{ $tenant->id }}">View Tenant Profile</a>
      </div>

      <p>If this registration looks suspicious, please verify the details before activation.</p>

      <p>Best regards,<br><strong>{{ config('app.name') }} System</strong></p>
    </div>

    <div class="email__footer">
      © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
      Need help? <a href="#">Contact support</a>.
    </div>
  </div>
</body>

</html>