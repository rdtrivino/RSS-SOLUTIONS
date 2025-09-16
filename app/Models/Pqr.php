<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Pqr extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tipo',
        'descripcion',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Radicado asociado a esta PQR (relación polimórfica).
     */
    public function radicado(): MorphOne
    {
        return $this->morphOne(Radicado::class, 'radicable');
    }
}
