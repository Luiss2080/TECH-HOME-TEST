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
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo_plan', ['basico', 'premium', 'profesional'])->default('basico');
            $table->date('fecha_inicio');
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['activa', 'suspendida', 'cancelada', 'expirada'])->default('activa');
            $table->decimal('precio', 10, 2)->default(0.00);
            $table->string('metodo_pago', 50)->nullable();
            $table->text('descripcion')->nullable();
            $table->json('caracteristicas')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index('estado');
            $table->index('fecha_vencimiento');
            $table->index(['usuario_id', 'estado']);
            $table->index(['tipo_plan', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suscripciones');
    }
};