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
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('users');
            $table->foreignId('curso_id')->constrained('courses');
            $table->integer('progreso_porcentaje')->default(0);
            $table->integer('lecciones_completadas')->default(0);
            $table->integer('tiempo_total_minutos')->default(0);
            $table->timestamp('ultima_actividad')->useCurrent();
            $table->timestamp('fecha_inicio')->useCurrent();
            
            // Índices
            $table->unique(['estudiante_id', 'curso_id']);
            $table->index('progreso_porcentaje');
            $table->index('ultima_actividad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_progress');
    }
};
