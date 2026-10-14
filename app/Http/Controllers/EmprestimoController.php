<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\V1\EmprestimoResource;
use App\Models\Emprestimo;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class EmprestimoController extends Controller
{
    private const STATUSES = ['Em andamento', 'Devolvido', 'Atrasado'];

    public function index(): AnonymousResourceCollection
    {
        return EmprestimoResource::collection(Emprestimo::query()->orderByDesc('id')->paginate());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'funcionario_id' => ['required', 'integer', 'exists:funcionario,id'],
            'chave_id' => ['required', 'integer', 'exists:chave,id'],
            'data_retirada' => ['sometimes', 'nullable', 'date'],
            'hora_retirada' => ['sometimes', 'nullable', 'date_format:H:i'],
            'data_devolucao' => ['sometimes', 'nullable', 'date'],
            'hora_devolucao' => ['sometimes', 'nullable', 'date_format:H:i'],
            'status' => ['sometimes', 'required', 'string', Rule::in(self::STATUSES)],
        ]);

        return (new EmprestimoResource(Emprestimo::create($validated)))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Emprestimo $emprestimo): EmprestimoResource
    {
        return new EmprestimoResource($emprestimo);
    }

    public function update(Request $request, Emprestimo $emprestimo): EmprestimoResource
    {
        $validated = $request->validate([
            'funcionario_id' => ['sometimes', 'required', 'integer', 'exists:funcionario,id'],
            'chave_id' => ['sometimes', 'required', 'integer', 'exists:chave,id'],
            'data_retirada' => ['sometimes', 'nullable', 'date'],
            'hora_retirada' => ['sometimes', 'nullable', 'date_format:H:i'],
            'data_devolucao' => ['sometimes', 'nullable', 'date'],
            'hora_devolucao' => ['sometimes', 'nullable', 'date_format:H:i'],
            'status' => ['sometimes', 'required', 'string', Rule::in(self::STATUSES)],
        ]);

        $emprestimo->update($validated);

        return new EmprestimoResource($emprestimo->refresh());
    }

    public function destroy(Emprestimo $emprestimo): Response
    {
        if ($emprestimo->logs()->exists()) {
            return response()->json([
                'message' => 'Não é possível excluir empréstimo que possui registros de log.',
            ], Response::HTTP_CONFLICT);
        }

        try {
            $emprestimo->delete();
        } catch (QueryException) {
            return response()->json([
                'message' => 'Não é possível excluir empréstimo que possui registros de log.',
            ], Response::HTTP_CONFLICT);
        }

        return response()->noContent();
    }
}
