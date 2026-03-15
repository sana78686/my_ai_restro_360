@component('mail::message')
# Welcome to {{ config('app.name') }}!

Dear {{ $tenant->name }},

Thank you for registering your restaurant on our platform! Your account has been successfully created and is ready to use.

**Your Restaurant Dashboard:**
You can access your dashboard using the button below. This is where you'll manage your restaurant's menu, orders, and settings.

@component('mail::button', ['url' => $dashboardUrl])
Access Dashboard
@endcomponent

**Quick Start Guide:**
1. Set up your menu items and categories
2. Configure your restaurant's operating hours
3. Customize your restaurant profile
4. Start accepting orders

If you need any assistance, our support team is available 24/7 to help you.

Best regards,<br>
{{ config('app.name') }} Team
@endcomponent 