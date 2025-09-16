<x-app-layout>
    <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-extrabold text-gray-900 mb-6">Radicar PQR</h1>

        @if (session()->has('success'))
            <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg">
            <form wire:submit.prevent="radicar" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-200">Tipo</label>
                    <select wire:model.defer="tipo"
                            class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-rose-500">
                        <option value="peticion">Petición</option>
                        <option value="queja">Queja</option>
                        <option value="reclamo">Reclamo</option>
                    </select>
                    @error('tipo') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-200">Descripción</label>
                    <textarea wire:model.defer="descripcion" rows="5"
                              class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-rose-500"
                              placeholder="Describe tu solicitud con el mayor detalle posible…"></textarea>
                    @error('descripcion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-lg shadow-md transition">
                    Registrar PQR
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13.172 12 8.222 7.05l1.414-1.414L16 12l-6.364 6.364-1.414-1.414z"/></svg>
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
