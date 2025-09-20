<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function radicado(): BelongsTo { return $this->belongsTo(Radicado::class); }
    public function items(): HasMany { return $this->hasMany(FacturaItem::class); }
    public function pagos(): HasMany { return $this->hasMany(FacturaPago::class); }

    // Compat: si algún código viejo usa "recalc()"
    public function recalc(): void { $this->recalcularTotales(); }

    public function recalcularTotales(): void
    {
        $subtotal = 0.0; $iva = 0.0; $total = 0.0;

        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        foreach ($items as $it) {
            $cant  = (float) ($it->cantidad ?? 0);
            $pu    = (float) ($it->precio_unitario ?? 0);
            $ivaPc = (float) ($it->iva_pct ?? 0);

            $base = round($cant * $pu, 2);
            $ivaL = round($base * ($ivaPc / 100), 2);
            $totL = round($base + $ivaL, 2);

            $subtotal += $base;
            $iva      += $ivaL;
            $total    += $totL;

            if ((float)$it->total !== (float)$totL) {
                $it->total = $totL;
                $it->save();
            }
        }

        // Si tu columna no es "monto" sino "valor", cambia aquí a sum('valor')
        $pagado = (float) $this->pagos()->sum('monto');
        $saldo  = max(0, round($total - $pagado, 2));

        $this->update([
            'subtotal' => round($subtotal, 2),
            'iva'      => round($iva, 2),
            'total'    => round($total, 2),
            'pagado'   => round($pagado, 2),
            'saldo'    => $saldo,
        ]);
    }

    // Helpers
    public static function normalizarMonto($monto): float
    {
        if (is_string($monto)) {
            $norm  = str_replace(['.', ','], ['', '.'], $monto);
            $monto = is_numeric($norm) ? (float) $norm : 0.0;
        }
        return round((float) $monto, 2);
    }

    public function registrarAnticipo(array $data): array
    {
        $monto = self::normalizarMonto($data['monto'] ?? $data['valor'] ?? 0);
        if ($monto <= 0) throw new \InvalidArgumentException('Monto inválido.');

        $pago = $this->pagos()->create([
            'user_id'    => $data['user_id'] ?? auth()->id(),
            'monto'      => $monto,
            'moneda'     => $data['moneda'] ?? 'COP',
            'metodo'     => $data['metodo'] ?? 'efectivo',
            'referencia' => $data['referencia'] ?? null,
            'fecha_pago' => $data['fecha_pago'] ?? $data['fecha'] ?? now()->toDateString(),
            'notas'      => $data['notas'] ?? null,
        ]);

        $this->load(['items','pagos']);
        $this->recalcularTotales();

        return ['id' => $pago->id];
    }

    public static function generarConsecutivo(): string
    {
        $year = date('Y');
        $lastIdThisYear = static::whereYear('created_at', $year)->max('id');
        $seq  = str_pad((string) (($lastIdThisYear ?? 0) + 1), 6, '0', STR_PAD_LEFT);
        return "FAC-{$year}-{$seq}";
    }

    // Compat: si hay código que aún lee/escribe "anticipo"
    public function getAnticipoAttribute() { return $this->pagado; }
    public function setAnticipoAttribute($v): void { $this->pagado = $v; }
}
