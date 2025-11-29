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
        Schema::create('reportes_acceso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->enum('recurso_tipo', ['curso', 'material', 'libro', 'laboratorio', 'componente']);
            $table->unsignedBigInteger('recurso_id');
            $table->string('recurso_nombre')->nullable();
            $table->enum('accion', ['visualizar', 'descargar', 'completar', 'inscribir', 'acceder']);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('duracion_sesion')->nullable()->comment('Duración en segundos');
            $table->json('datos_adicionales')->nullable();
            $table->timestamp('fecha_acceso')->useCurrent();
            
            // Índices
            $table->index(['recurso_tipo', 'recurso_id']);
            $table->index('fecha_acceso');
            $table->index('accion');
            $table->index(['usuario_id', 'fecha_acceso']);
            $table->index(['recurso_tipo', 'recurso_id', 'accion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes_acceso');
    }
};