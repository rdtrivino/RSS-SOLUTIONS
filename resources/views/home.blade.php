<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>RSS Solutions — Soporte & Tecnología</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    {{-- Swiper (carrusel) --}}
    <link rel="stylesheet" href="https://unpkg.com/swiper@10/swiper-bundle.min.css">

    {{-- Alpine.js (UI) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Lucide (iconos) --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <style>
        html{ scroll-behavior:smooth; }
        [data-lucide]{ stroke-width:2.1; }
        /* Oculta elementos con x-cloak hasta que Alpine monte */
        [x-cloak]{ display:none !important; }
    </style>
</head>
<body id="top"
      class="bg-gradient-to-b from-blue-50 via-white to-white text-gray-800"
      x-data="{ openTracking:false, openChat:false, openMenu:false, openInfo:false }"
      x-init="
        if (!sessionStorage.getItem('rss_info_shown')) {
          setTimeout(() => { openInfo = true }, 400);
          sessionStorage.setItem('rss_info_shown', '1');
        }
      ">

    {{-- NAVBAR (azul oscuro) --}}
    <header class="sticky top-0 z-50 backdrop-blur bg-blue-900/95 text-white border-b border-white/10">
      <nav class="max-w-7xl mx-auto px-4 h-16 flex items-center gap-4">
        {{-- IZQUIERDA: logo --}}
        <a href="#top" class="flex items-center gap-3">
          <img src="{{ asset('images/logo.jpg') }}" class="h-10 w-10 rounded-lg ring-1 ring-white/30 shadow" alt="RSS Solutions">
          <span class="font-extrabold text-lg tracking-tight">RSS Solutions</span>
        </a>

        {{-- CENTRO: links --}}
        <ul class="hidden md:flex items-center gap-4 text-sm mx-auto">
          <li><a href="#servicios"  class="px-3 py-2 rounded-lg hover:text-yellow-300">Servicios</a></li>
          <li><a href="#quienes"   class="px-3 py-2 rounded-lg hover:text-yellow-300">Quiénes somos</a></li>
          <li><a href="#blog"      class="px-3 py-2 rounded-lg hover:text-yellow-300">Blog</a></li>
          <li><a href="#confianza" class="px-3 py-2 rounded-lg hover:text-yellow-300">Confianza</a></li>
        </ul>

        {{-- DERECHA: acciones --}}
        <div class="hidden md:flex items-center gap-2">
          <button @click="openTracking = true"
                  class="px-3 py-2 rounded-lg font-semibold text-blue-900 bg-yellow-400 hover:brightness-105 active:scale-[.98] transition">
            Consultar Ticket
          </button>

          @guest
            <a href="{{ route('login') }}"
               class="ml-3 px-3 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-500 active:scale-[.98] transition">
              Login
            </a>
            @if (Route::has('register'))
            <a href="{{ route('register') }}"
               class="px-3 py-2 rounded-lg font-semibold text-blue-900 bg-white ring-1 ring-white/30 hover:bg-gray-50 active:scale-[.98] transition">
              Registrarse
            </a>
            @endif
          @else
            @if (auth()->user()->hasRole('admin'))
              <a href="{{ url('/admin') }}" class="px-3 py-2 rounded-lg hover:text-yellow-300">Panel Admin</a>
            @elseif (auth()->user()->hasRole('empleado'))
              <a href="{{ url('/staff') }}" class="px-3 py-2 rounded-lg hover:text-yellow-300">Panel Staff</a>
            @else
              <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg hover:text-yellow-300">Mi panel</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="ml-2 px-3 py-2 rounded-lg font-semibold text-blue-900 bg-white ring-1 ring-white/30 hover:bg-gray-50 active:scale-[.98] transition">
                Salir
              </button>
            </form>
          @endguest
        </div>

        {{-- Mobile trigger --}}
        <button class="md:hidden p-2 rounded-lg hover:bg-white/10" @click="openMenu = !openMenu" aria-label="Abrir menú">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
      </nav>

      {{-- Mobile menu (azul oscuro) --}}
      <div x-show="openMenu" x-transition class="md:hidden border-t border-white/10 bg-blue-900 text-white" style="display:none">
        <div class="max-w-7xl mx-auto px-4 py-3 grid gap-2">
          <a @click="openMenu=false" href="#servicios"  class="px-3 py-2 rounded-lg hover:bg-white/10">Servicios</a>
          <a @click="openMenu=false" href="#quienes"   class="px-3 py-2 rounded-lg hover:bg-white/10">Quiénes somos</a>
          <a @click="openMenu=false" href="#blog"      class="px-3 py-2 rounded-lg hover:bg-white/10">Blog</a>
          <a @click="openMenu=false" href="#confianza" class="px-3 py-2 rounded-lg hover:bg-white/10">Confianza</a>
          <a @click="openMenu=false" href="#contacto"  class="px-3 py-2 rounded-lg hover:bg-white/10">Contactos</a>

          <button @click="openTracking = true; openMenu=false"
                  class="px-3 py-2 rounded-lg font-semibold text-blue-900 bg-yellow-400 hover:brightness-105">
            Consultar ticket
          </button>

          @guest
            <a href="{{ route('login') }}"
               class="px-3 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-500">
              Login
            </a>
            @if (Route::has('register'))
            <a href="{{ route('register') }}"
               class="px-3 py-2 rounded-lg font-semibold text-blue-900 bg-white ring-1 ring-white/30 hover:bg-gray-50">
              Registrarse
            </a>
            @endif
          @else
            @if (auth()->user()->hasRole('admin'))
              <a href="{{ url('/admin') }}" class="px-3 py-2 rounded-lg hover:bg-white/10">Panel Admin</a>
            @elseif (auth()->user()->hasRole('empleado'))
              <a href="{{ url('/staff') }}" class="px-3 py-2 rounded-lg hover:bg-white/10">Panel Staff</a>
            @else
              <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-white/10">Mi panel</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="px-3 py-2 rounded-lg font-semibold text-blue-900 bg-white ring-1 ring-white/30 hover:bg-gray-50">
                Salir
              </button>
            </form>
          @endguest
        </div>
      </div>
    </header>

    {{-- HERO / CARRUSEL --}}
    <section class="relative">
        <div class="swiper heroSwiper">
            <div class="swiper-wrapper">

                {{-- Slide 1 --}}
                <div class="swiper-slide">
                    <div class="relative h-[72vh]">
                        <img class="w-full h-full object-cover" src="{{ asset('images/premium_photo-1740363268539-cd9093c3b5d1.avif') }}" alt="Soporte técnico">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end pb-16">
                            <div class="max-w-7xl mx-auto px-6 text-white">
                                <h1 class="text-4xl md:text-6xl font-black leading-tight drop-shadow">
                                    Soporte Técnico, Redes y Soluciones TI
                                </h1>
                                <p class="mt-3 text-lg md:text-xl/relaxed text-white/90 max-w-3xl">
                                    Resolvemos tus problemas de tecnología con rapidez, comunicación clara y garantía real.
                                </p>
                                <div class="mt-6 flex flex-wrap gap-3">
                                    <button @click="openTracking = true"
                                            class="inline-flex items-center gap-2 bg-white/95 text-blue-900 px-6 py-3 rounded-xl font-semibold shadow hover:bg-white active:scale-[.98] transition">
                                        Consultar ticket
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 2 --}}
                <div class="swiper-slide">
                    <div class="relative h-[72vh]">
                        <img class="w-full h-full object-cover" src="{{ asset('images/nat-hwcMLF374mY-unsplash.jpg') }}" alt="Mantenimiento de equipos">
                        <div class="absolute inset-0 bg-gradient-to-t from-blue-900/70 via-blue-900/30 to-transparent"></div>
                        <div class="absolute inset-0 flex items-end pb-16">
                            <div class="max-w-7xl mx-auto px-6 text-white">
                                <h2 class="text-4xl md:text-6xl font-black leading-tight drop-shadow">Mesa de Ayuda & SLA</h2>
                                <p class="mt-3 text-lg md:text-xl/relaxed text-white/90 max-w-3xl">Tickets, priorización de incidentes, inventarios y soporte remoto.</p>
                                <a href="#servicios"
                                   class="inline-flex mt-6 items-center gap-2 bg-white text-blue-900 px-6 py-3 rounded-xl font-semibold shadow hover:bg-gray-100 active:scale-[.98] transition">
                                    Ver servicios
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev !text-white"></div>
            <div class="swiper-button-next !text-white"></div>
        </div>
    </section>

    {{-- SERVICIOS --}}
    <section id="servicios" class="max-w-7xl mx-auto px-6 py-20">
        <h3 class="text-3xl md:text-4xl font-extrabold text-center mb-3 tracking-tight">Servicios principales</h3>
        <p class="text-center text-gray-600 mb-12">Lo que hacemos y por qué elegirnos</p>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach([
                ['title'=>'Soporte Técnico','desc'=>'Diagnóstico, reparación y mantenimiento de equipos.','icon'=>'wrench'],
                ['title'=>'Redes','desc'=>'Cableado, Wi-Fi, VPN, firewall y seguridad perimetral.','icon'=>'wifi'],
                ['title'=>'Mesa de Ayuda','desc'=>'Tickets, SLA, inventario, soporte remoto y onsite.','icon'=>'life-buoy'],
                ['title'=>'Soluciones TI','desc'=>'Automatización, backups, cloud y desarrollo a medida.','icon'=>'cpu'],
            ] as $s)
                <div class="bg-white/70 backdrop-blur rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition p-6 flex flex-col items-center text-center">
                    <div class="mb-4 p-3 rounded-xl bg-blue-50 text-blue-600">
                        <i data-lucide="{{ $s['icon'] }}" class="w-10 h-10"></i>
                    </div>
                    <h4 class="font-bold text-xl mb-2">{{ $s['title'] }}</h4>
                    <p class="text-gray-600">{{ $s['desc'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <p class="text-lg text-gray-700 max-w-3xl mx-auto">
                Somos un equipo de ingenieros y desarrolladores con experiencia en <b>soporte, redes y software</b>. Enfocados en <b>calidad</b>, <b>comunicación</b> y <b>resultados</b>.
            </p>
        </div>
    </section>

    {{-- MISIÓN, VISIÓN & LOGO --}}
    <section id="quienes" class="bg-white">
    <div class="max-w-7xl mx-auto px-6 py-20">
        <h3 class="text-3xl md:text-4xl font-extrabold text-center mb-12 tracking-tight">
        Nuestra esencia
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- MISIÓN --}}
        <article class="group rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition bg-white">
            <div class="p-6">
            <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-xl bg-blue-50 text-blue-600">
                <i data-lucide="target" class="w-6 h-6"></i>
            </div>
            <h4 class="text-xl font-bold">Misión</h4>
            <p class="text-gray-600 mt-2">
                Brindar soluciones integrales de <b>soporte técnico, redes e infraestructura TI</b>,
                con tiempos de respuesta ágiles, comunicación clara y enfoque en la calidad,
                para mantener la operación tecnológica de nuestros clientes siempre disponible.
            </p>
            </div>
        </article>

        {{-- VISIÓN --}}
        <article class="group rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition bg-white">
            <div class="p-6">
            <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600">
                <i data-lucide="eye" class="w-6 h-6"></i>
            </div>
            <h4 class="text-xl font-bold">Visión</h4>
            <p class="text-gray-600 mt-2">
                Ser el aliado tecnológico preferido por pymes y empresas en la región,
                destacándonos por nuestra <b>confiabilidad</b>, <b>innovación</b> y <b>resultados medibles</b>
                en proyectos de networking, ciberseguridad y automatización.
            </p>
            </div>
        </article>

        {{-- LOGO + IDENTIDAD --}}
        <article class="group rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition bg-white">
        <div class="p-6 flex flex-col items-center text-center">
            <img src="{{ asset('images/logo.jpg') }}"
                alt="RSS Solutions"
                class="w-48 h-48 md:w-56 md:h-56 rounded-2xl ring-1 ring-gray-200 shadow mb-6 object-contain">
            <div class="mt-6 flex flex-wrap items-center justify-center gap-4 text-sm text-gray-500">
            <span class="inline-flex items-center gap-1">
                <i data-lucide="shield-check" class="w-4 h-4"></i> Calidad
            </span>
            <span class="inline-flex items-center gap-1">
                <i data-lucide="messages-square" class="w-4 h-4"></i> Comunicación
            </span>
            <span class="inline-flex items-center gap-1">
                <i data-lucide="trending-up" class="w-4 h-4"></i> Resultados
            </span>
            </div>
        </div>
        </article>
        </div>
    </div>
    </section>

    {{-- BLOG --}}
    <section id="blog" class="max-w-7xl mx-auto px-6 py-20">
    <h3 class="text-3xl md:text-4xl font-extrabold text-center mb-12 tracking-tight">
        Blog / Consejos de Tecnología
    </h3>

    @php
        if (!isset($posts)) {
            $posts = [
                ['title'=>'5 tips para acelerar tu PC', 'excerpt'=>'Limpieza, SSD, RAM y prácticas rápidas…', 'url'=>'#'],
                ['title'=>'Cómo proteger tu Wi-Fi',     'excerpt'=>'Credenciales, WPA2/3, invitados y segmentación…', 'url'=>'#'],
                ['title'=>'Checklist antes de formatear','excerpt'=>'Backups, drivers, licencias y claves…', 'url'=>'#'],
            ];
        }

        $localCovers = [
            'images/premium_photo-1664301923554-fa1023546fd8.avif',
            'images/premium_photo-1676618539987-12b7c8a8ae61.avif',
            'images/premium_photo-1661373049672-3bae68ac5681.avif',
        ];

        $coverUrl = function($path) {
            $full = public_path($path);
            $v = file_exists($full) ? filemtime($full) : time();
            return asset($path) . '?v=' . $v;
        };
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($posts as $p)
        @php
            $idx = $loop->index % count($localCovers);
            $forcedCover = $coverUrl($localCovers[$idx]);
        @endphp

        <article class="group bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-md transition">
            @if(Str::endsWith($localCovers[$idx], '.avif'))
            <picture>
                <source srcset="{{ $forcedCover }}" type="image/avif">
                <img src="{{ $forcedCover }}"
                    class="w-full h-48 object-cover group-hover:scale-[1.01] transition"
                    alt="{{ $p['title'] }}" loading="lazy">
            </picture>
            @else
            <img src="{{ $forcedCover }}"
                class="w-full h-48 object-cover group-hover:scale-[1.01] transition"
                alt="{{ $p['title'] }}" loading="lazy">
            @endif

            <div class="p-6">
            <h4 class="font-bold text-xl">{{ $p['title'] }}</h4>
            <p class="text-gray-600 mt-2">{{ $p['excerpt'] ?? '' }}</p>
            <a href="{{ $p['url'] ?? '#' }}" class="inline-flex items-center gap-1 mt-4 text-blue-700 hover:underline">
                Leer más
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            </div>
        </article>
        @endforeach
    </div>
    </section>

{{-- CONFIANZA / RESPALDO --}}
<section id="confianza" class="bg-white">
    <div class="max-w-7xl mx-auto px-6 py-20">
        <h3 class="text-3xl md:text-4xl font-extrabold text-center mb-4 tracking-tight">
            Confianza & Respaldo
        </h3>
        <p class="text-center text-gray-600 mb-12">
            Certificaciones, capacitaciones y alianzas que respaldan nuestro trabajo.
        </p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 items-center">
            @foreach([
                ['label'=>'Cisco Intro Cybersecurity','img'=>asset('images/introduction_to_cybersecurity_16.png')],
                ['label'=>'Fortinet NSE 1-3','img'=>asset('images/NSE1-Certification.png')],
                ['label'=>'AWS Cloud Essentials','img'=>asset('images/cloud-essentials.png')],
                ['label'=>'Laravel Pro','img'=>asset('images/Laravel.svg.png')],
            ] as $b)
            <div class="bg-white/70 backdrop-blur rounded-2xl border border-gray-100 p-6 text-center shadow-sm hover:shadow-md transition">
                <img src="{{ $b['img'] }}" alt="{{ $b['label'] }}" class="h-16 mx-auto mb-3 object-contain">
                <div class="text-sm font-medium text-gray-700">{{ $b['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

    {{-- FOOTER (azul oscuro) --}}
    <footer class="bg-blue-900 text-white">
      <div class="max-w-7xl mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <p>&copy; {{ date('Y') }} RSS Solutions. Todos los derechos reservados.</p>
        <p class="text-sm opacity-80">Soporte técnico, redes, mesa de ayuda y soluciones TI.</p>
      </div>
    </footer>

    {{-- BOTÓN FLOTANTE: CHAT --}}
    <button @click="openChat = true"
      class="fixed bottom-36 right-5 z-[55] rounded-full shadow-lg p-4 bg-indigo-600 text-white hover:bg-indigo-500 active:scale-[.98] transition"
      aria-label="Chat">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor">
        <path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/>
      </svg>
    </button>

    {{-- BOTÓN FLOTANTE: WHATSAPP --}}
    <a href="https://wa.me/{{ env('WHATSAPP_NUMBER','573001112233') }}?text=Hola%20RSS%20Solutions,%20necesito%20soporte%20por%20favor."
       target="_blank"
       class="fixed bottom-20 right-5 z-[55] rounded-full shadow-lg p-4 bg-green-500 text-white hover:bg-green-600 active:scale-[.98] transition"
       aria-label="WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20.52 3.48A11.94 11.94 0 0 0 12.01 0C5.39 0 .03 5.37.03 12c0 2.1.55 4.16 1.6 5.98L0 24l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM12.01 22c-1.94 0-3.84-.5-5.5-1.46l-.39-.23-3.68.96.98-3.58-.25-.37A10.04 10.04 0 1 1 22.02 12c0 5.53-4.49 10-10.01 10zm5.63-7.5c-.31-.16-1.84-.9-2.12-.99-.28-.1-.49-.16-.7.16s-.81.99-.99 1.2-.37.24-.68.08c-.31-.16-1.31-.48-2.5-1.52-.92-.82-1.54-1.84-1.72-2.15-.18-.31-.02-.48.14-.64.14-.14.31-.37.47-.55.16-.18.21-.31.31-.52.1-.21.05-.39-.03-.55-.08-.16-.7-1.68-.96-2.31-.25-.6-.5-.52-.7-.53h-.6c-.21 0-.55.08-.84.39s-1.11 1.09-1.11 2.66 1.14 3.09 1.3 3.3c.16.21 2.24 3.42 5.43 4.79.76.33 1.35.52 1.81.67.76.24 1.45.21 2 .13.61-.09 1.84-.75 2.1-1.47.26-.72.26-1.34.18-1.47-.08-.13-.29-.21-.6-.37z"/>
        </svg>
    </a>

    {{-- MODAL: Chat --}}
    <div x-show="openChat"
         x-transition.opacity
         @keydown.escape.window="openChat=false"
         class="fixed inset-0 z-[60] flex items-end md:items-center md:justify-end p-0 md:p-4"
         style="display:none">
      <div class="absolute inset-0 bg-black/50" @click="openChat=false"></div>
      <div class="relative w-full md:w-[420px] max-h-[90vh] bg-white rounded-t-2xl md:rounded-2xl shadow-2xl overflow-hidden md:mr-4">
        <div class="px-5 py-4 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white flex items-center justify-between">
          <h3 class="text-lg font-bold">Chat — Escríbenos ahora</h3>
          <button class="text-white/90 hover:text-white" @click="openChat=false" aria-label="Cerrar">✕</button>
        </div>
        <div class="p-5 space-y-4">
          <div class="text-sm text-gray-600">Déjanos tu mensaje y tus datos básicos; te responderemos lo antes posible.</div>
          <form action="{{ route('contact.submit') }}" method="POST" class="space-y-3">
            @csrf
            <input name="name"  class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nombre *" required>
            <input name="phone" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Teléfono">
            <input name="email" type="email" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Email">
            <textarea name="message" rows="4" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Escribe tu mensaje…" required></textarea>
            <input type="hidden" name="channel" value="chat">
            <div class="flex items-center justify-end gap-2 pt-1">
              <button type="button" @click="openChat=false" class="px-4 py-2 rounded-lg border hover:bg-gray-50">Cerrar</button>
              <button class="px-5 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-500 active:scale-[.98] transition">Enviar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- MODAL: Consultar referencia --}}
    <!-- Importante: aquí NO redefinimos openTracking -->
    <div
      x-show="openTracking"
      x-transition.opacity
      @keydown.escape.window="openTracking=false"
      class="fixed inset-0 z-[60] flex items-center justify-center p-4"
      x-cloak
      style="display:none">

        <div class="absolute inset-0 bg-black/50" @click="openTracking=false"></div>

        <!-- Aquí sí usamos x-data, pero solo para el estado interno (code, loading, etc.) -->
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden ring-1 ring-black/5"
             x-data="trackingState('{{ route('tracking.lookup') }}')">
            <div class="px-6 py-5 border-b bg-gradient-to-r from-blue-600 to-blue-500 text-white flex items-center justify-between">
                <h3 class="text-lg md:text-xl font-bold">Consultar mi referencia</h3>
                <button class="text-white/90 hover:text-white" @click="openTracking=false" aria-label="Cerrar">✕</button>
            </div>

            <form @submit.prevent="consultar" class="p-6 space-y-4">
                <p class="text-gray-600">Ingresa el código entregado al dejar tu equipo (ej: RS-ABC123).</p>

                <div>
                    <label for="tracking_code" class="block text-sm font-medium text-gray-700">Código de ticket</label>
                    <input id="tracking_code" name="code" x-model="code" required
                           class="mt-1 w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="RS-ABC123">
                </div>

                <template x-if="error">
                    <div class="text-red-600 text-sm" x-text="error"></div>
                </template>

                <div x-show="resultado" x-cloak class="mt-3 rounded-xl border border-gray-200 p-4 bg-gray-50">
                    <h4 class="font-semibold text-gray-800 mb-2">Resultado</h4>
                    <dl class="grid grid-cols-2 gap-2 text-sm">
                        <dt class="text-gray-500">Código</dt>
                        <dd class="font-medium" x-text="resultado.codigo"></dd>

                        <dt class="text-gray-500">Módulo</dt>
                        <dd class="font-medium" x-text="capitalize(resultado.modulo)"></dd>

                        <dt class="text-gray-500">Estado</dt>
                        <dd class="font-medium" x-text="resultado.estado ?? '—'"></dd>
                    </dl>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="openTracking=false"
                            class="px-4 py-2 rounded-xl border bg-white hover:bg-gray-50">Cancelar</button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-500"
                            :disabled="loading">
                        <span x-show="!loading">Consultar</span>
                        <span x-show="loading">Consultando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Estado interno del modal (NO redefine openTracking)
    function trackingState(lookupUrl) {
        return {
            code: '',
            loading: false,
            error: '',
            resultado: null,

            async consultar() {
                this.error = '';
                this.resultado = null;

                if (!this.code.trim()) {
                    this.error = 'Debes ingresar un código de ticket.';
                    return;
                }

                this.loading = true;
                try {
                    const res = await fetch(`${lookupUrl}?code=${encodeURIComponent(this.code)}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();

                    if (!res.ok || !data.ok) {
                        this.error = data.message ?? 'No encontramos ningún ticket con ese código.';
                    } else {
                        this.resultado = data.data;
                    }
                } catch (e) {
                    this.error = 'Ocurrió un error al consultar.';
                } finally {
                    this.loading = false;
                }
            },

            capitalize(txt) {
                if (!txt) return '';
                return txt.charAt(0).toUpperCase() + txt.slice(1);
            }
        }
    }
    </script>

    {{-- MODAL: Información al abrir --}}
    <div x-show="openInfo"
         x-transition.opacity
         @keydown.escape.window="openInfo=false"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="display:none">
      <div class="absolute inset-0 bg-black/50" @click="openInfo=false"></div>

      <div class="relative w-full max-w-xl">
        <div class="absolute -top-16 -left-16 h-40 w-40 rounded-full bg-blue-500/20 blur-2xl"></div>
        <div class="absolute -bottom-12 -right-12 h-44 w-44 rounded-full bg-indigo-500/20 blur-2xl"></div>

        <div class="relative bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl ring-1 ring-black/5 overflow-hidden">
          <div class="px-6 py-5 bg-gradient-to-r from-blue-700 to-indigo-600 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 ring-1 ring-white/30">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </span>
              <div>
                <p class="text-xs uppercase tracking-widest text-white/80">Aviso</p>
                <h3 class="text-lg md:text-xl font-bold">Bienvenido a RSS Solutions</h3>
              </div>
            </div>
            <button class="text-white/90 hover:text-white" @click="openInfo=false" aria-label="Cerrar">✕</button>
          </div>

          <div class="p-6">
            <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-amber-100 text-amber-800 px-3 py-1 text-xs font-semibold ring-1 ring-amber-200">
              <span class="h-2 w-2 rounded-full bg-amber-500"></span> Novedad
            </div>

            <p class="text-gray-700 mb-4">
              Ya puedes <a href="#contacto" class="text-blue-700 hover:underline font-medium">solicitar soporte</a> desde el chat o el formulario.
            </p>

            <div class="mb-5 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3">
              <p class="text-sm text-blue-900">
                ¿Necesitas <b>solicitar cualquier servicio</b> y llevar una <b>guía de seguimiento</b> de tus equipos y tickets?
                <a href="{{ Route::has('register') ? route('register') : '#' }}"
                   class="font-semibold underline decoration-blue-300 hover:decoration-blue-600">
                  Te invitamos a registrarte
                </a>
                para activar tu panel y ver el estado en tiempo real.
              </p>
            </div>

            <ul class="space-y-3">
              <li class="flex items-start gap-3">
                <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-green-100 text-green-700 ring-1 ring-green-200">
                  <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                  </svg>
                </span>
                <div>
                  <p class="font-semibold text-gray-900">Atención rápida</p>
                  <p class="text-sm text-gray-600">Chat directo o formulario para registrar tu solicitud.</p>
                </div>
              </li>
              <li class="flex items-start gap-3">
                <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-blue-700 ring-1 ring-blue-200">
                  <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3"/>
                  </svg>
                </span>
                <div>
                  <p class="font-semibold text-gray-900">Horario</p>
                  <p class="text-sm text-gray-600">Lun–Vie 8:00–18:00 • Sáb 9:00–13:00</p>
                </div>
              </li>
              <li class="flex items-start gap-3">
                <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200">
                  <svg class="h-3.5 w-3.5" viewBox="0 0 24 24  fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4"/>
                  </svg>
                </span>
                <div>
                  <p class="font-semibold text-gray-900">Seguimiento de equipos</p>
                  <p class="text-sm text-gray-600">Usa <em>Consultar ticket</em> para ver el estado.</p>
                </div>
              </li>
            </ul>

            <details class="mt-5 group rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 open:ring-1 open:ring-gray-200">
              <summary class="cursor-pointer select-none list-none font-semibold text-gray-900 flex items-center justify-between">
                Guía rápida de seguimiento
                <span class="ml-3 text-gray-500 group-open:rotate-180 transition">▾</span>
              </summary>
              <ol class="mt-3 space-y-2 text-sm text-gray-700 list-decimal list-inside">
                <li>Regístrate y accede a tu panel.</li>
                <li>Genera una orden de servicio o registra tu equipo.</li>
                <li>Recibe tu <b>código de ticket</b> (ej: RS-ABC123).</li>
                <li>Consulta avances en <em>Mi panel</em> o con “Consultar ticket”.</li>
                <li>Recibe notificaciones por email/WhatsApp cuando haya actualizaciones.</li>
              </ol>
            </details>
          </div>

          <div class="px-6 pb-6 flex items-center justify-end gap-3">
            <button @click="openInfo=false" class="px-4 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50">
              Cerrar
            </button>
            <a href="{{ Route::has('register') ? route('register') : '#' }}"
               class="px-5 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-500">
              Registrarme
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- Swiper JS --}}
    <script src="https://unpkg.com/swiper@10/swiper-bundle.min.js"></script>
    <script>
        new Swiper('.heroSwiper', {
            loop: true,
            autoplay: { delay: 4500, disableOnInteraction: false },
            speed: 700,
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });

        document.addEventListener('DOMContentLoaded', () => {
          if (window.lucide && typeof window.lucide.createIcons === 'function') {
            lucide.createIcons();
          }
        });
    </script>
</body>
</html>
