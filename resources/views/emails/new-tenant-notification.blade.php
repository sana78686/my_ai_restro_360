@component('mail::message')
# New Restaurant Registration

A new restaurant has registered on your platform.

**Restaurant Details:**
- Name: {{ $tenant->name }}
- Location: {{ $tenant->city }}, {{ $tenant->state }}
- Email: {{ $tenant->owner_email }}

@component('mail::button', ['url' => $loginUrl])
View Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 