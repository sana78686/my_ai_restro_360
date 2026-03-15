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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('delivery_range_northeast_lat')->nullable()->comment('Northeast corner latitude of delivery area');
            $table->string('delivery_range_northeast_lng')->nullable()->comment('Northeast corner longitude of delivery area');
            $table->string('delivery_range_southwest_lat')->nullable()->comment('Southwest corner latitude of delivery area');
            $table->string('delivery_range_southwest_lng')->nullable()->comment('Southwest corner longitude of delivery area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_range_northeast_lat',
                'delivery_range_northeast_lng',
                'delivery_range_southwest_lat',
                'delivery_range_southwest_lng'
            ]);
        });
    }
};
