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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->string('autor', 150);
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->constrained('categories');
            $table->string('isbn', 20)->nullable();
            $table->integer('paginas')->default(0);
            $table->string('editorial', 100)->nullable();
            $table->year('año_publicacion')->nullable();
            $table->string('imagen_portada')->nullable();
            $table->string('archivo_pdf', 500)->nullable();
            $table->string('enlace_externo', 500)->nullable();
            $table->integer('tamaño_archivo')->default(0);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->decimal('precio', 10, 2)->default(0.00);
            $table->enum('estado', ['Disponible', 'Agotado', 'Descontinuado'])->default('Disponible');
            $table->integer('descargas')->default(0);
            $table->boolean('es_gratuito')->default(1);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();
            
            // Índices
            $table->index('isbn');
            $table->index('estado');
            $table->index('es_gratuito');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
