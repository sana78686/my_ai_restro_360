<?php

namespace App\Models;

use App\Helpers\FileUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase, HasMedia
{
    use HasDatabase, HasDomains, InteractsWithMedia;
    
    protected $connection = 'mysql';
    protected $guarded = [];

    // protected $fillable = [
    //     'id',
    //     'name',
    //     'business_name',
    //     'subdomain',
    //     'owner_name',
    //     'owner_email',
    //     'owner_phone',
    //     'address',
    //     'postal_code',
    //     'country',
    //     'state',
    //     'city',
    //     'place_id',
    //     'latitude',
    //     'longitude',
    //     'trial_started_at',
    //     'trial_ends_at',
    //     'status',
    // 'is_paid',
    // 'last_subscription_date',
    // ];

    protected $casts = [
        'trial_started_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'account_verification_token_expires_at' => 'datetime',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'business_name',
            'subdomain',
            'owner_name',
            'owner_email',
            'owner_phone',
            'address',
            'postal_code',
            'country',
            'state',
            'city',
            'place_id',
            'latitude',
            'longitude',
            'trial_started_at',
            'trial_ends_at',
            'status',
            'database_name',
            'stripe_customer_id', 'stripe_subscription_id', 'subscription_status',
            'subscription_ends_at',
            'account_verification_token',
            'account_verification_token_expires_at',
            'logo',
        ];
    }

    public function getDatabaseName(): string
    {
        return $this->database_name;
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function currentSubscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('subdomain', 'like', "%{$search}%")
                ->orWhere('owner_name', 'like', "%{$search}%")
                ->orWhere('owner_email', 'like', "%{$search}%");
        });
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function getLogoUrlAttribute()
    {
        return FileUpload::urlForStored($this->logo);
    }
    
    public function getDomain(): string
    {
        return $this->domains()->first()?->domain ?? 'No Domain';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images'); // multiple allowed
    }
} 