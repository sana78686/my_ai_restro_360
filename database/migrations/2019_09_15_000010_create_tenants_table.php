<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name')->nullable();
            $table->string('domain')->nullable()->unique();
            $table->timestamp('trial_ends_at')->nullable();
            $table->string('subdomain')->nullable()->unique();
            $table->string('owner_name')->nullable();
            $table->string('owner_email')->nullable();
            $table->string('logo')->nullable();
            $table->enum('status', ['trial','active', 'inactive', 'suspended'])->default('trial');
            // $table->softDeletes();
            $table->timestamps();
            $table->json('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
