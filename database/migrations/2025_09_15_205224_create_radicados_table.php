<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radicados', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique(); // Ej: RAD-2025-AB12CD o SOP-2025-000123
            $table->string('modulo');               // soporte | contrato | pqr (solo para facilitar filtros)
            $table->morphs('radicable');            // radicable_type, radicable_id (polimórfica)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // quien radicó
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radicados');
    }
};
