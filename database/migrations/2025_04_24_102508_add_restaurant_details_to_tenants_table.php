<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('name');
            $table->string('owner_phone')->nullable()->after('owner_email');
            $table->string('address')->nullable()->after('owner_phone');
            $table->string('postal_code')->nullable()->after('address');
            $table->string('country')->nullable()->after('postal_code');
            $table->string('state')->nullable()->after('country');
            $table->string('city')->nullable()->after('state');
            $table->string('place_id')->nullable()->after('city');
            $table->decimal('latitude', 10, 8)->nullable()->after('place_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->timestamp('trial_started_at')->default(DB::raw('CURRENT_TIMESTAMP'))->after('trial_ends_at');
            $table->string('database_name')->nullable()->after('domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
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
                'database_name'
            ]);
        });
    }
};
