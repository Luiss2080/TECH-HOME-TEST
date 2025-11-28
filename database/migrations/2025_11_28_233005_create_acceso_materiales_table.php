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
        Schema::create('acceso_materiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->timestamp('fecha_acceso')->useCurrent();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->integer('tiempo_permanencia')->default(0); // en segundos
            $table->boolean('descargado')->default(false);
            
            // Índices
            $table->index(['usuario_id', 'fecha_acceso']);
            $table->index('material_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acceso_materiales');
    }
};