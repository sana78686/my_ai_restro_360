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
        Schema::create('email_settings', function (Blueprint $table) {
            $table->id();
            $table->string('driver',25)->nullable();
            $table->string('host',25)->nullable();
            $table->string('port',25)->nullable();
            $table->string('username',25)->nullable();
            $table->string('password',25)->nullable();
            $table->string('encryption',25)->nullable();
            $table->string('email',25)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_settings');
    }
};
