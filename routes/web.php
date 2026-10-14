<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfilePhotoController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('/cadastro', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/cadastro', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::post('/profile/photo', [ProfilePhotoController::class, 'update'])->name('profile.photo.update');

    Route::view('/', 'tela_inicio', ['page' => 'inicio'])->name('inicio');
    Route::view('/agenda', 'tela_inicio', ['page' => 'agenda'])->name('agenda');
    Route::view('/emprestimos', 'tela_inicio', ['page' => 'emprestimos'])->name('emprestimos');
    Route::view('/usuarios', 'tela_inicio', ['page' => 'usuarios'])->name('usuarios');
    Route::view('/chaves', 'tela_inicio', ['page' => 'chaves'])->name('chaves');

    Route::redirect('/cadastro_usuario', '/usuarios')->name('cadastro_usuario');
});
