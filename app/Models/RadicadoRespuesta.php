<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RadicadoRespuesta extends Model
{
    use HasFactory;

    protected $table = 'radicado_respuestas';

    protected $fillable = [
        'radicado_id',
        'user_id',
        'formato',
        'resultado',
        'data',
        'cierra_caso',
        'pdf_path',
    ];

    protected $casts = [
        'data'        => 'array',
        'cierra_caso' => 'boolean',
    ];

    // (opcionales) constantes por claridad
    public const FORMATO_SOPORTE  = 'soporte';
    public const FORMATO_CONTRATO = 'contrato';
    public const FORMATO_PQR      = 'pqr';

    /* =========================
     |  Relaciones
     |=========================*/
    public function radicado(): BelongsTo
    {
        return $this->belongsTo(Radicado::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RadicadoRespuestaItem::class);
    }

    /* =========================
     |  Scopes
     |=========================*/
    public function scopeFormato($query, string $formato)
    {
        return $query->where('formato', $formato);
    }

    public function scopeCierre($query)
    {
        return $query->where('cierra_caso', true);
    }

    public function scopeDeRadicado($query, int $radicadoId)
    {
        return $query->where('radicado_id', $radicadoId);
    }

    /* =========================
     |  Mutators
     |=========================*/
    public function setResultadoAttribute($value): void
    {
        $this->attributes['resultado'] = is_string($value) ? strtolower($value) : $value;
    }

    /* =========================
     |  Accessors & Helpers
     |=========================*/
    public function getFotosAttribute(): array
    {
        $data = $this->data ?? [];
        return is_array($data) && isset($data['fotos']) ? (array) $data['fotos'] : [];
    }

    public function getNotasAttribute(): ?string
    {
        $data = $this->data ?? [];
        return is_array($data) ? ($data['notas'] ?? null) : null;
    }

    // Sumatorias desde items (lazy). Para eficiencia en listados, usa withSum() en la query.
    public function getSubtotalSumAttribute(): float
    {
        return (float) $this->items->sum('subtotal');
    }

    public function getIvaSumAttribute(): float
    {
        return (float) $this->items->sum('iva_monto');
    }

    public function getTotalSumAttribute(): float
    {
        return (float) $this->items->sum('total');
    }
}
