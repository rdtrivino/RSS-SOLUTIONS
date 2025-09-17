{{-- Plantilla Propuesta de Integraciones --}}
@include('pdf.propuestas.partials.base', [
  'titulo' => 'PROPUESTA — INTEGRACIONES / APIs',
  'alcance' => [
    'Análisis de sistemas origen/destino y definición de contratos de API.',
    'Desarrollo de conectores, autenticación y manejo de errores.',
    'Pruebas de integración y performance.',
    'Monitoreo básico y documentación.'
  ],
  'entregables' => [
    'Endpoints/documentación de API.',
    'Código fuente de integradores.',
    'Guía de despliegue y operación.'
  ],
  'cronograma' => [
    ['fase'=>'Análisis','semanas'=>1],
    ['fase'=>'Desarrollo','semanas'=>2],
    ['fase'=>'QA & Go-Live','semanas'=>1],
  ],
  'mensaje' => $contrato->mensaje ?: 'Integramos sistemas para automatizar procesos y reducir reprocesos.'
])
