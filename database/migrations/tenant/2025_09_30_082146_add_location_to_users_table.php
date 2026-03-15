<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('password');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->timestamp('last_seen_at')->nullable()->after('longitude');
            $table->boolean('is_in_range')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'last_seen_at']);
        });
    }
};
