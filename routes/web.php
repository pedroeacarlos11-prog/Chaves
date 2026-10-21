<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Painel\ChaveController as PainelChaveController;
use App\Http\Controllers\Painel\EmprestimoController as PainelEmprestimoController;
use App\Http\Controllers\Painel\FuncionarioController as PainelFuncionarioController;
use App\Http\Controllers\PainelController;
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

    Route::get('/', [PainelController::class, 'index'])->defaults('page', 'inicio')->name('inicio');
    Route::get('/agenda', [PainelController::class, 'index'])->defaults('page', 'agenda')->name('agenda');
    Route::get('/emprestimos', [PainelController::class, 'index'])->defaults('page', 'emprestimos')->name('emprestimos');
    Route::get('/funcionarios', [PainelController::class, 'index'])->defaults('page', 'funcionarios')->name('funcionarios');
    Route::get('/chaves', [PainelController::class, 'index'])->defaults('page', 'chaves')->name('chaves');

    Route::post('/funcionarios', [PainelFuncionarioController::class, 'store'])->name('painel.funcionarios.store');
    Route::put('/funcionarios/{funcionario}', [PainelFuncionarioController::class, 'update'])->name('painel.funcionarios.update');
    Route::delete('/funcionarios/{funcionario}', [PainelFuncionarioController::class, 'destroy'])->name('painel.funcionarios.destroy');

    Route::post('/chaves', [PainelChaveController::class, 'store'])->name('painel.chaves.store');
    Route::put('/chaves/{chave}', [PainelChaveController::class, 'update'])->name('painel.chaves.update');
    Route::delete('/chaves/{chave}', [PainelChaveController::class, 'destroy'])->name('painel.chaves.destroy');

    Route::post('/emprestimos', [PainelEmprestimoController::class, 'store'])->name('painel.emprestimos.store');
    Route::put('/emprestimos/{emprestimo}', [PainelEmprestimoController::class, 'update'])->name('painel.emprestimos.update');
    Route::delete('/emprestimos/{emprestimo}', [PainelEmprestimoController::class, 'destroy'])->name('painel.emprestimos.destroy');

    Route::redirect('/usuarios', '/funcionarios')->name('usuarios');
    Route::redirect('/cadastro_usuario', '/funcionarios')->name('cadastro_usuario');
});
