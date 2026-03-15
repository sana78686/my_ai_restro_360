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
        Schema::table('table_reservations', function (Blueprint $table) {
            // status 
            $table->string('status')->default('pending')->after('guests');
            // table no 
            $table->string('table_no')->nullable()->after('status');
            // assignet at 
            $table->timestamp('assigned_at')->nullable()->after('table_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_reservations', function (Blueprint $table) {
            $table->dropColumn(['status', 'table_no', 'assigned_at']);
        });
    }
};
