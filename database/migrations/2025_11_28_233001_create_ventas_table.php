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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_venta', 20)->unique();
            $table->foreignId('cliente_id')->constrained('users');
            $table->foreignId('vendedor_id')->nullable()->constrained('users');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0.00);
            $table->decimal('impuestos', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->enum('tipo_pago', ['Efectivo', 'Transferencia', 'Tarjeta', 'QR'])->default('Efectivo');
            $table->enum('estado', ['Pendiente', 'Completada', 'Cancelada', 'Reembolsada'])->default('Pendiente');
            $table->text('notas')->nullable();
            $table->timestamp('fecha_venta')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();
            
            // Índices
            $table->index('estado');
            $table->index('fecha_venta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};