<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaItem extends Model
{
    protected $table = 'factura_items';

    protected $fillable = [
        'factura_id',
        'producto_servicio_id', // <- opcional según tu catálogo
        'concepto',
        'unidad',
        'cantidad',
        'precio_unitario',
        'iva_pct',
        'total',
    ];

    protected $casts = [
        'cantidad'        => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'iva_pct'         => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    // (Opcional) relación al catálogo, si tienes App\Models\ProductoServicio
    public function producto(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ProductoServicio::class, 'producto_servicio_id');
    }

    protected static function booted(): void
    {
        static::saved(fn ($it) => $it->factura?->recalc());
        static::deleted(fn ($it) => $it->factura?->recalc());
    }
}
