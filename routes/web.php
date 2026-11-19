<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\HorarioSuministroController;
use App\Http\Controllers\TarifaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'active'])->name('dashboard');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. 🗺️ RUTAS ESPECIALES DE MAPAS (¡Colocarlas ANTES de los recursos para evitar conflictos 404!)
    Route::get('/mapa', [MapController::class, 'general'])->name('map.index');

    // 3. Recursos (CRUDs principales)
    Route::resource('redes', RedController::class);
    Route::resource('usuarios', UsuarioController::class);
    Route::resource('horarios', HorarioSuministroController::class)->parameters(['horarios' => 'horario']);

    Route::get('/tarifas/json', [TarifaController::class, 'indexAjax'])->name('tarifas.json');
    Route::post('/tarifas/store-ajax', [TarifaController::class, 'store'])->name('tarifas.store.ajax');
    Route::post('/tarifas/{tarifa}/toggle-status', [TarifaController::class, 'toggleStatus'])->name('tarifas.toggleStatus');

    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
    Route::get('/pagos/matriz', [PagoController::class, 'matriz'])->name('pagos.matriz');
    Route::post('/pagos/{pago}/anular', [PagoController::class, 'anular'])->name('pagos.anular');

    Route::get('/pagos/{pago}/ticket', [PagoController::class, 'ticket'])->name('pagos.ticket');
    Route::get('/pagos/{pago}/comprobante', [PagoController::class, 'comprobante'])->name('pagos.comprobante');

    Route::get('/caja/escanear', [ReciboController::class, 'vistaEscanear'])->name('recibos.escanear');
    Route::get('/recibos/verificar-json/{token}', [ReciboController::class, 'verificarPorQrJson'])->name('recibos.verificar.json');
    Route::post('/recibos/cobrar-qr/{id}', [ReciboController::class, 'procesarCobroQr'])->name('recibos.cobrar.qr');

    Route::get('/recibos/verificar/{token}', [ReciboController::class, 'verificarPorQr'])->name('recibos.verificar');
});

Route::middleware(['auth', 'active', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/cuentas', [UserManagementController::class, 'index'])->name('cuentas.index');
        Route::post('/cuentas', [UserManagementController::class, 'store'])->name('cuentas.store');
        Route::patch('/cuentas/{usuario}/status', [UserManagementController::class, 'updateStatus'])->name('cuentas.status');
    });

require __DIR__ . '/auth.php';