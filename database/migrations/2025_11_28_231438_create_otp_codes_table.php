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
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('codigo', 6);
            $table->datetime('expira_en');
            $table->boolean('utilizado')->default(0);
            $table->timestamp('creado_en')->useCurrent();
            
            // Índices
            $table->index('usuario_id');
            $table->index('codigo');
            $table->index('expira_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
