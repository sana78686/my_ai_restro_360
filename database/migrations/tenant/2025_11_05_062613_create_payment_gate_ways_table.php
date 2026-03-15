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
        Schema::create('payment_gate_ways', function (Blueprint $table) {
             $table->id();
            // Gateway identification
            $table->string('name'); // stripe, paypal, razorpay, etc.
            $table->string('display_name');
            $table->string('provider'); // stripe, paypal, custom_gateway
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);
            $table->integer('priority')->default(0);
            // Dynamic credentials storage - NO HARDCODED FIELDS
            $table->json('credentials')->nullable(); // All gateway credentials
            $table->json('config')->nullable(); // Gateway-specific configuration
            $table->json('webhook_config')->nullable(); // Webhook settings
            $table->json('supported_features')->nullable(); // What this gateway can do
            // Gateway metadata
            $table->string('version')->default('1.0');
            $table->string('category')->default('payment_processor'); // payment_processor, wallet, bank_transfer
            $table->json('supported_currencies')->nullable();
            $table->json('supported_countries')->nullable();
            // Status and timestamps
            $table->string('status')->default('inactive'); // active, inactive, pending, suspended
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['provider', 'is_active']);
            $table->index(['status']);
            $table->unique(['name']);
        });

        // Gateway capabilities reference table
        Schema::create('payment_gateway_capabilities', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_name',125);
            $table->string('capability',125); // recurring_payments, refunds, international, 3d_secure
            $table->boolean('is_supported')->default(true);
            $table->json('limits')->nullable(); // Transaction limits, etc.
            $table->json('config')->nullable();

            $table->timestamps();

            $table->foreign('gateway_name')->references('name')->on('payment_gate_ways')->onDelete('cascade');
            $table->index(['gateway_name', 'capability']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_capabilities');
        Schema::dropIfExists('payment_gate_ways');

    }
};
