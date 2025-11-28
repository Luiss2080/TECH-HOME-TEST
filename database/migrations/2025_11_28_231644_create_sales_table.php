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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('numero_venta', 20)->unique();
            $table->foreignId('vendedor_id')->constrained('users');
            $table->foreignId('cliente_id')->constrained('users');
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['Pendiente', 'Completada', 'Cancelada'])->default('Pendiente');
            $table->string('metodo_pago', 50)->nullable();
            $table->text('notas')->nullable();
            $table->timestamp('fecha_venta')->useCurrent();
            
            // Índices
            $table->index('numero_venta');
            $table->index('estado');
            $table->index('fecha_venta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
