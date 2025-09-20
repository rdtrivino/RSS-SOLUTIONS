<x-filament::page>
    <div class="min-h-[calc(100vh-10rem)] flex flex-col items-center justify-center gap-4">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-gray-100">
                Bienvenido, {{ auth()->user()->getRoleNames()->first() }}@
            </h2>
            ->brandLogo('
                <div class="flex items-center gap-2">
                    <img src="' . asset('images/logo.jpg') . '" alt="RSS Solutions logo" class="h-7 w-auto">
                    <span class="text-sm font-semibold tracking-wide">RSS&nbsp;SOLUTIONS</span>
                </div>
            ')
            ->favicon(asset('images/logo.png'))
    </div>
</x-filament::page>