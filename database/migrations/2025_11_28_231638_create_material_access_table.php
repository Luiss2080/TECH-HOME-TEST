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
        Schema::create('material_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->enum('tipo_acceso', ['visualizado', 'descargado']);
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamp('fecha_acceso')->useCurrent();
            
            // Índices
            $table->index(['usuario_id', 'material_id']);
            $table->index('tipo_acceso');
            $table->index('fecha_acceso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_access');
    }
};
