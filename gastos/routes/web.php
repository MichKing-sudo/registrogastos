<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GastoController;

// Forzar HTTPS en todas las rutas
URL::forceScheme('https');

Route::get('/', [GastoController::class, 'index'])->name('gastos.index');
Route::post('/gastos', [GastoController::class, 'store'])->name('gastos.store');
Route::delete('/gastos/{id}', [GastoController::class, 'destroy'])->name('gastos.destroy');
Route::post('/gastos/limpiar', [GastoController::class, 'limpiar'])->name('gastos.limpiar');