<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'factura_id',
        'fecha',
        'metodo',
        'referencia',
        'valor',
    ];

    protected $casts = [
        // Para trabajar con Carbon y serializar como Y-m-d
        'fecha' => 'date:Y-m-d',
        // decimal devuelve string con 2 decimales → útil para dinero
        'valor' => 'decimal:2',
    ];

    // Al tocar un Pago, actualiza el updated_at de la factura principal
    protected $touches = ['factura'];

    // ─────────────────────────────────────────────────────────────────────
    // Relaciones
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Pago directo (1:N) → pagos.factura_id
     */
    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    /**
     * Relación N:M con facturas aplicando valores en el pivote
     * Tabla pivote: factura_pagos (pago_id, factura_id, valor_aplicado)
     */
    public function facturas(): BelongsToMany
    {
        return $this->belongsToMany(Factura::class, 'factura_pagos', 'pago_id', 'factura_id')
            ->withPivot(['valor_aplicado'])
            ->withTimestamps();
    }

    // ─────────────────────────────────────────────────────────────────────
    // Atributos calculados
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Monto total ya aplicado de este pago en facturas N:M
     */
    public function getAplicadoAttribute(): string
    {
        // Suma segura del pivote, formateada con 2 decimales (string)
        $sum = $this->facturas()->sum('factura_pagos.valor_aplicado');
        return number_format((float) $sum, 2, '.', '');
    }

    /**
     * Saldo disponible del pago (valor - aplicado)
     */
    public function getDisponibleAttribute(): string
    {
        $aplicado = (float) $this->aplicado;
        $valor    = (float) $this->valor;
        $disp     = max($valor - $aplicado, 0);
        return number_format($disp, 2, '.', '');
    }

    /**
     * Representación bonita del valor
     */
    public function getValorFormatAttribute(): string
    {
        return number_format((float) $this->valor, 2, ',', '.');
    }

    // Si quieres exponer estos campos en arrays/json automáticamente:
    protected $appends = [
        'aplicado',
        'disponible',
        'valor_format',
    ];

    // ─────────────────────────────────────────────────────────────────────
    // Scopes útiles
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Filtra por método de pago (case-insensitive)
     */
    public function scopeMetodo($query, ?string $metodo)
    {
        if (! $metodo) return $query;
        return $query->whereRaw('LOWER(metodo) = ?', [mb_strtolower($metodo)]);
    }

    /**
     * Filtra por rango de fechas (inclusive)
     */
    public function scopeEntreFechas($query, ?string $desde, ?string $hasta)
    {
        if ($desde) $query->whereDate('fecha', '>=', $desde);
        if ($hasta) $query->whereDate('fecha', '<=', $hasta);
        return $query;
    }

    /**
     * Búsqueda por referencia (like)
     */
    public function scopeReferencia($query, ?string $ref)
    {
        if (! $ref) return $query;
        return $query->where('referencia', 'like', '%' . $ref . '%');
    }

    /**
     * Pagos con saldo disponible
     */
    public function scopeConSaldo($query)
    {
        // Usa una subconsulta para calcular lo aplicado y comparar
        return $query->whereRaw("
            (CAST(valor AS DECIMAL(12,2)) - COALESCE(
                (SELECT SUM(fp.valor_aplicado)
                 FROM factura_pagos fp
                 WHERE fp.pago_id = pagos.id), 0)
            ) > 0
        ");
    }

    // ─────────────────────────────────────────────────────────────────────
    // Mutators (normalización de datos)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Normaliza método: trim y mayúsculas
     */
    protected function metodo(): Attribute
    {
        return Attribute::make(
            set: fn ($v) => $v === null ? null : mb_strtoupper(trim($v))
        );
    }

    /**
     * Normaliza referencia: trim
     */
    protected function referencia(): Attribute
    {
        return Attribute::make(
            set: fn ($v) => $v === null ? null : trim($v)
        );
    }
}
