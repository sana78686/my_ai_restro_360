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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->nullable();
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('public_phone')->nullable() ;
            $table->string('public_email')->nullable();
            $table->string('place_id')->nullable();
            $table->string('pickup_start_end_time')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('currency_symbol')->nullable()->default('$');
            $table->string('timezone')->nullable()->default('UTC');
            $table->string('date_format')->nullable()->default('Y-m-d');
            $table->string('time_format')->nullable()->default('H:i:s');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        
        // dd(DB::connection()->getDatabaseName());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
