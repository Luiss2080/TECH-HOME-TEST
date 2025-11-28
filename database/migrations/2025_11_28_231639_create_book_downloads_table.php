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
        Schema::create('book_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('libro_id')->constrained('books');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamp('fecha_descarga')->useCurrent();
            
            // Índices
            $table->index(['usuario_id', 'libro_id']);
            $table->index('fecha_descarga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_downloads');
    }
};
