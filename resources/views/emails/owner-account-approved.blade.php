<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; line-height: 1.5; color: #1a1a1a;">
    <p>Hi {{ $tenant->owner_name ?? 'there' }},</p>
    <p>Great news — your <strong>{{ $tenant->business_name ?? $tenant->name }}</strong> account has been <strong>verified by our team</strong>. You can sign in to your restaurant dashboard anytime.</p>
    <p>
        <a href="{{ $loginUrl }}" style="display:inline-block;padding:12px 20px;background:#00844d;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;">Open sign-in</a>
    </p>
    <p style="font-size:14px;color:#555;">If the button does not work, copy this link into your browser:<br/>
        <a href="{{ $loginUrl }}">{{ $loginUrl }}</a>
    </p>
    <p style="font-size:14px;color:#888;">— {{ config('app.name') }}</p>
</body>
</html>
