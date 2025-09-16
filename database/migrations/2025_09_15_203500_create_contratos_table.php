<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();

            // Información del contacto
            $table->string('nombre');
            $table->string('email');
            $table->string('celular', 20)->nullable();     // Número de celular
            $table->string('empresa')->nullable();
            $table->string('nit', 30)->nullable();         // NIT de la empresa
            $table->string('servicio');                    // ej: Infraestructura, Desarrollo web, etc.
            $table->string('especificar')->nullable();     // Campo adicional para detallar el servicio
            $table->text('mensaje')->nullable();

            // Estado del contrato / lead
            $table->enum('estado', ['pendiente', 'en_proceso', 'aceptado', 'rechazado'])
                ->default('pendiente');

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
