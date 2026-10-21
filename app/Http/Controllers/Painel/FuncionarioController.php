<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Funcionario;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FuncionarioController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('funcionario', 'email')],
            'cargo' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        Funcionario::create($validated);

        return back()->with('status', 'Funcionário cadastrado.');
    }

    public function update(Request $request, Funcionario $funcionario): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('funcionario', 'email')->ignore($funcionario->getKey()),
            ],
            'cargo' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $funcionario->update($validated);

        return back()->with('status', 'Dados do funcionário atualizados.');
    }

    public function destroy(Funcionario $funcionario): RedirectResponse
    {
        if ($funcionario->emprestimos()->exists()) {
            return back()->with('error', 'Não é possível excluir um funcionário vinculado a empréstimos.');
        }

        try {
            $funcionario->delete();
        } catch (QueryException) {
            return back()->with('error', 'Não é possível excluir um funcionário vinculado a empréstimos.');
        }

        return back()->with('status', 'Funcionário removido.');
    }
}
