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
        Schema::create('sesiones_activas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->string('dispositivo');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('navegador', 100)->nullable();
            $table->string('sistema_operativo', 100)->nullable();
            $table->timestamp('fecha_inicio')->useCurrent();
            $table->timestamp('fecha_actividad')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('activa')->default(true);
            
            // Índices
            $table->index('session_id');
            $table->index(['usuario_id', 'activa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesiones_activas');
    }
};