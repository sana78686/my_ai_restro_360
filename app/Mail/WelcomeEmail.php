<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Tenant;
use App\Helpers\TenantHost;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $tenant;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Tenant $tenant)
    {
        $this->user = $user;
        $this->tenant = $tenant;
    }

    /**
     * Define the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your registration — ' . config('app.name', 'AiRestro360'),
        );
    }

    /**
     * Define the message content.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'user' => $this->user,
                'tenant' => $this->tenant,
                'loginUrl' => TenantHost::loginUrl((string) ($this->tenant->subdomain ?? ''), $this->tenant->id),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
