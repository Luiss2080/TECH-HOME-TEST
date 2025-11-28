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
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->constrained('categories');
            $table->string('codigo_producto', 50)->nullable();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->json('especificaciones')->nullable();
            $table->string('imagen_principal')->nullable();
            $table->json('imagenes_adicionales')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->string('proveedor', 150)->nullable();
            $table->enum('estado', ['Disponible', 'Agotado', 'Descontinuado'])->default('Disponible');
            $table->integer('stock_reservado')->default(0);
            $table->boolean('alerta_stock_bajo')->default(1);
            $table->boolean('permite_venta_sin_stock')->default(0);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();
            
            // Índices
            $table->index('codigo_producto');
            $table->index('estado');
            $table->index('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
