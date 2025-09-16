<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pqrs', function (Blueprint $table) {
            $table->id();

            // Si el usuario logueado radica la PQR; puede ser anónimo
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('tipo', ['peticion', 'queja', 'reclamo']);
            $table->text('descripcion');

            // Flujo/estado
            $table->enum('estado', ['radicado', 'en_proceso', 'resuelto'])->default('radicado');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pqrs');
    }
};
