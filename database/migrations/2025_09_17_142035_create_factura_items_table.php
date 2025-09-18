<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factura_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('factura_id')
                ->constrained('facturas')
                ->cascadeOnDelete();

            // Si usas catálogo de productos/servicios (opcional):
            $table->foreignId('producto_servicio_id')->nullable()->constrained('productos_servicios')->nullOnDelete();

            $table->string('concepto', 200);
            $table->decimal('cantidad', 12, 2)->default(1);
            $table->string('unidad', 30)->default('servicio');
            $table->decimal('precio_unitario', 14, 2)->default(0);
            $table->decimal('iva_pct', 5, 2)->default(19.00);

            // totales del ítem (congelados)
            $table->decimal('total', 14, 2)->default(0);

            $table->timestamps();

            $table->index(['factura_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_items');
    }
};
