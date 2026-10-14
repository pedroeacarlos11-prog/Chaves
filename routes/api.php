<?php

use App\Http\Controllers\ChaveController;
use App\Http\Controllers\EmprestimoController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\LogEmprestimoController;
use App\Http\Controllers\RetiradaController;
use App\Http\Controllers\TokenUsuarioController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function (): void {
        Route::post('login', [UsuarioController::class, 'login'])
            ->middleware('throttle:6,1')
            ->name('login');

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('logout', [TokenUsuarioController::class, 'logout'])->name('logout');
            Route::apiResource('usuarios', UsuarioController::class);
            Route::apiResource('retiradas', RetiradaController::class);
            Route::apiResource('tokens-usuario', TokenUsuarioController::class)->parameters(['tokens-usuario' => 'tokenUsuario']);
            Route::apiResource('funcionarios', FuncionarioController::class);
            Route::apiResource('chaves', ChaveController::class)->parameters(['chaves' => 'chave']);
            Route::apiResource('emprestimos', EmprestimoController::class);
            Route::apiResource('logs-emprestimos', LogEmprestimoController::class)->parameters(['logs-emprestimos' => 'logEmprestimo']);
            Route::get('ver_usuario', [UsuarioController::class, 'ver_usuario']);
        });
    });
