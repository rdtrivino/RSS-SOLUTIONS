<x-guest-layout>
    <div class="max-w-3xl mx-auto p-6 bg-white rounded-2xl shadow">
        <h1 class="text-xl font-bold mb-4">Estado del ticket</h1>

        <p><strong>Código:</strong> {{ $radicado->numero }}</p>
        <p><strong>Módulo:</strong> {{ ucfirst($radicado->modulo) }}</p>
        <p><strong>Estado:</strong> {{ $modelo->estado }}</p>
        <p><strong>Descripción:</strong> {{ $modelo->mensaje ?? 'Sin detalles' }}</p>
        <p><strong>Creado:</strong> {{ $radicado->created_at->format('d/m/Y H:i') }}</p>

        <div class="mt-4">
            <a href="{{ url('/') }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-500">Volver al inicio</a>
        </div>
    </div>
</x-guest-layout>
