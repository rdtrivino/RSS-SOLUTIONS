<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaPago extends Model
{
    protected $table = 'factura_pagos';

    protected $fillable = [
        'factura_id','user_id','monto','moneda',
        'metodo','referencia','fecha_pago','notas',
    ];

    protected $casts = [
        'monto'      => 'decimal:2',
        'fecha_pago' => 'date:Y-m-d',
    ];

    public function factura(): BelongsTo { return $this->belongsTo(Factura::class); }

    protected static function booted(): void
    {
        static::saved(fn ($p) => $p->factura?->recalc());
        static::deleted(fn ($p) => $p->factura?->recalc());
    }
}
