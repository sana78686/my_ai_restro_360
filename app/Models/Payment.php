<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Payment extends Model
{
    use CentralConnection;

    protected $connection = 'central';
    
    protected $fillable = [
        'email',
        'tenant_id',
        'plan_id',
        'subscription_id',
        'amount',
        'currency',
        'stripe_payment_intent_id',
        'stripe_invoice_id',
        'stripe_charge_id',
        'status',
        'payment_type'
    ];

    protected $casts = [
        'amount' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the payment.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the plan associated with the payment.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the subscription associated with the payment.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
