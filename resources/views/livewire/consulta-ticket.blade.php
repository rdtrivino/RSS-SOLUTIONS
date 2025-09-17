<div class="mx-auto max-w-6xl p-4 md:p-6">

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        {{-- IZQ: Formulario + Resultado --}}
        <div class="md:col-span-2">

            {{-- Card: Formulario --}}
            <div class="rounded-2xl bg-white shadow p-5 md:p-6">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Consultar ticket</h2>
                    <span class="text-sm text-gray-500">Ingresa el número de radicado</span>
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

                <form wire:submit.prevent="buscar" class="space-y-6">
                    <div>
                        <label for="radicado-input" class="mb-1 block text-sm font-medium text-gray-700">Número de radicado *</label>
                        <div class="flex gap-3">
                            <input
                                   id="radicado-input"
                                   type="text"
                                   wire:model.defer="numero"
                                   placeholder="PQR-2025-000123"
                                   class="w-full rounded-xl border border-gray-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 uppercase"
                                   oninput="this.value = this.value.toUpperCase()"
                                   autocomplete="off"
                                   spellcheck="false"
                                   />
                            <button type="submit"
                                    class="rounded-xl bg-blue-600 px-4 py-2 font-medium text-white shadow hover:bg-blue-700">
                                Buscar
                            </button>
                            <button type="button" wire:click="limpiar"
                                    class="rounded-xl border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                                Limpiar
                            </button>
                        </div>
                        @error('numero') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </form>

                {{-- Alerta si se buscó y no hay resultados --}}
                @if (($searched ?? false) && !$radicado)
                    <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-amber-800">
                        ⚠️ {{ $errorMsg ?? 'No encontramos un ticket con ese número.' }}
                    </div>
                @endif
            </div>

            {{-- Card: Resultado --}}
            @if ($radicado)
                @php
                    $estadoVal = strtolower($data['estado'] ?? '');
                    $badge = match($estadoVal) {
                        'radicado','pendiente','abierto'   => 'bg-amber-100 text-amber-800 border-amber-200',
                        'en_proceso','en progreso','en_progreso' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'cerrado','resuelto','finalizado','cerrado_tecnico' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        default                            => 'bg-gray-100 text-gray-800 border-gray-200',
                    };
                    $radNum = $data['radicado'] ?? '';
                @endphp

                <div class="mt-6 rounded-2xl bg-white shadow p-5 md:p-6">
                    <div class="mb-4 flex items-start justify-between gap-3">
                        <div>
                            <div class="text-xs text-gray-500">Radicado</div>
                            <div class="mt-0.5 font-mono text-base font-semibold text-gray-900">
                                {{ $radNum }}
                            </div>
                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center rounded-md border border-gray-200 bg-white px-2 py-0.5 text-xs text-gray-700">
                                    Módulo: {{ ucfirst($data['modulo'] ?? '-') }}
                                </span>
                                @if (!empty($data['estado']))
                                    <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs {{ $badge }}">
                                        {{ ucfirst(str_replace('_',' ', $data['estado'])) }}
                                    </span>
                                @endif
                                @if (!empty($data['creado']))
                                    <span class="inline-flex items-center rounded-md border border-gray-200 bg-white px-2 py-0.5 text-xs text-gray-700">
                                        {{ $data['creado'] }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- Botón PDF SOLO si hay URL disponible --}}
                            @if(!empty($pdfUrl))
                                <a href="{{ $pdfUrl }}" target="_blank" rel="noopener"
                                   class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white shadow hover:bg-indigo-700">
                                    Descargar PDF
                                </a>
                            @endif

                            {{-- (Opcional) Botón deshabilitado/tooltip cuando no hay PDF (descomentar si lo quieres visible) --}}
                            {{--
                            @if(empty($pdfUrl) && ($data['modulo'] ?? null) === 'soporte' && !in_array($estadoVal, ['cerrado','resuelto','finalizado','cerrado_tecnico'], true))
                                <button type="button" disabled
                                        title="El PDF estará disponible cuando el ticket se cierre."
                                        class="cursor-not-allowed rounded-lg bg-gray-200 px-3 py-1.5 text-sm font-medium text-gray-600">
                                    PDF no disponible
                                </button>
                            @endif
                            --}}

                            <button type="button"
                                    x-data="{}"
                                    data-val="{{ $radNum }}"
                                    @click="navigator.clipboard.writeText($el.dataset.val)"
                                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50">
                                Copiar número
                            </button>
                        </div>
                    </div>

                    @if (!empty($data['resumen']))
                        <p class="text-sm text-gray-700 mb-4">
                            {{ $data['resumen'] }}
                        </p>
                    @endif

                    {{-- Detalles (key => value) --}}
                    @if (!empty($data['detalles']) && is_array($data['detalles']))
                        <div class="rounded-xl border border-gray-200">
                            <dl class="divide-y divide-gray-200">
                                @foreach ($data['detalles'] as $k => $v)
                                    <div class="grid grid-cols-3 gap-4 p-3">
                                        <dt class="col-span-1 text-sm font-medium text-gray-600">{{ $k }}</dt>
                                        <dd class="col-span-2 text-sm text-gray-900">{{ $v }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-gray-500">
                            No hay detalles para mostrar.
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- DER: Tips --}}
        <div class="space-y-6 md:col-span-1">
            <div class="rounded-2xl bg-white shadow p-5">
                <h3 class="mb-3 text-lg font-semibold text-gray-800">Ayuda rápida</h3>
                <ul class="list-disc pl-5 text-sm text-gray-700 space-y-2">
                    <li>Ejemplo: <span class="font-mono">PQR-2025-000123</span></li>
                    <li>También puedes consultar radicados de <span class="font-medium">soporte</span> o <span class="font-medium">contrato</span>.</li>
                    <li>Mayúsculas/guiones no importan: normalizamos al buscar.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
