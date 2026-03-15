<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'description',
        'is_system',
        'restaurant_id'
    ];

    protected $casts = [
        'is_system' => 'boolean'
    ];

    protected $hidden = ['guard_name', 'created_at', 'updated_at', 'pivot'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public static function getSystemRoles()
    {
        return [
            'super_admin' => 'Super Admin',
            'restaurant_owner' => 'Restaurant Owner',
            'manager' => 'Manager',
            'staff' => 'Staff'
        ];
    }
}
