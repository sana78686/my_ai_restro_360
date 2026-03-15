<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unique_id',
        'ip_address',
        'name',
        'email',
        'phone',
        'address',
        'restaurant_id',
        'is_guest',
        'status',
        'created_by',
        'updated_by'
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class, 'customer_unique_id', 'unique_id');
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'customer_unique_id', 'unique_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
} 