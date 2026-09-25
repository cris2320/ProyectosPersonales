<?php

declare(strict_types=1);

use App\Modules\Identidad\Http\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/auth')->middleware(['api'])->group(function (): void {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware(['auth:personal'])->group(function (): void {
        Route::get('/yo', [AuthController::class, 'yo']);
        Route::post('/logout', [AuthController::class, 'logout']);
        // Configurar MFA: accesible con token completo o con el token temporal mfa:configurar
        Route::post('/mfa/generar', [AuthController::class, 'generarMfa'])->middleware('ability:mfa:configurar,rol:administrador,rol:almacen,rol:motorizado');
        Route::post('/mfa/confirmar', [AuthController::class, 'confirmarMfa'])->middleware('ability:mfa:configurar,rol:administrador,rol:almacen,rol:motorizado');
    });
});
