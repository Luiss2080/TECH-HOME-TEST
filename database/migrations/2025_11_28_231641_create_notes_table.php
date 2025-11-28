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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('users');
            $table->foreignId('curso_id')->constrained('courses');
            $table->decimal('calificacion', 3, 2)->nullable();
            $table->text('comentarios')->nullable();
            $table->foreignId('docente_id')->constrained('users');
            $table->timestamp('fecha_calificacion')->useCurrent();
            
            // Índices
            $table->unique(['estudiante_id', 'curso_id']);
            $table->index('calificacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
