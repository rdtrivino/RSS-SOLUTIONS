<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Soporte extends Model
{
    use HasFactory;

    /**
     * Estados, prioridades y modalidades permitidas
     */
    public const ESTADOS     = ['abierto', 'en_progreso', 'cerrado'];
    public const PRIORIDADES = ['baja', 'media', 'alta'];
    public const MODALIDADES = ['local', 'recoger'];

    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'estado',
        'prioridad',

        // Nuevos campos de contacto / reporte
        'tipo_documento',
        'numero_documento',
        'ciudad',
        'direccion',
        'telefono',
        'tipo_servicio',
        'modalidad',

        // Campos del equipo
        'tipo_equipo',
        'marca',
        'modelo',
        'serial',
        'so',
        'accesorios',
    ];

    protected $casts = [
        'estado'    => 'string',
        'prioridad' => 'string',
        'modalidad' => 'string',
    ];

    /**
     * Relación con Usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Radicado asociado a este Soporte (relación polimórfica).
     */
    public function radicado(): MorphOne
    {
        return $this->morphOne(Radicado::class, 'radicable');
    }

    /**
     * Mutator: normaliza el número de documento (sin espacios, puntos ni guiones).
     */
    public function setNumeroDocumentoAttribute(?string $value): void
    {
        if ($value === null) {
            $this->attributes['numero_documento'] = null;
            return;
        }

        $normalizado = preg_replace('/[\s\.\-]+/', '', $value);
        $this->attributes['numero_documento'] = $normalizado;
    }

    /**
     * Mutator: limpia el número de teléfono (solo dígitos, + y -).
     */
    public function setTelefonoAttribute(?string $value): void
    {
        if ($value === null) {
            $this->attributes['telefono'] = null;
            return;
        }

        // Permitir solo dígitos, +, espacios y guiones
        $normalizado = preg_replace('/[^0-9\+\s\-]/', '', $value);
        $this->attributes['telefono'] = trim($normalizado);
    }

    /**
     * Scopes útiles (opcionales)
     */
    public function scopeDeCiudad($query, ?string $ciudad)
    {
        return $ciudad ? $query->where('ciudad', $ciudad) : $query;
    }

    public function scopeEstado($query, ?string $estado)
    {
        return $estado ? $query->where('estado', $estado) : $query;
    }

    public function scopePrioridad($query, ?string $prioridad)
    {
        return $prioridad ? $query->where('prioridad', $prioridad) : $query;
    }
}
