<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Factura {{ $factura->numero ?? '' }}</title>
<style>
    /* DomPDF: usa CSS simple, sin flex ni grid */
    @page { margin: 22mm 18mm; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color:#111; }
    .muted { color:#666; }
    .xs { font-size: 10px; }
    .sm { font-size: 11px; }
    .md { font-size: 12px; }
    .lg { font-size: 14px; }
    .right { text-align: right; }
    .center { text-align: center; }
    .bold { font-weight: bold; }
    .sep { height: 6px; }
    .line { border-top: 1px solid #e5e7eb; height: 0; margin: 8px 0; }
    .pill { display:inline-block; padding:2px 8px; border:1px solid #94a3b8; border-radius: 999px; font-size:10px; }
    .accent { color: #0f172a; } /* gris oscuro elegante */
    .brand { font-size: 20px; letter-spacing:.3px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 6px 6px; vertical-align: top; }
    .t-bordered th, .t-bordered td { border: 1px solid #111; }
    .t-soft th { background: #f3f4f6; border: 1px solid #e5e7eb; }
    .t-soft td { border: 1px solid #e5e7eb; }
    .box { border:1px solid #e5e7eb; padding:8px; }
    .mt-4 { margin-top: 16px; }
    .mt-2 { margin-top: 8px; }
    .pt-2 { padding-top: 8px; }
    .badge-estado { background: #eef2ff; border:1px solid #c7d2fe; border-radius: 999px; padding:2px 8px; font-size: 10px; }
    .footer-note { margin-top: 16px; color:#475569; font-size:10px; line-height: 1.4; }
    .watermark {
        position: fixed; top: 50%; left: 50%;
        transform: translate(-50%, -50%) rotate(-20deg);
        font-size: 60px; color: #0002;
    }
</style>
</head>
<body>

@if(($factura->estado ?? '') === 'borrador')
    <div class="watermark">BORRADOR</div>
@endif

{{-- ENCABEZADO --}}
<table>
    <tr>
        <td style="width:60%;">
            <div class="brand accent bold">
                {{ $emisor['razon_social'] ?? config('app.name') }}
            </div>
            <div class="sm">
                <span class="bold">NIT:</span>
                {{ $emisor['nit'] ?? 'N/A' }}@if(!empty($emisor['dv']))-{{ $emisor['dv'] }}@endif
                @if(!empty($emisor['regimen'])) — {{ $emisor['regimen'] }} @endif
            </div>
            @if(!empty($emisor['direccion']) || !empty($emisor['ciudad']))
                <div class="sm">{{ $emisor['direccion'] ?? '' }} {{ !empty($emisor['ciudad']) ? ' - '.$emisor['ciudad'] : '' }}</div>
            @endif
            @if(!empty($emisor['telefono']) || !empty($emisor['email']))
                <div class="sm">
                    @if(!empty($emisor['telefono'])) Tel: {{ $emisor['telefono'] }} @endif
                    @if(!empty($emisor['email'])) — {{ $emisor['email'] }} @endif
                </div>
            @endif

            @if(!empty($emisor['dian_resolucion']))
                <div class="xs muted mt-2">
                    <span class="bold">Resolución DIAN:</span> {{ $emisor['dian_resolucion'] }}
                    @if(!empty($emisor['dian_prefijo']) && !empty($emisor['dian_rango']))
                        — Prefijo {{ $emisor['dian_prefijo'] }} Rango {{ $emisor['dian_rango'] }}
                    @endif
                    @if(!empty($emisor['dian_vigencia'])) — Vigencia: {{ $emisor['dian_vigencia'] }} @endif
                </div>
            @endif
        </td>
        <td class="right" style="width:40%;">
            <div class="lg bold">FACTURA DE VENTA</div>
            <div class="mt-2">
                <div class="sm"><span class="bold">N°:</span> {{ $factura->numero }}</div>
                <div class="sm">Estado: <span class="badge-estado">{{ strtoupper($factura->estado) }}</span></div>
                <div class="xs muted">Emisión: {{ $factura->created_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</div>
            </div>
            @if(!empty($emisor['qr_base64']))
                <div class="mt-2">
                    <img src="data:image/png;base64,{{ $emisor['qr_base64'] }}" alt="QR" width="96">
                </div>
            @endif
        </td>
    </tr>
</table>

<div class="line"></div>

{{-- CLIENTE / REFERENCIA --}}
<table>
    <tr>
        <td class="box" style="width:60%;">
            <div class="bold">Cliente</div>
            <div class="sm">
                {{ $factura->cliente_nombre ?? 'Cliente' }}<br>
                @if($factura->cliente_empresa) {{ $factura->cliente_empresa }}<br>@endif
                @php
                    $docLabel = trim(($factura->cliente_doc_tipo ?? '').' '.($factura->cliente_doc_num ?? ''));
                @endphp
                @if($docLabel) Doc: {{ $docLabel }}<br>@endif
                @if($factura->cliente_nit) NIT: {{ $factura->cliente_nit }}<br>@endif
                @if($factura->cliente_email) {{ $factura->cliente_email }}<br>@endif
                @if($factura->cliente_telefono) Tel: {{ $factura->cliente_telefono }}<br>@endif
                @if($factura->cliente_direccion || $factura->cliente_ciudad)
                    {{ $factura->cliente_direccion }} {{ $factura->cliente_ciudad ? ' - '.$factura->cliente_ciudad : '' }}
                @endif
            </div>
        </td>
        <td style="width:2%"></td>
        <td class="box" style="width:38%;">
            <div class="bold">Referencia</div>
            <div class="sm">
                Radicado: {{ $factura->radicado_id ?? '—' }}<br>
                @if(!empty($emisor['condicion_pago'])) Condición de pago: {{ $emisor['condicion_pago'] }}<br>@endif
                @if(!empty($emisor['vencimiento']))
                    Vence: {{ \Carbon\Carbon::parse($emisor['vencimiento'])->format('d/m/Y') }}
                @endif
            </div>
        </td>
    </tr>
</table>

{{-- ITEMS --}}
@php
    $fmt = fn($n, $d=2) => number_format((float)$n, $d, ',', '.');
@endphp

<table class="t-soft mt-4">
    <thead>
        <tr>
            <th style="width:42%;">Concepto</th>
            <th style="width:10%;" class="right">Cant.</th>
            <th style="width:13%;" class="right">Unidad</th>
            <th style="width:15%;" class="right">P. Unitario</th>
            <th style="width:8%;"  class="right">IVA %</th>
            <th style="width:12%;" class="right">Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($factura->items as $it)
            <tr>
                <td>{{ $it->concepto }}</td>
                <td class="right">{{ $fmt($it->cantidad) }}</td>
                <td class="right">{{ $it->unidad }}</td>
                <td class="right">$ {{ $fmt($it->precio_unitario) }}</td>
                <td class="right">{{ $fmt($it->iva_pct) }}</td>
                <td class="right">$ {{ $fmt($it->total) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="center muted">No hay ítems agregados.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- TOTALES --}}
<table style="margin-top:12px;">
    <tr>
        <td style="width:58%; vertical-align: top;">
            {{-- Observaciones --}}
            @if(!empty($emisor['notas']))
                <div class="box">
                    <div class="bold">Observaciones</div>
                    <div class="xs pt-2">{{ $emisor['notas'] }}</div>
                </div>
            @endif
        </td>
        <td style="width:2%"></td>
        <td style="width:40%; vertical-align: top;">
            <table class="t-bordered">
                <tr>
                    <td class="right"><span class="bold">Subtotal</span></td>
                    <td class="right">$ {{ $fmt($factura->subtotal) }}</td>
                </tr>
                <tr>
                    <td class="right"><span class="bold">IVA</span></td>
                    <td class="right">$ {{ $fmt($factura->iva) }}</td>
                </tr>
                <tr>
                    <td class="right"><span class="bold">Total</span></td>
                    <td class="right bold">$ {{ $fmt($factura->total) }}</td>
                </tr>
                <tr>
                    <td class="right">Pagado</td>
                    <td class="right">$ {{ $fmt($factura->pagado) }}</td>
                </tr>
                <tr>
                    <td class="right">Saldo</td>
                    <td class="right bold">$ {{ $fmt($factura->saldo) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- PAGOS (opcional) --}}
@if(!empty($mostrar_pagos))
    <div class="mt-4 bold">Pagos / Abonos</div>
    <table class="t-soft">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Método</th>
                <th>Referencia</th>
                <th class="right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($factura->pagos as $p)
                @php
                    $raw = $p->fecha_pago ?? $p->fecha ?? null;
                    $fecha = $raw ? \Carbon\Carbon::parse($raw)->format('d/m/Y') : '';
                    $monto = (float)($p->monto ?? $p->valor ?? 0);
                @endphp
                <tr>
                    <td>{{ $fecha }}</td>
                    <td>{{ $p->metodo ?? '—' }}</td>
                    <td>{{ $p->referencia ?? '—' }}</td>
                    <td class="right">$ {{ $fmt($monto) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="center muted">No hay pagos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
@endif

{{-- PIE LEGAL --}}
<div class="footer-note">
    <div><span class="bold">Responsable:</span> {{ $emisor['razon_social'] ?? config('app.name') }} — NIT {{ $emisor['nit'] ?? 'N/A' }}@if(!empty($emisor['dv']))-{{ $emisor['dv'] }}@endif</div>
    @if(!empty($emisor['dian_nota']))
        <div>{{ $emisor['dian_nota'] }}</div>
    @endif
    <div>Representación impresa de la factura de venta. Documento generado electrónicamente. Contacto: {{ $emisor['email'] ?? '' }} {{ !empty($emisor['telefono']) ? ' - '.$emisor['telefono'] : '' }}</div>
</div>

</body>
</html>
