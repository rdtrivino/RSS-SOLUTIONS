<?php

namespace App\Services;

use App\Models\Factura;
use App\Models\FacturaItem;
use App\Models\FacturaPago;
use App\Models\Radicado;
use App\Models\Soporte;
use App\Models\RadicadoRespuesta;
use Illuminate\Support\Facades\DB;

class FacturacionService
{
    /**
     * Asegura/crea factura en borrador para el radicado.
     * Usa campos reales de Factura (pagado/saldo) y no depende de 'moneda' en Factura.
     */
    public function ensureFacturaBorrador(Radicado $radicado, ?Soporte $soporte = null): Factura
    {
        $factura = Factura::firstOrCreate(
            ['radicado_id' => $radicado->id],
            [
                'numero'            => \App\Models\Factura::generarConsecutivo(),
                'estado'            => 'borrador',
                'subtotal'          => 0,
                'iva'               => 0,
                'total'             => 0,
                'pagado'            => 0,
                'saldo'             => 0,
                'cliente_nombre'    => $radicado->user->name ?? ($soporte->titulo ?? 'Cliente'),
                'cliente_email'     => $radicado->user->email ?? null,
                'cliente_telefono'  => $soporte->telefono ?? null,
                'cliente_ciudad'    => $soporte->ciudad ?? null,
                'cliente_direccion' => $soporte->direccion ?? null,
            ]
        );

        if (!$factura->cliente_nombre && ($radicado->user || $soporte)) {
            $factura->update([
                'cliente_nombre' => $radicado->user->name ?? ($soporte->titulo ?? 'Cliente'),
            ]);
        }

        return $factura->fresh(['items','pagos']);
    }

    /**
     * Registra anticipo (pago) 1:N en factura_pagos.monto y recalcula totales.
     * $meta: user_id, moneda, metodo, referencia, fecha_pago, notas
     */
    public function registrarAnticipo(Factura $factura, float $monto, array $meta = []): Factura
    {
        return DB::transaction(function () use ($factura, $monto, $meta) {
            FacturaPago::create([
                'factura_id' => $factura->id,
                'user_id'    => $meta['user_id'] ?? auth()->id(),
                'monto'      => $monto,
                'moneda'     => $meta['moneda'] ?? 'COP', // la moneda vive en FacturaPago
                'metodo'     => $meta['metodo'] ?? 'efectivo',
                'referencia' => $meta['referencia'] ?? null,
                'fecha_pago' => $meta['fecha_pago'] ?? now()->toDateString(),
                'notas'      => $meta['notas'] ?? null,
            ]);

            $factura->load(['items','pagos']);
            $factura->recalc();

            return $factura->fresh('pagos');
        });
    }

    /**
     * Cierra con ítems usando SOLO las columnas reales de tu tabla factura_items:
     * factura_id, producto_servicio_id (opcional), concepto, unidad, cantidad,
     * precio_unitario, iva_pct, total
     *
     * $items acepta 'concepto' o 'descripcion' para el nombre del ítem.
     */
    public function cerrarConItems(Factura $factura, array $items): Factura
    {
        return DB::transaction(function () use ($factura, $items) {

            // Limpia items previos
            $factura->items()->delete();

            foreach ($items as $it) {
                $concepto = (string) ($it['concepto'] ?? $it['descripcion'] ?? 'Servicio');
                $unidad   = (string) ($it['unidad'] ?? 'servicio');
                $cantidad = (float)  ($it['cantidad'] ?? 1);
                $pu       = (float)  ($it['precio_unitario'] ?? 0);
                $ivaPct   = (float)  ($it['iva_pct'] ?? 0);

                // Calcula total si no viene
                $base = round($cantidad * $pu, 2);
                $ivaL = round($base * ($ivaPct / 100), 2);
                $totL = (float) ($it['total'] ?? ($base + $ivaL));

                FacturaItem::create([
                    'factura_id'           => $factura->id,
                    'producto_servicio_id' => $it['producto_servicio_id'] ?? null, // opcional
                    'concepto'             => $concepto,
                    'unidad'               => $unidad,
                    'cantidad'             => $cantidad,
                    'precio_unitario'      => $pu,
                    'iva_pct'              => $ivaPct,
                    'total'                => round($totL, 2),
                ]);
            }

            // Recalcular totales (subtotal/iva/total/pagado/saldo)
            $factura->load(['items','pagos']);
            $factura->recalc();

            // Cambia estado a 'emitida' (o deja 'borrador' si prefieres)
            $factura->update(['estado' => 'emitida']);

            return $factura->fresh(['items','pagos']);
        });
    }

    /**
     * Crear/usar borrador desde Soporte y cerrar con items del RadicadoRespuesta.
     * Espera que $respuesta->data['items'] traiga los ítems en formato flexible.
     */
    public function crearDesdeSoporteCierre(Radicado $radicado, Soporte $soporte, RadicadoRespuesta $respuesta): Factura
    {
        $factura = $this->ensureFacturaBorrador($radicado, $soporte);

        $data  = $respuesta->data ?? [];
        $items = $data['items'] ?? [];

        return $this->cerrarConItems($factura, $items);
    }
}
