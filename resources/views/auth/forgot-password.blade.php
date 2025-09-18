<x-guest-layout>
    <div class="text-center mb-6">
        <div class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-sky-100">
            <!-- Icono de candado -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 11c.828 0 1.5-.672 1.5-1.5S12.828 8 12 8s-1.5.672-1.5 1.5S11.172 11 12 11zm0 0v2m0 4h.01M6 11V9a6 6 0 1112 0v2m-9 0h6" />
            </svg>
        </div>
        <h2 class="mt-4 text-2xl font-bold text-gray-800">¿Olvidaste tu contraseña?</h2>
        <p class="text-gray-500 text-sm mt-1">
            No hay problema, ingresa tu correo y te enviaremos un enlace para restablecerla.
        </p>
    </div>

    <!-- Estado de sesión -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Correo electrónico -->
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" class="sr-only" />
            <x-text-input id="email"
                type="email"
                name="email"
                :value="old('email')"
                required autofocus
                placeholder="Correo electrónico"
                class="w-full rounded-lg border-gray-300 focus:border-sky-600 focus:ring-sky-600" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Botón -->
        <x-primary-button
            class="w-full justify-center rounded-lg bg-sky-600 hover:bg-sky-700 focus:ring-sky-600 py-2.5 text-base font-semibold">
            Enviar enlace de recuperación
        </x-primary-button>

        <!-- Volver a login -->
        <p class="text-center text-sm text-gray-500 mt-4">
            <a href="{{ route('login') }}" class="text-sky-600 hover:underline font-semibold">
                ← Volver al inicio de sesión
            </a>
        </p>
    </form>
</x-guest-layout>
