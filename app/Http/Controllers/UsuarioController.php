<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\V1\UsuarioResource;
use App\Models\TokenUsuario;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'senha' => ['required', 'string'],
            'device_name' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $usuario = Usuario::query()->where('email', $validated['email'])->first();

        $storedPassword = (string) ($usuario?->senha ?? '');
        $isLegacyMd5 = preg_match('/\A[a-f0-9]{32}\z/i', $storedPassword) === 1;
        $passwordIsValid = $usuario && ($isLegacyMd5
            ? hash_equals(strtolower($storedPassword), md5($validated['senha']))
            : Hash::check($validated['senha'], $storedPassword));

        if (! $passwordIsValid) {
            throw ValidationException::withMessages([
                'email' => ['E-mail ou senha inválidos.'],
            ]);
        }

        if ($isLegacyMd5) {
            $usuario->update([
                'senha' => Hash::make($validated['senha']),
            ]);
        }

        $expiresAt = now()->addDays(30);
        $newAccessToken = $usuario->createToken(
            ($validated['device_name'] ?? null) ?: 'api',
            ['*'],
            $expiresAt,
        );

        TokenUsuario::create([
            'usuario_id' => $usuario->getKey(),
            'token' => $newAccessToken->accessToken->token,
            'valido_ate' => $expiresAt,
        ]);

        return response()->json([
            'access_token' => $newAccessToken->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toISOString(),
            'usuario' => (new UsuarioResource($usuario))->resolve($request),
        ]);
    }

    public function index(): AnonymousResourceCollection
    {
        return UsuarioResource::collection(
            Usuario::query()->orderBy('nome')->orderBy('id')->paginate(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('mysql_laravel.usuario', 'email')],
            'senha' => ['required', 'string', 'min:8'],
            'cpf' => ['sometimes', 'nullable', 'string', 'max:255'],
            'data_nascimento' => ['required', 'date'],
        ]);

        $validated['senha'] = Hash::make($validated['senha']);
        $usuario = Usuario::create($validated);

        return (new UsuarioResource($usuario))->response()->setStatusCode(201);
    }

    public function show(Usuario $usuario): UsuarioResource
    {
        return new UsuarioResource($usuario);
    }

    public function update(Request $request, Usuario $usuario): UsuarioResource
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('mysql_laravel.usuario', 'email')->ignore($usuario->getKey()),
            ],
            'senha' => ['sometimes', 'required', 'string', 'min:8'],
            'cpf' => ['sometimes', 'nullable', 'string', 'max:255'],
            'data_nascimento' => ['sometimes', 'required', 'date'],
        ]);

        if (isset($validated['senha'])) {
            $validated['senha'] = Hash::make($validated['senha']);
        }

        $usuario->update($validated);

        return new UsuarioResource($usuario->refresh());
    }

    public function destroy(Usuario $usuario): Response
    {
        $usuario->delete();

        return response()->noContent();
    }

    public function ver_usuario(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:mysql_laravel.usuario,id'],
        ]);

        $usuario = Usuario::findOrFail($validated['id']);

        return response()->json([
            'usuario' => (new UsuarioResource($usuario))->resolve($request),
        ]);
    }
}
