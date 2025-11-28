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
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('curso_id')->constrained('courses')->onDelete('cascade');
            $table->timestamp('fecha_inscripcion')->useCurrent();
            $table->enum('estado', ['activa', 'completada', 'cancelada'])->default('activa');
            $table->decimal('precio_pagado', 10, 2)->default(0.00);
            $table->text('notas')->nullable();
            
            // Índices
            $table->unique(['estudiante_id', 'curso_id']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};