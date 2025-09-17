<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Contrato extends Model
{
    use HasFactory;

    // Campos que se pueden asignar de forma masiva
    protected $fillable = [
        'nombre',
        'email',
        'celular',
        'empresa',
        'nit',
        'servicio',
        'especificar',
        'mensaje',
        'estado',
        'plantilla',
    ];

    /**
     * Relación polimórfica con Radicado (si lo usas).
     */
    public function radicado(): MorphOne
    {
        return $this->morphOne(Radicado::class, 'radicable');
    }
}

