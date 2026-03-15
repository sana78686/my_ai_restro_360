<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Subscriber Notification</title>
    <style>
        :root {
            --color-primary: #d32f2f;
            --color-primary-dark: #b71c1c;
            --color-accent-bg: #ffebee;
            --color-surface: #ffffff;
            --color-background: #f6f6f6;
            --color-text: #333333;
            --color-muted: #777777;
            --color-border: #f1f1f1;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--color-background);
            color: var(--color-text);
            margin: 0;
            padding: 0;
            line-height: 1.6;
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
            text-align: center;
            padding: 24px;
        }

        .email__header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }

        .email__body {
            padding: 28px 24px;
        }

        .email__body p {
            margin: 12px 0;
            font-size: 15px;
        }

        .email__highlight {
            background-color: var(--color-accent-bg);
            padding: 18px 20px;
            border-left: 5px solid var(--color-primary);
            border-radius: 6px;
            margin: 24px 0;
        }

        .email__highlight strong {
            color: var(--color-primary-dark);
            display: block;
            margin-bottom: 8px;
        }

        .email__highlight ul {
            padding-left: 20px;
            margin: 0;
        }

        .email__highlight li {
            font-size: 14px;
            margin-bottom: 6px;
        }

        .email__footer {
            text-align: center;
            font-size: 13px;
            color: var(--color-muted);
            background-color: #fff5f5;
            padding: 18px 10px;
            border-top: 1px solid #ffcdd2;
        }

        .email__footer p {
            margin: 6px 0;
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
        }
    </style>
</head>

<body>
    <div class="email">
        <div class="email__header">
            <h1>🎉 New Subscription Activated</h1>
        </div>

        <div class="email__body">
            <p>Hello <strong>Admin</strong>,</p>
            <p>A new subscription has just been activated in the system.</p>

            <div class="email__highlight">
                <strong>Tenant Details:</strong>
                <ul>
                    <li><strong>Name:</strong> {{ $tenant->name ?? 'N/A' }}</li>
                    <li><strong>Email:</strong> {{ $tenant->owner_email ?? 'N/A' }}</li>
                    <li><strong>Subscription Date:</strong> {{ now()->format('F d, Y') }}</li>
                    <li><strong>Tenant ID:</strong> {{ $tenant->id }}</li>
                </ul>
            </div>

            <p>You can view more details in the <a href="{{ config('app.url') }}/admin/tenants/{{ $tenant->id }}"
                    style="color: var(--color-primary); text-decoration: none; font-weight: 500;">Admin Dashboard</a>.
            </p>

            <p>Best regards,<br><strong>{{ config('app.name') }} System</strong></p>
        </div>

        <div class="email__footer">
            <p>This is an automated notification. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>