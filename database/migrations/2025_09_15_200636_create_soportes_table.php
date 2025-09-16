<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soportes', function (Blueprint $table) {
            $table->id();

            // Relación con usuario que reporta
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Datos del reporte
            $table->string('titulo'); // Ej: "No funciona el internet"
            $table->text('descripcion'); // detalle del problema
            $table->enum('estado', ['abierto', 'en_progreso', 'cerrado'])->default('abierto');
            $table->enum('prioridad', ['baja', 'media', 'alta'])->default('media');

            // --- Nuevos campos solicitados ---
            $table->string('tipo_documento', 50)->nullable();   // CC, CE, NIT, Pasaporte, etc.
            $table->string('numero_documento', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('direccion', 200)->nullable();

            $table->string('telefono', 30)->nullable(); // Número de celular o fijo

            $table->string('tipo_servicio', 100)->nullable(); 
            // Ejemplo: "Soporte de red", "Computador", "Servidor", "Impresora"

            $table->enum('modalidad', ['local', 'recoger'])->default('local');
            // local = usuario trae el equipo, recoger = el soporte debe ir a domicilio

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('soportes');
    }
};