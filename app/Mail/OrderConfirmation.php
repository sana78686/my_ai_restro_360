<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $tenantDomain;
    public $businessName;

    public function __construct($order)
    {
        $this->order = $order;

        // Get tenant business name from settings
        try {
            $setting = \App\Models\Setting::first();
            $this->businessName = $setting && !empty(trim($setting->business_name ?? '')) 
                ? trim($setting->business_name) 
                : \App\Helpers\AppNameHelper::getAppName();
        } catch (\Exception $e) {
            $this->businessName = \App\Helpers\AppNameHelper::getAppName();
        }

        // if using Stancl tenancy
        $this->tenantDomain = tenant()?->domains->first()?->domain 
            ?? request()->getHost(); // fallback
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation - ' . $this->businessName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_placed_customer',
            with: [
                'order'        => $this->order,
                'tenantDomain' => $this->tenantDomain,
                'businessName' => $this->businessName,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
