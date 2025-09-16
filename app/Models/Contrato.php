<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Contrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'email',
        'celular',      // agregado
        'empresa',
        'nit',          // agregado
        'servicio',
        'especificar',  // agregado
        'mensaje',
        'estado',
    ];

    /**
     * Radicado asociado al contrato (relación polimórfica).
     */
    public function radicado(): MorphOne
    {
        return $this->morphOne(Radicado::class, 'radicable');
    }
}
