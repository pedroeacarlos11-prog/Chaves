<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\V1\RetiradaResource;
use App\Http\Resources\Api\V1\SimpleItemResource;
use App\Models\Retirada;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class RetiradaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return RetiradaResource::collection(
            Retirada::query()
                ->where('usuario_id', $request->user()->getAuthIdentifier())
                ->orderByDesc('data_retirada')
                ->orderByDesc('id')
                ->paginate(),
        );
    }

    public function listar_retiradas(Request $request): AnonymousResourceCollection
    {
        return $this->index($request);
    }

    public function listar_retiradas_simples(Request $request): AnonymousResourceCollection
    {
        return $this->simpleIndex($request);
    }

    public function salvar_retirada(Request $request): JsonResponse
    {
        return $this->store($request);
    }

    public function simpleIndex(Request $request): AnonymousResourceCollection
    {
        return SimpleItemResource::collection(
            Retirada::query()
                ->where('usuario_id', $request->user()->getAuthIdentifier())
                ->select(['id', 'bloco', 'sala', 'data_retirada', 'hora_retirada', 'status'])
                ->orderByDesc('data_retirada')
                ->orderByDesc('id')
                ->paginate(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bloco' => ['required', 'string', 'max:80'],
            'sala' => ['required', 'string', 'max:120'],
            'data_retirada' => ['required', 'date'],
            'hora_retirada' => ['required', 'date_format:H:i,H:i:s'],
            'hora_devolucao' => ['sometimes', 'nullable', 'date_format:H:i,H:i:s'],
            'status' => ['sometimes', 'required', 'string', 'max:20'],
        ]);

        $validated['usuario_id'] = $request->user()->getAuthIdentifier();
        $retirada = Retirada::create($validated);

        return (new RetiradaResource($retirada))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Request $request, int $retirada): RetiradaResource
    {
        return new RetiradaResource($this->findOwnedRetirada($request, $retirada));
    }

    public function update(Request $request, int $retirada): RetiradaResource
    {
        $required = $request->isMethod('PUT') ? ['required'] : ['sometimes', 'required'];
        $present = $request->isMethod('PUT') ? ['present'] : ['sometimes'];

        $validated = $request->validate([
            'bloco' => [...$required, 'string', 'max:80'],
            'sala' => [...$required, 'string', 'max:120'],
            'data_retirada' => [...$required, 'date'],
            'hora_retirada' => [...$required, 'date_format:H:i,H:i:s'],
            'hora_devolucao' => [...$present, 'nullable', 'date_format:H:i,H:i:s'],
            'status' => [...$required, 'string', 'max:20'],
        ]);

        $registro = $this->findOwnedRetirada($request, $retirada);
        $registro->update($validated);

        return new RetiradaResource($registro->refresh());
    }

    public function ver_retirada(Request $request): RetiradaResource
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        return $this->show($request, (int) $validated['id']);
    }

    public function alterar_retirada(Request $request): RetiradaResource
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        return $this->update($request, (int) $validated['id']);
    }

    public function destroy(Request $request, int $retirada): Response
    {
        $this->findOwnedRetirada($request, $retirada)->delete();

        return response()->noContent();
    }

    public function deletar_retirada(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        return $this->destroy($request, (int) $validated['id']);
    }

    private function findOwnedRetirada(Request $request, int $id): Retirada
    {
        return Retirada::query()
            ->where('usuario_id', $request->user()->getAuthIdentifier())
            ->findOrFail($id);
    }
}
