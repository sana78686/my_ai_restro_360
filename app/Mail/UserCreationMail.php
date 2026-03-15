<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserCreationMail extends Mailable
{
    use Queueable, SerializesModels;

    use Queueable, SerializesModels;

    public $user;
    public $plainPassword;
    public $loginUrl;

    public function __construct(Model $user, $plainPassword, $loginUrl)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
        $this->loginUrl = $loginUrl;
    }

    public function build()
    {
        $subject = 'Your Account Has Been Created - ' . config('app.name');

        return $this->subject($subject)
            ->markdown('emails.user-creation')
            ->with([
                'user' => $this->user,
                'plainPassword' => $this->plainPassword,
                'loginUrl' => $this->loginUrl,
                'appName' => config('app.name'),
            ]);
    }
}
