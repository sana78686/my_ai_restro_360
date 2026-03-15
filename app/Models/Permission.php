<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'description',
        'group',
        'is_system'
    ];

    protected $casts = [
        'is_system' => 'boolean'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public static function getSystemPermissions()
    {
        return [
            'categories' => [
                'view_categories' => 'View Categories',
                'create_categories' => 'Create Categories',
                'edit_categories' => 'Edit Categories',
                'delete_categories' => 'Delete Categories',
                'sort_categories' => 'Sort Categories'
            ],
            'products' => [
                'view_products' => 'View Products',
                'create_products' => 'Create Products',
                'edit_products' => 'Edit Products',
                'delete_products' => 'Delete Products',
                'sort_products' => 'Sort Products'
            ],
            'orders' => [
                'view_orders' => 'View Orders',
                'create_orders' => 'Create Orders',
                'edit_orders' => 'Edit Orders',
                'delete_orders' => 'Delete Orders'
            ],
            'settings' => [
                'manage_settings' => 'Manage Settings',
                'manage_users' => 'Manage Users',
                'manage_roles' => 'Manage Roles'
            ]
        ];
    }
}
