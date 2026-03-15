<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeTenant extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function build()
    {
        $dashboardUrl = 'https://' . $this->tenant->domain . '.localhost:5000/dashboard';

        return $this->markdown('emails.welcome-tenant')
                    ->subject('Welcome to Our Platform!')
                    ->with([
                        'tenant' => $this->tenant,
                        'dashboardUrl' => $dashboardUrl
                    ]);
    }
}
