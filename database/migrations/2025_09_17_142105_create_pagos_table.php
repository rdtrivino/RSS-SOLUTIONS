<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('factura_id')
                ->constrained('facturas')
                ->cascadeOnDelete();

            $table->date('fecha')->nullable();
            $table->enum('metodo', ['efectivo','transferencia','tarjeta','nequi','daviplata','otro'])->default('efectivo');
            $table->string('referencia', 120)->nullable();
            $table->decimal('valor', 14, 2)->default(0);

            $table->timestamps();

            $table->index(['factura_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
