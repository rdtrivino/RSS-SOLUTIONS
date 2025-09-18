{{-- resources/views/pdf/propuestas/partials/mensaje_integraciones.blade.php --}}
<style>
  /* Estilos locales, no globales */
  .legal-watermark {
    position: fixed; top: 45%; left: 50%;
    transform: translate(-50%,-50%) rotate(-30deg);
    opacity: .06; font-size: 90px; font-weight: 700; color: #000;
    width: 100%; text-align:center; z-index: 0;
  }
  .legal-content * { box-sizing: border-box; }
  .legal-h1 { font-size: 18px; margin: 0 0 6px; font-weight: 700; }
  .legal-h2 { font-size: 15px; margin: 0 0 6px; font-weight: 700; text-align: left; }
  .legal-h3 { font-size: 13px; margin: 0 0 6px; font-weight: 700; }
  .legal-p  { margin: 0 0 8px; text-align: justify; }
  .legal-ul { margin: 6px 0 10px 16px; }
  .legal-ol { margin: 6px 0 10px 18px; }
  .legal-muted { color:#555; }
  .legal-small { font-size:11px; }
  .legal-titlebar {
    background:#f5f5f5; border:0.6px solid #e5e7eb; padding:8px 12px; font-weight:700; margin:12px 0;
  }
  .legal-page-break { page-break-before: always; }
  .legal-sign { height: 60px; border-bottom:0.6px solid #aaa; margin-bottom: 4px; }

  /* Tablas legales sobrias */
  .legal-table {
    width:100%; border-collapse: collapse; margin: 8px 0 12px; font-size: 12px;
  }
  .legal-table th, .legal-table td {
    border:0.6px solid #555; padding:6px 8px; vertical-align: top;
  }
  .legal-table thead th { background:#f9f9f9; text-align:center; font-weight:700; }
  .legal-table, .legal-table tr, .legal-table td, .legal-table th { page-break-inside: avoid; }
</style>

<div class="legal-watermark">PROPUESTA / INTEGRACIONES</div>

<div class="legal-content">
  {{-- PORTADA --}}
  <div>
    <div class="legal-h1">Propuesta de Integraciones / APIs</div>
    <p class="legal-p legal-muted">Fecha: {{ now()->format('d/m/Y') }}</p>
    <p class="legal-p">
      Propuesta presentada por <strong>{{ config('app.name') }}</strong>
      @if(!empty($contrato?->empresa))
        a <strong>{{ $contrato->empresa }}</strong> (NIT: {{ $contrato->nit ?? 'N/D' }}).
      @endif
    </p>

    <div class="legal-titlebar">Resumen Ejecutivo</div>
    <p class="legal-p">
      Esta propuesta detalla el alcance, entregables, plan de trabajo, niveles de servicio, condiciones económicas,
      criterios de aceptación, garantías y demás cláusulas aplicables a los servicios de integración de sistemas
      mediante APIs y conectores, con el objetivo de <strong>automatizar procesos y reducir reprocesos</strong>.
    </p>

    <div class="legal-titlebar">Índice</div>
    <ol class="legal-ol">
      <li>Objeto y Alcance</li>
      <li>Definiciones</li>
      <li>Entregables</li>
      <li>Plan de Trabajo y Cronograma</li>
      <li>Roles, Responsabilidades y RACI</li>
      <li>Niveles de Servicio (SLA)</li>
      <li>Seguridad y Protección de Datos</li>
      <li>Propiedad Intelectual</li>
      <li>Criterios de Aceptación y Pruebas</li>
      <li>Gestión de Cambios (CR)</li>
      <li>Honorarios, Impuestos y Forma de Pago</li>
      <li>Garantía y Soporte</li>
      <li>Limitación de Responsabilidad</li>
      <li>Confidencialidad</li>
      <li>Fuerza Mayor</li>
      <li>Vigencia y Terminación</li>
      <li>Solución de Controversias</li>
      <li>Anexos (A–J)</li>
    </ol>
  </div>

  <div class="legal-page-break"></div>

  {{-- 1. Objeto y Alcance --}}
  <div class="legal-h2">1. Objeto y Alcance</div>
  <p class="legal-p">
    {{ config('app.name') }} (el <strong>Proveedor</strong>) se compromete a diseñar, desarrollar, probar y poner en
    producción integraciones entre sistemas de información del <strong>Cliente</strong>, incluyendo conectores,
    definiciones de contratos de API, autenticación, manejo de errores y documentación técnica.
  </p>

  <div class="legal-h3">1.1 Alcance Detallado</div>
  <ul class="legal-ul">
    @foreach(($alcance ?? []) as $item)
      <li>{{ $item }}</li>
    @endforeach
  </ul>

  <p class="legal-p">
    El alcance podrá ser refinado durante la fase de Análisis sin que ello implique un incremento de costos, siempre que
    no se modifique la naturaleza del proyecto ni se incremente el esfuerzo estimado en más de un 10%.
  </p>

  <div class="legal-h3">1.2 Exclusiones</div>
  <ul class="legal-ul">
    <li>Licencias de terceros no indicadas explícitamente.</li>
    <li>Desarrollos fuera del entorno aprobado o no soportados por el proveedor cloud.</li>
    <li>Migraciones masivas de datos, salvo que se contrate como servicio adicional.</li>
  </ul>

  <div class="legal-page-break"></div>

  {{-- 2. Definiciones --}}
  <div class="legal-h2">2. Definiciones</div>
  <p class="legal-p legal-muted legal-small">A efectos de este documento:</p>
  <ul class="legal-ul">
    <li><strong>API:</strong> Interfaz de Programación de Aplicaciones para intercambiar datos de forma segura.</li>
    <li><strong>Conector:</strong> Componente que implementa la lógica de integración entre sistemas.</li>
    <li><strong>Ambiente:</strong> Desarrollo, QA/Testing y Producción.</li>
    <li><strong>CR (Change Request):</strong> Solicitud formal de cambio de alcance o requisitos.</li>
  </ul>

  {{-- 3. Entregables --}}
  <div class="legal-h2">3. Entregables</div>
  <ul class="legal-ul">
    @foreach(($entregables ?? []) as $e)
      <li>{{ $e }}</li>
    @endforeach
    <li>Documento de Arquitectura de Integración.</li>
    <li>Manual de Operación y Soporte.</li>
  </ul>

  {{-- 4. Plan de trabajo --}}
  <div class="legal-h2">4. Plan de Trabajo y Cronograma</div>
  <table class="legal-table">
    <thead>
      <tr><th>Fase</th><th>Duración estimada (semanas)</th><th>Descripción</th></tr>
    </thead>
    <tbody>
      @foreach(($cronograma ?? []) as $row)
        <tr>
          <td>{{ $row['fase'] }}</td>
          <td class="text-center">{{ $row['semanas'] }}</td>
          <td>
            @switch($row['fase'])
              @case('Análisis') Levantamiento, validación de APIs, definición de contratos y flujos. @break
              @case('Desarrollo') Construcción de conectores, autenticación, observabilidad. @break
              @case('QA & Go-Live') Pruebas integrales, performance, hardening y despliegue. @break
              @default Actividades específicas de la fase.
            @endswitch
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <p class="legal-p legal-muted legal-small">
    * Las duraciones son estimadas y pueden variar por dependencias del Cliente (accesos, datos, aprobaciones).
  </p>

  <div class="legal-page-break"></div>

  {{-- 5. RACI --}}
  <div class="legal-h2">5. Roles, Responsabilidades y RACI</div>
  <table class="legal-table">
    <thead>
      <tr><th>Actividad</th><th>R</th><th>A</th><th>C</th><th>I</th></tr>
    </thead>
    <tbody>
      <tr><td>Definición de API Contracts</td><td>Proveedor</td><td>Cliente</td><td>Seguridad TI</td><td>Usuarios</td></tr>
      <tr><td>Desarrollo de Conectores</td><td>Proveedor</td><td>Proveedor</td><td>Cliente</td><td>Usuarios</td></tr>
      <tr><td>Pruebas Funcionales</td><td>Proveedor</td><td>Cliente</td><td>QA</td><td>Stakeholders</td></tr>
      <tr><td>Despliegue a Producción</td><td>Proveedor</td><td>Cliente</td><td>Infra/DevOps</td><td>Usuarios</td></tr>
    </tbody>
  </table>

  {{-- 6. SLA --}}
  <div class="legal-h2">6. Niveles de Servicio (SLA)</div>
  <p class="legal-p"><strong>Ventana de Soporte:</strong> L–V 8:00–18:00 (GMT-5), excluyendo festivos nacionales.</p>
  <table class="legal-table">
    <thead>
      <tr><th>Severidad</th><th>Ejemplo</th><th>Tiempo de respuesta</th><th>Tiempo objetivo de resolución</th></tr>
    </thead>
    <tbody>
      <tr><td>Crítica (S1)</td><td>Caída total</td><td>2h</td><td>8h hábiles</td></tr>
      <tr><td>Alta (S2)</td><td>Degradación severa</td><td>4h</td><td>16h hábiles</td></tr>
      <tr><td>Media (S3)</td><td>Falla parcial</td><td>8h</td><td>40h hábiles</td></tr>
      <tr><td>Baja (S4)</td><td>Consulta/Mejora</td><td>16h</td><td>Planificada</td></tr>
    </tbody>
  </table>

  <div class="legal-page-break"></div>

  {{-- 7. Seguridad y Datos --}}
  <div class="legal-h2">7. Seguridad y Protección de Datos</div>
  <ul class="legal-ul">
    <li>Cumplimiento de la Ley 1581 de 2012 y normativa de protección de datos en Colombia.</li>
    <li>Principio de mínimo privilegio, segregación de ambientes y control de accesos.</li>
    <li>Registros de auditoría de eventos críticos y gestión de incidentes.</li>
  </ul>

  {{-- 8. Propiedad Intelectual --}}
  <div class="legal-h2">8. Propiedad Intelectual</div>
  <p class="legal-p">
    El código fuente desarrollado por el Proveedor será de su titularidad, otorgando al Cliente una licencia
    no exclusiva, perpetua e intransferible para usarlo en su operación interna, salvo pacto distinto.
  </p>

  {{-- 9. Aceptación --}}
  <div class="legal-h2">9. Criterios de Aceptación y Pruebas</div>
  <p class="legal-p">
    La aceptación se producirá al cumplirse los criterios definidos en el Anexo G y emitirse el acta de
    aceptación por el Cliente. La ausencia de objeciones dentro de 5 días hábiles desde la entrega se
    entenderá como aceptación tácita.
  </p>

  {{-- 10. Cambios --}}
  <div class="legal-h2">10. Gestión de Cambios (CR)</div>
  <p class="legal-p">Todo cambio será tramitado mediante CR con impacto en alcance, esfuerzo, costos y fechas.</p>

  <div class="legal-page-break"></div>

  {{-- 11. Precios --}}
  <div class="legal-h2">11. Honorarios, Impuestos y Forma de Pago</div>
  <ul class="legal-ul">
    <li>Honorarios según propuesta económica adjunta.</li>
    <li>Impuestos indirectos a cargo del Cliente según ley aplicable.</li>
    <li>Pagos contra hitos: 40% inicio, 40% QA, 20% Go-Live (ejemplo).</li>
    <li>Plazo de pago: 30 días calendario desde factura.</li>
  </ul>

  {{-- 12. Garantía y Soporte --}}
  <div class="legal-h2">12. Garantía y Soporte</div>
  <p class="legal-p">
    Garantía de 60 días calendario sobre defectos atribuibles al desarrollo, contados desde la aceptación.
    Soporte post-garantía disponible bajo bolsa de horas o plan mensual.
  </p>

  {{-- 13. Limitación --}}
  <div class="legal-h2">13. Limitación de Responsabilidad</div>
  <p class="legal-p">
    La responsabilidad total del Proveedor no excederá el valor efectivamente pagado por el Cliente en los
    últimos 6 meses previos al reclamo. Se excluyen daños indirectos, lucro cesante y pérdida de datos.
  </p>

  {{-- 14. Confidencialidad --}}
  <div class="legal-h2">14. Confidencialidad</div>
  <p class="legal-p">
    Las partes mantendrán en reserva la información confidencial por 5 años posteriores a la terminación.
  </p>

  {{-- 15. Fuerza Mayor --}}
  <div class="legal-h2">15. Fuerza Mayor</div>
  <p class="legal-p">
    Ninguna de las partes será responsable por incumplimientos debidos a eventos de fuerza mayor debidamente
    demostrables; los plazos se extenderán por el tiempo del evento.
  </p>

  {{-- 16. Vigencia y Terminación --}}
  <div class="legal-h2">16. Vigencia y Terminación</div>
  <p class="legal-p">
    La propuesta tiene una vigencia comercial de 30 días. El contrato terminará por cumplimiento del objeto,
    mutuo acuerdo o incumplimiento material con preaviso escrito de 30 días.
  </p>

  {{-- 17. Controversias --}}
  <div class="legal-h2">17. Solución de Controversias</div>
  <p class="legal-p">
    Las partes procurarán resolver disputas de forma directa; en su defecto, se someterán a conciliación.
    La ley aplicable será la de la República de Colombia y los jueces de la ciudad de Bogotá D.C.
  </p>

  <div class="legal-page-break"></div>

  {{-- FIRMAS --}}
  <div class="legal-h2">Firmas</div>
  <table class="legal-table">
    <tr>
      <td>
        <div class="legal-sign"></div>
        <strong>EL CLIENTE</strong><br>
        @if(!empty($contrato?->empresa)) {{ $contrato->empresa }} @else Razón Social Cliente @endif<br>
        NIT/CC: {{ $contrato->nit ?? '__________' }}
      </td>
      <td>
        <div class="legal-sign"></div>
        <strong>EL PROVEEDOR</strong><br>
        {{ config('app.name') }}<br>
        NIT: __________________
      </td>
    </tr>
  </table>

  <div class="legal-page-break"></div>

  {{-- ANEXO A --}}
  <div class="legal-h2">Anexo A. Alcance Detallado</div>
  @php $i=1; @endphp
  @foreach(($alcance ?? []) as $item)
    <div class="legal-h3">A.{{ $i++ }} {{ $item }}</div>
    <ul class="legal-ul">
      <li>Descripción técnica del componente.</li>
      <li>Interfaces implicadas (origen/destino) y formatos.</li>
      <li>Seguridad: autenticación, autorización, secretos y rotación.</li>
      <li>Errores y reintentos (backoff exponencial, DLQ si aplica).</li>
      <li>Observabilidad: logs, métricas y trazas.</li>
    </ul>
  @endforeach

  <div class="legal-page-break"></div>

  {{-- ANEXO B --}}
  <div class="legal-h2">Anexo B. Plan de Trabajo</div>
  <table class="legal-table">
    <thead>
      <tr><th>Fase</th><th>Semana</th><th>Actividades clave</th><th>Entregables</th></tr>
    </thead>
    <tbody>
      @foreach(($cronograma ?? []) as $row)
        @for($w=1; $w<=($row['semanas'] ?? 1); $w++)
        <tr>
          <td>{{ $row['fase'] }}</td>
          <td class="text-center">{{ $w }}</td>
          <td>
            @if($row['fase']==='Análisis') Talleres, definición de contratos, riesgos.
            @elseif($row['fase']==='Desarrollo') Sprints de construcción y revisión de código.
            @else Pruebas, hardening, plan de liberación. @endif
          </td>
          <td>Artefactos de la fase; bitácora semanal.</td>
        </tr>
        @endfor
      @endforeach
    </tbody>
  </table>

  <div class="legal-page-break"></div>

  {{-- ANEXO C --}}
  <div class="legal-h2">Anexo C. Matriz RACI</div>
  <table class="legal-table">
    <thead>
      <tr><th>Entregable/Actividad</th><th>R</th><th>A</th><th>C</th><th>I</th></tr>
    </thead>
    <tbody>
      <tr><td>Arquitectura de Integración</td><td>Proveedor</td><td>Cliente</td><td>Seguridad TI</td><td>Stakeholders</td></tr>
      <tr><td>Conectores</td><td>Proveedor</td><td>Proveedor</td><td>Cliente</td><td>Usuarios</td></tr>
      <tr><td>Documentación</td><td>Proveedor</td><td>Cliente</td><td>QA</td><td>Operaciones</td></tr>
    </tbody>
  </table>

  <div class="legal-page-break"></div>

  {{-- ANEXO D --}}
  <div class="legal-h2">Anexo D. SLA Detallado</div>
  <p class="legal-p">Procedimiento de incidencias:</p>
  <ol class="legal-ol">
    <li>Registro en mesa de ayuda con severidad y evidencias.</li>
    <li>Diagnóstico inicial y mitigación.</li>
    <li>Comunicación periódica de estado.</li>
    <li>Cierre con causa raíz y acciones preventivas.</li>
  </ol>

  <div class="legal-page-break"></div>

  {{-- ANEXO E --}}
  <div class="legal-h2">Anexo E. Seguridad &amp; Protección de Datos</div>
  <ul class="legal-ul">
    <li>Cifrado en tránsito (TLS 1.2+) y, si aplica, en reposo.</li>
    <li>Gestión de secretos (no hardcode, rotación periódica).</li>
    <li>Backups según política del Cliente o plan acordado.</li>
  </ul>

  <div class="legal-page-break"></div>

  {{-- ANEXO F --}}
  <div class="legal-h2">Anexo F. Propiedad Intelectual &amp; Licencias</div>
  <p class="legal-p">
    Se indicarán dependencias open-source y sus licencias (MIT/Apache/GPL). El Cliente es responsable de licencias
    comerciales requeridas por sus plataformas.
  </p>

  <div class="legal-page-break"></div>

  {{-- ANEXO G --}}
  <div class="legal-h2">Anexo G. Criterios de Aceptación</div>
  <ul class="legal-ul">
    <li>Pruebas funcionales y de integración superadas al 100%.</li>
    <li>Rendimiento: p95 &lt;= umbral definido, sin errores severos.</li>
    <li>Documentación y handover completados.</li>
  </ul>

  <div class="legal-page-break"></div>

  {{-- ANEXO H --}}
  <div class="legal-h2">Anexo H. Soporte Posterior y Garantía</div>
  <p class="legal-p">
    Alcances de soporte, canales, tiempos, exclusiones (p.ej., cambios sobrevenidos por terceros, datos corruptos
    por cargas externas, etc.).
  </p>

  <div class="legal-page-break"></div>

  {{-- ANEXO I --}}
  <div class="legal-h2">Anexo I. Política de Cambios (CR)</div>
  <p class="legal-p">Flujo: solicitud → evaluación de impacto → aprobación → ejecución → cierre.</p>

  <div class="legal-page-break"></div>

  {{-- ANEXO J --}}
  <div class="legal-h2">Anexo J. Tarifas y Facturación</div>
  <table class="legal-table">
    <thead>
      <tr><th>Concepto</th><th>Unidad</th><th>Tarifa</th><th>Notas</th></tr>
    </thead>
    <tbody>
      <tr><td>Desarrollo</td><td>Hora</td><td>$ __________</td><td>Bolsa mínima / acuerdos por sprint.</td></tr>
      <tr><td>Soporte</td><td>Mes</td><td>$ __________</td><td>Incluye N horas y SLA indicado.</td></tr>
      <tr><td>Infra/DevOps</td><td>Hora</td><td>$ __________</td><td>Según demanda.</td></tr>
    </tbody>
  </table>

  <p class="legal-p legal-muted legal-small">
    <strong>Nota:</strong> Los valores definitivos se fijan en la propuesta económica anexa o en la orden de compra del Cliente.
  </p>
</div>
