<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Propuesta de Servicios - {{ $radicado->numero }}</title>
    <style>
        @page { margin: 24px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
        .content { position: relative; z-index: 1; }

        /* Marca de agua */
        .watermark {
            position: fixed;
            top: 45%; left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: 0.06; font-size: 90px; font-weight: 700; color: #000;
            width: 100%; text-align: center; z-index: 0;
        }

        .muted { color:#666; }
        .small { font-size:11px; }
        .titlebar { background:#f3f4f6; border:1px solid #dcdcdc; padding:6px 10px; font-weight:bold; text-align:center; margin:0 0 10px 0; }

        .block { border:1px solid #dcdcdc; padding:0; margin:0 0 10px 0; }
        .block h3 { background:#fafafa; margin:0; padding:6px 10px; border-bottom:1px solid #e9e9e9; font-size:13px; }
        .boxpad { padding:10px; }

        table.meta, table.grid, table.prices, table.sign { width:100%; border-collapse:collapse; }
        table.meta td, table.meta th,
        table.grid td, table.grid th,
        table.prices td, table.prices th,
        table.sign td, table.sign th { border:1px solid #e6e6e6; padding:6px; vertical-align:top; }
        table.meta th, table.grid th, table.prices th, table.sign th { background:#fbfbfb; text-align:left; }
        table.meta th { width:28%; }

        .right { text-align:right; }
        .center { text-align:center; }

        .line { border-bottom:1px solid #888; height:24px; }
        .line-lg { border-bottom:1px solid #888; height:60px; }

        /* Pie de página fijo */
        .footer {
            position: fixed;
            left: 24px; right: 24px; bottom: 10px;
            font-size: 11px; color: #666; text-align: left; z-index: 2;
        }
    </style>
</head>
<body>
<div class="watermark">RSS SOLUTIONS</div>

<div class="content">

    {{-- TÍTULO (sin encabezado de logo/NIT) --}}
    <div class="titlebar">PROPUESTA DE SERVICIOS</div>

    {{-- META --}}
    <div class="block">
        <h3>Información general</h3>
        <table class="meta">
            <tr>
                <th>Radicado</th>
                <td>{{ $radicado->numero }}</td>
                <th>Fecha de propuesta</th>
                <td>{{ $respuesta->created_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <th>Cliente</th>
                <td>{{ $contrato->nombre }} @if($contrato->empresa) — {{ $contrato->empresa }} @endif</td>
                <th>Contacto</th>
                <td>{{ $contrato->email }} @if($contrato->celular) — {{ $contrato->celular }} @endif</td>
            </tr>
            <tr>
                <th>NIT</th>
                <td>{{ $contrato->nit ?: '—' }}</td>
                <th>Servicio solicitado</th>
                <td>{{ $contrato->servicio }}</td>
            </tr>
        </table>
    </div>

    {{-- INTRODUCCIÓN --}}
    <div class="block">
        <h3>Introducción</h3>
        <div class="boxpad">
            <p>
                Presentamos la siguiente propuesta de servicios en respuesta a su requerimiento
                relacionado con <strong>{{ $contrato->servicio }}</strong>.
                Nuestro objetivo es ofrecer una solución efectiva, alineada con las necesidades
                descritas por el cliente.
            </p>
            @if($contrato->especificar || $contrato->mensaje)
                <p>
                    <strong>Resumen del requerimiento:</strong>
                    {{ trim(($contrato->especificar ? $contrato->especificar.'. ' : '').($contrato->mensaje ?: '')) }}
                </p>
            @endif
        </div>
    </div>

    {{-- ALCANCE --}}
    <div class="block">
        <h3>Alcance</h3>
        <div class="boxpad">
            <ul>
                <li>Análisis de necesidad y definición de solución.</li>
                <li>Ejecución de actividades técnicas y/o de desarrollo requeridas.</li>
                <li>Entregables conforme a la sección siguiente.</li>
                <li>Pruebas básicas de validación y acompañamiento en salida a producción (si aplica).</li>
            </ul>
        </div>
    </div>

    {{-- ENTREGABLES --}}
    <div class="block">
        <h3>Entregables</h3>
        <div class="boxpad">
            <ul>
                <li>Documento/artefacto técnico o funcional (según el caso).</li>
                <li>Manual o guía breve de uso (si procede).</li>
                <li>Reporte de finalización y acta de aceptación.</li>
            </ul>
        </div>
    </div>

    {{-- CRONOGRAMA --}}
    <div class="block">
        <h3>Cronograma estimado</h3>
        <div class="boxpad">
            <p>El cronograma definitivo se acordará con el cliente al inicio del proyecto. De forma referencial:</p>
            <table class="grid">
                <tr>
                    <th>Fase</th><th>Descripción</th><th>Duración estimada</th>
                </tr>
                <tr>
                    <td>Inicio</td><td>Levantamiento y confirmación de alcance</td><td>1–3 días hábiles</td>
                </tr>
                <tr>
                    <td>Ejecución</td><td>Desarrollo/implementación/soporte</td><td>Dependiendo del alcance</td>
                </tr>
                <tr>
                    <td>Cierre</td><td>Pruebas, entrega y aceptación</td><td>1–2 días hábiles</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- SUPUESTOS / EXCLUSIONES --}}
    <div class="block">
        <h3>Supuestos y exclusiones</h3>
        <div class="boxpad">
            <ul>
                <li>Disponibilidad oportuna de la información y accesos necesarios por parte del cliente.</li>
                <li>El alcance económico cubre únicamente los ítems listados en la presente propuesta.</li>
                <li>Actividades no contempladas serán cotizadas como adicionales.</li>
            </ul>
        </div>
    </div>

    {{-- CONDICIONES COMERCIALES (con tabla solo si hay items) --}}
    <div class="block">
        <h3>Condiciones comerciales</h3>
        <div class="boxpad">
            @php
                $items = $items ?? [];
            @endphp

            @if(!empty($items))
                <table class="prices">
                    <tr>
                        <th>#</th>
                        <th>Concepto</th>
                        <th class="right">Cant.</th>
                        <th class="right">Vlr. Unitario</th>
                        <th class="right">Subtotal</th>
                        <th class="right">IVA</th>
                        <th class="right">Total</th>
                    </tr>
                    @foreach($items as $i => $it)
                        <tr>
                            <td class="center">{{ $i+1 }}</td>
                            <td>{{ $it['concepto'] ?? '—' }}</td>
                            <td class="right">{{ number_format($it['cantidad'] ?? 1, 0) }}</td>
                            <td class="right">{{ number_format($it['precio_unitario'] ?? 0, 0, ',', '.') }}</td>
                            <td class="right">{{ number_format($it['subtotal'] ?? 0, 0, ',', '.') }}</td>
                            <td class="right">
                                @php
                                    $ivaPct = $it['iva_pct'] ?? 0;
                                    $ivaMonto = $it['iva_monto'] ?? (($it['subtotal'] ?? 0) * $ivaPct / 100);
                                @endphp
                                {{ number_format($ivaMonto, 0, ',', '.') }}
                            </td>
                            <td class="right">{{ number_format($it['total'] ?? (($it['subtotal'] ?? 0) + ($ivaMonto ?? 0)), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </table>

                <table class="grid" style="margin-top:8px">
                    <tr>
                        <th class="right">Subtotal</th>
                        <td class="right">{{ number_format($subtotal ?? array_sum(array_column($items,'subtotal')), 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="right">IVA (19%)</th>
                        <td class="right">{{ number_format($iva ?? array_sum(array_column($items,'iva_monto')), 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="right">Total</th>
                        <td class="right"><strong>{{ number_format($total ?? array_sum(array_column($items,'total')), 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            @else
                <p>El valor de la propuesta será definido de común acuerdo con el cliente según el alcance final (moneda: COP, IVA del 19% cuando aplique).</p>
            @endif

            <p class="small muted" style="margin-top:6px">
                Forma de pago sugerida: 50% anticipo - 50% contra entrega (ajustable según negociación).<br>
                Vigencia de la propuesta: 30 días calendario.
            </p>
        </div>
    </div>

    {{-- TÉRMINOS LEGALES --}}
    <div class="block">
        <h3>Términos y condiciones</h3>
        <div class="boxpad">
            <ul>
                <li><strong>Confidencialidad:</strong> la información compartida será tratada como confidencial.</li>
                <li><strong>Propiedad intelectual:</strong> los entregables acordados serán del cliente una vez realizado el pago.</li>
                <li><strong>Garantía:</strong> 30 días por defectos atribuibles a la ejecución (no cubre mal uso ni cambios externos).</li>
                <li><strong>Soporte:</strong> soporte básico durante el periodo de garantía; ampliaciones se cotizan aparte.</li>
                <li><strong>Jurisdicción:</strong> legislación colombiana; mecanismos alternos de solución de controversias antes de instancia judicial.</li>
            </ul>
        </div>
    </div>

    {{-- ACEPTACIÓN --}}
    <div class="block">
        <h3>Aceptación de la propuesta</h3>
        <table class="sign">
            <tr>
                <th>Firma cliente</th>
                <td class="line-lg"></td>
            </tr>
            <tr>
                <th>Nombre completo</th>
                <td class="line"></td>
            </tr>
            <tr>
                <th>CC/NIT</th>
                <td class="line"></td>
            </tr>
            <tr>
                <th>Fecha</th>
                <td class="line"></td>
            </tr>
        </table>
    </div>

</div>

{{-- Pie fijo en todas las páginas --}}
<div class="footer">
    Documento generado automáticamente por RSS Solutions
    el {{ $respuesta->created_at?->format('d/m/Y') ?? now()->format('d/m/Y') }}
    a las {{ $respuesta->created_at?->format('H:i') ?? now()->format('H:i') }},
    por el usuario: {{ optional($respuesta->user)->name ?? '—' }}.
</div>

</body>
</html>
