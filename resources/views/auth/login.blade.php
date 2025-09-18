<x-guest-layout>
    <!-- Estado de sesión -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <div class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-sky-100">
            <!-- Icono de usuario -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5.121 17.804A10.001 10.001 0 0112 15a10.001 10.001 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <h2 class="mt-4 text-2xl font-bold text-gray-800">Bienvenido de nuevo</h2>
        <p class="text-gray-500 text-sm">Ingresa tus credenciales para continuar</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Correo electrónico -->
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" class="sr-only" />
            <x-text-input id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="Correo electrónico"
                class="w-full rounded-lg border-gray-300 focus:border-sky-600 focus:ring-sky-600" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div>
            <x-input-label for="password" :value="__('Contraseña')" class="sr-only" />
            <x-text-input id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Contraseña"
                class="w-full rounded-lg border-gray-300 focus:border-sky-600 focus:ring-sky-600" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Recuérdame y Olvidaste tu contraseña -->
        <div class="flex items-center justify-between text-sm">
            <label for="remember_me" class="flex items-center gap-2">
                <input id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                <span class="text-gray-700">Recuérdame</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sky-600 hover:underline">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <!-- Botón -->
        <x-primary-button
            class="w-full justify-center rounded-lg bg-sky-600 hover:bg-sky-700 focus:ring-sky-600 py-2.5 text-base font-semibold">
            Iniciar sesión
        </x-primary-button>

        <!-- Registro -->
        @if (Route::has('register'))
            <p class="text-center text-sm text-gray-500 mt-4">
                ¿No tienes cuenta?
                <a href="{{ route('register') }}" class="text-sky-600 hover:underline font-semibold">Regístrate</a>
            </p>
        @endif
    </form>
</x-guest-layout>
