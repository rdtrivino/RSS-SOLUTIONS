{{-- Plantilla Propuesta de Soporte --}}
@include('pdf.propuestas.partials.base', [
  'titulo' => 'PROPUESTA — SOPORTE & MANTENIMIENTO',
  'alcance' => [
    'Soporte correctivo y preventivo para equipos/servidores.',
    'Monitoreo básico y respuesta a incidentes.',
    'Gestión de actualizaciones y parches.',
    'Reporte mensual de incidentes y acciones.'
  ],
  'entregables' => [
    'SLA acordado y matriz de atención.',
    'Informes de mantenimiento/soporte.',
    'Checklist de equipos intervenidos.'
  ],
  'cronograma' => [
    ['fase'=>'Onboarding','semanas'=>1],
    ['fase'=>'Operación (mensual)','semanas'=>4],
  ],
  'mensaje' => $contrato->mensaje ?: 'Ofrecemos un esquema de soporte flexible, orientado a la continuidad operativa.'
])
