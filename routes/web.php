<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\AcceptInvitation;
use App\Http\Controllers\Auth\CustomLoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    // DASHBOARD - Todos los roles autenticados

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // PERFIL - Todos los roles autenticados

    Route::get('/profile', function () {
        return view('profile.show');
    })->name('profile.show');

    // RUTAS SOLO PARA ADMIN (role_id = 1)

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Gestión de Usuarios
        Route::get('/usuarios', function () {
            return view('admin.users');
        })->name('users');

        // Instituciones (TODO)
        // Route::get('/instituciones', function () {
        //     return view('admin.institutions');
        // })->name('instituciones');

        // Reportes Generales (TODO)
        // Route::get('/reportes', function () {
        //     return view('admin.reports');
        // })->name('reportes');

        // Configuración (TODO)
        // Route::get('/configuracion', function () {
        //     return view('admin.settings');
        // })->name('configuracion');
    });

    // RUTAS SOLO PARA ORIENTADOR (role_id = 2)

    Route::middleware(['role:advisor'])->prefix('orientador')->name('orientador.')->group(function () {
        
        // Mis Usuarios (TODO)
        // Route::get('/usuarios', function () {
        //     return view('orientador.users');
        // })->name('users');

        // Asignar Tests (TODO)
        // Route::get('/asignar-tests', function () {
        //     return view('orientador.assign-tests');
        // })->name('asignar-tests');

        // Resultados (TODO)
        // Route::get('/resultados', function () {
        //     return view('orientador.results');
        // })->name('resultados');

        // Estadísticas (TODO)
        // Route::get('/estadisticas', function () {
        //     return view('orientador.statistics');
        // })->name('estadisticas');
    });

    // RUTAS SOLO PARA USUARIO (role_id = 3)

    Route::middleware(['role:user'])->prefix('usuario')->name('usuario.')->group(function () {
        
        // Mis Tests (TODO)
        // Route::get('/mis-tests', function () {
        //     return view('usuario.my-tests');
        // })->name('mis-tests');

        // Mis Resultados (TODO)
        // Route::get('/mis-resultados', function () {
        //     return view('usuario.my-results');
        // })->name('mis-resultados');

        // Mi Perfil (TODO - o usar el /profile global)
        // Route::get('/perfil', function () {
        //     return view('usuario.profile');
        // })->name('perfil');
    });

    // RUTAS COMPARTIDAS ADMIN + ORIENTADOR

    Route::middleware(['role:admin,advisor'])->group(function () {
        
        // Tests (TODO)
        // Route::get('/tests', function () {
        //     return view('shared.tests');
        // })->name('tests.index');
    });
});

// RUTAS PÚBLICAS (sin autenticación)

// Aceptar invitación (link firmado en email)
Route::get('/invitation/accept/{token}', AcceptInvitation::class)
    ->name('invitation.accept')
    ->middleware('signed');

// Login y Logout personalizados
Route::post('/login', [CustomLoginController::class, 'login'])->name('login');
Route::post('/logout', [CustomLoginController::class, 'logout'])->name('logout');