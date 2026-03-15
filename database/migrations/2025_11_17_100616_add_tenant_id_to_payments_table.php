<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop existing foreign key constraint if it exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'payments' 
            AND COLUMN_NAME = 'tenant_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        foreach ($foreignKeys as $foreignKey) {
            DB::statement("ALTER TABLE payments DROP FOREIGN KEY `{$foreignKey->CONSTRAINT_NAME}`");
        }

        Schema::table('payments', function (Blueprint $table) {
            // Drop and recreate tenant_id column if it exists (to fix type mismatch)
            if (Schema::hasColumn('payments', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }
            
            // Add tenant_id with correct string type to match tenants.id
            $table->string('tenant_id')->nullable()->after('email');
            
            // Add subscription_id if it doesn't exist
            if (!Schema::hasColumn('payments', 'subscription_id')) {
                $table->string('subscription_id')->nullable()->after('plan_id');
            }
            
            // Add stripe_invoice_id if it doesn't exist
            if (!Schema::hasColumn('payments', 'stripe_invoice_id')) {
                $table->string('stripe_invoice_id')->nullable()->after('stripe_payment_intent_id');
            }
            
            // Add stripe_charge_id if it doesn't exist
            if (!Schema::hasColumn('payments', 'stripe_charge_id')) {
                $table->string('stripe_charge_id')->nullable()->after('stripe_invoice_id');
            }
            
            // Add payment_type if it doesn't exist
            if (!Schema::hasColumn('payments', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('status');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            // Add foreign key for tenant_id
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'tenant_id')) {
                $table->dropForeign(['tenant_id']);
            }
            
            $columnsToDrop = [];
            if (Schema::hasColumn('payments', 'tenant_id')) $columnsToDrop[] = 'tenant_id';
            if (Schema::hasColumn('payments', 'subscription_id')) $columnsToDrop[] = 'subscription_id';
            if (Schema::hasColumn('payments', 'stripe_invoice_id')) $columnsToDrop[] = 'stripe_invoice_id';
            if (Schema::hasColumn('payments', 'stripe_charge_id')) $columnsToDrop[] = 'stripe_charge_id';
            if (Schema::hasColumn('payments', 'payment_type')) $columnsToDrop[] = 'payment_type';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
