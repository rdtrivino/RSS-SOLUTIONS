<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductoServicio extends Model
{
    use HasFactory;

    protected $table = 'productos_servicios';

    protected $fillable = [
        'tipo', 'nombre', 'descripcion', 'unidad',
        'precio_base', 'moneda', 'iva_pct', 'activo',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
        'iva_pct'     => 'decimal:2',
        'activo'      => 'boolean',
    ];
}
