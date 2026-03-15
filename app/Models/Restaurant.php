<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Restaurant extends Model implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'name',
        'description',
        'address',
        'phone',
        'email',
        'logo',
        'trial_ends_at',
        'subscription_plan',
        'is_active'
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'description',
            'address',
            'phone',
            'email',
            'logo',
            'trial_ends_at',
            'subscription_plan',
            'is_active'
        ];
    }
}
