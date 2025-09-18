<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaPago extends Model
{
    protected $fillable = [
        'factura_id','user_id','monto','moneda','metodo',
        'referencia','fecha_pago','notas',
    ];

    protected $casts = [
        'monto'      => 'float',
        'fecha_pago' => 'date',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
