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
        
        // Mis Usuarios
        Route::get('/usuarios', function () {
            return view('orientador.users');
        })->name('users');

        // Estadísticas
        Route::get('/estadisticas', function () {
            return view('orientador.statistics');
        })->name('estadisticas');

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

    Route::middleware(['role:user'])->group(function () {
        
        // Responder Tests
        Route::get('/tests/responder/{assignmentId}', function ($assignmentId) {
            return view('tests.take', ['assignmentId' => $assignmentId]);
        })->name('tests.take');

        // Mis Resultados
        Route::get('/mis-resultados', function () {
            return view('results.index');
        })->name('results.index');

        // Ver Resultado Específico
        Route::get('/resultados/{responseId}', function ($responseId) {
            return view('results.show', ['responseId' => $responseId]);
        })->name('results.show');
    });

    // RUTAS COMPARTIDAS ADMIN + ORIENTADOR

    Route::middleware(['role:admin,advisor'])->group(function () {

        // Gestión de Grupos
        Route::get('/grupos', function () {
            return view('grupos.index');
        })->name('grupos.index');

        // Asignar Tests
        Route::get('/tests/asignar', function () {
            return view('tests.assignments');
        })->name('tests.assignments');

        // Resultados
        Route::get('/resultados', function () {
            return view('advisor.results');
        })->name('advisor.results');

        // Ver Resultado Específico
        Route::get('/resultados/{responseId}/ver', function ($responseId) {
            return view('advisor.results-show', ['responseId' => $responseId]);
        })->name('advisor.results.show');
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