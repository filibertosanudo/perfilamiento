<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\AcceptInvitation;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/admin/usuarios', function () {
        return view('admin.users');
    })->name('admin.users');

});

Route::get('/invitation/accept/{token}', AcceptInvitation::class)
    ->name('invitation.accept')
    ->middleware('signed');