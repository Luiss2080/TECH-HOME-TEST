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
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('curso_id')->constrained('courses')->onDelete('cascade');
            $table->decimal('nota', 5, 2);
            $table->timestamp('fecha_calificacion')->useCurrent();
            
            // Índices
            $table->unique(['estudiante_id', 'curso_id']);
            $table->index('nota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};