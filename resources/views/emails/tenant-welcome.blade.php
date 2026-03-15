@component('mail::message')
# Welcome to {{ config('app.name') }}

Dear {{ $user->name }},

Thank you for registering your restaurant, {{ $tenant->name }}, with our Restaurant Management System. We're excited to have you on board!

@if($isTrial)
## Trial Period
You are currently on a 14-day trial period which will end on {{ $trialEndsAt }}. During this time, you'll have access to all our features to help you evaluate our system.
@endif

## Getting Started
Here are some next steps to help you get started:

1. Log in to your dashboard at: {{ route('tenant.dashboard') }}
2. Set up your restaurant profile
3. Add your menu items and categories
4. Configure your settings

If you have any questions or need assistance, please don't hesitate to contact our support team.

@component('mail::button', ['url' => route('tenant.dashboard')])
Go to Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
