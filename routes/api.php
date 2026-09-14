<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BibliotecaController;
use App\Http\Controllers\Api\V1\CalendarioController;
use App\Http\Controllers\Api\V1\ConfigController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\InformesController;
use App\Http\Controllers\Api\V1\MigracionController;
use App\Http\Controllers\Api\V1\MinistriesController;
use App\Http\Controllers\Api\V1\NotificacionesController;
use App\Http\Controllers\Api\V1\OracionController;
use App\Http\Controllers\Api\V1\PerfilController;
use App\Http\Controllers\Api\V1\RelacionamientoController;
use App\Http\Controllers\Api\V1\ReportesController;
use App\Http\Controllers\Api\V1\RolesController;
use App\Http\Controllers\Api\V1\SedesController;
use App\Http\Controllers\Api\V1\SystemController;
use App\Http\Controllers\Api\V1\TemasController;
use App\Http\Controllers\Api\V1\UsuariosController;
use App\Http\Controllers\Api\V1\WebPushController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/system/status', [SystemController::class, 'status']);

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/login/google', [AuthController::class, 'googleLogin']);
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::post('/auth/setup-status', [AuthController::class, 'setupStatus']);
Route::post('/auth/setup-password', [AuthController::class, 'setupPassword']);
Route::post('/auth/user-status', [AuthController::class, 'userStatus']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/auth/vapid-public-key', [WebPushController::class, 'vapidPublicKey']);

// Rutas protegidas (requieren token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/config/church', [ConfigController::class, 'index']);
    Route::put('/config/church', [ConfigController::class, 'update'])->middleware('admin');

    Route::get('/migracion', [MigracionController::class, 'index'])->middleware('admin');
    Route::post('/migracion', [MigracionController::class, 'store'])->middleware('admin');

    // Rutas específicas ANTES de apiResource (evitar ser capturadas por {relacionamiento})
    Route::get('/relacionamiento/mired', [RelacionamientoController::class, 'myNetwork']);
    Route::get('/relacionamiento/disciples/available', [RelacionamientoController::class, 'availableDisciples']);
    Route::get('/relacionamiento/{relacionamiento}/reports', [RelacionamientoController::class, 'getReports']);

    Route::get('/oracion/lista', [OracionController::class, 'list']);
    Route::get('/calendario/notifications', [CalendarioController::class, 'notifications']);

    Route::apiResource('temas', TemasController::class)->except(['create', 'edit'])->middleware('admin');
    Route::apiResource('roles', RolesController::class)->except(['create', 'edit'])->middleware('admin');
    Route::apiResource('usuarios', UsuariosController::class)->except(['create', 'edit'])->middleware('admin');
    Route::apiResource('relacionamiento', RelacionamientoController::class)->except(['create', 'edit']);
    Route::apiResource('informes', InformesController::class)->except(['create', 'edit']);
    Route::apiResource('oracion', OracionController::class)->except(['create', 'edit']);
    Route::apiResource('calendario', CalendarioController::class)->except(['create', 'edit']);
    Route::apiResource('perfil', PerfilController::class)->only(['show', 'update']);

    Route::get('/informes/export', [InformesController::class, 'export']);

    Route::get('/reportes', [ReportesController::class, 'index']);

    Route::apiResource('ministries', MinistriesController::class)->except(['create', 'edit'])->middleware('admin');
    Route::apiResource('sedes', SedesController::class)->except(['create', 'edit'])->middleware('admin');

    Route::get('/notificaciones', [NotificacionesController::class, 'index']);
    Route::get('/biblioteca', [BibliotecaController::class, 'index']);

    Route::get('/webpush/vapid-public-key', [WebPushController::class, 'vapidPublicKey']);
    Route::post('/webpush/subscribe', [WebPushController::class, 'subscribe']);
    Route::post('/webpush/unsubscribe', [WebPushController::class, 'unsubscribe']);
});
