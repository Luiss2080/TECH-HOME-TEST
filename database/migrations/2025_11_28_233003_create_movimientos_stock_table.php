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
        Schema::create('movimientos_stock', function (Blueprint $table) {
            $table->id();
            $table->string('producto_tipo', 20); // 'libro', 'componente'
            $table->unsignedBigInteger('producto_id');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste']);
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2)->nullable();
            $table->text('motivo')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('documento_referencia', 100)->nullable();
            $table->timestamp('fecha_movimiento')->useCurrent();
            
            // Índices
            $table->index(['producto_tipo', 'producto_id', 'fecha_movimiento']);
            $table->index('tipo_movimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_stock');
    }
};