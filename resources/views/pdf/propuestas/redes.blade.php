{{-- Plantilla Propuesta de Redes --}}
@include('pdf.propuestas.partials.base', [
  'titulo' => 'PROPUESTA TÉCNICA — REDES Y CONECTIVIDAD',
  'alcance' => [
    'Levantamiento de topología y diagnóstico de la infraestructura actual.',
    'Diseño de red (LAN/WiFi/VLAN) y plan de direccionamiento.',
    'Suministro/instalación de equipos (si aplica) y configuración.',
    'Pruebas de cobertura, capacidad y seguridad.',
    'Documentación y handover.'
  ],
  'entregables' => [
    'Memoria técnica de diseño y configuración.',
    'Plano lógico y físico de la red.',
    'Informe de pruebas de rendimiento.',
    'Guía de operación básica.'
  ],
  'cronograma' => [
    ['fase'=>'Levantamiento','semanas'=>1],
    ['fase'=>'Implementación','semanas'=>1],
    ['fase'=>'Pruebas y Entrega','semanas'=>1],
  ],
  'mensaje' => $contrato->mensaje ?: 'Proponemos optimizar la infraestructura de red para garantizar estabilidad, seguridad y desempeño.'
])
