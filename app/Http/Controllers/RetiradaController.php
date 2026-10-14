<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\V1\RetiradaResource;
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
        $validated = $request->validate([
            'bloco' => ['sometimes', 'required', 'string', 'max:80'],
            'sala' => ['sometimes', 'required', 'string', 'max:120'],
            'data_retirada' => ['sometimes', 'required', 'date'],
            'hora_retirada' => ['sometimes', 'required', 'date_format:H:i,H:i:s'],
            'hora_devolucao' => ['sometimes', 'nullable', 'date_format:H:i,H:i:s'],
            'status' => ['sometimes', 'required', 'string', 'max:20'],
        ]);

        $registro = $this->findOwnedRetirada($request, $retirada);
        $registro->update($validated);

        return new RetiradaResource($registro->refresh());
    }

    public function destroy(Request $request, int $retirada): Response
    {
        $this->findOwnedRetirada($request, $retirada)->delete();

        return response()->noContent();
    }

    private function findOwnedRetirada(Request $request, int $id): Retirada
    {
        return Retirada::query()
            ->where('usuario_id', $request->user()->getAuthIdentifier())
            ->findOrFail($id);
    }
}
