<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radicado_respuestas', function (Blueprint $table) {
            $table->id();

            // Radicado dueño de la respuesta
            $table->foreignId('radicado_id')
                ->constrained('radicados')
                ->cascadeOnDelete();

            // Operador que responde (opcional)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Formato del flujo (indexado)
            $table->enum('formato', ['soporte', 'contrato', 'pqr'])->index();

            // Resultado visible (por módulo) e indexado
            // soporte:   abierto | en_progreso | cerrado
            // contrato:  aceptado | rechazado | en_estudio
            // pqr:       resuelto | no_procedente | parcial
            $table->string('resultado', 50)->nullable()->index();

            // Payload flexible del formulario diligenciado
            $table->json('data')->nullable();

            // ¿Esta respuesta cerró el caso?
            $table->boolean('cierra_caso')->default(false)->index();

            // Ruta del PDF generado (disco 'public')
            $table->string('pdf_path', 2048)->nullable();

            $table->timestamps();

            // Índices compuestos útiles para consultas
            $table->index(['radicado_id', 'formato']);
            $table->index(['radicado_id', 'formato', 'cierra_caso']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radicado_respuestas');
    }
};
