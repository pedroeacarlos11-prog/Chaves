<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Emprestimo;
use App\Services\EmprestimoService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EmprestimoController extends Controller
{
    private const STATUSES = ['Em andamento', 'Devolvido', 'Atrasado'];

    public function store(Request $request, EmprestimoService $emprestimoService): RedirectResponse
    {
        $validated = $request->validate([
            'funcionario_id' => ['required', 'integer', 'exists:funcionario,id'],
            'chave_id' => ['required', 'integer', 'exists:chave,id'],
            'data_retirada' => ['required', 'date'],
            'hora_retirada' => ['required', 'date_format:H:i'],
        ]);

        $emprestimoService->create($validated);

        return back()->with('status', 'Empréstimo registrado.');
    }

    public function update(
        Request $request,
        Emprestimo $emprestimo,
        EmprestimoService $emprestimoService,
    ): RedirectResponse {
        $validated = $request->validate([
            'funcionario_id' => ['required', 'integer', 'exists:funcionario,id'],
            'chave_id' => ['required', 'integer', 'exists:chave,id'],
            'data_retirada' => ['required', 'date'],
            'hora_retirada' => ['required', 'date_format:H:i'],
            'data_devolucao' => ['nullable', 'date', 'required_if:status,Devolvido'],
            'hora_devolucao' => ['nullable', 'date_format:H:i', 'required_if:status,Devolvido'],
            'status' => ['required', 'string', Rule::in(self::STATUSES)],
        ]);

        if ($validated['status'] === 'Devolvido' && (! $validated['data_devolucao'] || ! $validated['hora_devolucao'])) {
            throw ValidationException::withMessages([
                'data_devolucao' => 'Informe a data e o horário em que a chave foi devolvida.',
            ]);
        }

        $emprestimoService->update($emprestimo, $validated);

        return back()->with('status', 'Empréstimo atualizado.');
    }

    public function destroy(Emprestimo $emprestimo, EmprestimoService $emprestimoService): RedirectResponse
    {
        if ($emprestimo->logs()->exists()) {
            return back()->with('error', 'Não é possível excluir um empréstimo com registros de histórico.');
        }

        try {
            $emprestimoService->delete($emprestimo);
        } catch (QueryException) {
            return back()->with('error', 'Não é possível excluir um empréstimo vinculado a registros de histórico.');
        }

        return back()->with('status', 'Empréstimo removido.');
    }
}
