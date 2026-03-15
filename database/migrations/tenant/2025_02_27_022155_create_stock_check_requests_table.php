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
        Schema::create('stock_check_requests', function (Blueprint $table) {
            $table->id();
            $table->string('job_name')->nullable();
            $table->json('rooms')->nullable(); // Stores rooms with products under them
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('user_address')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_phone')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_check_requests');
    }
};
