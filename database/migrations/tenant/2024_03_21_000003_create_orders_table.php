<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('tracking_id')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('applied_discount', 10, 2);
            $table->string('status')->default('pending'); // pending, confirmed, preparing, ready, delivered, cancelled
            $table->string('currency')->nullable();
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->string('payment_method')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->text('delivery_address')->nullable();
            $table->text('special_instructions')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}; 