<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('delivery_boy_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('delivery_assigned_at')->nullable();
            $table->timestamp('delivery_updated_at')->nullable();
            $table->timestamp('delivery_accepted_at')->nullable();
            $table->timestamp('delivery_started_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('delivery_canceled_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_boy_id']);
            $table->dropColumn('delivery_boy_id');
            $table->dropColumn('delivery_assigned_at');
            $table->dropColumn('delivery_updated_at');
            $table->dropColumn('delivery_accepted_at');
            $table->dropColumn('delivery_started_at');
            $table->dropColumn('delivered_at');
            $table->dropColumn('delivery_canceled_at');
        });
    }
};
