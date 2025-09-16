<div
    x-data="{
        toast: { show:false, msg:'' },
        copy(txt){ navigator.clipboard.writeText(txt); this.toast.msg='Copiado al portapapeles'; this.toast.show=true; setTimeout(()=>this.toast.show=false, 1400) },
        otro:false
    }"
    x-init="
        (() => {
            const catálogo = @js($servicios);
            const actual   = @js($servicio ?? '');
            if (actual && !catálogo.includes(actual)) { otro = true }
        })()
    "
    class="min-h-[80vh] bg-gradient-to-br from-indigo-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-950"
>
    {{-- Toast flotante --}}
    <div
        x-show="toast.show" x-transition
        class="fixed top-4 right-4 z-50 rounded-xl backdrop-blur bg-gray-900/90 text-white px-4 py-2 shadow-2xl"
        style="display:none" x-text="toast.msg">
    </div>

    {{-- Hero / encabezado --}}
    <section class="relative">
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-24 -left-20 h-64 w-64 rounded-full bg-indigo-200/60 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-20 h-64 w-64 rounded-full bg-sky-200/60 blur-3xl"></div>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-8 pb-4">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                Contrátanos
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-300">
                Cuéntanos tu necesidad. Generaremos un radicado para hacer seguimiento.
            </p>
        </div>
    </section>

    {{-- Contenido --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 pb-10 grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Columna izquierda: Formulario --}}
        <div class="md:col-span-2 space-y-6">
            {{-- Alertas --}}
            @if ($errors->any())
                <div class="rounded-xl border border-red-200/70 bg-red-50/70 text-red-800 px-4 py-3 shadow-sm">
                    <p class="font-semibold">Revisa los siguientes campos:</p>
                    <ul class="mt-1 list-disc list-inside text-sm space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($flash)
                <div class="rounded-xl border border-emerald-200/70 bg-emerald-50/70 text-emerald-800 px-4 py-3 shadow-sm">
                    <p class="font-medium">{{ $flash }}</p>
                </div>
            @endif

            {{-- Tarjeta formulario --}}
            <div class="rounded-2xl bg-white/80 dark:bg-white/5 backdrop-blur shadow-xl ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
                <div class="mb-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Datos de contacto</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Los campos con <span class="text-red-600">*</span> son obligatorios.</p>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Nombre --}}
                        <label class="block">
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre <span class="text-red-600">*</span></span>
                            <input type="text" wire:model.defer="nombre" placeholder="Ej. Ana Gómez"
                                   class="mt-1 w-full rounded-xl border border-gray-300/80 dark:border-white/10 bg-white/70 dark:bg-white/5 backdrop-blur px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                            @error('nombre') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </label>

                        {{-- Email --}}
                        <label class="block">
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo electrónico <span class="text-red-600">*</span></span>
                            <input type="email" wire:model.defer="email" placeholder="nombre@empresa.com"
                                   class="mt-1 w-full rounded-xl border border-gray-300/80 dark:border-white/10 bg-white/70 dark:bg-white/5 backdrop-blur px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                            @error('email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </label>

                        {{-- Celular --}}
                        <label class="block">
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Celular</span>
                            <input type="text" wire:model.defer="celular" placeholder="+57 300 000 0000"
                                   class="mt-1 w-full rounded-xl border border-gray-300/80 dark:border-white/10 bg-white/70 dark:bg-white/5 backdrop-blur px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                            @error('celular') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </label>

                        {{-- Empresa --}}
                        <label class="block">
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Empresa</span>
                            <input type="text" wire:model.defer="empresa" placeholder="Nombre de la empresa"
                                   class="mt-1 w-full rounded-xl border border-gray-300/80 dark:border-white/10 bg-white/70 dark:bg-white/5 backdrop-blur px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                            @error('empresa') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </label>

                        {{-- NIT --}}
                        <label class="block">
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIT</span>
                            <input type="text" wire:model.defer="nit" placeholder="900123456-7"
                                   class="mt-1 w-full rounded-xl border border-gray-300/80 dark:border-white/10 bg-white/70 dark:bg-white/5 backdrop-blur px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                            @error('nit') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </label>

                        {{-- Servicio (select + Otro) --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Servicio <span class="text-red-600">*</span></label>
                            <div class="mt-1 grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <select
                                    wire:model.defer="servicio"
                                    x-on:change="otro = ($event.target.value === 'Otro')"
                                    class="col-span-2 rounded-xl border border-gray-300/80 dark:border-white/10 bg-white/70 dark:bg-white/5 backdrop-blur px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"
                                >
                                    <option value="">Selecciona...</option>
                                    @foreach($servicios as $srv)
                                        <option value="{{ $srv }}">{{ $srv }}</option>
                                    @endforeach
                                    <option value="Otro">Otro...</option>
                                </select>

                                <button type="button"
                                        class="rounded-xl border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 active:scale-[.99] transition px-3 py-2 text-sm"
                                        x-on:click="otro = true; $nextTick(()=>{ document.getElementById('srv_otro')?.focus() })">
                                    Especificar
                                </button>
                            </div>
                            @error('servicio') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror

                            <div x-show="otro" x-transition class="mt-2">
                                <input id="srv_otro" type="text" placeholder="Describe el servicio que necesitas"
                                       x-on:input="$wire.set('servicio', $event.target.value)"
                                       class="w-full rounded-xl border border-gray-300/80 dark:border-white/10 bg-white/70 dark:bg-white/5 backdrop-blur px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ej.: Integración con ERP, portal clientes, RPA, etc.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Mensaje --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mensaje</label>
                        <textarea rows="4" wire:model.defer="mensaje"
                                  placeholder="Cuéntanos alcance, prioridades y fechas estimadas."
                                  class="mt-1 w-full rounded-xl border border-gray-300/80 dark:border-white/10 bg-white/70 dark:bg-white/5 backdrop-blur px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition"></textarea>
                        @error('mensaje') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- CTA --}}
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Al enviar, se generará un radicado de seguimiento.</p>

                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-sky-600 text-white px-5 py-2.5 font-medium shadow-lg shadow-indigo-600/20 hover:from-indigo-700 hover:to-sky-700 focus:ring-4 focus:ring-indigo-500/30 active:scale-[.99] transition disabled:opacity-60 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled">
                            <svg wire:loading class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4A4 4 0 004 12z"/>
                            </svg>
                            <span wire:loading.remove>Enviar solicitud</span>
                            <span wire:loading>Guardando…</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Último radicado --}}
            @if ($ultimoRadicado)
                <div class="rounded-2xl bg-emerald-50/70 dark:bg-emerald-900/20 border border-emerald-200/70 dark:border-emerald-800 p-4 flex items-center justify-between shadow-sm">
                    <div>
                        <p class="text-sm text-emerald-700 dark:text-emerald-300">Solicitud radicada correctamente</p>
                        <p class="text-emerald-900 dark:text-emerald-100 font-semibold tracking-wide">
                            Radicado: {{ $ultimoRadicado->numero }}
                        </p>
                    </div>
                    <button type="button"
                            x-on:click="copy('{{ $ultimoRadicado->numero }}')"
                            class="text-sm rounded-lg border border-emerald-300/70 bg-white/70 dark:bg-white/10 backdrop-blur px-3 py-1.5 text-emerald-800 dark:text-emerald-200 hover:bg-white transition">
                        Copiar
                    </button>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-6">
            <div class="sticky top-6 space-y-6">
                {{-- Stats --}}
                <div class="rounded-2xl bg-white/80 dark:bg-white/5 backdrop-blur shadow-xl ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen</h3>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-xl bg-gray-50 dark:bg-white/10 p-3 text-center shadow-sm">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Total</div>
                            <div class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
                        </div>
                        <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 p-3 text-center shadow-sm">
                            <div class="text-xs text-amber-700 dark:text-amber-300">Pendientes</div>
                            <div class="text-2xl font-extrabold text-amber-600">{{ $stats['pendientes'] }}</div>
                        </div>
                        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 p-3 text-center shadow-sm">
                            <div class="text-xs text-emerald-700 dark:text-emerald-300">Cerrados</div>
                            <div class="text-2xl font-extrabold text-emerald-600">{{ $stats['cerrados'] }}</div>
                        </div>
                    </div>
                </div>

                {{-- Últimos radicados --}}
                <div class="rounded-2xl bg-white/80 dark:bg-white/5 backdrop-blur shadow-xl ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Últimos radicados</h3>
                    <ul class="divide-y divide-gray-200/70 dark:divide-white/10 text-sm">
                        @forelse ($radicados as $r)
                            <li class="py-3 flex items-center justify-between group">
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ $r->numero }}</p>
                                    <p class="text-gray-500 dark:text-gray-400">{{ optional($r->created_at)->diffForHumans() }}</p>
                                </div>
                                <button type="button"
                                        class="text-xs rounded-md px-2 py-1 border border-indigo-200 bg-indigo-50 text-indigo-700 group-hover:bg-indigo-100 dark:border-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-300 transition"
                                        x-on:click="copy('{{ $r->numero }}')">
                                    Copiar
                                </button>
                            </li>
                        @empty
                            <li class="py-2 text-gray-500 dark:text-gray-400">Sin registros</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </aside>
    </div>
</div>
