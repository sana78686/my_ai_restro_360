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
        Schema::table('stock_check_reqs', function (Blueprint $table) {
             // Add status field
            $table->enum('status', ['pending', 'in_progress', 'available', 'out_of_stock', 'completed'])->default('pending');
            
            // Add special instructions field
            $table->text('special_instructions')->nullable();
            
            // Add admin notes field
            $table->text('admin_notes')->nullable();
            
            // Add status updated timestamp
            $table->timestamp('status_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_check_reqs', function (Blueprint $table) {
            $table->dropColumn(['status', 'special_instructions', 'admin_notes', 'status_updated_at']);
        });
    }
};
