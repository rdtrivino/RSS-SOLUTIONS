{{-- resources/views/pdf/decision-rechazo.blade.php --}}
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Decisión de Propuesta - {{ $radicado->numero }}</title>
    <style>
        @page { margin: 24px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
        .content { position: relative; z-index: 1; }

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

        table.meta, table.grid, table.sign { width:100%; border-collapse:collapse; }
        table.meta td, table.meta th,
        table.grid td, table.grid th,
        table.sign td, table.sign th { border:1px solid #e6e6e6; padding:6px; vertical-align:top; }
        table.meta th, table.grid th, table.sign th { background:#fbfbfb; text-align:left; }
        table.meta th { width:28%; }

        .right { text-align:right; }
        .line { border-bottom:1px solid #888; height:24px; }
        .line-lg { border-bottom:1px solid #888; height:60px; }

        .footer {
            position: fixed; left: 24px; right: 24px; bottom: 10px;
            font-size: 11px; color: #666; text-align: left; z-index: 2;
        }

        .qr {
            display:flex; gap:10px; align-items:center;
            border:1px dashed #d3d3d3; padding:8px; margin-top:6px;
        }
        .qr img { width:90px; height:90px; object-fit:contain; }
        .qr p { margin:0; font-size:11px; color:#444; }
    </style>
</head>
<body>
<div class="watermark">RSS SOLUTIONS</div>

<div class="content">

    <div class="titlebar">COMUNICADO DE DECISIÓN – PROPUESTA RECHAZADA</div>

    {{-- META --}}
    <div class="block">
        <h3>Información general</h3>
        <table class="meta">
            <tr>
                <th>Radicado</th>
                <td>{{ $radicado->numero }}</td>
                <th>Fecha de decisión</th>
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

    {{-- DECISIÓN (tono formal o cercano) --}}
    <div class="block">
        <h3>Decisión</h3>
        <div class="boxpad">
            @php
                $tono = $tono ?? 'formal';
            @endphp

            @if($tono === 'cercano')
                {{-- TEXTO CÁLIDO / CERCANO --}}
                <p>
                    Gracias por confiar en nosotros y compartir su propuesta. Después de una revisión cuidadosa por parte del equipo,
                    le informamos que la solicitud asociada al radicado <strong>{{ $radicado->numero }}</strong> ha sido
                    <strong>rechazada</strong> por el momento. Sabemos que preparar una propuesta implica tiempo y dedicación; por ello,
                    queremos expresarle nuestro reconocimiento y reiterar que su iniciativa es valiosa para RSS Solutions.
                </p>

                @if(!empty($respuesta->data['observaciones']))
                    <p><strong>Resumen de la evaluación:</strong></p>
                    <p style="white-space:pre-wrap; margin-top:4px">{{ $respuesta->data['observaciones'] }}</p>
                @else
                    <p>
                        En esta fase, priorizamos proyectos con un alcance más acotado y cronogramas ajustados a nuestras ventanas
                        de despliegue, así como propuestas con costos totales optimizados y menor dependencia de terceros.
                    </p>
                @endif

                <p>
                    Nos encantaría volver a evaluar su iniciativa si decide presentarla de nuevo con algunos ajustes. Para facilitarlo,
                    sugerimos: (i) dividir el proyecto en etapas, (ii) identificar entregables medibles por sprint, (iii) revisar
                    la estimación de tiempos y costos, y (iv) incluir un plan de riesgos y soporte.
                </p>

                <p>
                    Si desea una sesión breve de retroalimentación para comentar hallazgos y posibles mejoras,
                    escríbanos a <strong>soporte@rsssolutions.co</strong> o comuníquese al <strong>+57 3XX XXX XXXX</strong>.
                    Será un gusto acompañarle en la construcción de una nueva versión.
                </p>

                <p class="muted small">
                    Esta decisión aplica exclusivamente a la propuesta evaluada y no limita futuras oportunidades de colaboración.
                </p>
            @else
                {{-- TEXTO MUY FORMAL Y DETALLADO --}}
                <p>
                    Por medio del presente, nos permitimos informar que, tras el análisis técnico, económico y operativo
                    realizado por el equipo evaluador, la propuesta asociada al radicado <strong>{{ $radicado->numero }}</strong>
                    ha sido <strong>rechazada</strong> en esta ocasión. Esta determinación se adopta en coherencia con los
                    criterios de priorización, disponibilidad presupuestal y alineación estratégica definidos para el período
                    vigente, procurando siempre la transparencia del proceso y el uso responsable de los recursos.
                </p>

                @if(!empty($respuesta->data['observaciones']))
                    <p class="small muted" style="margin-top:6px">
                        <strong>Motivo / Observaciones registradas:</strong>
                    </p>
                    <p style="white-space:pre-wrap; margin-top:4px">{{ $respuesta->data['observaciones'] }}</p>
                @else
                    <p class="small muted" style="margin-top:6px">
                        En la mesa de evaluación se revisaron variables de alcance, tiempos de implementación, dependencias técnicas,
                        riesgos y costos totales de propiedad. Si bien valoramos positivamente la iniciativa y el interés,
                        actualmente no resulta viable continuar con esta solicitud.
                    </p>
                @endif

                <p style="margin-top:10px">
                    Agradecemos profundamente el tiempo y la dedicación invertidos en la construcción de su requerimiento.
                    Nuestro objetivo es mantener canales abiertos y constructivos; por ello, si lo considera pertinente, con gusto
                    podremos revisar una <strong>nueva versión</strong> de la propuesta que contemple:
                </p>
                <ul style="margin:6px 0 0 18px">
                    <li>Ajustes de alcance (entregables medibles y priorizados por etapas).</li>
                    <li>Calendario de implementación realista frente a las dependencias internas.</li>
                    <li>Optimización de costos y/o alternativas modulares.</li>
                    <li>Mitigaciones de riesgo y plan de soporte post–implementación.</li>
                </ul>

                <p style="margin-top:10px">
                    Si requiere mayor detalle sobre la evaluación o desea programar una <strong>reunión de retroalimentación</strong>,
                    puede contactarnos a <strong>soporte@rsssolutions.co</strong> o al número <strong>+57 3XX XXX XXXX</strong>.
                    Estaremos atentos para orientar los siguientes pasos y explorar posibles escenarios de colaboración futura.
                </p>

                <p class="muted small" style="margin-top:6px">
                    Nota: esta decisión aplica únicamente a la propuesta radicada con el número indicado y no impide la presentación
                    de nuevas alternativas en fechas posteriores.
                </p>
            @endif
        </div>
    </div>

    {{-- CONTEXTO DEL REQUERIMIENTO (referencia) --}}
    <div class="block">
        <h3>Resumen del requerimiento referenciado</h3>
        <div class="boxpad">
            @if(($contrato->especificar ?? null) || ($contrato->mensaje ?? null))
                <p style="white-space:pre-wrap; margin:0">
                    {{ trim((($contrato->especificar ?? '') ? $contrato->especificar.'. ' : '').(($contrato->mensaje ?? '') ?: '')) }}
                </p>
            @else
                <p class="muted" style="margin:0">Sin detalles adicionales del requerimiento.</p>
            @endif
        </div>
    </div>

    {{-- ANEXOS (si hubo) --}}
    @php
        $anexos = $respuesta->data['anexos'] ?? [];
    @endphp
    @if(!empty($anexos))
        <div class="block">
            <h3>Anexos asociados</h3>
            <div class="boxpad">
                <ul style="margin:0; padding-left:18px">
                    @foreach($anexos as $file)
                        <li>{{ is_string($file) ? basename($file) : 'Adjunto' }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- QR opcional (URL o SVG/Base64) --}}
    @if(!empty($qrSvg) || !empty($qrUrl))
        <div class="block">
            <h3>Verificación / Seguimiento</h3>
            <div class="boxpad">
                <div class="qr">
                    @if(!empty($qrSvg))
                        {{-- Si recibes SVG/Base64 del QR (ej. Simple QrCode::format('svg')->size(200)->generate($url) ) --}}
                        <div>{!! $qrSvg !!}</div>
                    @elseif(!empty($qrUrl))
                        {{-- Si recibes una imagen (PNG/JPG) en Base64 o URL absoluta --}}
                        <img src="{{ $qrUrl }}" alt="QR seguimiento">
                    @endif
                    <p>
                        Escanee el código para validar la autenticidad del documento o consultar el estado del radicado
                        <strong>{{ $radicado->numero }}</strong> en línea.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- FIRMAS (quien emite la decisión) --}}
    <div class="block">
        <h3>Registro</h3>
        <table class="sign">
            <tr><th>Emitido por</th><td>{{ optional($respuesta->user)->name ?? '—' }}</td></tr>
            <tr><th>Firma</th><td class="line-lg"></td></tr>
            <tr><th>Fecha</th><td class="line"></td></tr>
        </table>
    </div>

</div>

<div class="footer">
    Documento generado automáticamente por RSS Solutions
    el {{ $respuesta->created_at?->format('d/m/Y') ?? now()->format('d/m/Y') }}
    a las {{ $respuesta->created_at?->format('H:i') ?? now()->format('H:i') }},
    por el usuario: {{ optional($respuesta->user)->name ?? '—' }}.
</div>

</body>
</html>
