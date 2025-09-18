<?php

namespace App\Services;

use App\Models\Factura;
use App\Models\FacturaItem;
use App\Models\FacturaPago;
use App\Models\Radicado;
use App\Models\Soporte;
use App\Models\RadicadoRespuesta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FacturacionService
{
    public function ensureFacturaBorrador(Radicado $radicado, ?Soporte $soporte = null): Factura
    {
        $factura = Factura::firstOrCreate(
            ['radicado_id' => $radicado->id],
            [
                'numero'            => 'FAC-' . date('Y') . '-' . Str::padLeft((string) $radicado->id, 6, '0'),
                'estado'            => 'borrador',
                'moneda'            => 'COP',
                'subtotal'          => 0,
                'iva'               => 0,
                'total'             => 0,
                'anticipo'          => 0,
                'saldo'             => 0,
                'cliente_nombre'    => $radicado->user->name ?? ($soporte->titulo ?? 'Cliente'),
                'cliente_email'     => $radicado->user->email ?? null,
                'cliente_telefono'  => $soporte->telefono ?? null,
                'cliente_ciudad'    => $soporte->ciudad ?? null,
                'cliente_direccion' => $soporte->direccion ?? null,
                'notas'             => 'Factura en borrador ligada al radicado.',
            ]
        );

        // Si no tenía datos de cliente y ahora sí, los completamos
        $dirty = false;
        if (! $factura->cliente_nombre && ($radicado->user || $soporte)) {
            $factura->cliente_nombre = $radicado->user->name ?? ($soporte->titulo ?? 'Cliente');
            $dirty = true;
        }
        if ($dirty) $factura->save();

        return $factura->fresh('items','pagos');
    }

    public function registrarAnticipo(Factura $factura, float $monto, array $meta = []): Factura
    {
        return DB::transaction(function () use ($factura, $monto, $meta) {
            $pago = FacturaPago::create([
                'factura_id' => $factura->id,
                'user_id'    => $meta['user_id'] ?? null,
                'monto'      => $monto,
                'moneda'     => $meta['moneda'] ?? $factura->moneda ?? 'COP',
                'metodo'     => $meta['metodo'] ?? 'efectivo',
                'referencia' => $meta['referencia'] ?? null,
                'fecha_pago' => $meta['fecha_pago'] ?? now()->toDateString(),
                'notas'      => $meta['notas'] ?? null,
            ]);

            // Recalcular anticipo/saldo
            $anticipo = (float) $factura->pagos()->sum('monto');
            $saldo    = max(0, (float) $factura->total - $anticipo);

            $factura->update([
                'anticipo' => $anticipo,
                'saldo'    => $saldo,
            ]);

            return $factura->fresh('pagos');
        });
    }

    /** Se usa al cerrar el soporte: setea items y totales, y recalcula saldo con los anticipos ya registrados. */
    public function cerrarConItems(Factura $factura, array $items, array $totales): Factura
    {
        return DB::transaction(function () use ($factura, $items, $totales) {
            // Limpia items (por si ya existían)
            $factura->items()->delete();

            $subtotal = (float) ($totales['subtotal'] ?? 0);
            $iva      = (float) ($totales['iva'] ?? 0);
            $total    = (float) ($totales['total'] ?? 0);

            $orden = 1;
            foreach ($items as $it) {
                FacturaItem::create([
                    'factura_id'      => $factura->id,
                    'concepto'        => (string) ($it['concepto'] ?? 'Servicio'),
                    'unidad'          => (string) ($it['unidad'] ?? 'servicio'),
                    'cantidad'        => (float)  ($it['cantidad'] ?? 1),
                    'precio_unitario' => (float)  ($it['precio_unitario'] ?? 0),
                    'iva_pct'         => (float)  ($it['iva_pct'] ?? 0),
                    'subtotal'        => (float)  ($it['subtotal'] ?? 0),
                    'iva_monto'       => (float)  ($it['iva_monto'] ?? 0),
                    'total'           => (float)  ($it['total'] ?? 0),
                    'orden'           => $orden++,
                ]);
            }

            // Recalcula saldo con anticipos ya cargados
            $anticipo = (float) $factura->pagos()->sum('monto');
            $saldo    = max(0, $total - $anticipo);

            $factura->update([
                'estado'   => 'emitida', // o 'borrador' si prefieres; yo al cerrar emito.
                'subtotal' => $subtotal,
                'iva'      => $iva,
                'total'    => $total,
                'anticipo' => $anticipo,
                'saldo'    => $saldo,
            ]);

            return $factura->fresh(['items','pagos']);
        });
    }

    /** (ya la tenías) crear desde cierre, si no existía borrador. */
    public function crearDesdeSoporteCierre(Radicado $radicado, Soporte $soporte, RadicadoRespuesta $respuesta): Factura
    {
        $factura = $this->ensureFacturaBorrador($radicado, $soporte);

        $data    = $respuesta->data ?? [];
        $items   = $data['items']   ?? [];
        $totales = $data['totales'] ?? ['subtotal'=>0,'iva'=>0,'total'=>0,'moneda'=>'COP'];

        return $this->cerrarConItems($factura, $items, $totales);
    }
}
