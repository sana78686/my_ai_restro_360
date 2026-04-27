<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tenants MODIFY COLUMN status ENUM('trial','active','inactive','suspended','pending') NOT NULL DEFAULT 'trial'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tenants MODIFY COLUMN status ENUM('trial','active','inactive','suspended') NOT NULL DEFAULT 'trial'");
    }
};
