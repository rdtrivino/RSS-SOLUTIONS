<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name','Laravel') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Assets --}}
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-neutral-100 text-neutral-900">
    <div class="min-h-screen relative flex items-center justify-center px-4">

        {{-- ← Inicio (más abajo y un poco a la derecha) --}}
        <nav class="absolute top-10 left-8">
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 text-sm text-neutral-600 hover:text-neutral-900 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Inicio</span>
            </a>
        </nav>

        {{-- Card centrado --}}
        <main class="w-full max-w-md bg-white border border-neutral-200 rounded-xl shadow-sm p-6 md:p-7">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
