<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Chave;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChaveController extends Controller
{
    private const STATUSES = ['Disponível', 'Emprestada', 'Indisponível'];

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome_chave' => ['required', 'string', 'max:100'],
            'localizacao' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['required', 'string', Rule::in(self::STATUSES)],
        ]);

        Chave::create($validated);

        return back()->with('status', 'Chave cadastrada.');
    }

    public function update(Request $request, Chave $chave): RedirectResponse
    {
        $validated = $request->validate([
            'nome_chave' => ['required', 'string', 'max:100'],
            'localizacao' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['required', 'string', Rule::in(self::STATUSES)],
        ]);

        if (
            $validated['status'] !== 'Emprestada'
            && $chave->emprestimos()->whereIn('status', ['Em andamento', 'Atrasado'])->exists()
        ) {
            return back()->with('error', 'Finalize o empréstimo antes de alterar a disponibilidade desta chave.');
        }

        $chave->update($validated);

        return back()->with('status', 'Dados da chave atualizados.');
    }

    public function destroy(Chave $chave): RedirectResponse
    {
        if ($chave->emprestimos()->exists()) {
            return back()->with('error', 'Não é possível excluir uma chave vinculada a empréstimos.');
        }

        try {
            $chave->delete();
        } catch (QueryException) {
            return back()->with('error', 'Não é possível excluir uma chave vinculada a empréstimos.');
        }

        return back()->with('status', 'Chave removida.');
    }
}
