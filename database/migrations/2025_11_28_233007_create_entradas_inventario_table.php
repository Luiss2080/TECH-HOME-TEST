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
        Schema::create('entradas_inventario', function (Blueprint $table) {
            $table->id();
            $table->string('numero_entrada', 50)->unique();
            $table->foreignId('proveedor_id')->nullable()->constrained('users');
            $table->foreignId('responsable_id')->constrained('users');
            $table->text('observaciones')->nullable();
            $table->decimal('total_compra', 10, 2)->default(0.00);
            $table->timestamp('fecha_entrada')->useCurrent();
            $table->enum('estado', ['pendiente', 'recibida', 'verificada', 'cancelada'])->default('pendiente');
            
            // Índices
            $table->index('estado');
            $table->index('fecha_entrada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entradas_inventario');
    }
};