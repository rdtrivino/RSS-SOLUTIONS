<?php

use Illuminate\Support\Facades\Route;

// ⬇️ Importa tus controladores
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\ProfileController;

// Home público (tu vista bonita)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Dashboard de usuarios (Breeze) — solo autenticados + verificados
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Formularios del Home:
Route::post('/contacto/enviar', [ContactController::class, 'submit'])->name('contact.submit'); // Form “Contactos”
Route::get('/tracking', [TrackingController::class, 'lookup'])->name('tracking.lookup');       // Modal “Consultar referencia”

// Rutas de Breeze (login/registro/reset/etc.)
require __DIR__.'/auth.php';
