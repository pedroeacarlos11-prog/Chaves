<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\V1\LogEmprestimoResource;
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
        $validated = $request->validate([
            'emprestimo_id' => ['sometimes', 'required', 'integer', 'exists:emprestimo,id'],
            'acao' => ['sometimes', 'required', 'string', 'max:100'],
            'data_hora' => ['sometimes', 'required', 'date'],
        ]);

        $logEmprestimo->update($validated);

        return new LogEmprestimoResource($logEmprestimo->refresh());
    }

    public function destroy(LogEmprestimo $logEmprestimo): Response
    {
        $logEmprestimo->delete();

        return response()->noContent();
    }
}
