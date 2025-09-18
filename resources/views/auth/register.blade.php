<x-guest-layout>
    <div class="text-center mb-6">
        <div class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-sky-100">
            <!-- Icono de registro -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <h2 class="mt-4 text-2xl font-bold text-gray-800">Crear cuenta</h2>
        <p class="text-gray-500 text-sm">Regístrate para comenzar a usar la plataforma</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Nombre -->
        <div>
            <x-input-label for="name" :value="__('Nombre completo')" class="sr-only" />
            <x-text-input id="name"
                type="text"
                name="name"
                :value="old('name')"
                required autofocus
                placeholder="Nombre completo"
                class="w-full rounded-lg border-gray-300 focus:border-sky-600 focus:ring-sky-600" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Correo -->
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" class="sr-only" />
            <x-text-input id="email"
                type="email"
                name="email"
                :value="old('email')"
                required autocomplete="username"
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
                required autocomplete="new-password"
                placeholder="Contraseña"
                class="w-full rounded-lg border-gray-300 focus:border-sky-600 focus:ring-sky-600" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar contraseña -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" class="sr-only" />
            <x-text-input id="password_confirmation"
                type="password"
                name="password_confirmation"
                required autocomplete="new-password"
                placeholder="Confirmar contraseña"
                class="w-full rounded-lg border-gray-300 focus:border-sky-600 focus:ring-sky-600" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Botón -->
        <x-primary-button
            class="w-full justify-center rounded-lg bg-sky-600 hover:bg-sky-700 focus:ring-sky-600 py-2.5 text-base font-semibold">
            Registrarse
        </x-primary-button>

        <!-- Link a login -->
        <p class="text-center text-sm text-gray-500 mt-4">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="text-sky-600 hover:underline font-semibold">Inicia sesión</a>
        </p>
    </form>
</x-guest-layout>
