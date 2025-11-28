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
        Schema::create('activation_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email', 150);
            $table->string('token');
            $table->boolean('usado')->default(0);
            $table->timestamp('fecha_creacion')->useCurrent();
            
            // Índices
            $table->index('email');
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activation_tokens');
    }
};
