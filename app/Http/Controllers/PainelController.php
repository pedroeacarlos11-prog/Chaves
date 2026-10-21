<?php

namespace App\Http\Controllers;

use App\Models\Chave;
use App\Models\Emprestimo;
use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PainelController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'busca' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'nullable', Rule::in([
                'Disponível',
                'Emprestada',
                'Indisponível',
                'Em andamento',
                'Devolvido',
                'Atrasado',
            ])],
        ]);

        $page = (string) $request->route('page', 'inicio');
        $search = $request->string('busca')->trim()->toString();
        $status = $request->string('status')->trim()->toString();

        $stats = [
            'chaves' => Chave::query()->count(),
            'chaves_disponiveis' => Chave::query()->where('status', 'Disponível')->count(),
            'funcionarios' => Funcionario::query()->count(),
            'emprestimos_andamento' => Emprestimo::query()->where('status', 'Em andamento')->count(),
            'emprestimos_atrasados' => Emprestimo::query()->where('status', 'Atrasado')->count(),
        ];

        $viewData = [
            'page' => $page,
            'stats' => $stats,
        ];

        if ($page === 'inicio') {
            $viewData['recentEmprestimos'] = Emprestimo::query()
                ->with(['funcionario', 'chave'])
                ->orderByDesc('data_retirada')
                ->orderByDesc('id')
                ->limit(5)
                ->get();
        }

        if ($page === 'funcionarios') {
            $viewData['funcionarios'] = Funcionario::query()
                ->when($search !== '', function (Builder $query) use ($search): void {
                    $query->where(function (Builder $query) use ($search): void {
                        $query->where('nome', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%')
                            ->orWhere('cargo', 'like', '%'.$search.'%');
                    });
                })
                ->orderBy('nome')
                ->orderBy('id')
                ->paginate(10)
                ->withQueryString();
        }

        if ($page === 'chaves') {
            $viewData['chaves'] = Chave::query()
                ->when($search !== '', function (Builder $query) use ($search): void {
                    $query->where(function (Builder $query) use ($search): void {
                        $query->where('nome_chave', 'like', '%'.$search.'%')
                            ->orWhere('localizacao', 'like', '%'.$search.'%');
                    });
                })
                ->when($status !== '', fn (Builder $query) => $query->where('status', $status))
                ->orderBy('nome_chave')
                ->orderBy('id')
                ->paginate(10)
                ->withQueryString();
        }

        if (in_array($page, ['agenda', 'emprestimos'], true)) {
            $viewData['funcionarioOptions'] = Funcionario::query()
                ->orderBy('nome')
                ->get(['id', 'nome']);
            $viewData['chaveOptions'] = Chave::query()
                ->orderBy('nome_chave')
                ->get(['id', 'nome_chave', 'localizacao', 'status']);
            $viewData['emprestimos'] = Emprestimo::query()
                ->with(['funcionario', 'chave'])
                ->when($search !== '', function (Builder $query) use ($search): void {
                    $query->where(function (Builder $query) use ($search): void {
                        $query->whereHas('funcionario', fn (Builder $funcionarioQuery) => $funcionarioQuery->where('nome', 'like', '%'.$search.'%'))
                            ->orWhereHas('chave', fn (Builder $chaveQuery) => $chaveQuery->where('nome_chave', 'like', '%'.$search.'%'));
                    });
                })
                ->when($status !== '', fn (Builder $query) => $query->where('status', $status))
                ->orderByDesc('data_retirada')
                ->orderByDesc('id')
                ->paginate(10)
                ->withQueryString();
        }

        return view('tela_inicio', $viewData);
    }
}
