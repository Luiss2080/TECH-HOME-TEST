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
        Schema::create('progreso_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('curso_id')->constrained('courses')->onDelete('cascade');
            $table->decimal('progreso_porcentaje', 5, 2)->default(0.00);
            $table->integer('tiempo_estudiado')->default(0); // en segundos
            $table->timestamp('ultima_actividad')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('completado')->default(false);
            $table->timestamp('fecha_inscripcion')->useCurrent();
            
            // Índices
            $table->unique(['estudiante_id', 'curso_id']);
            $table->index('completado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progreso_estudiantes');
    }
};