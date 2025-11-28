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
        Schema::create('laboratories', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->constrained('categories');
            $table->foreignId('docente_responsable_id')->constrained('users');
            $table->string('ubicacion', 150)->nullable();
            $table->integer('capacidad_estudiantes')->default(20);
            $table->json('equipamiento')->nullable();
            $table->boolean('disponible')->default(1);
            $table->text('normas_seguridad')->nullable();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();
            
            // Índices
            $table->index('disponible');
            $table->index('capacidad_estudiantes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratories');
    }
};
