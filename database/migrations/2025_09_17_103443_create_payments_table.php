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
        Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->string('email');
        $table->string('plan_id');
        $table->integer('amount');
        $table->string('currency')->default('usd');
        $table->string('stripe_payment_intent_id');
        $table->string('status')->default('pending'); // pending, succeeded, failed
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
