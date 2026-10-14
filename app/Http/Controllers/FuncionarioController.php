<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\V1\FuncionarioResource;
use App\Models\Funcionario;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class FuncionarioController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return FuncionarioResource::collection(
            Funcionario::query()->orderBy('nome')->orderBy('id')->paginate(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('funcionario', 'email')],
            'cargo' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        return (new FuncionarioResource(Funcionario::create($validated)))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Funcionario $funcionario): FuncionarioResource
    {
        return new FuncionarioResource($funcionario);
    }

    public function update(Request $request, Funcionario $funcionario): FuncionarioResource
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('funcionario', 'email')->ignore($funcionario->getKey()),
            ],
            'cargo' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $funcionario->update($validated);

        return new FuncionarioResource($funcionario->refresh());
    }

    public function destroy(Funcionario $funcionario): Response
    {
        if ($funcionario->emprestimos()->exists()) {
            return response()->json([
                'message' => 'Não é possível excluir funcionário vinculado a empréstimos.',
            ], Response::HTTP_CONFLICT);
        }

        try {
            $funcionario->delete();
        } catch (QueryException) {
            return response()->json([
                'message' => 'Não é possível excluir funcionário vinculado a empréstimos.',
            ], Response::HTTP_CONFLICT);
        }

        return response()->noContent();
    }

    public function cadastro_html(): View
    {
        return view('cadastro_funcionario');
    }
}
