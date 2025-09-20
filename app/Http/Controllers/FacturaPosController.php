<?php

namespace App\Http\Controllers;

use App\Models\Factura;

class FacturaPosController extends Controller
{
    public function print(Factura $factura)
    {
        // Si usas policies:
        // $this->authorize('view', $factura);

        $factura->load(['items', 'pagos', 'radicado.user']);

        $emisor = [
            'razon_social' => config('app.name'),
            'nit'          => config('app.company_nit', ''), // opcional
            'dv'           => config('app.company_dv', ''),  // opcional
        ];

        $mostrar_pagos = true;

        return view('pos.print', compact('factura', 'emisor', 'mostrar_pagos'));
    }
}
