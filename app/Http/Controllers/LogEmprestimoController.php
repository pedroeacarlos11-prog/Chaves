<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\V1\LogEmprestimoResource;
use App\Http\Resources\Api\V1\SimpleItemResource;
use App\Models\LogEmprestimo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class LogEmprestimoController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return LogEmprestimoResource::collection(
            LogEmprestimo::query()->orderByDesc('id')->paginate(),
        );
    }

    public function listar_logs_emprestimos(Request $request): AnonymousResourceCollection
    {
        return $this->index();
    }

    public function listar_logs_emprestimos_simples(Request $request): AnonymousResourceCollection
    {
        return $this->simpleIndex();
    }

    public function salvar_log_emprestimo(Request $request): JsonResponse
    {
        return $this->store($request);
    }

    public function simpleIndex(): AnonymousResourceCollection
    {
        return SimpleItemResource::collection(
            LogEmprestimo::query()
                ->select(['id', 'emprestimo_id', 'acao', 'data_hora'])
                ->orderByDesc('id')
                ->paginate(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'emprestimo_id' => ['required', 'integer', 'exists:emprestimo,id'],
            'acao' => ['required', 'string', 'max:100'],
            'data_hora' => ['sometimes', 'required', 'date'],
        ]);

        $logEmprestimo = LogEmprestimo::create($validated);

        return (new LogEmprestimoResource($logEmprestimo->refresh()))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(LogEmprestimo $logEmprestimo): LogEmprestimoResource
    {
        return new LogEmprestimoResource($logEmprestimo);
    }

    public function update(Request $request, LogEmprestimo $logEmprestimo): LogEmprestimoResource
    {
        $required = $request->isMethod('PUT') ? ['required'] : ['sometimes', 'required'];

        $validated = $request->validate([
            'emprestimo_id' => [...$required, 'integer', 'exists:emprestimo,id'],
            'acao' => [...$required, 'string', 'max:100'],
            'data_hora' => [...$required, 'date'],
        ]);

        $logEmprestimo->update($validated);

        return new LogEmprestimoResource($logEmprestimo->refresh());
    }

    public function ver_log_emprestimo(Request $request): LogEmprestimoResource
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        return $this->show(LogEmprestimo::query()->findOrFail($validated['id']));
    }

    public function alterar_log_emprestimo(Request $request): LogEmprestimoResource
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:log_emprestimo,id'],
        ]);

        return $this->update($request, LogEmprestimo::query()->findOrFail($validated['id']));
    }

    public function destroy(LogEmprestimo $logEmprestimo): Response
    {
        $logEmprestimo->delete();

        return response()->noContent();
    }

    public function deletar_log_emprestimo(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:log_emprestimo,id'],
        ]);

        return $this->destroy(LogEmprestimo::query()->findOrFail($validated['id']));
    }
}
