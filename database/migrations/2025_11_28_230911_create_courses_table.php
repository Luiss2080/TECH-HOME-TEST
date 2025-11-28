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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->text('descripcion');
            $table->string('video_url', 500)->nullable()->comment('URL del video de YouTube');
            $table->foreignId('docente_id')->constrained('users');
            $table->foreignId('categoria_id')->constrained('categories');
            $table->string('imagen_portada')->nullable();
            $table->enum('nivel', ['Principiante', 'Intermedio', 'Avanzado'])->default('Principiante');
            $table->enum('estado', ['Borrador', 'Publicado', 'Archivado'])->default('Borrador');
            $table->boolean('es_gratuito')->default(1)->comment('1 = Gratuito, 0 = De pago');
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();
            
            // Índices
            $table->index('nivel');
            $table->index('estado');
            $table->index('es_gratuito');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
