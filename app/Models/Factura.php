<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Factura extends Model
{
    protected $fillable = [
        'radicado_id','numero','estado',
        'cliente_nombre','cliente_doc_tipo','cliente_doc_num',
        'cliente_email','cliente_telefono','cliente_direccion','cliente_ciudad',
        'cliente_empresa','cliente_nit',
        'subtotal','iva','total','pagado','saldo','pdf_path',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'iva'      => 'decimal:2',
        'total'    => 'decimal:2',
        'pagado'   => 'decimal:2',
        'saldo'    => 'decimal:2',
    ];

    /* ===================== Relaciones ===================== */

    public function radicado(): BelongsTo
    {
        return $this->belongsTo(Radicado::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FacturaItem::class);
    }

    /**
     * Pagos 1:N directos (tabla 'pagos' con factura_id).
     */
    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    /**
     * Pagos N:M vía pivot 'factura_pagos' (cuando un pago se reparte en varias facturas).
     * Se asume columna pivot: 'valor_aplicado'.
     */
    public function pagosPivot(): BelongsToMany
    {
        return $this->belongsToMany(Pago::class, 'factura_pagos', 'factura_id', 'pago_id')
            ->withPivot(['valor_aplicado'])
            ->withTimestamps();
    }

    /* ===================== Lógica de totales ===================== */

    /**
     * Suma pagos aplicados desde ambas fuentes:
     * - Directos (pagos.valor)
     * - Pivot (factura_pagos.valor_aplicado)
     */
    public function totalPagosAplicados(): float
    {
        $directos = (float) $this->pagos()->sum('valor');

        // Suma del campo en la tabla pivot.
        $pivot = (float) $this->pagosPivot()
            ->selectRaw('COALESCE(SUM(factura_pagos.valor_aplicado),0) as total')
            ->value('total');

        return round($directos + $pivot, 2);
    }

    /**
     * Recalcula subtotal/iva/total en base a items y saldo en base a pagos (directos + pivot).
     */
    public function recalcularTotales(): void
    {
        $subtotal = 0.0;
        $iva      = 0.0;
        $total    = 0.0;

        // Aseguramos items cargados
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        foreach ($items as $it) {
            $lineaBase = round($it->cantidad * $it->precio_unitario, 2);
            $lineaIva  = round($lineaBase * ($it->iva_pct / 100), 2);
            $lineaTot  = round($lineaBase + $lineaIva, 2);

            $subtotal += $lineaBase;
            $iva      += $lineaIva;
            $total    += $lineaTot;

            if ((float) $it->total !== (float) $lineaTot) {
                $it->total = $lineaTot;
                $it->save();
            }
        }

        // Pagos (directos + pivot)
        $pagado = $this->totalPagosAplicados();
        $saldo  = max(0, round($total - $pagado, 2));

        $this->update([
            'subtotal' => round($subtotal, 2),
            'iva'      => round($iva, 2),
            'total'    => round($total, 2),
            'pagado'   => round($pagado, 2),
            'saldo'    => $saldo,
        ]);
    }

    /**
     * Genera número con formato FAC-YYYY-######.
     */
    public static function generarConsecutivo(): string
    {
        $year = date('Y');
        $lastIdThisYear = static::whereYear('created_at', $year)->max('id');
        $seq  = str_pad((string) (($lastIdThisYear ?? 0) + 1), 6, '0', STR_PAD_LEFT);

        return "FAC-{$year}-{$seq}";
    }
}
