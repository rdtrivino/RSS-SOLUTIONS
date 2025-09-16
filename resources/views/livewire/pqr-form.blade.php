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
                    <h2 class="text-xl font-semibold text-gray-800">Radicar PQR</h2>
                    <span class="text-sm text-gray-500">Serás contactado(a) con la respuesta</span>
                </div>

                {{-- Alerta de validación --}}
                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3">
                        <p class="text-sm font-medium text-red-800">Revisa los campos marcados:</p>
                        <ul class="mt-2 list-disc pl-5 text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- (Opcional) Aviso --}}
                <div class="mb-4 rounded-lg bg-blue-50 border border-blue-200 p-3">
                    <p class="text-sm text-blue-800 font-medium">
                        ℹ️ Describe tu solicitud con el mayor detalle posible para agilizar la respuesta.
                    </p>
                </div>

                <form wire:submit.prevent="radicar" class="space-y-6">
                    {{-- Tipo --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Tipo *</label>
                        <select wire:model="tipo"
                                class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <option value="peticion">Petición</option>
                            <option value="queja">Queja</option>
                            <option value="reclamo">Reclamo</option>
                        </select>
                        @error('tipo') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Descripción *</label>
                        <textarea rows="5" wire:model.defer="descripcion"
                                  class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                  placeholder="Cuéntanos tu petición, queja o reclamo..."></textarea>
                        @error('descripcion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    {{-- Acciones --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="reset" class="rounded-xl border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                            Limpiar
                        </button>
                        <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 font-medium text-white shadow hover:bg-blue-700">
                            Radicar PQR
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Columna derecha: Resumen y Recientes --}}
        <div class="space-y-6 md:col-span-1 min-h-0">
            {{-- Resumen --}}
            <div class="rounded-2xl bg-white shadow p-5">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Resumen PQR</h3>

                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-xl border border-gray-200 p-3 text-center">
                        <div class="text-xs text-gray-500">Total</div>
                        <div class="text-2xl font-semibold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-center">
                        <div class="text-xs text-amber-700">Radicados</div>
                        <div class="text-2xl font-semibold text-amber-800">{{ $stats['radicados'] }}</div>
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
                    @forelse ($recientes as $p)
                        @php
                            $estadoColor = match($p->estado) {
                                'radicado'    => 'bg-amber-100 text-amber-800 border-amber-200',
                                'en_proceso'  => 'bg-blue-100 text-blue-800 border-blue-200',
                                'cerrado'     => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                default       => 'bg-gray-100 text-gray-800 border-gray-200',
                            };
                        @endphp

                        <div class="rounded-xl border border-gray-200 p-3 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-medium text-gray-900">
                                        {{ ucfirst($p->tipo) }} — {{ \Illuminate\Support\Str::limit($p->descripcion, 40) }}
                                    </div>
                                    <div class="mt-1 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center rounded-md border border-gray-200 bg-gray-100 px-2 py-0.5 text-xs text-gray-700">
                                            {{ optional($p->radicado)->numero ?? 'Sin radicado' }}
                                        </span>
                                        <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs {{ $estadoColor }}">
                                            {{ ucfirst(str_replace('_',' ', $p->estado)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">{{ $p->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-gray-500">
                            Aún no hay PQR registradas.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
