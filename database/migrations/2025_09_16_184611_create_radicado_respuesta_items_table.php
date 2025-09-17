<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('radicado_respuesta_items', function (Blueprint $table) {
            $table->id();

            // Ítems anclados al paso/respuesta del radicado
            $table->foreignId('radicado_respuesta_id')
                ->constrained('radicado_respuestas')
                ->cascadeOnDelete();

            // (Opcional) referencia al catálogo
            $table->foreignId('producto_servicio_id')
                ->nullable()
                ->constrained('productos_servicios')
                ->nullOnDelete();

            // Datos del ítem (se "congelan" aquí para auditoría)
            $table->string('concepto', 200);                  // Ej: “Mantenimiento PC”
            $table->string('unidad', 30)->default('servicio');
            $table->decimal('cantidad', 12, 2)->default(1);
            $table->decimal('precio_unitario', 14, 2)->default(0);
            $table->decimal('descuento_pct', 5, 2)->nullable();   // 0..100
            $table->decimal('iva_pct', 5, 2)->default(19.00);
            $table->string('moneda', 6)->default('COP');

            // Montos calculados (evitan cambios por reglas futuras)
            $table->decimal('subtotal', 14, 2)->default(0);   // sin IVA, con descuento
            $table->decimal('iva_monto', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);

            $table->unsignedInteger('orden')->default(1);
            $table->timestamps();

            $table->index(['radicado_respuesta_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radicado_respuesta_items');
    }
};
