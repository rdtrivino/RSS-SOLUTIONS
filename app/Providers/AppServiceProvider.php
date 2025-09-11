<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as FilamentLogoutResponseContract;
use App\Http\Responses\FilamentLogoutResponse;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Redirección de logout de Filament → Home
        $this->app->bind(FilamentLogoutResponseContract::class, FilamentLogoutResponse::class);
    }

    public function boot(): void
    {
        //
    }
}