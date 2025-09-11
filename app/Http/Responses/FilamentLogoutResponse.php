<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;

class FilamentLogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        // A dónde quieres enviar cuando hacen logout del panel
        return redirect()->to('/'); // Home
    }
}
