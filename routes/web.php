<?php

use Illuminate\Support\Facades\Route;

// Controladores propios
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Público
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Área autenticada (Breeze)
|--------------------------------------------------------------------------
| Nota: /dashboard será el dashboard NORMAL de Breeze (no redirige al Home).
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard normal de Breeze
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // Perfil de usuario (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Formularios del Home
|--------------------------------------------------------------------------
*/
Route::post('/contacto/enviar', [ContactController::class, 'submit'])->name('contact.submit'); // Form “Contactos”
Route::get('/tracking', [TrackingController::class, 'lookup'])->name('tracking.lookup');       // Modal “Consultar referencia”

/*
|--------------------------------------------------------------------------
| Autenticación (Breeze / Fortify)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
