<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('factura_pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('factura_id')
                ->constrained('facturas')
                ->cascadeOnDelete();

            $table->foreignId('user_id')        // quién registró el pago (op)
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('monto', 14, 2);
            $table->string('moneda', 6)->default('COP');

            $table->enum('metodo', [
                'efectivo','transferencia','tarjeta','nequi','daviplata','otro'
            ])->default('efectivo');

            $table->string('referencia', 190)->nullable();  // # transacción, consignación, etc.
            $table->date('fecha_pago')->nullable();
            $table->text('notas')->nullable();

            $table->timestamps();

            $table->index(['factura_id', 'metodo']);
            $table->index('fecha_pago');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_pagos');
    }
};
