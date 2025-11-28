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
        Schema::create('rate_limit_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('client_id');
            $table->string('action');
            $table->integer('attempts')->default(1);
            $table->timestamp('window_start')->useCurrent();
            $table->timestamp('last_attempt')->useCurrent();
            
            // Índices
            $table->index(['client_id', 'action']);
            $table->index('window_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_limit_attempts');
    }
};
