{{-- Plantilla Propuesta de Automatizaciones / RPA --}}
@include('pdf.propuestas.partials.base', [
  'titulo' => 'PROPUESTA — AUTOMATIZACIONES / RPA',
  'alcance' => [
    'Identificación de procesos candidatos y definición de métricas.',
    'Desarrollo de bots/automatizaciones.',
    'Pruebas controladas y puesta en producción.',
    'Capacitación y transferencia de conocimiento.'
  ],
  'entregables' => [
    'Bots/Workflows implementados.',
    'Documentación de operación.',
    'Informe de mejora (ahorro de tiempo/errores).'
  ],
  'cronograma' => [
    ['fase'=>'Descubrimiento','semanas'=>1],
    ['fase'=>'Implementación','semanas'=>2],
    ['fase'=>'Ajustes & Cierre','semanas'=>1],
  ],
  'mensaje' => $contrato->mensaje ?: 'Automatizamos tareas repetitivas para aumentar la eficiencia.'
])
