<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>POS {{ $factura->numero ?? '' }}</title>
<style>
    /* Ajustes para térmica 80mm y print inmediato */
    @page { size: 80mm auto; margin: 6mm 5mm; }
    @media print {
        body { width: 80mm; margin: 0 auto; }
        .no-print { display: none !important; }
    }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color:#111; width: 80mm; margin: 0 auto; }
    .center { text-align: center; }
    .right  { text-align: right; }
    .bold   { font-weight: bold; }
    .muted  { color:#666; }
    .line   { border-top: 1px dashed #999; margin: 6px 0; }
    table   { width:100%; border-collapse: collapse; }
    th, td  { padding: 2px 0; vertical-align: top; }
    .xs { font-size: 10px; }
    .wrap { word-break: break-word; }
    .toolbar { margin: 8px 0 10px; display: flex; gap: 8px; }
    .btn { border:1px solid #ddd; padding:6px 10px; border-radius:4px; background:#f8f8f8; font-size:12px; cursor:pointer; }
</style>
<script>
    // Dispara el diálogo de impresión al cargar, con pequeño delay por fuentes
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            window.print();
        }, 300);
    });
</script>
</head>
<body>

    <!-- Barra opcional para reintentar impresión o descargar POS PDF (no sale en papel) -->
    <div class="toolbar no-print">
        <button class="btn" onclick="window.print()">Imprimir</button>
        @php
            $posPath = "facturas/pos-{$factura->id}.pdf";
            $posUrl  = \Storage::disk('public')->exists($posPath) ? \Storage::disk('public')->url($posPath) : null;
        @endphp
        @if($posUrl)
            <a class="btn" href="{{ $posUrl }}" target="_blank" rel="noopener">Descargar POS PDF</a>
        @endif
        <button class="btn" onclick="window.close()">Cerrar</button>
    </div>

    <div class="center">
        <div class="bold">{{ $emisor['razon_social'] ?? config('app.name') }}</div>
        <div class="xs">
            NIT: {{ $emisor['nit'] ?? '' }}@if(!empty($emisor['dv']))-{{ $emisor['dv'] }}@endif
        </div>
    </div>

    <div class="line"></div>

    <table>
        <tr>
            <td class="wrap">Factura:</td>
            <td class="right bold">{{ $factura->numero }}</td>
        </tr>
        <tr>
            <td>Fecha:</td>
            <td class="right">{{ ($factura->created_at?->format('d/m/Y H:i')) ?? now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Estado:</td>
            <td class="right">{{ strtoupper($factura->estado ?? '') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <div class="bold">Cliente</div>
    <div class="wrap">
        {{ $factura->cliente_nombre ?? ($factura->radicado?->user?->name ?? 'Cliente') }}
    </div>
    @if($factura->cliente_doc_tipo || $factura->cliente_doc_num)
        <div class="xs muted">
            Doc: {{ trim(($factura->cliente_doc_tipo ?? '').' '.($factura->cliente_doc_num ?? '')) }}
        </div>
    @endif

    <div class="line"></div>

    @php $fmt = fn($n, $d=2) => number_format((float)$n, $d, ',', '.'); @endphp
    <table>
        <thead>
            <tr>
                <th class="wrap">Descripción</th>
                <th class="right">Cant</th>
                <th class="right">Vlr</th>
            </tr>
        </thead>
        <tbody>
            @forelse($factura->items as $it)
                @php
                    $cant = (float) $it->cantidad;
                    $pu   = (float) $it->precio_unitario;
                    $tot  = (float) $it->total;
                @endphp
                <tr>
                    <td class="wrap">{{ $it->concepto }}</td>
                    <td class="right">{{ $fmt($cant, 0) }}</td>
                    <td class="right">$ {{ $fmt($tot) }}</td>
                </tr>
                <tr>
                    <td class="xs muted wrap">PU: $ {{ $fmt($pu) }} — IVA {{ $fmt((float)$it->iva_pct, 0) }}%</td>
                    <td></td><td></td>
                </tr>
            @empty
                <tr><td colspan="3" class="center muted">Sin ítems</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td class="bold">Subtotal</td>
            <td class="right bold">$ {{ $fmt($factura->subtotal ?? 0) }}</td>
        </tr>
        <tr>
            <td>IVA</td>
            <td class="right">$ {{ $fmt($factura->iva ?? 0) }}</td>
        </tr>
        <tr>
            <td class="bold">TOTAL</td>
            <td class="right bold">$ {{ $fmt($factura->total ?? 0) }}</td>
        </tr>
        <tr>
            <td>Pagado</td>
            <td class="right">$ {{ $fmt($factura->pagado ?? 0) }}</td>
        </tr>
        <tr>
            <td>Saldo</td>
            <td class="right bold">$ {{ $fmt($factura->saldo ?? 0) }}</td>
        </tr>
    </table>

    @if(!empty($mostrar_pagos))
        <div class="line"></div>
        <div class="bold">Pagos</div>
        <table>
            <tbody>
                @forelse($factura->pagos as $p)
                    @php
                        $raw = $p->fecha_pago ?? $p->fecha ?? null;
                        $fecha = $raw ? \Carbon\Carbon::parse($raw)->format('d/m/Y') : '';
                        $monto = (float)($p->monto ?? $p->valor ?? 0);
                    @endphp
                    <tr>
                        <td class="xs wrap">{{ $fecha }} — {{ $p->metodo ?? '—' }} {{ $p->referencia ? '('.$p->referencia.')' : '' }}</td>
                        <td class="right xs">$ {{ $fmt($monto) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="xs muted center">Sin pagos</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="line"></div>
    <div class="center xs muted">¡Gracias por su compra!</div>

</body>
</html>
