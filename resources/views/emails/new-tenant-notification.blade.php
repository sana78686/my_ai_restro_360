@component('mail::message')
# New restaurant registration

Someone has created an account and is **waiting for your approval** before they can use their restaurant dashboard.

**Details**
- Business: {{ $tenant->business_name ?? $tenant->name }}
- Owner: {{ $tenant->owner_name }}
- Email: {{ $tenant->owner_email }}
@if($tenant->owner_phone)
- Phone: {{ $tenant->owner_phone }}
@endif
@if($tenant->city || $tenant->state || $tenant->country)
- Location: {{ collect([$tenant->city, $tenant->state, $tenant->country])->filter()->implode(', ') }}
@endif

Please review the tenant in the super admin dashboard and set their status to **active** (and approve the owner account if you use that step) when you are ready.

@component('mail::button', ['url' => $reviewUrl ?? $loginUrl])
Review tenants
@endcomponent

@component('mail::button', ['url' => $loginUrl, 'color' => 'secondary'])
Super admin login
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 