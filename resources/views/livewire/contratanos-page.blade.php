<div class="mx-auto max-w-6xl p-4 md:p-6">
    {{-- Notificación de éxito --}}
    @if ($flash)
        <div class="mb-4 flex items-center gap-3 rounded-lg border-l-4 border-green-700 bg-green-100 p-4 text-green-800 shadow">
            <svg class="h-5 w-5 flex-shrink-0 text-green-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="font-medium">{{ $flash }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        {{-- Columna izquierda: Formulario --}}
        <div class="md:col-span-2">
            <div class="rounded-2xl bg-white shadow p-5 md:p-6">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Contrata a nuestro equipo</h2>
                    <span class="text-sm text-gray-500">Radicará a tu nombre</span>
                </div>

                {{-- Aviso de costo --}}
                <div class="mb-4 rounded-lg bg-yellow-50 border border-yellow-200 p-3">
                    <p class="text-sm text-blue-800 font-medium">
                        ℹ️ Envía tu solicitud y nuestro equipo te contactará con una propuesta personalizada.
                    </p>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    {{-- Nombre / Email --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Nombre *</label>
                            <input type="text" wire:model.defer="nombre"
                                   class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('nombre') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" wire:model.defer="email"
                                   class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Celular / Empresa / NIT --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Celular</label>
                            <input type="text" wire:model.defer="celular" placeholder="+57 300 123 4567"
                                   class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('celular') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Empresa</label>
                            <input type="text" wire:model.defer="empresa"
                                   class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('empresa') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">NIT</label>
                            <input type="text" wire:model.defer="nit"
                                   class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('nit') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

{{-- Servicio + Especificar --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Servicio *</label>
        <select wire:model="servicio"
                class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            <option value="">Seleccione…</option>
            @foreach ($servicios as $srv)
                <option value="{{ $srv }}">{{ $srv }}</option>
            @endforeach
        </select>
        @error('servicio') 
            <span class="text-sm text-red-600">{{ $message }}</span> 
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Especificar *</label>
        <input type="text" wire:model.defer="especificar" placeholder="Describe lo que necesitas"
               class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        @error('especificar') 
            <span class="text-sm text-red-600">{{ $message }}</span> 
        @enderror
    </div>
</div>

                    {{-- Mensaje --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Mensaje</label>
                        <textarea rows="4" wire:model.defer="mensaje"
                                  class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                  placeholder="Cuéntanos sobre tu necesidad"></textarea>
                        @error('mensaje') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    {{-- Acciones --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="reset" class="rounded-xl border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                            Limpiar
                        </button>
                        <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 font-medium text-white shadow hover:bg-blue-700">
                            Enviar solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Columna derecha: Resumen y recientes (persisten al refrescar) --}}
        <div class="space-y-6 md:col-span-1 min-h-0">
            {{-- Resumen --}}
            <div class="rounded-2xl bg-white shadow p-5">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Tus servicios</h3>

                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-xl border border-gray-200 p-3 text-center">
                        <div class="text-xs text-gray-500">Total</div>
                        <div class="text-2xl font-semibold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-center">
                        <div class="text-xs text-amber-700">Pendientes</div>
                        <div class="text-2xl font-semibold text-amber-800">{{ $stats['pendientes'] }}</div>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-center">
                        <div class="text-xs text-emerald-700">Cerrados</div>
                        <div class="text-2xl font-semibold text-emerald-800">{{ $stats['cerrados'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Recientes --}}
            <div class="rounded-2xl bg-white shadow p-5 min-h-0">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Recientes</h3>
                    <span class="text-xs text-gray-500">últimos 8</span>
                </div>

                <div style="max-height: 220px; overflow-y: auto;" class="pr-2 space-y-3">
                    @forelse ($solicitudes as $s)
                        @php
                            $estadoColor = match($s->estado) {
                                'pendiente' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'cerrado'   => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                default     => 'bg-blue-100 text-blue-800 border-blue-200',
                            };
                        @endphp

                        <div class="rounded-xl border border-gray-200 p-3 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-medium text-gray-900">
                                        {{ $s->servicio === 'Otro' ? ($s->especificar ?: 'Otro') : $s->servicio }}
                                    </div>
                                    <div class="mt-1 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center rounded-md border border-gray-200 bg-gray-100 px-2 py-0.5 text-xs text-gray-700">
                                            {{ optional($s->radicado)->numero ?? 'Sin radicado' }}
                                        </span>
                                        <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs {{ $estadoColor }}">
                                            {{ ucfirst($s->estado) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">{{ $s->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-gray-500">
                            Aún no tienes servicios solicitados.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
