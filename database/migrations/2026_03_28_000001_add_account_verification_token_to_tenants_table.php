<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('account_verification_token', 32)->nullable()->after('owner_email');
            $table->timestamp('account_verification_token_expires_at')->nullable()->after('account_verification_token');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'account_verification_token',
                'account_verification_token_expires_at',
            ]);
        });
    }
};
