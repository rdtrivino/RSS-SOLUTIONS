<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>POS {{ $factura->numero ?? '' }}</title>
<style>
    @page { margin: 6mm 5mm; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color:#111; }
    .center { text-align: center; }
    .right  { text-align: right; }
    .bold   { font-weight: bold; }
    .muted  { color:#666; }
    .line   { border-top: 1px dashed #999; margin: 6px 0; }
    table   { width:100%; border-collapse: collapse; }
    th, td  { padding: 2px 0; vertical-align: top; }
    .xs { font-size: 10px; }
    .wrap { word-break: break-word; }

    /* Sello “PAGADO” */
    .paid-stamp {
        margin-top: 6px;
        display: inline-block;
        padding: 2px 8px;
        border: 1px solid #0a7a2f;
        color: #0a7a2f;
        font-weight: 800;
        font-size: 11px;
        border-radius: 3px;
    }

    /* Código de barras al pie */
    .barcode-footer { text-align: center; margin-top: 8px; }
    .barcode-img { height: 48px; width: auto; display: inline-block; }
    .barcode-text { font-size: 10px; color: #334155; margin-top: 2px; }
</style>
</head>
<body>

@php
    // Formateador
    $fmt = fn($n, $d=2) => number_format((float)$n, $d, ',', '.');

    // ¿Pagada?
    $saldo    = (float) ($factura->saldo ?? 0);
    $esPagada = $saldo <= 0.00001;

    // ===== Código de barras (Code128) =====
    // Contenido: número de factura (cámbialo si deseas usar el id o radicado)
    $barcodeValue = (string) ($factura->numero ?? ('FAC-'.$factura->id));

    try {
        // Genera PNG base64 (ancho escala=2, alto=48px aprox)
        $barcodeBase64 = (new \Milon\Barcode\DNS1D)->getBarcodePNG($barcodeValue, 'C128', 2, 48);
    } catch (\Throwable $e) {
        $barcodeBase64 = null; // fallback silencioso
    }
@endphp

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
        <td class="right">{{ $esPagada ? 'PAGADA' : strtoupper($factura->estado ?? '') }}</td>
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
    @unless($esPagada)
        <tr>
            <td>Saldo</td>
            <td class="right bold">$ {{ $fmt($saldo) }}</td>
        </tr>
    @endunless
</table>

@if(!empty($mostrar_pagos))
    <div class="line"></div>
    <div class="bold">Pagos</div>
    <table>
        <tbody>
            @forelse($factura->pagos as $p)
                @php
                    $raw   = $p->fecha_pago ?? $p->fecha ?? null;
                    $fecha = $raw ? \Carbon\Carbon::parse($raw)->format('d/m/Y') : '';
                    $monto = (float)($p->monto ?? $p->valor ?? 0);
                @endphp
                <tr>
                    <td class="xs wrap">
                        {{ $fecha }} — {{ $p->metodo ?? '—' }} {{ $p->referencia ? '('.$p->referencia.')' : '' }}
                    </td>
                    <td class="right xs">$ {{ $fmt($monto) }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="xs muted center">Sin pagos</td></tr>
            @endforelse
        </tbody>
    </table>
@endif

<div class="line"></div>

{{-- Sello PAGADO si corresponde --}}
@if($esPagada)
    <div class="center">
        <span class="paid-stamp">PAGADO</span>
    </div>
@endif

{{-- ===== Código de barras al pie ===== --}}
<div class="barcode-footer">
    @if(!empty($barcodeBase64))
        <img class="barcode-img" src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Código de barras">
        <div class="barcode-text">{{ $barcodeValue }}</div>
    @else
        <div class="xs muted">[Sin código de barras]</div>
    @endif
</div>

<div class="line"></div>
<div class="center xs muted">¡Gracias por su compra!</div>
</body>
</html>
