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
        $base = rtrim(config('app.url', url('/')), '/');

        return $this->markdown('emails.new-tenant-notification')
            ->subject('Action required: New restaurant registration — '.$this->tenant->business_name)
            ->with([
                'tenant' => $this->tenant,
                'reviewUrl' => $base.'/dashboard/tenants',
                'loginUrl' => $base.'/login',
            ]);
    }
}
