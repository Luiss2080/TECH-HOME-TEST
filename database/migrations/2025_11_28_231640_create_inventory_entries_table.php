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
        Schema::create('inventory_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('componente_id')->constrained('components');
            $table->integer('cantidad');
            $table->decimal('precio_compra', 10, 2);
            $table->string('lote', 100)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->foreignId('usuario_registro_id')->constrained('users');
            $table->text('observaciones')->nullable();
            $table->timestamp('fecha_entrada')->useCurrent();
            
            // Índices
            $table->index('lote');
            $table->index('fecha_entrada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_entries');
    }
};
