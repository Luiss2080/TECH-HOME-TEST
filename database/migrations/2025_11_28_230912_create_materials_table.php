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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->string('tipo', 20);
            $table->string('archivo', 500)->nullable();
            $table->string('enlace_externo', 500)->nullable();
            $table->integer('tamaño_archivo')->default(0);
            $table->integer('duracion')->nullable()->comment('Duración en segundos para videos/audios');
            $table->foreignId('categoria_id')->constrained('categories');
            $table->foreignId('docente_id')->constrained('users');
            $table->string('imagen_preview')->nullable();
            $table->boolean('publico')->default(1)->comment('Si es accesible sin login');
            $table->integer('descargas')->default(0);
            $table->boolean('estado')->default(1);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();
            
            // Índices
            $table->index('tipo');
            $table->index('publico');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
