{{-- Plantilla Propuesta de Consultoría --}}
@include('pdf.propuestas.partials.base', [
  'titulo' => 'PROPUESTA — CONSULTORÍA TECNOLÓGICA',
  'alcance' => [
    'Diagnóstico del estado actual y brechas.',
    'Hoja de ruta tecnológica.',
    'Sesiones de trabajo y workshops.',
    'Acompañamiento en la ejecución inicial.'
  ],
  'entregables' => [
    'Informe diagnóstico.',
    'Roadmap / plan de acción.',
    'Presentación ejecutiva de recomendaciones.'
  ],
  'cronograma' => [
    ['fase'=>'Diagnóstico','semanas'=>1],
    ['fase'=>'Plan de acción','semanas'=>1],
    ['fase'=>'Acompañamiento','semanas'=>2],
  ],
  'mensaje' => $contrato->mensaje ?: 'Alineamos tecnología con objetivos de negocio para maximizar valor.'
])
