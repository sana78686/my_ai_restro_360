<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Tenant;
use App\Models\User;

class TenantRegistrationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;
    public $user;

    public function __construct(Tenant $tenant, User $user)
    {
        $this->tenant = $tenant;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('New Tenant Registration: ' . $this->tenant->name)
            ->markdown('emails.tenant-registration-notification')
            ->with([
                'tenant' => $this->tenant,
                'user' => $this->user,
                'isTrial' => $this->tenant->status === 'trial',
                'trialEndsAt' => $this->tenant->trial_ends_at->format('F j, Y'),
            ]);
    }
} 