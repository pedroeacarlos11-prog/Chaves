<?php

use App\Http\Controllers\ChaveController;
use App\Http\Controllers\EmprestimoController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\LogEmprestimoController;
use App\Http\Controllers\RetiradaController;
use App\Http\Controllers\TokenUsuarioController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::post('/salvar_usuario', [UsuarioController::class, 'salvar_usuario']);
Route::get('/listar_usuarios', [UsuarioController::class, 'listar_usuarios']);
Route::get('listar_usuarios_simples', [UsuarioController::class, 'listar_usuarios_simples'])->name('legacy.usuarios.simple');
Route::put('alterar_usuario', [UsuarioController::class, 'alterar_usuario'])->name('legacy.usuarios.update');
Route::delete('deletar_usuario', [UsuarioController::class, 'deletar_usuario'])->name('legacy.usuarios.destroy');

Route::post('/salvar_funcionario', [FuncionarioController::class, 'salvar_funcionario'])->name('legacy.funcionarios.store');
Route::get('/ver_funcionario', [FuncionarioController::class, 'ver_funcionario'])->name('legacy.funcionarios.show');
Route::get('/listar_funcionarios', [FuncionarioController::class, 'listar_funcionarios']);
Route::get('/listar_funcionarios_simples', [FuncionarioController::class, 'listar_funcionarios_simples'])->name('legacy.funcionarios.simple');
Route::put('/alterar_funcionario', [FuncionarioController::class, 'alterar_funcionario']);
Route::delete('/deletar_funcionario', [FuncionarioController::class, 'deletar_funcionario']);

Route::post('/salvar_chave', [ChaveController::class, 'salvar_chave'])->name('legacy.chaves.store');
Route::get('/ver_chave', [ChaveController::class, 'ver_chave'])->name('legacy.chaves.show');
Route::get('/listar_chaves', [ChaveController::class, 'listar_chaves'])->name('legacy.chaves.index');
Route::get('/listar_chaves_simples', [ChaveController::class, 'listar_chaves_simples'])->name('legacy.chaves.simple');
Route::put('/alterar_chave', [ChaveController::class, 'alterar_chave'])->name('legacy.chaves.update');
Route::patch('/alterar_chave', [ChaveController::class, 'alterar_chave']);
Route::delete('/deletar_chave', [ChaveController::class, 'deletar_chave'])->name('legacy.chaves.destroy');

Route::post('salvar_emprestimo', [EmprestimoController::class, 'salvar_emprestimo'])->name('legacy.emprestimos.store');
Route::get('ver_emprestimo', [EmprestimoController::class, 'ver_emprestimo'])->name('legacy.emprestimos.show');
Route::get('listar_emprestimo', [EmprestimoController::class, 'listar_emprestimos']);
Route::get('listar_emprestimos', [EmprestimoController::class, 'listar_emprestimos'])->name('legacy.emprestimos.index');
Route::get('listar_emprestimos_simples', [EmprestimoController::class, 'listar_emprestimos_simples'])->name('legacy.emprestimos.simple');
Route::put('alterar_emprestimo', [EmprestimoController::class, 'alterar_emprestimo'])->name('legacy.emprestimos.update');
Route::patch('alterar_emprestimo', [EmprestimoController::class, 'alterar_emprestimo']);
Route::delete('deletar_emprestimo', [EmprestimoController::class, 'deletar_emprestimo'])->name('legacy.emprestimos.destroy');

Route::post('salvar_log_emprestimo', [LogEmprestimoController::class, 'salvar_log_emprestimo'])->name('legacy.logs-emprestimos.store');
Route::get('ver_log_emprestimo', [LogEmprestimoController::class, 'ver_log_emprestimo'])->name('legacy.logs-emprestimos.show');
Route::get('listar_logs_emprestimos', [LogEmprestimoController::class, 'listar_logs_emprestimos'])->name('legacy.logs-emprestimos.index');
Route::get('listar_logs_emprestimos_simples', [LogEmprestimoController::class, 'listar_logs_emprestimos_simples'])->name('legacy.logs-emprestimos.simple');
Route::put('alterar_log_emprestimo', [LogEmprestimoController::class, 'alterar_log_emprestimo'])->name('legacy.logs-emprestimos.update');
Route::patch('alterar_log_emprestimo', [LogEmprestimoController::class, 'alterar_log_emprestimo']);
Route::delete('deletar_log_emprestimo', [LogEmprestimoController::class, 'deletar_log_emprestimo'])->name('legacy.logs-emprestimos.destroy');

Route::post('salvar_retirada', [RetiradaController::class, 'salvar_retirada'])->name('legacy.retiradas.store');
Route::get('ver_retirada', [RetiradaController::class, 'ver_retirada'])->name('legacy.retiradas.show');
Route::get('listar_retiradas', [RetiradaController::class, 'listar_retiradas'])->name('legacy.retiradas.index');
Route::get('listar_retiradas_simples', [RetiradaController::class, 'listar_retiradas_simples'])->name('legacy.retiradas.simple');
Route::put('alterar_retirada', [RetiradaController::class, 'alterar_retirada'])->name('legacy.retiradas.update');
Route::patch('alterar_retirada', [RetiradaController::class, 'alterar_retirada']);
Route::delete('deletar_retirada', [RetiradaController::class, 'deletar_retirada'])->name('legacy.retiradas.destroy');

Route::post('salvar_token_usuario', [TokenUsuarioController::class, 'salvar_token_usuario'])->name('legacy.tokens-usuario.store');
Route::get('ver_token_usuario', [TokenUsuarioController::class, 'ver_token_usuario'])->name('legacy.tokens-usuario.show');
Route::get('listar_tokens_usuario', [TokenUsuarioController::class, 'listar_tokens_usuario'])->name('legacy.tokens-usuario.index');
Route::get('listar_tokens_usuario_simples', [TokenUsuarioController::class, 'listar_tokens_usuario_simples'])->name('legacy.tokens-usuario.simple');
Route::put('alterar_token_usuario', [TokenUsuarioController::class, 'alterar_token_usuario'])->name('legacy.tokens-usuario.update');
Route::patch('alterar_token_usuario', [TokenUsuarioController::class, 'alterar_token_usuario']);
Route::delete('deletar_token_usuario', [TokenUsuarioController::class, 'deletar_token_usuario'])->name('legacy.tokens-usuario.destroy');

Route::get('usuarios/simples', [UsuarioController::class, 'simpleIndex'])->name('usuarios.simple');
Route::apiResource('usuarios', UsuarioController::class);
Route::get('retiradas/simples', [RetiradaController::class, 'simpleIndex'])->name('retiradas.simple');
Route::apiResource('retiradas', RetiradaController::class);
Route::get('tokens-usuario/simples', [TokenUsuarioController::class, 'simpleIndex'])->name('tokens-usuario.simple');
Route::apiResource('tokens-usuario', TokenUsuarioController::class)->parameters(['tokens-usuario' => 'tokenUsuario']);
Route::get('funcionarios/simples', [FuncionarioController::class, 'simpleIndex'])->name('funcionarios.simple');
Route::apiResource('funcionarios', FuncionarioController::class);
Route::get('chaves/simples', [ChaveController::class, 'simpleIndex'])->name('chaves.simple');
Route::apiResource('chaves', ChaveController::class)->parameters(['chaves' => 'chave']);
Route::get('emprestimos/simples', [EmprestimoController::class, 'simpleIndex'])->name('emprestimos.simple');
Route::apiResource('emprestimos', EmprestimoController::class);
Route::get('logs-emprestimos/simples', [LogEmprestimoController::class, 'simpleIndex'])->name('logs-emprestimos.simple');
Route::apiResource('logs-emprestimos', LogEmprestimoController::class)->parameters(['logs-emprestimos' => 'logEmprestimo']);
Route::get('ver_usuario', [UsuarioController::class, 'ver_usuario']);

Route::get('emprestimo/{id}', [EmprestimoController::class, 'ver_emprestimo'])->name('emprestimos.show');
