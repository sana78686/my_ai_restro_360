<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewTenantNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function build()
    {
        return $this->markdown('emails.new-tenant-notification')
                    ->subject('New Restaurant Registration')
                    ->with([
                        'tenant' => $this->tenant,
                        'loginUrl' => config('app.url') . '/login'
                    ]);
    }
}
