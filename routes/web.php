<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\AcceptInvitation;
use App\Http\Controllers\PdfController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    // ========================================
    // DASHBOARD - Todos los roles autenticados
    // ========================================

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    // ========================================
    // RUTAS SOLO PARA ADMIN (role_id = 1)
    // ========================================

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Gestión de Usuarios
        Route::get('/users', function () {
            return view('admin.users');
        })->name('users');

        // Gestión de Áreas
        Route::get('/areas', function () {
            return view('admin.areas');
        })->name('areas');

        // Gestión de Tests
        Route::get('/tests', function () {
            return view('admin.tests');
        })->name('tests');

        // PDFs de Admin
        Route::get('/pdf/dashboard', [PdfController::class, 'downloadAdminDashboard'])
            ->name('pdf.dashboard');

        Route::get('/pdf/user-history/{userId}', [PdfController::class, 'downloadUserHistory'])
            ->name('pdf.user-history');
    });

    // ========================================
    // RUTAS SOLO PARA ORIENTADOR (role_id = 2)
    // ========================================

    Route::middleware(['role:advisor'])->prefix('advisor')->name('advisor.')->group(function () {
        
        // Mis Usuarios
        Route::get('/users', function () {
            return view('orientador.users');
        })->name('users');

        // Estadísticas
        Route::get('/statistics', function () {
            return view('orientador.statistics');
        })->name('statistics');

        // PDFs de Orientador
        Route::get('/pdf/statistics', [PdfController::class, 'downloadAdvisorStatistics'])
            ->name('pdf.statistics');
        
        Route::get('/pdf/group/{groupId}', [PdfController::class, 'downloadGroupReport'])
            ->name('pdf.group');
        
        Route::get('/pdf/user/{userId}', [PdfController::class, 'downloadUserHistory'])
            ->name('pdf.user-history');
    });

    // ========================================
    // RUTAS SOLO PARA USUARIO (role_id = 3)
    // ========================================

    Route::middleware(['role:user'])->group(function () {
        
        // Responder Tests
        Route::get('/tests/take/{assignmentId}', function ($assignmentId) {
            return view('tests.take', ['assignmentId' => $assignmentId]);
        })->name('tests.take');

        // Mis Resultados
        Route::get('/my-results', function () {
            return view('results.index');
        })->name('results.index');

        // Ver Resultado Específico
        Route::get('/results/{responseId}', function ($responseId) {
            return view('results.show', ['responseId' => $responseId]);
        })->name('results.show');
    });

    // ========================================
    // RUTAS PDF COMPARTIDAS (Acceso controlado por el controlador)
    // ========================================
    Route::middleware(['role:admin,advisor,user'])->group(function () {
        Route::get('/pdf/test-result/{responseId}', [PdfController::class, 'downloadTestResult'])
            ->name('pdf.test-result');
        
        Route::get('/pdf/user-history/{userId?}', [PdfController::class, 'downloadUserHistory'])
            ->name('pdf.user-history');

        Route::get('/pdf/user-integral/{userId?}', [PdfController::class, 'downloadUserIntegralReport'])
            ->name('pdf.user-integral');
    });

    // ========================================
    // RUTAS COMPARTIDAS ADMIN + ORIENTADOR
    // ========================================

    Route::middleware(['role:admin,advisor'])->group(function () {

        // Gestión de Grupos
        Route::get('/groups', function () {
            return view('grupos.index');
        })->name('groups.index');

        // Asignar Tests
        Route::get('/tests/assign', function () {
            return view('tests.assignments');
        })->name('tests.assignments');

        // Resultados
        Route::get('/results', function () {
            return view('advisor.results');
        })->name('advisor.results');

        // Ver Resultado Específico
        Route::get('/results/{responseId}/show', function ($responseId) {
            return view('advisor.results-show', ['responseId' => $responseId]);
        })->name('advisor.results.show');
    });
});

// ========================================
// RUTAS PÚBLICAS (sin autenticación)
// ========================================

// Aceptar invitación (link firmado en email)
Route::get('/invitation/accept/{token}', AcceptInvitation::class)
    ->name('invitation.accept')
    ->middleware('signed');