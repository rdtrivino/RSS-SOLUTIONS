<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Radicado extends Model
{
    protected $fillable = [
        'numero',
        'modulo',
        'radicable_type',
        'radicable_id',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // si ya tienes relación polimórfica:
    public function radicable()
    {
        return $this->morphTo();
    }
    public function respuestas()
    {
    return $this->hasMany(\App\Models\RadicadoRespuesta::class);
    }
}
