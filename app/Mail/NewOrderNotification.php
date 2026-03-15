<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $customer;
    public $tenant;
    public $owner;
    public $orderUrl;
    public $businessName;

    public function __construct($order)
    {
        $this->order = $order;
        $this->customer = $order->customer ?? null;
        $this->tenant = tenant() ?? null; // multi-tenant support, adjust if not needed

        // Get tenant business name from settings
        try {
            $setting = \App\Models\Setting::first();
            $this->businessName = $setting && !empty(trim($setting->business_name ?? '')) 
                ? trim($setting->business_name) 
                : \App\Helpers\AppNameHelper::getAppName();
        } catch (\Exception $e) {
            $this->businessName = \App\Helpers\AppNameHelper::getAppName();
        }

        // Store owner details (set statically or fetch from settings)
        $this->owner = (object) [
            'name'  => $this->tenant->name ?? 'Store Owner',
            'email' => $this->tenant->email ?? config('mail.from.address'),
        ];

        // URL to order in dashboard
        // $this->orderUrl = route('admin.orders.show', $order->id); // adjust route name
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Order - #' . ($this->order->order_number ?? 'N/A'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_placed_owner',
            with: [
                'order'    => $this->order,
                'customer' => $this->customer,
                'tenant'   => $this->tenant,
                'owner'    => $this->owner,
                'businessName' => $this->businessName,
                // 'orderUrl' => $this->orderUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
