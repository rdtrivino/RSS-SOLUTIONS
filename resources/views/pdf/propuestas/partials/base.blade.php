@php
  $empresaNit = $empresaNit ?? 'NIT 1131110766';
  $logoPath   = $logoPath   ?? public_path('images/logo-removebg-preview.png');
  $titulo     = $titulo     ?? ('PROPUESTA — ' . ($contrato->servicio ?? 'Servicio'));
  $mensaje    = $mensaje    ?? ($contrato->mensaje ?: 'Presentamos la siguiente propuesta acorde a los requerimientos suministrados.');
  $alcance    = $alcance    ?? [];
  $entregables= $entregables?? [];
  $cronograma = $cronograma ?? [];
  $exclusiones= $exclusiones?? [];
  $supuestos  = $supuestos  ?? [];
  $precioTxt  = $precioTxt  ?? null;   // Ej: "$ 8.500.000 + IVA"
  $formaPago  = $formaPago  ?? '50% anticipo y 50% contra entrega, salvo pacto distinto';
  $vigencia   = $vigencia   ?? '15 días calendario';
  $ciudad     = $ciudad     ?? 'Bogotá D.C.';
  $qrTexto    = $qrTexto    ?? null;   // Contenido opcional para QR (radicado, hash, URL de verificación)
@endphp
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $titulo }} - {{ $radicado->numero }}</title>
  <style>
    /* ===== Reset & Page ===== */
    *{box-sizing:border-box}
    @page{margin:26px 24px}
    body{font-family:DejaVu Sans, sans-serif;font-size:12px;color:#111;line-height:1.45}
    .content{position:relative;z-index:1}

    /* ===== Brand ===== */
    .watermark{position:fixed;top:45%;left:50%;transform:translate(-50%,-50%) rotate(-30deg);opacity:.05;font-size:90px;font-weight:800;color:#0f172a;width:100%;text-align:center;z-index:0;letter-spacing:2px}
    .header{width:100%;border-bottom:1px solid #e5e7eb;margin-bottom:10px}
    .header td{border:0;vertical-align:middle}
    .brand{display:inline-table;border-collapse:collapse}
    .brand td{padding:0 0 0 0;vertical-align:middle}
    .brand-logo{padding-right:12px}
    .logo{max-height:60px}
    .tagline{font-size:11px;color:#6b7280}

    /* ===== Blocks ===== */
    .titlebar{background:#f8fafc;border:1px solid #e5e7eb;padding:8px 12px;font-weight:800;text-align:center;margin:12px 0 10px;border-radius:8px}
    .subtitle{font-size:12px;color:#475569;margin-top:2px}
    .block{border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;margin:0 0 12px;background:#ffffff}
    .block h3{background:#f1f5f9;margin:0;padding:8px 12px;border-bottom:1px solid #e5e7eb;font-size:13px;font-weight:700;color:#0f172a}
    .boxpad{padding:12px}

    /* ===== Tables ===== */
    table.meta,table.grid{width:100%;border-collapse:collapse}
    table.meta th,table.meta td,table.grid th,table.grid td{border:1px solid #e5e7eb;padding:7px;vertical-align:top}
    table.meta th,table.grid th{background:#f8fafc;text-align:left;color:#111827}
    table.grid .num{text-align:right}

    /* ===== Pills / Badges ===== */
    .badge{display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px;border:1px solid #e5e7eb;background:#f8fafc;color:#334155}

    /* ===== Footer ===== */
    .footer{position:fixed;left:24px;right:24px;bottom:10px;font-size:10.5px;color:#6b7280;text-align:left;z-index:2}

    /* ===== Utility ===== */
    .muted{color:#6b7280}.small{font-size:11px}
    ul{margin:0 0 0 16px}
    .sign-row{display:table;width:100%;table-layout:fixed;margin-top:30px}
    .sign-col{display:table-cell;vertical-align:bottom;padding:0 10px}
    .sign-line{border-top:1px solid #111;height:48px}
    .sign-meta{font-size:11px;color:#374151;margin-top:4px}
    .page-break{page-break-after:always}
    .notice{padding:10px;border:1px dashed #94a3b8;color:#334155;background:#f8fafc;border-radius:8px}

    /* ===== Disclaimer ===== */
    .disclaimer{font-size:11px;color:#334155}
  </style>
</head>
<body>
<div class="watermark">RSS SOLUTIONS</div>
<div class="content">
  <!-- ===== Header ===== -->
  <table class="header"><tr><td>
    <table class="brand"><tr>
      <td class="brand-logo">@if(file_exists($logoPath))<img class="logo" src="{{ $logoPath }}" alt="Logo">@endif</td>
      <td>
        <div style="font-size:16px;font-weight:800;color:#0f172a">RSS SOLUTIONS</div>
        <div class="tagline">{{ $empresaNit }} • {{ $ciudad }}</div>
      </td>
    </tr></table>
  </td><td style="text-align:right">
      <div class="badge">Radicado: {{ $radicado->numero }}</div>
      <div class="small muted">Generado {{ now()->format('d/m/Y H:i') }}</div>
  </td></tr></table>

  <!-- ===== Title ===== -->
  <div class="titlebar">
    {{ $titulo }}
    <div class="subtitle">Documento de propuesta (no constituye contrato)</div>
  </div>

  <!-- ===== Info General ===== -->
  <div class="block"><h3>1. Información general</h3>
    <div class="boxpad">
      <table class="meta">
        <tr>
          <th>Cliente</th><td>{{ $contrato->nombre }} @if($contrato->empresa) — {{ $contrato->empresa }} @endif</td>
          <th>Contacto</th><td>{{ $contrato->email }} @if($contrato->celular)/ {{ $contrato->celular }}@endif</td>
        </tr>
        <tr>
          <th>Servicio solicitado</th><td>{{ $contrato->servicio }}</td>
          <th>Vigencia de la propuesta</th><td>{{ $vigencia }}</td>
        </tr>
        @if($precioTxt)
        <tr>
          <th>Precio referencial</th><td colspan="3">{{ $precioTxt }} <span class="muted">(valores en COP + impuestos aplicables)</span></td>
        </tr>
        @endif
      </table>
    </div>
  </div>

  <!-- ===== Resumen ===== -->
  <div class="block"><h3>2. Resumen ejecutivo</h3>
    <div class="boxpad">{!! nl2br(e($mensaje)) !!}</div>
  </div>

  @if(!empty($alcance))
  <div class="block"><h3>3. Alcance del servicio</h3>
    <div class="boxpad">
      <ul>
        @foreach($alcance as $i)<li>{{ $i }}</li>@endforeach
      </ul>
    </div>
  </div>
  @endif

  @if(!empty($entregables))
  <div class="block"><h3>4. Entregables</h3>
    <div class="boxpad">
      <ul>
        @foreach($entregables as $i)<li>{{ $i }}</li>@endforeach
      </ul>
    </div>
  </div>
  @endif

  @if(!empty($cronograma))
  <div class="block"><h3>5. Cronograma estimado</h3>
    <div class="boxpad">
      <table class="grid">
        <tr><th>Fase</th><th>Duración (semanas)</th><th>Descripción</th></tr>
        @foreach($cronograma as $c)
          <tr>
            <td>{{ $c['fase'] ?? '' }}</td>
            <td class="num">{{ $c['semanas'] ?? '' }}</td>
            <td>{{ $c['descripcion'] ?? '' }}</td>
          </tr>
        @endforeach
      </table>
    </div>
  </div>
  @endif

  @if(!empty($exclusiones))
  <div class="block"><h3>6. Exclusiones</h3>
    <div class="boxpad"><ul>@foreach($exclusiones as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  </div>
  @endif

  @if(!empty($supuestos))
  <div class="block"><h3>7. Supuestos</h3>
    <div class="boxpad"><ul>@foreach($supuestos as $s)<li>{{ $s }}</li>@endforeach</ul></div>
  </div>
  @endif

  <!-- ===== Términos Legales de Propuesta ===== -->
  <div class="block"><h3>8. Condiciones comerciales</h3>
    <div class="boxpad">
      <ul>
        <li>Vigencia de la propuesta: {{ $vigencia }}.</li>
        <li>Forma de pago sugerida: {{ $formaPago }}.</li>
        <li>Precios en COP, más impuestos y retenciones aplicables conforme a ley.</li>
        <li>Gastos de viaje y viáticos (si aplican) se cotizan y facturan por separado.</li>
      </ul>
    </div>
  </div>

  <div class="block"><h3>9. Propiedad intelectual</h3>
    <div class="boxpad">
      <p>Salvo pacto distinto en contrato, el código fuente, librerías y herramientas preexistentes empleadas por RSS Solutions mantienen su titularidad. Los entregables específicos del proyecto podrán ser licenciados al Cliente para su uso interno, no exclusivo y no transferible. La cesión de derechos patrimoniales (si aplica) se formalizará por escrito en el contrato definitivo.</p>
    </div>
  </div>

  <div class="block"><h3>10. Confidencialidad y datos personales</h3>
    <div class="boxpad">
      <p>Las partes preservarán la confidencialidad de la información intercambiada. El tratamiento de datos personales se realizará conforme a la normativa colombiana aplicable y a las políticas de privacidad de cada parte. RSS Solutions actuará como encargado o responsable según se defina en el contrato.</p>
    </div>
  </div>

  <div class="block"><h3>11. Garantía y soporte</h3>
    <div class="boxpad">
      <p>Los entregables cuentan con una garantía de corrección por defectos de fabricación o implementación por {{ $garantia ?? '30 días calendario' }} a partir de la aceptación formal. Quedan excluidos incidentes derivados de cambios fuera de alcance, infraestructura del cliente o uso indebido.</p>
    </div>
  </div>

  <div class="block"><h3>12. Limitación de responsabilidad</h3>
    <div class="boxpad">
      <p>La responsabilidad total de RSS Solutions frente al Cliente por daños directos se limita al valor efectivamente pagado por el proyecto objeto de esta propuesta. En ningún caso responderá por daños indirectos, lucro cesante o pérdida de datos.</p>
    </div>
  </div>

  <div class="block"><h3>13. Aceptación y siguientes pasos</h3>
    <div class="boxpad">
      <p>Esta propuesta no constituye contrato. La prestación del servicio queda sujeta a la firma del contrato definitivo y a la recepción del anticipo (cuando aplique). Si está de acuerdo con el alcance y condiciones, por favor confirme por escrito para preparar el contrato.</p>
      <div class="notice small">Nota: En caso de discrepancia entre esta propuesta y el contrato que llegare a suscribirse, prevalecerá lo dispuesto en el contrato.</div>
    </div>
  </div>

  <div class="block"><h3>14. Ley aplicable y jurisdicción</h3>
    <div class="boxpad">
      <p>Este documento se rige por las leyes de la República de Colombia. Cualquier controversia se someterá a los jueces de {{ $ciudad }} salvo acuerdo de conciliación o arbitraje entre las partes.</p>
    </div>
  </div>

  <!-- ===== Firmas ===== -->
  <div class="block"><h3>15. Firmas</h3>
    <div class="boxpad">
      <div class="sign-row">
        <div class="sign-col">
          <div class="sign-line"></div>
          <div class="sign-meta"><strong>Por RSS Solutions</strong><br>Nombre: __________________________<br>Cargo: ___________________________</div>
        </div>
        <div class="sign-col">
          <div class="sign-line"></div>
          <div class="sign-meta"><strong>Por el Cliente</strong><br>Nombre: __________________________<br>Cargo: ___________________________</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== Anexos ===== -->
  <div class="block"><h3>16. Anexos</h3>
    <div class="boxpad">
      <ol style="margin-left:16px">
        <li><strong>Anexo A — Especificaciones técnicas</strong> (requerimientos detallados y arquitectura propuesta).</li>
        <li><strong>Anexo B — Matriz de responsabilidades (RACI)</strong>.</li>
        <li><strong>Anexo C — Acuerdo de niveles de servicio (SLA)</strong> y ventanas de mantenimiento.</li>
        <li><strong>Anexo D — Plan de pruebas y aceptación</strong>.</li>
        <li><strong>Anexo E — Cronograma ampliado y hitos</strong>.</li>
      </ol>
    </div>
  </div>

  <!-- ===== QR / Verificación (opcional) ===== -->
  @if($qrTexto)
    <div class="block"><h3>Verificación</h3>
      <div class="boxpad">
        <table style="width:100%"><tr>
          <td style="width:120px;vertical-align:top;padding-right:12px">
            {{-- Genera un QR con una librería o helper y guarda su path en $qrPath --}}
            @isset($qrPath)
              <img src="{{ $qrPath }}" alt="QR" style="max-width:120px;max-height:120px">
            @endisset
          </td>
          <td style="vertical-align:top">
            <div class="small muted">Contenido de verificación:</div>
            <div style="font-family:monospace;font-size:11px;word-break:break-all">{{ $qrTexto }}</div>
          </td>
        </tr></table>
      </div>
    </div>
  @endif

  <div class="disclaimer small" style="margin-top:8px">
    Este es un documento de propuesta. No genera obligaciones hasta la firma del contrato correspondiente. La información contenida es confidencial y de uso exclusivo de los destinatarios.
  </div>
</div>

<div class="footer">Documento generado automáticamente por RSS Solutions el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}.</div>

<!-- ===== Página de Anexos en blanco (opcional) ===== -->
<div class="page-break"></div>
<div class="content">
  <div class="titlebar">Anexos — Desarrollo</div>
  <div class="block"><h3>Anexo A — Especificaciones técnicas</h3><div class="boxpad" style="min-height:400px">&nbsp;</div></div>
  <div class="block"><h3>Anexo B — Matriz RACI</h3><div class="boxpad" style="min-height:250px">&nbsp;</div></div>
  <div class="block"><h3>Anexo C — SLA</h3><div class="boxpad" style="min-height:250px">&nbsp;</div></div>
  <div class="block"><h3>Anexo D — Plan de pruebas</h3><div class="boxpad" style="min-height:250px">&nbsp;</div></div>
</div>
</body>
</html>