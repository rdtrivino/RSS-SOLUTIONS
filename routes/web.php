<?php

use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// Controladores (HTTP) clásicos
// ─────────────────────────────────────────────────────────────────────────────
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RadicadoPdfController;
use App\Http\Controllers\FacturaPosController;

// ─────────────────────────────────────────────────────────────────────────────
// Componentes Livewire (páginas internas)
// ─────────────────────────────────────────────────────────────────────────────
use App\Livewire\SoporteForm;       // /soporte
use App\Livewire\ContratanosPage;   // /contacto  (Contrátanos)
use App\Livewire\PqrForm;           // /pqr
use App\Livewire\ConsultaTicket;    // /consulta-nit (Consulta de ticket/solicitud)

// ─────────────────────────────────────────────────────────────────────────────
// Público
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/contacto/enviar', [ContactController::class, 'submit'])
    ->name('contact.submit');

Route::get('/tracking', [TrackingController::class, 'lookup'])
    ->name('tracking.lookup');

Route::get('/pdf/radicado/{radicado}', RadicadoPdfController::class)
    ->whereNumber('radicado')
    ->name('radicado.pdf')       // nombre de la ruta para URL firmada
    ->middleware('signed');      // exige URL firmada

// Ruta POS protegida con login
Route::middleware('auth')->group(function () {
    Route::get('/facturas/{factura}/pos/print', [FacturaPosController::class, 'print'])
        ->name('facturas.pos.print');
});

// ─────────────────────────────────────────────────────────────────────────────
// Área autenticada (Breeze)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (Blade normal de Breeze)
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Perfil (Breeze)
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Páginas internas — Livewire
    Route::get('/soporte', SoporteForm::class)->name('soporte.index');
    Route::get('/contacto', ContratanosPage::class)->name('contacto.index');       // "Contrátanos"
    Route::get('/pqr', PqrForm::class)->name('pqr.index');
    Route::get('/consulta-nit', ConsultaTicket::class)->name('consulta-nit.index');
});

// ─────────────────────────────────────────────────────────────────────────────
// Autenticación (Breeze / Fortify / etc.)
// ─────────────────────────────────────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ─────────────────────────────────────────────────────────────────────────────
// Fallback 404 (opcional)
// ─────────────────────────────────────────────────────────────────────────────
// Route::fallback(fn () => abort(404));
