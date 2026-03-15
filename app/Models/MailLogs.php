<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailLogs extends Model
{
    protected $fillable = [
        'send_by',
        'sent_to',
        'content',
        'mail_type',
    ];

    /**
     * Store a new mail log entry.
     *
     * @param string|null $sendBy   Who sent the mail (user name/email)
     * @param string      $sentTo   Recipient email
     * @param string      $content  Mail body/content
     * @param string      $mailType Mail type (order_type, contact, marketing, etc.)
     * @return static
     */
    public static function logMail(?string $sendBy, string $sentTo, string $content, ?string $mailType = 'default')
    {
        return self::create([
            'send_by'   => $sendBy,
            'sent_to'   => $sentTo,
            'content'   => $content,
            'mail_type' => $mailType,
        ]);
    }
}
