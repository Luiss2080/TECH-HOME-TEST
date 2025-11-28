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
        Schema::create('reserved_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('libro_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('venta_id')->constrained('sales')->onDelete('cascade');
            $table->integer('cantidad_reservada');
            $table->timestamp('fecha_reserva')->useCurrent();
            $table->timestamp('fecha_expiracion');
            $table->enum('estado', ['activa', 'liberada', 'convertida'])->default('activa');
            
            // Índices
            $table->index('estado');
            $table->index('fecha_expiracion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserved_stock');
    }
};
