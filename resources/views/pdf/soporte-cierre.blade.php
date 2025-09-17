@php
    // ===== Variables base / defaults =====
    $empresaNit = $empresaNit ?? 'NIT 1131110766';
    $logoPath   = $logoPath   ?? public_path('images/logo-removebg-preview.png');
    $gracias    = $gracias    ?? '¡Gracias por confiar en nosotros! Trabajamos para darte una atención rápida y efectiva. Si tienes dudas adicionales, con gusto te ayudamos.';

    // util “checkbox” (cuadrado lleno/ vacío)
    $cb = $cb ?? fn($on) => ($on ? '■' : '□');

    // Fotos (paths relativos dentro de storage/public)
    $fotos = $fotos ?? ($respuesta->data['fotos'] ?? []);
    $filas = array_chunk($fotos, 2);

    // Tareas
    $tareasLabels = [
        'diag'           => 'Diagnóstico',
        'formateo'       => 'Formateo e instalación de S.O.',
        'backup'         => 'Backup de información',
        'cambio_bateria' => 'Cambio de batería',
        'cambio_disco'   => 'Cambio de disco / SSD',
        'limpieza'       => 'Limpieza y mantenimiento',
        'inst_software'  => 'Instalación de programas',
        'drivers'        => 'Actualización de drivers',
        'red'            => 'Reparación de red / internet',
        'pantalla'       => 'Cambio de pantalla',
        'otros'          => 'Otros',
    ];
    $tareasMarcadas = $tareasMarcadas ?? ($respuesta->data['tareas'] ?? []);
    $tareasOtras    = $tareasOtras    ?? trim((string)($respuesta->data['tareas_otras'] ?? ''));

    // Totales
    $items    = $items    ?? ($respuesta->data['items'] ?? []);
    $subtotal = $subtotal ?? ($respuesta->data['totales']['subtotal'] ?? 0);
    $iva      = $iva      ?? ($respuesta->data['totales']['iva'] ?? 0);
    $total    = $total    ?? ($respuesta->data['totales']['total'] ?? 0);

    $prioridad = strtolower($soporte->prioridad ?? '');
@endphp
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cierre de Soporte - {{ $radicado->numero }}</title>
    <style>
        /* ===== Reset & Page ===== */
        *{box-sizing:border-box}
        @page { margin: 26px 24px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; line-height:1.45; }
        .content { position: relative; z-index: 1; }

        /* ===== Brand / Header ===== */
        .watermark { position: fixed; top: 45%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); opacity: .05; font-size: 90px; font-weight: 800; color: #0f172a; width: 100%; text-align: center; z-index: 0; letter-spacing: 2px; }
        .header { width:100%; border-bottom:1px solid #e5e7eb; margin-bottom:10px; }
        .header td { border:0; vertical-align:middle; }
        .brand { display:inline-table; border-collapse:collapse; }
        .brand td { padding:0; vertical-align:middle; }
        .brand-logo { padding-right:12px; }
        .logo { max-height:60px; }
        .tagline{ font-size:11px; color:#6b7280 }

        /* ===== Blocks ===== */
        .titlebar { background:#f8fafc; border:1px solid #e5e7eb; padding:8px 12px; font-weight:800; text-align:center; margin:12px 0 10px; border-radius:8px; }
        .subtitle{ font-size:12px; color:#475569; margin-top:2px }
        .block { border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; margin:0 0 12px; background:#fff; }
        .block h3 { background:#f1f5f9; margin:0; padding:8px 12px; border-bottom:1px solid #e5e7eb; font-size:13px; font-weight:700; color:#0f172a; }
        .boxpad { padding:12px; }

        /* ===== Tables ===== */
        table.meta, table.grid, table.photos, table.sign { width:100%; border-collapse:collapse; }
        table.meta th, table.meta td, table.grid th, table.grid td, table.sign th, table.sign td { border:1px solid #e5e7eb; padding:7px; vertical-align:top; }
        table.meta th, table.grid th, table.sign th { background:#f8fafc; text-align:left; color:#111827; }
        table.meta th { width:28%; }
        .num-right { text-align:right } .num-center { text-align:center }
        .badge { display:inline-block; padding:2px 8px; border-radius:999px; font-size:11px; border:1px solid #e5e7eb; background:#f8fafc; color:#334155 }

        /* ===== Checks / photos ===== */
        .checks { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
        .check-item { min-width: 46%; margin-bottom:4px; }
        .photos { border-collapse:separate; border-spacing:8px 8px; }
        .photos td { width:50%; border:1px solid #e5e7eb; padding:6px; vertical-align:top; }
        .photo-img { width:100%; height:auto; display:block; margin-bottom:6px; }
        .caption { text-align:center; font-size:11px; color:#334155; }

        /* ===== Signatures ===== */
        .line { border-bottom:1px solid #111; height:24px; }
        .line-lg { border-bottom:1px solid #111; height:60px; }

        /* ===== Footer ===== */
        .footer { position: fixed; left: 24px; right: 24px; bottom: 10px; font-size: 10.5px; color: #6b7280; text-align: left; z-index: 2; }

        /* ===== Utility ===== */
        .muted { color:#6b7280 } .small { font-size:11px }
    </style>
</head>
<body>

<div class="watermark">RSS SOLUTIONS</div>
<div class="content">
    <!-- ===== Header ===== -->
    <table class="header"><tr>
        <td style="width:100%">
            <table class="brand"><tr>
                <td class="brand-logo">@if($logoPath && file_exists($logoPath))<img class="logo" src="{{ $logoPath }}" alt="Logo">@endif</td>
                <td>
                    <div style="font-size:16px;font-weight:800;color:#0f172a">RSS SOLUTIONS</div>
                    <div class="tagline">{{ $empresaNit }}</div>
                </td>
            </tr></table>
        </td>
        <td style="text-align:right">
            <div class="small muted">Generado {{ now()->format('d/m/Y H:i') }}</div>
        </td>
    </tr></table>

    <!-- ===== Title ===== -->
    <div class="titlebar">
        INFORME DE SOPORTE / CIERRE DE ACTIVIDAD
        <div class="subtitle">Documento de cierre técnico (no constituye factura)</div>
    </div>

    <!-- ===== Meta ===== -->
    <div class="block">
        <h3>1. Información general</h3>
        <table class="meta">
            <tr>
                <th>Radicado</th><td>{{ $radicado->numero }}</td>
                <th>Fecha de radicado</th><td>{{ $radicado->created_at?->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <th>Usuario</th><td>{{ optional($radicado->user)->name ?? '—' }}</td>
                <th>Estado final</th><td><span class="badge">CERRADO</span></td>
            </tr>
            <tr>
                <th>Fecha de cierre</th><td>{{ $respuesta->created_at?->format('d/m/Y H:i') }}</td>
                <th>Atendido por</th><td>{{ optional($respuesta->user)->name ?? '—' }}</td>
            </tr>
        </table>
    </div>

    <!-- ===== Soporte ===== -->
    <div class="block">
        <h3>2. Datos del soporte</h3>
        <table class="grid">
            <tr><th>Título</th><td colspan="3">{{ $soporte->titulo }}</td></tr>
            <tr><th>Descripción</th><td colspan="3" style="white-space:pre-wrap">{{ $soporte->descripcion }}</td></tr>
            <tr>
                <th>Prioridad</th>
                <td>
                    <div class="checks">
                        <span class="check-item">{{ $cb($prioridad==='baja') }} Baja</span>
                        <span class="check-item">{{ $cb($prioridad==='media') }} Media</span>
                        <span class="check-item">{{ $cb($prioridad==='alta') }} Alta</span>
                    </div>
                </td>
                <th>Teléfono</th><td>{{ $soporte->telefono }}</td>
            </tr>
            <tr>
                <th>Documento</th><td>{{ $soporte->tipo_documento }} {{ $soporte->numero_documento }}</td>
                <th>Ubicación</th><td>{{ $soporte->ciudad }} / {{ $soporte->direccion }}</td>
            </tr>
            <tr>
                <th>Equipo</th>
                <td colspan="3">
                    {{ $soporte->tipo_equipo }} — {{ $soporte->marca }} {{ $soporte->modelo }} &nbsp;&nbsp;
                    Serial: {{ $soporte->serial }} &nbsp;&nbsp; S.O.: {{ $soporte->so }} &nbsp;&nbsp;
                    Accesorios: {{ $soporte->accesorios }}
                </td>
            </tr>
        </table>
    </div>

    <!-- ===== Mensaje ===== -->
    <div class="block">
        <h3>3. Mensaje</h3>
        <div class="boxpad"><p style="white-space:pre-wrap; margin:0">{{ $gracias }}</p></div>
    </div>

    <!-- ===== Trabajo realizado ===== -->
    <div class="block">
        <h3>4. Trabajo realizado</h3>
        <div class="boxpad">
            @if(!empty($respuesta->data['notas']))
                <p style="white-space:pre-wrap; margin:0">{{ $respuesta->data['notas'] }}</p>
            @else
                <p class="muted" style="margin:0">Sin notas.</p>
            @endif
        </div>
    </div>

    <!-- ===== Servicios realizados ===== -->
    <div class="block">
        <h3>5. Servicios realizados</h3>
        <div class="boxpad">
            @if(!empty($tareasMarcadas))
                <div class="checks">
                    @foreach($tareasLabels as $key => $label)
                        <span class="check-item">{{ $cb(in_array($key, $tareasMarcadas)) }} {{ $label }}</span>
                    @endforeach
                </div>
                @if($tareasOtras)
                    <div class="small" style="margin-top:6px;"><strong>Otros (detalle):</strong> {{ $tareasOtras }}</div>
                @endif
            @else
                <p class="muted" style="margin:0">No se marcaron servicios.</p>
            @endif
        </div>
    </div>

    <!-- ===== Costos / Cobro ===== -->
    @if(!empty($items))
    <div class="block">
        <h3>6. Costos / Cobro</h3>
        <table class="grid">
            <thead>
            <tr>
                <th style="width:34%">Concepto</th>
                <th style="width:8%">Cant.</th>
                <th style="width:12%">Unidad</th>
                <th style="width:14%">V. Unitario</th>
                <th style="width:8%">IVA %</th>
                <th style="width:16%">Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $it)
                <tr>
                    <td>{{ $it['concepto'] }}</td>
                    <td class="num-center">{{ number_format($it['cantidad'], 2, ',', '.') }}</td>
                    <td class="num-center">{{ $it['unidad'] }}</td>
                    <td class="num-right">COP {{ number_format($it['precio_unitario'], 2, ',', '.') }}</td>
                    <td class="num-center">{{ number_format($it['iva_pct'], 2, ',', '.') }}</td>
                    <td class="num-right"><strong>COP {{ number_format($it['total'], 2, ',', '.') }}</strong></td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4"></td><th>Subtotal</th>
                <td class="num-right"><strong>COP {{ number_format($subtotal ?? 0, 2, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td colspan="4"></td><th>IVA</th>
                <td class="num-right"><strong>COP {{ number_format($iva ?? 0, 2, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td colspan="4"></td><th>Total</th>
                <td class="num-right"><strong>COP {{ number_format($total ?? 0, 2, ',', '.') }}</strong></td>
            </tr>
            </tbody>
        </table>
        <div class="boxpad small muted">Este detalle es informativo y no constituye factura.</div>
    </div>
    @endif

    <!-- ===== Registro fotográfico ===== -->
    @if(!empty($filas))
    <div class="block">
        <h3>7. Registro fotográfico</h3>
        <table class="photos">
            @foreach($filas as $fila)
                <tr>
                    @foreach($fila as $path)
                        @php $abs = public_path('storage/' . ltrim($path, '/')); $nombre = basename($path); @endphp
                        <td>
                            @if(file_exists($abs))
                                <img class="photo-img" src="{{ $abs }}" alt="{{ $nombre }}">
                            @else
                                <div class="muted small">No se encontró la imagen: {{ $path }}</div>
                            @endif
                            <div class="caption">{{ $nombre }}</div>
                        </td>
                    @endforeach
                    @if(count($fila) === 1)
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>
    @endif

    <!-- ===== Recepción del usuario ===== -->
    <div class="block">
        <h3>8. Recepción del usuario</h3>
        <table class="sign">
            <tr><th style="width:28%">Firma</th><td class="line-lg"></td></tr>
            <tr><th>Nombre completo</th><td class="line"></td></tr>
            <tr><th>Fecha de recibo</th><td class="line"></td></tr>
            <tr><th>Hora de recibo</th><td class="line"></td></tr>
        </table>
    </div>

    <!-- ===== Nota legal ===== -->
    <div class="small muted">Este documento certifica el cierre técnico del caso reportado y el trabajo realizado. No reemplaza la factura ni el contrato de prestación de servicios.</div>
</div>

<!-- ===== Footer ===== -->
<div class="footer">
    Documento generado automáticamente por RSS Solutions el {{ $respuesta->created_at?->format('d/m/Y') }} a las {{ $respuesta->created_at?->format('H:i') }}, por el usuario: {{ optional($respuesta->user)->name ?? '—' }}.
</div>

</body>
</html>
