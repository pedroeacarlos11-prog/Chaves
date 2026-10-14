<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\V1\ChaveResource;
use App\Models\Chave;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ChaveController extends Controller
{
    private const STATUSES = ['Disponível', 'Emprestada', 'Indisponível'];

    public function index(): AnonymousResourceCollection
    {
        return ChaveResource::collection(
            Chave::query()->orderBy('nome_chave')->orderBy('id')->paginate(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome_chave' => ['required', 'string', 'max:100'],
            'localizacao' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'required', 'string', Rule::in(self::STATUSES)],
        ]);

        return (new ChaveResource(Chave::create($validated)))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Chave $chave): ChaveResource
    {
        return new ChaveResource($chave);
    }

    public function update(Request $request, Chave $chave): ChaveResource
    {
        $validated = $request->validate([
            'nome_chave' => ['sometimes', 'required', 'string', 'max:100'],
            'localizacao' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'required', 'string', Rule::in(self::STATUSES)],
        ]);

        $chave->update($validated);

        return new ChaveResource($chave->refresh());
    }

    public function destroy(Chave $chave): Response
    {
        if ($chave->emprestimos()->exists()) {
            return response()->json([
                'message' => 'Não é possível excluir chave vinculada a empréstimos.',
            ], Response::HTTP_CONFLICT);
        }

        try {
            $chave->delete();
        } catch (QueryException) {
            return response()->json([
                'message' => 'Não é possível excluir chave vinculada a empréstimos.',
            ], Response::HTTP_CONFLICT);
        }

        return response()->noContent();
    }
}
