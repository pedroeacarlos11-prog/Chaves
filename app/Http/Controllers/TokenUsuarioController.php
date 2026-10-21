<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\V1\SimpleItemResource;
use App\Http\Resources\Api\V1\TokenUsuarioResource;
use App\Models\TokenUsuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class TokenUsuarioController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return TokenUsuarioResource::collection(
            TokenUsuario::query()
                ->where('usuario_id', $request->user()->getAuthIdentifier())
                ->latest('id')
                ->paginate(),
        );
    }

    public function listar_tokens_usuario(Request $request): AnonymousResourceCollection
    {
        return $this->index($request);
    }

    public function listar_tokens_usuario_simples(Request $request): AnonymousResourceCollection
    {
        return $this->simpleIndex($request);
    }

    public function salvar_token_usuario(Request $request): JsonResponse
    {
        return $this->store($request);
    }

    public function simpleIndex(Request $request): AnonymousResourceCollection
    {
        return SimpleItemResource::collection(
            TokenUsuario::query()
                ->where('usuario_id', $request->user()->getAuthIdentifier())
                ->select(['id', 'usuario_id', 'valido_ate'])
                ->latest('id')
                ->paginate(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'valido_ate' => ['sometimes', 'required', 'date', 'after:now'],
        ]);

        $expiresAt = isset($validated['valido_ate'])
            ? Carbon::parse($validated['valido_ate'])
            : now()->addDays(30);
        $newAccessToken = $request->user()->createToken(
            ($validated['device_name'] ?? null) ?: 'api-token',
            ['*'],
            $expiresAt,
        );
        $tokenUsuario = TokenUsuario::create([
            'usuario_id' => $request->user()->getAuthIdentifier(),
            'token' => $newAccessToken->accessToken->token,
            'valido_ate' => $expiresAt,
        ]);

        return response()->json([
            'token_usuario' => (new TokenUsuarioResource($tokenUsuario))->resolve($request),
            'access_token' => $newAccessToken->plainTextToken,
            'token_type' => 'Bearer',
        ], Response::HTTP_CREATED);
    }

    public function show(Request $request, int $tokenUsuario): TokenUsuarioResource
    {
        return new TokenUsuarioResource($this->findOwnedToken($request, $tokenUsuario));
    }

    public function update(Request $request, int $tokenUsuario): TokenUsuarioResource
    {
        $required = $request->isMethod('PUT') ? ['required'] : ['sometimes', 'required'];

        $validated = $request->validate([
            'valido_ate' => [...$required, 'date', 'after:now'],
        ]);

        $token = $this->findOwnedToken($request, $tokenUsuario);
        $token->update($validated);
        if (isset($validated['valido_ate'])) {
            $request->user()->tokens()
                ->where('token', $token->token)
                ->update(['expires_at' => $validated['valido_ate']]);
        }

        return new TokenUsuarioResource($token->refresh());
    }

    public function ver_token_usuario(Request $request): TokenUsuarioResource
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        return $this->show($request, (int) $validated['id']);
    }

    public function alterar_token_usuario(Request $request): TokenUsuarioResource
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        return $this->update($request, (int) $validated['id']);
    }

    public function destroy(Request $request, int $tokenUsuario): Response
    {
        $token = $this->findOwnedToken($request, $tokenUsuario);
        $request->user()->tokens()->where('token', $token->token)->delete();
        $token->delete();

        return response()->noContent();
    }

    public function deletar_token_usuario(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        return $this->destroy($request, (int) $validated['id']);
    }

    public function logout(Request $request): Response
    {
        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            TokenUsuario::query()->where('token', $token->token)->delete();
            $token->delete();
        }

        return response()->noContent();
    }

    private function findOwnedToken(Request $request, int $id): TokenUsuario
    {
        return TokenUsuario::query()
            ->where('usuario_id', $request->user()->getAuthIdentifier())
            ->findOrFail($id);
    }
}
