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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('libro_id')->constrained('books')->onDelete('cascade');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste']);
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2)->nullable();
            $table->text('motivo')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('documento_referencia', 100)->nullable();
            $table->timestamp('fecha_movimiento')->useCurrent();
            
            // Índices
            $table->index(['libro_id', 'fecha_movimiento']);
            $table->index('tipo_movimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
