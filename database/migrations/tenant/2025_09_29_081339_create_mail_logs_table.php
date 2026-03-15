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
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();

            // User who sent the mail (can link to users table later if needed)
            $table->string('send_by')->nullable();

            // Recipient of the mail
            $table->string('sent_to');

            // Mail content (text or HTML)
            $table->longText('content');

            // Mail type (order, marketing, contact, etc.)
            $table->enum('mail_type', ['order_type'])->default('order_type');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};
