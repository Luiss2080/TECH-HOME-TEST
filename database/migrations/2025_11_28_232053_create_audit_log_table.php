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
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('accion', 100); // CREATE, UPDATE, DELETE, LOGIN, etc.
            $table->string('tabla_afectada', 100)->nullable();
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->json('valores_anteriores')->nullable();
            $table->json('valores_nuevos')->nullable();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamp('fecha_accion')->useCurrent();
            
            // Índices
            $table->index(['tabla_afectada', 'registro_id']);
            $table->index('accion');
            $table->index('fecha_accion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
