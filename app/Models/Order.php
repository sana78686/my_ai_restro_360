<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    // protected $fillable = [
    //     'tracking_id',
    //     'customer_id',
    //     'restaurant_id',
    //     'total_amount',
    //     'status',
    //     'payment_status',
    //     'payment_method',
    //     'delivery_address',
    //     'special_instructions',
    //     'delivery_boy_id', // ← Add this line
    //     'delivery_assigned_at',
    //     'delivery_updated_at',
    //     'delivery_accepted_at',
    //     'delivery_started_at',
    //     'delivered_at',
    //     'delivery_canceled_at',
    // ];

    protected $guarded = [];

    protected $casts = [
        'total_amount' => 'decimal:2'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }


    // Add relationship (optional but useful)
    public function deliveryBoy()
    {
        return $this->belongsTo(User::class, 'delivery_boy_id');
    }
} 