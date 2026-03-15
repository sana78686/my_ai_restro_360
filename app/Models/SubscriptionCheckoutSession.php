<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class SubscriptionCheckoutSession extends Model
{
    use HasFactory, CentralConnection;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'stripe_session_id',
        'status',
        'completed_at',
        'canceled_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
