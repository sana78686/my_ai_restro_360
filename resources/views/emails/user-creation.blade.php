<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Welcome to {{ \App\Helpers\AppNameHelper::getAppName() }}</title>
  <style>
    :root {
      --color-primary: #d32f2f;
      --color-primary-dark: #b71c1c;
      --color-background: #f9f9f9;
      --color-surface: #ffffff;
      --color-text: #333333;
      --color-muted: #777777;
      --color-border: #e0e0e0;
    }

    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      background-color: var(--color-background);
      color: var(--color-text);
      margin: 0;
      padding: 0;
    }

    .email {
      max-width: 600px;
      margin: 40px auto;
      background-color: var(--color-surface);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .email__header {
      background-color: var(--color-primary);
      color: #fff;
      padding: 24px 32px;
      text-align: center;
    }

    .email__header-title {
      margin: 0;
      font-size: 22px;
      font-weight: 600;
      letter-spacing: 0.3px;
    }

    .email__body {
      padding: 32px;
      line-height: 1.6;
      font-size: 15px;
    }

    .email__body p {
      margin: 0 0 15px;
    }

    .email__credentials {
      background-color: #ffebee;
      border: 1px solid #ffcdd2;
      border-radius: 8px;
      padding: 16px 20px;
      margin: 20px 0;
    }

    .email__credentials p {
      margin: 6px 0;
      font-size: 15px;
    }

    .email__button {
      display: inline-block;
      background-color: var(--color-primary);
      color: #fff !important;
      text-decoration: none;
      padding: 12px 28px;
      border-radius: 6px;
      font-weight: 500;
      margin-top: 16px;
      transition: background 0.2s ease-in-out;
    }

    .email__button:hover {
      background-color: var(--color-primary-dark);
    }

    .email__divider {
      border: none;
      border-top: 1px solid var(--color-border);
      margin: 28px 0;
    }

    .email__subtext {
      font-size: 14px;
      color: var(--color-muted);
    }

    .email__footer {
      background-color: #fff5f5;
      border-top: 1px solid #ffcdd2;
      text-align: center;
      font-size: 13px;
      color: var(--color-muted);
      padding: 18px 20px;
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

      .email__button {
        width: 100%;
        text-align: center;
      }
    }
  </style>
</head>

<body>
  <div class="email">
    <div class="email__header">
      <h1 class="email__header-title">Welcome to {{ $appName }}</h1>
    </div>

    <div class="email__body">
      <p>Hello <strong>{{ $user->name ?? $user->email }}</strong>,</p>

      <p>We’re thrilled to have you join <strong>{{ $appName }}</strong>! Your account has been created successfully.
        Here are your login details:</p>

      <div class="email__credentials">
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Temporary Password:</strong> {{ $plainPassword }}</p>
      </div>

      <p>Click the button below to sign in and get started:</p>

      <p style="text-align:center;">
        <a href="{{ $loginUrl }}" class="email__button">Sign In to {{ $appName }}</a>
      </p>

      <p>For security, please change your password after your first login.</p>

      <p>Warm regards,<br><strong>{{ $appName }} Team</strong></p>

      <hr class="email__divider">

      <p class="email__subtext">
        If the button above doesn’t work, copy and paste this link into your browser:<br>
        <a href="{{ $loginUrl }}">{{ $loginUrl }}</a>
      </p>
    </div>

    <div class="email__footer">
      © {{ date('Y') }} {{ $appName }}. All rights reserved.<br>
      Didn’t request this account? <a href="#">Contact support</a>.
    </div>
  </div>
</body>

</html>
