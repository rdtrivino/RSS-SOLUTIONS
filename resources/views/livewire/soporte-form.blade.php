<div class="mx-auto max-w-6xl p-4 md:p-6">
    {{-- Notificación de éxito --}}
@if ($mensaje)
    <div class="mb-4 flex items-center gap-3 rounded-lg border-l-4 border-green-700 bg-green-100 p-4 text-green-800 shadow">
        {{-- Icono check --}}
        <svg class="h-5 w-5 flex-shrink-0 text-green-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span class="font-medium">{{ $mensaje }}</span>
    </div>
@endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        {{-- Columna izquierda: Formulario --}}
        <div class="md:col-span-2">
            <div class="rounded-2xl bg-white shadow p-5 md:p-6">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Crear soporte</h2>
                    <span class="text-sm text-gray-500">Radicará a tu nombre</span>
                </div>

                 {{-- Aviso de costo --}}
                <div class="mb-4 rounded-lg bg-yellow-50 border border-yellow-200 p-3">
                    <p class="text-sm text-yellow-800 font-medium">
                        ⚠️ Cualquier revisión tiene un costo de $30.000
                    </p>
                </div>
                <form wire:submit.prevent="save" class="space-y-6">
                    {{-- Título --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Título</label>
                        <input type="text" wire:model.defer="titulo" class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        @error('titulo') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea rows="4" wire:model.defer="descripcion" class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
                        @error('descripcion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    {{-- Grid de campos adicionales --}}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        {{-- Prioridad --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Prioridad</label>
                            <select wire:model.defer="prioridad" class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                            </select>
                            @error('prioridad') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        {{-- Teléfono --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Teléfono / Celular</label>
                            <input type="text" wire:model.defer="telefono" placeholder="+57 300 123 4567" class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('telefono') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        {{-- Tipo documento --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Tipo de documento</label>
                            <select wire:model.defer="tipo_documento" class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="">Seleccione...</option>
                                <option value="CC">Cédula de ciudadanía</option>
                                <option value="CE">Cédula de extranjería</option>
                                <option value="NIT">NIT</option>
                                <option value="PAS">Pasaporte</option>
                                <option value="TI">Tarjeta de identidad</option>
                            </select>
                            @error('tipo_documento') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        {{-- Número documento --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Número de documento</label>
                            <input type="text" wire:model.defer="numero_documento" class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('numero_documento') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        {{-- Ciudad --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Ciudad</label>
                            <input type="text" wire:model.defer="ciudad" class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('ciudad') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        {{-- Dirección --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Dirección exacta</label>
                            <input type="text" wire:model.defer="direccion" class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('direccion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        {{-- Tipo servicio --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Tipo de servicio</label>
                            <select wire:model.defer="tipo_servicio" class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="">Seleccione...</option>
                                <option value="Redes">Redes</option>
                                <option value="Hardware">Hardware</option>
                                <option value="Software">Software</option>
                                <option value="Impresora">Impresora</option>
                                <option value="Servidor">Servidor</option>
                                <option value="Otros">Otros</option>
                            </select>
                            @error('tipo_servicio') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        {{-- Modalidad --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Modalidad</label>
                            <div class="flex items-center gap-6 rounded-xl border border-gray-200 p-2.5">
                                <label class="flex items-center gap-2">
                                    <input type="radio" wire:model="modalidad" value="local" class="h-4 w-4">
                                    <span class="text-sm text-gray-700">Llevar al local</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" wire:model="modalidad" value="recoger" class="h-4 w-4">
                                    <span class="text-sm text-gray-700">Requiere recogida</span>
                                </label>
                            </div>
                            @error('modalidad') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="reset" class="rounded-xl border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                            Limpiar
                        </button>
                        <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 font-medium text-white shadow hover:bg-blue-700">
                            Crear soporte
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Columna derecha: Panel de tickets del usuario --}}
        <div class="space-y-6 md:col-span-1 min-h-0">
            {{-- Resumen --}}
            <div class="rounded-2xl bg-white shadow p-5">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Tus tickets</h3>

                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-xl border border-gray-200 p-3 text-center">
                        <div class="text-xs text-gray-500">Total</div>
                        <div class="text-2xl font-semibold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-center">
                        <div class="text-xs text-amber-700">Abiertos</div>
                        <div class="text-2xl font-semibold text-amber-800">{{ $stats['abiertos'] }}</div>
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

                {{-- Contenedor con scroll --}}
                <div style="max-height: 220px; overflow-y: auto;" class="pr-2 space-y-3">
                    @forelse ($tickets as $t)
                        @php
                            $estadoColor = match($t->estado) {
                                'abierto'      => 'bg-amber-100 text-amber-800 border-amber-200',
                                'en_progreso'  => 'bg-blue-100 text-blue-800 border-blue-200',
                                'cerrado'      => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                default        => 'bg-gray-100 text-gray-800 border-gray-200',
                            };

                            $prioColor = match($t->prioridad) {
                                'alta'  => 'bg-rose-100 text-rose-800 border-rose-200',
                                'media' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                'baja'  => 'bg-slate-100 text-slate-800 border-slate-200',
                                default => 'bg-slate-100 text-slate-800 border-slate-200',
                            };
                        @endphp

                        <div class="rounded-xl border border-gray-200 p-3 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-medium text-gray-900">{{ $t->titulo }}</div>
                                    <div class="mt-1 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center rounded-md border border-gray-200 bg-gray-100 px-2 py-0.5 text-xs text-gray-700">
                                            {{ optional($t->radicado)->numero ?? 'Sin radicado' }}
                                        </span>
                                        <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs {{ $estadoColor }}">
                                            {{ ucfirst(str_replace('_',' ', $t->estado)) }}
                                        </span>
                                        <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs {{ $prioColor }}">
                                            Prioridad: {{ ucfirst($t->prioridad) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">{{ $t->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>

                            @if ($t->tipo_servicio || $t->modalidad)
                                <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-600">
                                    @if ($t->tipo_servicio)
                                        <span class="inline-flex items-center rounded-md border border-gray-200 bg-white px-2 py-0.5">
                                            Servicio: {{ $t->tipo_servicio }}
                                        </span>
                                    @endif
                                    @if ($t->modalidad)
                                        <span class="inline-flex items-center rounded-md border border-gray-200 bg-white px-2 py-0.5">
                                            Modalidad: {{ ucfirst($t->modalidad) }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-gray-500">
                            Aún no tienes tickets registrados.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
