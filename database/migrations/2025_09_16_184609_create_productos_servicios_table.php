<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('productos_servicios', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['producto','servicio'])->default('servicio');
            $table->string('nombre', 180);
            $table->text('descripcion')->nullable();
            $table->string('unidad', 30)->default('servicio');   // hora, unidad, visita, etc.
            $table->decimal('precio_base', 14, 2)->default(0);
            $table->string('moneda', 6)->default('COP');
            $table->decimal('iva_pct', 5, 2)->default(19.00);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['tipo', 'activo']);
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos_servicios');
    }
};
