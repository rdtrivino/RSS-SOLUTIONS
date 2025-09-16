{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- =========================== --}}
            {{-- GRID SUPERIOR: 3 TARJETAS  --}}
            {{-- =========================== --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">

                {{-- Soporte (ancha) --}}
                <a href="{{ route('soporte.index') }}"
                aria-label="Crear solicitud de soporte"
                class="group relative block h-full overflow-hidden rounded-3xl p-7 shadow-xl ring-1 ring-inset ring-white/20
                        bg-gradient-to-br from-indigo-500 via-blue-500 to-cyan-500
                        hover:shadow-2xl hover:scale-[1.005] transition focus:outline-none focus-visible:ring-4 focus-visible:ring-white/40
                        flex flex-col">

                    <div class="flex-1">
                        <div class="flex items-start gap-5">
                            <div class="shrink-0 rounded-2xl p-3.5 bg-white/15 ring-1 ring-white/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 13v5a3 3 0 0 1-3 3h-3m-6-8v5a3 3 0 0 0 3 3h3m6-11a6 6 0 1 0-12 0m12 0v3a2 2 0 0 1-2 2h-1m-9-5v3a2 2 0 0 0 2 2h1"/>
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h3 class="text-2xl font-extrabold text-white text-balance">Soporte Técnico Integral</h3>
                                <p class="mt-2 text-base leading-relaxed text-white/90">
                                    ¿Equipo fallando o red lenta? Nuestro <span class="font-semibold">service desk</span> atiende
                                    <span class="font-semibold">hardware, software, red y telefonía IP</span> con tiempos de respuesta claros.
                                </p>
                                <ul class="mt-4 space-y-2 text-white/90 text-sm">
                                    <li class="flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2Z"/></svg> Seguimiento en tiempo real</li>
                                    <li class="flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2Z"/></svg> Prioridades y SLA por criticidad</li>
                                    <li class="flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2Z"/></svg> Notificaciones y cierre validado</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/30 group-hover:bg-white/20">
                        Crear solicitud ahora
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition -translate-x-0.5 group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="currentColor"><path d="M13.172 12 8.222 7.05l1.414-1.414L16 12l-6.364 6.364-1.414-1.414z"/></svg>
                    </div>
                </a>

                {{-- Contrátanos (ancha) --}}
                <a href="{{ route('contacto.index') }}"
                aria-label="Contratar servicios profesionales"
                class="group relative block h-full overflow-hidden rounded-3xl p-7 shadow-xl ring-1 ring-inset ring-white/20
                        bg-gradient-to-br from-purple-600 via-fuchsia-500 to-pink-500
                        hover:shadow-2xl hover:scale-[1.005] transition focus:outline-none focus-visible:ring-4 focus-visible:ring-white/40
                        flex flex-col">

                    <div class="flex-1">
                        <div class="flex items-start gap-5">
                            <div class="shrink-0 rounded-2xl p-3.5 bg-white/15 ring-1 ring-white/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none"><path stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" d="M15.5 15.5 13 13m0 0-2-2m2 2 3.5-3.5a2.121 2.121 0 0 0-3-3L11 9l-1.5-1.5a2.121 2.121 0 0 0-3 3L10 13l-2.5 2.5a2.121 2.121 0 0 0 3 3L13 15l2.5 2.5a2.121 2.121 0 0 0 3-3L16 13l2.5-2.5a2.121 2.121 0 0 0-3-3L13 10Z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-2xl font-extrabold text-white text-balance">Contrata a nuestro equipo</h3>
                                <p class="mt-2 text-base leading-relaxed text-white/90">
                                    Tu <span class="font-semibold">aliado tecnológico</span> para proyectos de
                                    <span class="font-semibold">infraestructura</span>, <span class="font-semibold">desarrollo web</span> y <span class="font-semibold">soporte</span>.
                                    Entregables claros y resultados medibles.
                                </p>
                                <ul class="mt-4 space-y-2 text-white/90 text-sm">
                                    <li class="flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2Z"/></svg> Soporte y mantenimiento de infraestructura</li>
                                    <li class="flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2Z"/></svg> Desarrollo de aplicaciones a medida</li>
                                    <li class="flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2Z"/></svg> Consultoría en telecomunicaciones y redes</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/30 group-hover:bg-white/20">
                        Solicitar propuesta →
                    </div>
                </a>

                {{-- PQR (ancha) --}}
                <a href="{{ route('pqr.index') }}"
                aria-label="Registrar PQR"
                class="group relative block h-full overflow-hidden rounded-3xl p-7 shadow-xl ring-1 ring-inset ring-white/20
                        bg-gradient-to-br from-rose-500 via-red-500 to-orange-400
                        hover:shadow-2xl hover:scale-[1.005] transition focus:outline-none focus-visible:ring-4 focus-visible:ring-white/40
                        flex flex-col">

                    <div class="flex-1">
                        <div class="flex items-start gap-5">
                            <div class="shrink-0 rounded-2xl p-3.5 bg-white/15 ring-1 ring-white/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9 5h6m-6 0a2 2 0 1 0 0 4h6a2 2 0 1 0 0-4m-6 0V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1M7 9h10v11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V9z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-2xl font-extrabold text-white text-balance">PQR: Te Escuchamos</h3>
                                <p class="mt-2 text-base leading-relaxed text-white/90">
                                    Radica tus <span class="font-semibold">Peticiones, Quejas y Reclamos</span> con atención clara,
                                    seguimiento visible y respuesta dentro de los tiempos comprometidos.
                                </p>
                                <ul class="mt-4 space-y-2 text-white/90 text-sm">
                                    <li class="flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2Z"/></svg> Radicación rápida y segura</li>
                                    <li class="flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2Z"/></svg> Estado visible 24/7</li>
                                    <li class="flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2Z"/></svg> Respuesta con trazabilidad</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/30 group-hover:bg-white/20">
                        Registrar una PQR →
                    </div>
                </a>

                {{-- Consulta Ticket / Solicitud (ancha) --}}
                <a href="{{ route('consulta-nit.index') }}"
                aria-label="Consultar estado de ticket o solicitud"
                class="group relative block h-full overflow-hidden rounded-3xl p-7 shadow-xl ring-1 ring-inset ring-white/20
                        bg-gradient-to-br from-sky-500 via-blue-500 to-indigo-600
                        hover:shadow-2xl hover:scale-[1.005] transition focus:outline-none focus-visible:ring-4 focus-visible:ring-white/40
                        flex flex-col">

                    <div class="flex-1">
                        <div class="flex items-start gap-5">
                            <div class="shrink-0 rounded-2xl p-3.5 bg-white/15 ring-1 ring-white/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-2xl font-extrabold text-white text-balance">Consulta tu Ticket o Solicitud</h3>
                                <p class="mt-2 text-base leading-relaxed text-white/90">
                                    Ingresa el número de ticket y conoce su <span class="font-semibold">estado</span>,
                                    responsable asignado y <span class="font-semibold">línea de tiempo</span>.
                                </p>
                                <ul class="mt-4 space-y-2 text-white/90 text-sm">
                                    <li class="flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2Z"/></svg> Registro verificado y trazabilidad</li>
                                    <li class="flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2Z"/></svg> Estado en tiempo real</li>
                                    <li class="flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2Z"/></svg> Notificaciones cuando cambie</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/30 group-hover:bg-white/20">
                        Consultar ahora →
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>
