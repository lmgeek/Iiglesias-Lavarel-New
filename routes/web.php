<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BibliotecaController;
use App\Http\Controllers\Web\CalendarioController;
use App\Http\Controllers\Web\ConfigController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\InformesController;
use App\Http\Controllers\Web\MiembrosController;
use App\Http\Controllers\Web\OracionController;
use App\Http\Controllers\Web\PerfilController;
use App\Http\Controllers\Web\RelacionamientoController;
use App\Http\Controllers\Web\ReportesController;
use App\Http\Controllers\Web\UsuariosController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/setup', [AuthController::class, 'setup'])->name('setup');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Recuperación de contraseña (web)
Route::get('/olvidaste', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/olvidaste', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Google OAuth (web)
Route::get('/auth/google', [GoogleController::class, 'webRedirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'webCallback'])->name('auth.google.callback');

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));

    // Completar datos obligatorios (bloquea el resto hasta completar)
    Route::get('/completar-datos', [AuthController::class, 'completeForm'])->name('perfil.completar');
    Route::post('/completar-datos', [AuthController::class, 'complete'])->name('perfil.completar.store');

    Route::middleware('profile.complete')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Discipulado
        Route::get('/relacionamiento', [RelacionamientoController::class, 'index'])->name('relacionamiento.index');
        Route::get('/relacionamiento/informes', [RelacionamientoController::class, 'informes'])->name('relacionamiento.informes');
        Route::get('/relacionamiento/mired', [RelacionamientoController::class, 'red'])->name('relacionamiento.red');
        Route::post('/relacionamiento', [RelacionamientoController::class, 'store'])->name('relacionamiento.store');
        Route::get('/discipulos/buscar', [RelacionamientoController::class, 'searchDisciples'])->name('relacionamiento.buscar');
        Route::get('/relacionamiento/{disciple}/informe', [RelacionamientoController::class, 'informeCreate'])->name('relacionamiento.informe.create');
        Route::post('/relacionamiento/{disciple}/informe', [RelacionamientoController::class, 'informeStore'])->name('relacionamiento.informe.store');

        // Grupos de Conexión
        Route::get('/informes', [InformesController::class, 'index'])->name('informes.index');
        Route::get('/informes/nuevo', [InformesController::class, 'create'])->name('informes.create');
        Route::post('/informes', [InformesController::class, 'store'])->name('informes.store');

        // Miembros
        Route::get('/miembros', [MiembrosController::class, 'index'])->name('miembros.index');
        Route::get('/miembros/nuevo', [MiembrosController::class, 'create'])->name('miembros.create');
        Route::post('/miembros', [MiembrosController::class, 'store'])->name('miembros.store');

        // Oración
        Route::get('/oracion', [OracionController::class, 'index'])->name('oracion.index');
        Route::get('/oracion/lista', [OracionController::class, 'lista'])->name('oracion.lista');
        Route::post('/oracion/pedidos', [OracionController::class, 'storePedido'])->name('oracion.pedidos.store');
        Route::patch('/oracion/pedidos/{pedido}', [OracionController::class, 'togglePedido'])->name('oracion.pedidos.toggle');

        // Calendario
        Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
        Route::post('/calendario', [CalendarioController::class, 'store'])->name('calendario.store');
        Route::put('/calendario/{evento}', [CalendarioController::class, 'update'])->name('calendario.update');
        Route::delete('/calendario/{evento}', [CalendarioController::class, 'destroy'])->name('calendario.destroy');

        // Reportes
        Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/conexion', [ReportesController::class, 'conexion'])->name('reportes.conexion');
        Route::get('/reportes/discipulado', [ReportesController::class, 'discipulado'])->name('reportes.discipulado');
        Route::get('/reportes/exportar', [ReportesController::class, 'export'])->name('reportes.export');

        // Biblioteca
        Route::get('/biblioteca', [BibliotecaController::class, 'index'])->name('biblioteca.index');
        Route::post('/biblioteca', [BibliotecaController::class, 'store'])->name('biblioteca.store');
        Route::delete('/biblioteca/{material}', [BibliotecaController::class, 'destroy'])->name('biblioteca.destroy');
        Route::get('/biblioteca/{material}/descargar', [BibliotecaController::class, 'download'])->name('biblioteca.download');

        // Perfil
        Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
        Route::post('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

        // Usuarios
        Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/{user}/editar', [UsuariosController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{user}', [UsuariosController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{user}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');

        // Configuración
        Route::get('/configuracion/roles', [ConfigController::class, 'roles'])->name('config.roles');
        Route::get('/configuracion/temas', [ConfigController::class, 'temas'])->name('config.temas');
        Route::post('/configuracion/temas', [ConfigController::class, 'temasStore'])->name('config.temas.store');
        Route::get('/configuracion/ajustes', [ConfigController::class, 'ajustes'])->name('config.ajustes');
        Route::post('/configuracion/ajustes', [ConfigController::class, 'ajustesStore'])->name('config.ajustes.store');
        Route::get('/configuracion/ministries', [ConfigController::class, 'ministries'])->name('config.ministries');
        Route::post('/configuracion/ministries', [ConfigController::class, 'ministriesStore'])->name('config.ministries.store');
        Route::delete('/configuracion/ministries/{ministry}', [ConfigController::class, 'ministriesDestroy'])->name('config.ministries.destroy');
        Route::get('/configuracion/sedes', [ConfigController::class, 'sedes'])->name('config.sedes');
        Route::post('/configuracion/sedes', [ConfigController::class, 'sedesStore'])->name('config.sedes.store');
        Route::delete('/configuracion/sedes/{sede}', [ConfigController::class, 'sedesDestroy'])->name('config.sedes.destroy');
        Route::get('/configuracion/migracion', [ConfigController::class, 'migracion'])->name('config.migracion');
        Route::get('/configuracion/migracion/churches', [ConfigController::class, 'migracionChurches'])->name('config.migracion.churches');
        Route::post('/configuracion/migracion', [ConfigController::class, 'migracionStore'])->name('config.migracion.store');
    });
});
