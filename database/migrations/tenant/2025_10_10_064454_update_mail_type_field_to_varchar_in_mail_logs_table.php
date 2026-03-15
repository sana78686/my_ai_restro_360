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
        Schema::table('mail_logs', function (Blueprint $table) {
            // Change mail_type from enum to varchar 
            $table->string('mail_type', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_logs', function (Blueprint $table) {
            // revert the mail_type field back to enum if needed
            $table->enum('mail_type', ['order_type', 'contact', 'marketing', 'user_creation'])->change();
        });
    }
};
