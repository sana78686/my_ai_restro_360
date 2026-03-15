<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
       use CentralConnection; // 👈 forces model to use central DB
    protected $fillable = [
        'name',
        'stripe_product_id',
        'stripe_price_id',
        'price',
        'currency',
        'interval',
         'features',
    ];


protected $casts = [
    'features' => 'array',
];

    /**
     * Optionally, you can define some helper methods or relationships.
     */

    // Format price as dollars (if you store price in cents)
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price / 100, 2) . ' ' . strtoupper($this->currency);
    }

public function subscriptions()
{
    return $this->hasMany(Subscription::class);
}


    // Example: check if plan is monthly
    public function isMonthly(): bool
    {
        return $this->interval === 'month';
    }

    // Example: check if plan is yearly
    public function isYearly(): bool
    {
        return $this->interval === 'year';
    }
}
