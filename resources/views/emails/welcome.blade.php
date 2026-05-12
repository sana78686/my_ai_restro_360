<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #43a047, #388e3c);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 15px;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome-badge {
            background: linear-gradient(135deg, #4caf50, #43a047);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            display: inline-block;
            font-weight: 600;
            margin: 20px 0;
            font-size: 16px;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }
        .feature-card {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        .feature-icon {
            font-size: 28px;
            margin-bottom: 10px;
            color: #43a047;
        }
        .feature-title {
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
        }
        .feature-desc {
            font-size: 14px;
            color: #666;
        }
        .cta-section {
            background-color: #e8f5e9;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #43a047;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 15px 0;
            font-size: 15px;
        }
        .cta-button:hover {
            background-color: #388e3c;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 25px 20px;
            text-align: center;
            color: #666;
            font-size: 13px;
        }
        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 30px 0;
        }
        .highlight {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
            font-size: 14px;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px 15px;
            }
            .feature-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>Thanks for registering with {{ config('app.name', 'AiRestro360') }}</h1>
            <p>Your request is being reviewed</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="welcome-badge">✅ We’ve received your registration</div>

            <h2 style="color: #333; margin-top: 0;">Hello {{ $user->name ?? 'there' }} 👋</h2>

            <p>Your restaurant account has been <strong>created</strong>, but it is <strong>not active yet</strong>. Our team (or your platform administrator) must <strong>approve</strong> your registration before you can sign in and use your full dashboard.</p>

            <p>You’ll get another email when your account has been approved. If you have questions, reply to our support contact below.</p>

            @if($tenant ?? false)
            <div class="highlight">
                <strong>🏪 Restaurant Name:</strong> {{ $tenant->business_name ?? 'Your Restaurant' }}<br>
                <strong>🌐 Subdomain:</strong> <a href="https://{{ \App\Helpers\TenantHost::fqdn($tenant->subdomain) }}" style="color:#43a047;">{{ \App\Helpers\TenantHost::fqdn($tenant->subdomain) }}</a>
            </div>
            @endif

            <div class="highlight">
                <strong>What happens next?</strong><br>
                After approval, you’ll be able to sign in at your restaurant URL and finish setup (menu, staff, orders, and more).
            </div>

            <div class="cta-section">
                <h3>Your login link (after approval)</h3>
                <p>You can bookmark this page for when your account is active.</p>
                <a href="{{ $loginUrl ?? (config('app.url') . '/login') }}" class="cta-button">Restaurant sign-in</a>
            </div>

            <div class="divider"></div>

            <h3>Need Help?</h3>
            <p>We’re here to support you as you get started:</p>
            <ul style="color: #666;">
                <li>📚 <strong>Docs:</strong> Access guides and setup help anytime.</li>
                <li>💬 <strong>Support:</strong> Email us at
                    <a href="mailto:{{ config('app.contact.email', 'info@apimstec.com') }}" style="color: #43a047;">{{ config('app.contact.email', 'info@apimstec.com') }}</a>
                </li>
                <li>🎥 <strong>Tutorials:</strong> Watch our quick video guides.</li>
            </ul>

            <div class="highlight">
                <strong>💡 Tip:</strong> Start by setting up your restaurant profile and adding your first menu — you'll be taking your first orders in minutes!
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                <strong>{{ config('app.name', 'AiRestro360') }}</strong> — Simplifying Restaurant Management
            </p>
            <p style="margin: 0; font-size: 12px; color: #999;">
                This is an automated message, please do not reply.
            </p>
        </div>
    </div>
</body>
</html>
