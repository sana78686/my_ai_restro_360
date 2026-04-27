<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create superuser account
        $superUser = User::create([
            'name' => 'Super Admin',
            'email' => 'sanafatima22647@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Restro360'),
        ]);

        // Assign super_user role
        $superUser->assignRole('super_user');
    }
}
