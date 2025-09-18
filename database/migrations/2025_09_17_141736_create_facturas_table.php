<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();

            // Relación con el radicado (soporte, contrato o pqr)
            $table->foreignId('radicado_id')
                ->constrained('radicados')
                ->cascadeOnDelete();

            // Número único de factura
            $table->string('numero', 40)->unique(); // Ej: FAC-2025-000001

            // Estado de la factura
            $table->enum('estado', ['borrador','emitida','anulada'])
                ->default('borrador')
                ->index();

            // Datos del cliente (se copian del radicado para mantener snapshot)
            $table->string('cliente_nombre', 180)->nullable();
            $table->string('cliente_doc_tipo', 10)->nullable();   // CC, NIT, CE, PAS
            $table->string('cliente_doc_num', 40)->nullable();
            $table->string('cliente_email', 190)->nullable();
            $table->string('cliente_telefono', 40)->nullable();
            $table->string('cliente_direccion', 200)->nullable();
            $table->string('cliente_ciudad', 100)->nullable();
            $table->string('cliente_empresa', 180)->nullable();
            $table->string('cliente_nit', 40)->nullable();

            // Totales de la factura
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('iva', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('pagado', 14, 2)->default(0);
            $table->decimal('saldo', 14, 2)->default(0);

            // Ruta del PDF en disco 'public'
            $table->string('pdf_path', 2048)->nullable();

            $table->timestamps();

            // Índices adicionales
            $table->index(['radicado_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
